<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\BioType;
use Symfony\Component\HttpFoundation\Request;


// Controller qui gère les utilisateurs et membre du site

#[\AllowDynamicProperties] class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/membres', name: 'app_user_index')]
    public function index(): Response
    {
        $user = $this->getUser(); // Assurez-vous d'avoir l'utilisateur actuel
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page');
        }

        $randomAvatarUrl = 'https://i.pravatar.cc/150?img=' . rand(1, 70);

        $users = $this->repository->findAll();

        return $this->render('front/user/index.html.twig', [
            'users' => $users,
            'randomAvatarUrl' => $randomAvatarUrl,
        ]);
    }

    #[Route('/profil/{id}', name: 'app_user_show')]
    public function show($id, FriendshipRepository $friendshipRepository, EntityManagerInterface $entityManager): Response
    {
        $id = (int) $id;
        $randomAvatarUrl = 'https://i.pravatar.cc/150?img=' . rand(1, 70);

        $user = $entityManager->getRepository(User::class)->find($id);

        $currentUser = $this->getUser();
        $existingFriendship = $friendshipRepository->findOneBy([
            'requester' => $currentUser,
            'receiver' => $user,
        ]);

        $friendship = $friendshipRepository->findOneBy([
            'receiver' => $currentUser,
            'requester' => $user,
        ]) ?? $friendshipRepository->findOneBy([
            'receiver' => $user,
            'requester' => $currentUser,
        ]);

        return $this->render('front/user/show.html.twig', [
            'user' => $user,
            'friendship' => $friendship,
            'existingFriendship' => $existingFriendship,
            'randomAvatarUrl' => $randomAvatarUrl,
        ]);
    }


    #[Route('/profil/{id}/biography', name: 'app_biographie')]
    public function BioType(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Récupère l'utilisateur actuellement connecté
        $user = $this->getUser();
    
        // Crée le formulaire en utilisant la classe BioType et l'utilisateur actuel
        $form = $this->createForm(BioType::class, $user, );
    
        // Gère la soumission du formulaire
        $form->handleRequest($request);
    
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Ajoutez une redirection ou un message de succès ici si nécessaire
            $this->addFlash('success', 'Biographie mise à jour avec succès.');
            return $this->redirectToRoute('app_profile'); 
        }
    
        // Rend la vue 'biography.html.twig' en passant le formulaire à la vue
        return $this->render('front/user/biography.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
}