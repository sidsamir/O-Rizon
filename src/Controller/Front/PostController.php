<?php

namespace App\Controller\Front;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Contrôleur gérant les opérations liées aux articles (posts) dans la partie front-end.
#[Route('/post')]
class PostController extends AbstractController
{
    // Affiche la liste de tous les articles.
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('front/post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    // Crée un nouvel article en fonction du voyage depuis lequel la route est demandée.
    #[Route('/new/{id<\d+>}', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $id, TripRepository $tripRepository): Response
    {
        // Récupérer le voyage en fonction de son ID
        $trip = $tripRepository->find($id);

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        // Création d'une nouvelle instance d'article et d'un formulaire associé.
        $post = new Post();
        $post->setTrip($trip); // Remplir automatiquement le champ voyage avec le voyage récupéré

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter la date et l'heure de création automatiquement
            $post->setCreatedAt(new \DateTimeImmutable());
            // Persiste l'article en base de données.
            $entityManager->persist($post);
            $entityManager->flush();

            // Redirige vers la liste des articles après la création réussie.
            return $this->redirectToRoute('app_trip_posts', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire de création d'article.
        return $this->render('front/post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    // Affiche les détails d'un article spécifique.
    #[Route('/{id<\d+>}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('front/post/show.html.twig', [
            'post' => $post,
        ]);
    }

    // Modifie un article existant.
    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, int $id, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire pré-rempli avec les données de l'article existant.
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour l'article modifié en base de données.
            $entityManager->flush();

            // Redirige vers la liste des articles après l'édition réussie.
            return $this->redirectToRoute('app_trip_posts', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire d'édition d'article.
        return $this->render('front/post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    // Supprime un article.
    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, int $id, EntityManagerInterface $entityManager): Response
    {
        // Vérifie la validité du jeton CSRF avant de supprimer l'article.
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            // Supprime l'article de la base de données.
            $entityManager->remove($post);
            $entityManager->flush();
        }

        // Redirige vers la liste des articles après la suppression réussie.
        return $this->redirectToRoute('app_trip_posts', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
