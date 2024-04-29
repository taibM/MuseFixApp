<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/panier')]
class PanierController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/', name: 'app_panier_index', methods: ['GET'])]
    public function index(Request $request, PanierRepository $panierRepository): Response
    {
        // Récupérer le numéro de page à partir de la requête
        $page = $request->query->getInt('page', 1);

        // Récupérer les valeurs de filtrage depuis la requête
        $quantiteFiltre = $request->query->getInt('quantiteFiltre');
        $sousTotalFiltre = $request->query->get('sousTotalFiltre', null);

        // Convertir la valeur de sous-total en float (assurez-vous de gérer les cas où la valeur est vide ou invalide)
        $sousTotalFiltre = floatval($sousTotalFiltre);

        // Récupérer les données depuis le repository en fonction des critères de filtrage
        $paniers = $panierRepository->findWithFilters($quantiteFiltre, $sousTotalFiltre);
        // Créer un adaptateur Pagerfanta pour les données récupérées
        $adapter = new ArrayAdapter($paniers);

        // Créer une instance de Pagerfanta
        $pagerfanta = new Pagerfanta($adapter);

        // Définir le nombre d'éléments par page
        $pagerfanta->setMaxPerPage(4); // Par exemple, 10 éléments par page

        // Définir la page actuelle
        $pagerfanta->setCurrentPage($page);

        // Récupérer les résultats paginés
        $paniersPagines = $pagerfanta->getCurrentPageResults();

        // Passer les résultats paginés et l'objet Pagerfanta à votre vue pour l'affichage
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniersPagines,
            'pagerfanta' => $pagerfanta, // Ceci vous permettra de créer des liens de pagination dans votre vue
        ]);
    }

    #[Route('/panier/new', name: 'app_panier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $panier = new Panier();
        $panier->setQte(1); // Valeur par défaut de la quantité

        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($panier);
            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index');
        }

        return $this->render('panier/new.html.twig', [
            'panier' => $panier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prixUnitaire = $panier->getPrixUnite();
            $quantite = $panier->getQte();
            $sousTotal = $prixUnitaire * $quantite;
            $panier->setSousTotal($sousTotal);

            $entityManager->flush();

            return $this->redirectToRoute('app_panier_index');
        }

        return $this->render('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/update-quantity', name: 'app_panier_update_quantity', methods: ['POST'])]
    public function updateQuantity(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['quantite'])) {
            $ancienneQuantite = $panier->getQte();
            $nouvelleQuantite = $data['quantite'];

            $panier->setQte($nouvelleQuantite);

            // Calculer le sous-total en fonction de la nouvelle quantité
            $prixUnitaire = $panier->getPrixUnite();
            $sousTotal = $prixUnitaire * $nouvelleQuantite;

            $panier->setSousTotal($sousTotal);

            $entityManager->flush();

            return new JsonResponse(['message' => 'Quantité et sous-total mis à jour avec succès'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Erreur lors de la mise à jour de la quantité'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'app_panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_panier_index', [], Response::HTTP_SEE_OTHER);
    }

}
