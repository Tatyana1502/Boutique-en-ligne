<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session, int $maxIdleTime=30): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // Si l'utilisateur est authentifié avec succès
        if ($this->getUser())
         {
            // Récupérer le rôle de l'utilisateur
            $role = $this->getUser()->getRoles()[0]; // Supposons que vous récupérez le premier rôle
            $session->start();
            if (time() - $session->getMetadataBag()->getLastUsed() > $maxIdleTime)
            {
                $session->invalidate();
                throw new SessionExpired(); // redirect to expired session page
            }
        }
        return $this->render('/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    
    #[Route('/logout', name: 'app_logout')]
    public function logout(TokenStorageInterface $tokenStorage): RedirectResponse
    {
        // Удаляем токен аутентификации из хранилища, что приведет к выходу из системы
        $tokenStorage->setToken(null);

        // Перенаправляем пользователя на страницу входа или другую страницу
        return $this->redirectToRoute('app_login');
    }

}
