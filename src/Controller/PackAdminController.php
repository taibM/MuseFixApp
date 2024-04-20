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

#[Route('/admin/pack')]
class PackAdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_pack_index', methods: ['GET'])]
    public function index(PackRepository $packRepository): Response
    {
        $packs = $packRepository->findAll();
      
        return $this->render('admin_pack/index.html.twig', [
            'packs' => $packs,
        ]);
    }

    #[Route('/new', name: 'app_admin_pack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pack = new Pack();
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pack);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_pack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_pack/new.html.twig', [
            'pack' => $pack,
            'form' => $form,
        ]);
    }

    #[Route('/{idPack}', name: 'app_admin_pack_show', methods: ['GET'])]
    public function show(PackRepository $packRepository, int $idPack): Response
    {
        $pack = $packRepository->find($idPack);
    
        if (!$pack) {
            throw $this->createNotFoundException('Pack not found');
        }
    
        return $this->render('admin_pack/show.html.twig', [
            'pack' => $pack,
        ]);
    }

    #[Route('/{idPack}/edit', name: 'app_admin_pack_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, PackRepository $packRepository, int $idPack, EntityManagerInterface $entityManager): Response
{
    $pack = $packRepository->find($idPack);

    if (!$pack) {
        throw $this->createNotFoundException('Pack not found');
    }

    $form = $this->createForm(PackType::class, $pack);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_pack_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('admin_pack/edit.html.twig', [
        'pack' => $pack,
        'form' => $form,
    ]);
}


    #[Route('/{idPack}', name: 'app_admin_pack_delete', methods: ['POST'])]
    public function delete(Request $request, Pack $pack, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pack->getIdPack(), $request->request->get('_token'))) {
            $entityManager->remove($pack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_pack_index', [], Response::HTTP_SEE_OTHER);
    }
}
