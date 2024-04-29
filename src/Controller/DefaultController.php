<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;


class DefaultController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    #[Route('/admin', name: 'app_admin')]
    public function admin(): Response
    {
        return $this->render('base2.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    #[Route('/download-pdf', name: 'app_download1_pdf')]
    public function downloadPdf1(CommandeRepository $repository): Response
    {
        // Récupérer toutes les commandes
        $commandes = $repository->findAll();

        // Générer le contenu du PDF
        $html = $this->renderView('commande/pdf.html.twig', ['commandes' => $commandes]);

        // Générer le PDF
        $pdf = new  TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html);

        // Envoyer le PDF en réponse
        $pdfContent = $pdf->Output('', 'S');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="commandes.pdf"');

        return $response;
    }

    #[Route('/export/excel', name: 'app_commande_export_excel1', methods: ['GET'])]
    public function exportExcel1(CommandeRepository $commandeRepository): Response
    {
        // Récupérer toutes les commandes
        $commandes = $commandeRepository->findAll();

        // Créer un nouveau Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes de colonnes
        $sheet->setCellValue('A1', 'idCommande');
        $sheet->setCellValue('B1', 'orderDate');
        $sheet->setCellValue('C1', 'status');
        $sheet->setCellValue('D1', 'modePaiement');
        $sheet->setCellValue('E1', 'adresseLivraison');
        $sheet->setCellValue('F1', 'fraisLivraison');
        $sheet->setCellValue('G1', 'total');
        $sheet->setCellValue('H1', 'userID');

        // Remplir les données
        $row = 2;
        foreach ($commandes as $commande) {
            $sheet->setCellValue('A' . $row, $commande->getId());
            $sheet->setCellValue('B' . $row, $commande->getOrderDate()->format('Y-m-d H:i:s'));
            $sheet->setCellValue('C' . $row, $commande->getStatus());
            $sheet->setCellValue('D' . $row, $commande->getModePaiement());
            $sheet->setCellValue('E' . $row, $commande->getAdresseLivraison());
            $sheet->setCellValue('F' . $row, $commande->getFraisLivraison());
            $sheet->setCellValue('G' . $row, $commande->getTotal());
            $sheet->setCellValue('H' . $row, $commande->getUserid()->getId());

            $row++;
        }

        // Créer un fichier temporaire pour sauvegarder le fichier Excel
        $tempFilePath = tempnam(sys_get_temp_dir(), 'commandes_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        // Créer une réponse avec le fichier temporaire
        $response = new BinaryFileResponse($tempFilePath, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        // Définir le nom de fichier pour le téléchargement
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'commandes.xlsx');

        return $response;
    }
    #[Route('/download-pdf', name: 'app_download_pdf')]
    public function downloadPdf(PanierRepository $repository): Response
    {
        // Récupérer tous les paniers
        $paniers = $repository->findAll();

        // Générer le contenu du PDF
        $html = $this->renderView('panier/pdf.html.twig', ['paniers' => $paniers]);

        // Générer le PDF
        $pdf = new  TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->writeHTML($html);

        // Envoyer le PDF en réponse
        $pdfContent = $pdf->Output('', 'S');
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="paniers.pdf"');

        return $response;
    }

    #[Route('/export/excel', name: 'app_panier_export_excel', methods: ['GET'])]
    public function exportExcel(PanierRepository $panierRepository): Response
    {
        // Récupérer tous les paniers
        $paniers = $panierRepository->findAll();

        // Créer un nouveau Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes de colonnes
        $sheet->setCellValue('A1', 'idPanier');
        $sheet->setCellValue('B1', 'qte');
        $sheet->setCellValue('C1', 'prixUnite');
        $sheet->setCellValue('D1', 'sousTotal');
        $sheet->setCellValue('E1', 'userID');
        $sheet->setCellValue('F1', 'idProduit');

        // Remplir les données
        $row = 2;
        foreach ($paniers as $panier) {
            $sheet->setCellValue('A' . $row, $panier->getId());
            $sheet->setCellValue('B' . $row, $panier->getQte());
            $sheet->setCellValue('C' . $row, $panier->getPrixUnite());
            $sheet->setCellValue('D' . $row, $panier->getSousTotal());
            $sheet->setCellValue('E' . $row, $panier->getUserid()->getId());
            $sheet->setCellValue('F' . $row, $panier->getIdproduit()->getId());

            $row++;
        }

        // Créer un fichier temporaire pour sauvegarder le fichier Excel
        $tempFilePath = tempnam(sys_get_temp_dir(), 'paniers_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFilePath);

        // Créer une réponse avec le fichier temporaire
        $response = new BinaryFileResponse($tempFilePath, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);

        // Définir le nom de fichier pour le téléchargement
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'paniers.xlsx');

        return $response;
    }



}


