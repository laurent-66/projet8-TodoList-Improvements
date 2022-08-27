<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils  = $authenticationUtils ; 
    }

    #[Route('/login', name: 'login')]
    public function loginAction(): Response
    {

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error
        ]);
    }

    #[Route("/login_check", name: "login_check")]
    public function loginCheck()
    {
        // This code is never executed.
    }

    #[Route("/logout", name:"logout")]
    public function logoutCheck()
    {
        // This code is never executed.
        
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
