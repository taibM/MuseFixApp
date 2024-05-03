<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Endroid\QrCode\QrCode;
use Stripe\Stripe;
use Stripe\Checkout\Session; 
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;





#[Route('/abonnement')]
class AbonnementController extends AbstractController
{ 

    #[Route('/', name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
// Récupérer tous les abonnements
$abonnements = $abonnementRepository->findAll();


        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        Stripe::setApiKey('sk_test_51OqiEOGjfoBklovpNyFwgkNUmmPvbzZYjF4hCmYozOTi6YjYDTaC9dC65LPzucDuxNNsbak6i295szOk7zoHAuum00kJVS4yNM');

    if ($request->isMethod('POST')) {
        // Create a new Stripe Checkout Session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Abonnement',
                    ],
                    'unit_amount' => 2000, // Amount in cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_abonnement_index', ['success' => true], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_abonnement_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url);
    }
        $abonnement = new Abonnement();
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);        
        
    }

    
    #[Route('/{idAbonnement}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{idAbonnement}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/{idAbonnement}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getIdAbonnement(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
 
    #[Route('/export/excel', name: 'app_abonnement_export_excel', methods: ['GET'])]
public function exportExcel(AbonnementRepository $abonnementRepository): Response
{
    // Récupérer tous les abonnements
    $abonnements = $abonnementRepository->findAll();

    // Créer un nouveau Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // En-têtes de colonnes
    $sheet->setCellValue('A1', 'IdAbonnement');
    $sheet->setCellValue('B1', 'userID');
    $sheet->setCellValue('C1', 'idPack');
    $sheet->setCellValue('D1', 'datedeb');
    $sheet->setCellValue('E1', 'datefin');

    // Remplir les données
    $row = 2;
    foreach ($abonnements as $abonnement) {
        $sheet->setCellValue('A' . $row, $abonnement->getIdAbonnement());
        $sheet->setCellValue('B' . $row, $abonnement->getUserID());
        $sheet->setCellValue('C' . $row, $abonnement->getIdPack());
        $sheet->setCellValue('D' . $row, $abonnement->getDatedeb()->format('Y-m-d H:i:s'));
        $sheet->setCellValue('E' . $row, $abonnement->getDatefin()->format('Y-m-d H:i:s'));

        $row++;
    }


    // Create a temporary file to save the Excel
    $tempFilePath = tempnam(sys_get_temp_dir(), 'abonnements_') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFilePath);

    // Create a response with the temporary file
    $response = new BinaryFileResponse($tempFilePath, Response::HTTP_OK, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ]);

    // Set the file name for download
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'abonnements.xlsx');

    return $response;
}
#[Route('/export/pdf', name: 'app_abonnement_export_pdf', methods: ['GET'])]
public function exportPdf(AbonnementRepository $abonnementRepository): Response
{
    // Récupérer tous les abonnements
    $abonnements = $abonnementRepository->findAll();

    // Générer le contenu du PDF
    $html = '<table border="1"><tr><th>IdAbonnement</th><th>userID</th><th>idPack</th><th>datedeb</th><th>datefin</th></tr>';
    foreach ($abonnements as $abonnement) {
        $html .= '<tr>';
        $html .= '<td>' . $abonnement->getIdAbonnement() . '</td>';
        $html .= '<td>' . $abonnement->getUserID() . '</td>';
        $html .= '<td>' . $abonnement->getIdPack() . '</td>';
        $html .= '<td>' . $abonnement->getDatedeb()->format('Y-m-d H:i:s') . '</td>';
        $html .= '<td>' . $abonnement->getDatefin()->format('Y-m-d H:i:s') . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';

    // Générer le PDF
    $pdf = new TCPDF();
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->writeHTML($html);
    $pdfContent = $pdf->Output('abonnements.pdf', 'S');

    // Créer une réponse avec le contenu PDF
    $response = new Response($pdfContent, Response::HTTP_OK, [
        'Content-Type' => 'application/pdf',
    ]);

    // Définir le nom de fichier pour le téléchargement
    $response->headers->set('Content-Disposition', 'attachment; filename="abonnements.pdf"');

    return $response;
}
#[Route('/send-verification-email', name: 'app_send_verification_email')]
public function sendVerificationEmail(MailerInterface $mailer): Response
{
    // Créer le contenu de l'e-mail de vérification
    $email = (new Email())
        ->from('khalilsoltani64@gmail.com')
        ->to('khalilsoltani64@gmail.com')
        ->subject('Verification Email')
        ->text('Please verify your email.');

    // Envoyer l'e-mail de vérification
    $mailer->send($email);

    // Répondre avec une confirmation
    return new Response('Verification email sent successfully');
}


#[Route('/abonnement', name: 'app_abonnement_dashboard', methods: ['GET'])]
    public function dashboard(AbonnementRepository $abonnementRepository): Response
    {
        // Récupérer tous les abonnements
        $abonnements = $abonnementRepository->findAll();

        // Collecter les données de performance
        $nombreAbonnements = count($abonnements);
        // Autres données de performance à collecter selon vos besoins

        // Afficher la vue avec les données d'abonnement et de performance
        return $this->render('abonnement/dashboard.html.twig', [
            'abonnements' => $abonnements,
            'nombreAbonnements' => $nombreAbonnements,
            // Autres données de performance à passer à la vue
        ]);
    }
    #[Route('/{idAbonnement}/share', name: 'app_abonnement_share', methods: ['GET'])]
    public function socialMediaShare(Abonnement $abonnement, Facebook $facebook): Response
    {
        // Initialisez l'objet Facebook avec vos clés d'API
        // Vous devez d'abord installer le SDK Facebook via Composer
        $facebook->setDefaultAccessToken('EAATRDaO5IFwBO71rEHURBxuZB2jA3ZArYgh3OJq6G1YYsa9Tqek0EXFaZBJyLhgfknoS9ZB3khbd06PZCO10tc8oVv82quWPdpeHbLC1vG4LZCHVxlOGlzPvZCcR5tI6K6KvglXe0nzGfR08F9rukTjeQhdRym62GjPu9kjGN6t9zoIZCZCmaDClHCNPDD3Wo50CMZAmdVtZCS65DyuIyGNTTnyzRhdDpcHBFERbbiFIzzM3pEpROWjBlxVOON2FAAXJgZDZD');

        // Créez un lien de partage Facebook avec une URL spécifique
        $facebookShareUrl = $facebook->post('/me/feed', ['link' => 'https://127.0.0.1:8000/abonnement//' . $abonnement->getIdAbonnement()]);

        // Rendez la vue avec le bouton de partage Facebook
        return $this->render('abonnement/share.html.twig', [
            'abonnement' => $abonnement,
            'facebookShareUrl' => $facebookShareUrl,
        ]);
    }
   
    

    }
    


