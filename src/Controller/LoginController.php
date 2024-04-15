<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
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
    public function checkLogin(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // get the username and password from the request
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');

        // check if username and password are correct (for demo purposes)
        if ($username === 'admin' && $password === '1234') {
            // redirect to homepage or any other secure page upon successful login
            return $this->redirectToRoute('app_admin');
        }

        // get the login error if there is one
        $error = 'Invalid username or password.';

        // render the login page with error message
        return $this->render('login.html.twig', ['last_username' => $username, 'error' => $error]);
    }

}
