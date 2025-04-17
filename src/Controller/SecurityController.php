<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    //route pour la page login
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        // Rendu du template 'login.html.twig' en passant le dernier nom d'utilisateur et l'erreur (le cas échéant)
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, // Variable pour pré-remplir le champ du nom d'utilisateur
            'error' => $error,
        ]);
    }

    //route pour la déconnexion
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode est interceptée par le mécanisme de sécurité, donc elle ne doit pas contenir de logique
        // Une exception est lancée ici, mais elle ne sera jamais atteinte car la déconnexion est gérée par Symfony
        //Cette exception est présente simplement pour indiquer que cette méthode ne doit pas contenir de logique.        
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
