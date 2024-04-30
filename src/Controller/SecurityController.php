<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    private LoggerInterface $logger;


    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(LoggerInterface $logger,UserPasswordHasherInterface $passwordHasher)
    {
        $this->logger = $logger;

        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new Exception('This method should not be called directly.');
    }

    public function checkLogin(Request $request, UserRepository $userRepository): Response
    {
        // Get the username and password from the request
        $username = $request->request->get('_email');
        $plaintextPassword = $request->request->get('_password');

        // Retrieve the user from the database based on the username
        $user = $userRepository->findOneBy(['email' => $username]);
        if ($user) {
            // Get the hashed password from the user entity
            $storedHashedPassword = $user->getPassword();

            // Log the plaintext password, hashed password, and result of password validation for debugging purposes
            $this->logger->debug('Plaintext Password: ' . $plaintextPassword);
            $this->logger->debug('Stored Hashed Password: ' . $storedHashedPassword);
            $this->logger->debug('Is Password Valid: ' . ($this->passwordHasher->isPasswordValid($user, $plaintextPassword) ? 'true' : 'false'));

            // Verify the plaintext password against the stored hashed password
            if ($this->passwordHasher->isPasswordValid($user, $plaintextPassword)) {
                // Password is correct
                // Check the role of the user and redirect accordingly
                $role = $user->getRole();
                if ($role === 0) {
                    return $this->redirectToRoute('app_admin_user_index');
                } elseif ($role === 1) {
                    return $this->redirectToRoute('app_user_index');
                }
            } else {
                // Passwords do not match
                $this->logger->debug('Invalid password.');
            }
        } else {
            $this->logger->debug('User not found in the database.');
        }

        // render the login page with error message
        $error = 'Invalid username or password.';
        return $this->render('login.html.twig', ['last_username' => $username, 'error' => $error]);
    }

}
