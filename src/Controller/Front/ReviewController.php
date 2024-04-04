<?php

namespace App\Controller\Front;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/review')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_front_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('front/review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    #[Route('/new/{id<\d+>}', name: 'app_front_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $id, TripRepository $tripRepository): Response
    {
        // Récupérer le voyage en fonction de son ID
        $trip = $tripRepository->find($id);

        // Vérifier si le voyage existe
        if (!$trip) {
            throw $this->createNotFoundException('Le voyage avec l\'ID '.$id.' n\'existe pas.');
        }

        // Créer une nouvelle instance de review
        $review = new Review();

        // Définir le trip et le participant pour la review
        $review->setTrip($trip);
        $review->setParticipant($this->getUser());

        // Date automatique
        $review->setCreatedAt(new \DateTimeImmutable());

        // Créer le formulaire de review
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        // Traiter le formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // Persister la review en base de données
            $entityManager->persist($review);
            $entityManager->flush();

            // Rediriger vers la liste des reviews après la création réussie
            return $this->redirectToRoute('app_trip_review', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Afficher le formulaire de création de review
        return $this->render('front/review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    // Affiche les détails d'une seule review .
    #[Route('/{id<\d+>}', name: 'app_front_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('front/review/show.html.twig', [
            'review' => $review,
        ]);
    }

    // Modification d'une review
    #[Route('/{id}/edit', name: 'app_front_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, int $id, EntityManagerInterface $entityManager): Response
    {
        // Crée un formulaire pré-rempli avec les données de la review
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // Met à jour de la review modifié en base de données.
            $entityManager->flush();

            // Redirige vers la liste des reviews après la modification réussie.
            return $this->redirectToRoute('app_trip_review', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        // Affiche le formulaire d'édition de la review.
        return $this->render('front/review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_front_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, int $id, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trip_review', ['id' => $id], Response::HTTP_SEE_OTHER);
    }
}
