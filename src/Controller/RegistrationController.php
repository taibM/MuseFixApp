<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class RegistrationController extends AbstractController
{
    private PasswordHasherFactoryInterface $passwordHasherFactory;
    private $passwordHasher;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
        // Initialize the password hasher using the factory
        $this->passwordHasher = $this->passwordHasherFactory->getPasswordHasher(User::class);
    }

    #[Route('/register', name: 'registration')]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        // Initialize error variable
        $error = '';

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password before persisting the user
            $hashedPassword = $this->passwordHasher->hash($user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setSignupdate(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            // Set success message
            $this->addFlash('success', 'Registration successful. Please check your email.');

            // Redirect to login page after successful registration
            return $this->redirectToRoute('app_login');
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            // Set error message if form submission is invalid
            $error = 'Invalid form submission. Please check your inputs.';
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
            'error' => $error, // Pass error variable to the template
        ]);
    }
}
