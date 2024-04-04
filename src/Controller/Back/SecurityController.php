<?php

namespace App\Controller\Back;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main_home');
        }

        // Obtenir l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendre la vue du formulaire de connexion avec les données nécessaires
        return $this->render('front/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être vide - elle sera interceptée par la clé de déconnexion sur le pare-feu.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(UserRepository $user): Response
    {
        $randomAvatarUrl = 'https://i.pravatar.cc/150?img=' . rand(1, 70);
        $users = $user->findOneBy(['id' => $this->getUser()]);

        return $this->render('front/security/profile.html.twig', [
            'users' => $users,
            'randomAvatarUrl' => $randomAvatarUrl,
        ]);
    }
}
