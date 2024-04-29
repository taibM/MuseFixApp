<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'La commande a été créée avec succès.');

            return $this->redirectToRoute('app_commande_index');
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ancienFraisLivraison = $commande->getFraisLivraison();
            $nouveauFraisLivraison = $commande->getFraisLivraison();

            // Mettre à jour le total de la commande
            $ancienTotal = $commande->getTotal();
            $nouveauTotal = $ancienTotal - $ancienFraisLivraison + $nouveauFraisLivraison;
            $commande->setTotal($nouveauTotal);

            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update-frais-livraison', name: 'app_commande_update_frais_livraison', methods: ['POST'])]
    public function updateFraisLivraison(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        // Mettre à jour les frais de livraison de la commande
        $commande->setFraisLivraison($data['fraisLivraison']);

        $entityManager->flush();

        return new Response(null, Response::HTTP_OK);
    }
    #[Route('/{id}/update-total', name: 'app_commande_update_total', methods: ['POST'])]
    public function updateTotal(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['total'])) {
            $nouveauTotal = $data['total'];

            // Mettre à jour le total de la commande
            $commande->setTotal($nouveauTotal);

            $entityManager->flush();

            return new JsonResponse(['message' => 'Total mis à jour avec succès'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Erreur lors de la mise à jour du total'], Response::HTTP_BAD_REQUEST);
    }


    #[Route('/{id}/delete', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

#[Route('/create-checkout-session', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, PaymentService $stripeService): \Symfony\Component\HttpFoundation\JsonResponse
    {
        // Récupérer les informations nécessaires depuis la requête
        $montant = 1000; // Montant en centimes (10 EUR)
        $currency = 'eur';
        // Autres informations nécessaires pour la session de paiement, telles que les articles, la description, etc.

        try {
            // Créer une session de paiement avec Stripe Checkout
            $sessionId = $stripeService->createCheckoutSession($montant, $currency); // Utilisez votre méthode dans le service StripeService pour créer la session

            return $this->json(['id' => $sessionId]);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
    #[Route('/search', name: 'app_commande_search', methods: ['POST'])]
    public function search(Request $request, CommandeRepository $commandeRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $query = $data['query'];

        // Exécutez une requête dans votre repository pour récupérer les commandes filtrées par adresse de livraison
        $commandesFiltrees = $commandeRepository->searchByAddress($query);

        // Retournez les résultats de la recherche en tant que réponse JSON
        return $this->json($commandesFiltrees);
    }
}
