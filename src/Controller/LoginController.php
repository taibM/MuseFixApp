<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        throw new \Exception('This method should not be called directly.');
    }

    #[Route('/check_login', name: 'check_login')]
    public function checkLogin(Request $request, UserRepository $userRepository): Response
    {
        // get the username and password from the request
        $username = $request->request->get('_email');
        $password = $request->request->get('_password');

        // Add debug message to check if the method is called
        $this->logger->debug('checkLogin method called.');

        // Add debug message to check the username and password
        $this->logger->debug('Username: ' . $username);
        $this->logger->debug('Password: ' . $password);

        // retrieve the user from the database based on the username
        $user = $userRepository->findOneBy(['email' => $username]);

        // Add debug message to check if user is retrieved from the database
        if ($user) {
            $this->logger->debug('User found in the database: ' . $user->getEmail());
        } else {
            $this->logger->debug('User not found in the database.');
        }

        // check if user exists and password is correct
        if ($user && password_verify($password, $user->getPasswd())) {
            // check the role of the user
            $role = $user->getRole();

            // render the appropriate template based on the user's role
            if ($role === 0) {
                return $this->redirectToRoute('app_admin_user_index');
            } elseif ($role === 1) {
                return $this->redirectToRoute('app_user_index');
            }
        }

        // Add debug message for failed login
        $this->logger->debug('Login failed.');

        // render the login page with error message
        $error = 'Invalid username or password.';
        return $this->render('login.html.twig', ['last_username' => $username, 'error' => $error]);
    }
}
