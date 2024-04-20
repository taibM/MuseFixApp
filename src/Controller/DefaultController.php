<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/home', name: 'app_default')]
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
}
