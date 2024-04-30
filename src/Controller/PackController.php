<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Form\PackType;
use App\Repository\PackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nexmo\Client;
use Nexmo\Client\Credentials\Basic;
use TCPDF;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Import de la classe SessionInterface






#[Route('/pack')]
class PackController extends AbstractController
{
    #[Route('/', name: 'app_pack_index', methods: ['GET'])]
    public function index(PackRepository $packRepository): Response
    {
        $packs = $packRepository->findAll();
        $totalPrice = array_reduce($packs, function ($total, $pack) {
            return $total + $pack->getPrix();
        }, 0);
        return $this->render('pack/index.html.twig', [
            'packs' => $packRepository->findAll(),
            'totalPrice' => $totalPrice,

        ]);
    }

    #[Route('/new', name: 'app_pack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pack = new Pack();
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pack);
            $entityManager->flush();

            return $this->redirectToRoute('app_pack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pack/new.html.twig', [
            'pack' => $pack,
            'form' => $form,
        ]);    
    }
   

    #[Route('/{idPack}', name: 'app_pack_show', methods: ['GET'])]
public function show(int $idPack, PackRepository $packRepository): Response
{
    $pack = $packRepository->find($idPack);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }
    

    return $this->render('pack/show.html.twig', [
        'pack' => $pack,
    ]);
}
        
#[Route('/{idPack}/edit', name: 'app_pack_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, int $idPack, PackRepository $packRepository, EntityManagerInterface $entityManager): Response
{
    $pack = $packRepository->find($idPack);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }

    $form = $this->createForm(PackType::class, $pack);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_pack_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('pack/edit.html.twig', [
        'pack' => $pack,
        'form' => $form,
    ]);
}


#[Route('/{idPack}', name: 'app_pack_delete', methods: ['POST'])]
public function delete(Request $request, int $idPack, PackRepository $packRepository, EntityManagerInterface $entityManager): Response
{
    $pack = $packRepository->find($idPack);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }

    if ($this->isCsrfTokenValid('delete'.$pack->getIdPack(), $request->request->get('_token'))) {
        $entityManager->remove($pack);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_pack_index', [], Response::HTTP_SEE_OTHER);
}
#[Route('/export/pdf', name: 'app_pack_export_pdf', methods: ['GET'])]
public function exportPdf(PackRepository $packRepository): Response
{
    // Récupérer tous les packs
    $packs = $packRepository->findAll();

    // Générer le contenu du PDF
    $html = '<h1>Liste des packs</h1><ul>';
    foreach ($packs as $pack) {
        $html .= '<li>' . $pack->getIdpack() . ': ' . $pack->getTypePack() . '</li>';
    }
    $html .= '</ul>';

    // Générer le PDF
    $pdf = new \TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($html);

    // Obtenez le contenu du PDF
    $pdfContent = $pdf->Output('packs.pdf', 'S');

    // Créez une réponse avec le contenu du PDF
    $response = new Response($pdfContent);

    // Définir les en-têtes pour indiquer que c'est un fichier PDF à télécharger
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="packs.pdf"');

    return $response;
}
#[Route('/generate-qr-code/{id}', name: 'generate_qr_code')]
public function generateQrCode(PackRepository $repository, $id): Response
{
    $pack = $repository->find(29);

    // Create the QR code content
    $qrContent = sprintf(
        "Pack ID: %s\nType: %s\nPrice: %s\nDescription: %s",
        $pack->getIdpack(),
        $pack->getTypePack(),
        $pack->getPrix(),
        $pack->getAvantage()
    );

    // Generate the QR code image
    $qrCode = $this->generateQrCodeImage($qrContent);

    // Generate a response with the QR code image content
    $response = new Response($qrCode, Response::HTTP_OK, [
        'Content-Type' => 'image/png',
        'Content-Disposition' => 'inline; filename="qr_code.png"'
    ]);

    return $response;
}

private function generateQrCodeImage($qrContent)
{
    // URL for QR code API
    $baseUrl = 'https://api.qrserver.com/v1/create-qr-code/';

    // Parameters for QR code API
    $params = [
        'size' => '300x300',  // Image size
        'data' => urlencode($qrContent),  // QR code content
    ];

    // Construct the URL
    $url = $baseUrl . '?' . http_build_query($params);

    // Fetch the QR code image content
    return file_get_contents($url);
}

#[Route('/', name: 'app_pack_index', methods: ['GET'])]
public function offre(PackRepository $packRepository): Response
{
    $packs = $packRepository->findAll();
    $totalPrice = array_reduce($packs, function ($total, $pack) {
        return $total + $pack->getPrix();
    }, 0);

    // Exemple de données de promotion basées sur des packs musicaux
    $promotions = [
        ['title' => 'Pack Musique Classique', 'description' => 'Profitez d\'une réduction de 20% sur notre pack exclusif de musique classique.'],
        ['title' => 'Pack Rock Anthems', 'description' => 'Offre spéciale sur notre collection de hits rock, avec des prix réduits pour une durée limitée.'],
        ['title' => 'Pack Jazz Essentials', 'description' => 'Économisez sur notre pack de jazz incontournable, idéal pour les amateurs de jazz de tous les niveaux.'],
    ];

    return $this->render('pack/index.html.twig', [
        'packs' => $packRepository->findAll(),
        'totalPrice' => $totalPrice,
        'promotions' => $promotions, // Passer les données de promotion au template
    ]);
}
#[Route('/pack/{idPack}/comment', name: 'app_pack_comment', methods: ['POST'])]
public function comment(int $idPack, Request $request, SessionInterface $session): Response
{
    // Récupérer les données du formulaire de commentaire
    $comment = $request->request->get('comment');

    // Valider et traiter le commentaire (vous pouvez ajouter plus de validation ici)

    // Récupérer les commentaires existants de la session
    $comments = $session->get('pack_comments', []);

    // Ajouter le nouveau commentaire à la liste
    $comments[] = $comment;

    // Mettre à jour les commentaires dans la session
    $session->set('pack_comments', $comments);

    // Rediriger vers la page du pack ou afficher un message de succès
    return $this->redirectToRoute('app_pack_show', ['idPack' => $idPack]);
}
#[Route('/chatbot/answer', name: 'app_chatbot_answer', methods: ['POST'])]
public function answer(Request $request): Response
{
    // Récupérer la question posée par le chatbot
    $question = $request->request->get('question');

    // Votre logique pour répondre à la question du chatbot
    $answer = $this->getChatbotAnswer($question);

    // Retourner la réponse au chatbot
    return new JsonResponse(['answer' => $answer]);
}

private function getChatbotAnswer($question)
{
    // Votre logique pour générer une réponse basée sur la question
    // Par exemple, vous pouvez utiliser une logique conditionnelle pour répondre à différentes questions
    
    // Pour cet exemple, nous renvoyons une réponse statique
    return 'Je suis un chatbot, comment puis-je vous aider ?';
}

}



