<?php

namespace App\Controller\Front;

use App\Entity\Checklist;
use App\Entity\Trip;
use App\Form\TripType;
use App\Repository\ChecklistRepository;
use App\Repository\FriendshipRepository;
use App\Repository\PostRepository;
use App\Repository\ReviewRepository;
use App\Repository\TripRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trip')]
class TripController extends AbstractController
{
    #[Route('/', name: 'app_trip_index', methods: ['GET'])]
    public function index(TripRepository $tripRepository): Response
    {
        return $this->render('front/trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trip_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FriendshipRepository $repository): Response
    {
        $user = $this->getUser(); // Récupérer l'utilisateur connecté
        $trip = new Trip();

        $user = $this->getUser();

        $trip->setCreator($user);

        $form = $this->createForm(TripType::class, $trip);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trip->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->redirectToRoute('app_main_mytrip', [], Response::HTTP_SEE_OTHER);
        }

        // Utilisez la méthode findFriendsOfUser pour récupérer la liste d'amis
        $friends = $repository->findFriendsOfUser($user);

        return $this->render('front/trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
            'friends' => $friends,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_trip_show', methods: ['GET'])]
    public function show(Trip $trip): Response
    {
        return $this->render('front/trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_trip_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TripType::class, $trip);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Rediriger vers la page de détails du voyage
            return $this->redirectToRoute('app_trip_board', ['id' => $trip->getId()]);
        }

        return $this->render('front/trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_trip_delete', methods: ['POST'])]
    public function delete(Request $request, Trip $trip, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_main_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/board/{id<\d+>}', name: 'app_trip_board', methods: ['GET'])]
    public function board(Trip $trip, TripRepository $tripRepository, PostRepository $postRepository, ChecklistRepository $checklistRepository, ReviewRepository $reviewRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à ce voyage.');
        }

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        // Récupérer les posts associés à ce voyage
        $posts = $postRepository->findBy(['trip' => $trip]);

        // Récupérer la checklist associée à ce voyage
        $checklists = $checklistRepository->findBy(['trip' => $trip]);

        // Récupérer les reviews associés à ce voyage
        $reviews = $reviewRepository->findBy(['trip' => $trip]);

        return $this->render('front/trip/board.html.twig', [
            'trip' => $trip,
            'posts' => $posts,
            'checklists' => $checklists,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/board/{id<\d+>}/posts', name: 'app_trip_posts', methods: ['GET'])]
    public function tripPosts(Trip $trip, TripRepository $tripRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        // Récupérer les posts associés à ce voyage
        $posts = $trip->getPosts();

        return $this->render('front/trip/post.html.twig', [
            'trip' => $trip,
            'posts' => $posts,
        ]);
    }

    #[Route('/board/{id<\d+>}/checklists', name: 'app_trip_checklists', methods: ['GET'])]
    public function tripChecklist(Trip $trip, TripRepository $tripRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        // Récupérer la checklist associée à ce voyage
        $checklists = $trip->getChecklists();

        return $this->render('front/trip/checklist.html.twig', [
            'trip' => $trip,
            'checklists' => $checklists,
        ]);
    }

    #[Route('/board/{id<\d+>}/participant', name: 'app_trip_participant', methods: ['GET'])]
    public function myFriend(Trip $trip, TripRepository $tripRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        return $this->render('front/trip/participant.html.twig', [
            'trip' => $trip,
        ]);
    }

    #[Route('/board/{id<\d+>}/review', name: 'app_trip_review', methods: ['GET'])]
    public function tripReviews(Trip $trip, TripRepository $tripRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur connecté est le créateur du voyage
        if ($this->getUser() !== $trip->getCreator()) {
            // Vérifier si l'utilisateur connecté est un participant du voyage
            if (!$tripRepository->isUserParticipant($this->getUser(), $trip)) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce voyage.');
            }
        }

        // Récupérer les reviews associées à ce voyage
        $reviews = $trip->getReviews();

        return $this->render('front/trip/review.html.twig', [
            'trip' => $trip,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/jointrip', name: 'app_main_jointrip')]
    public function joinTrip(TripRepository $tripRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer les voyages auxquels l'utilisateur est invité
        $invitedTrips = $tripRepository->findInvitedTrips($user);

        return $this->render('front/trip/jointrip.html.twig', [
            'invitedTrips' => $invitedTrips,
        ]);
    }

    #[Route('/my-trip', name: 'app_main_mytrip')]
    public function trip(UserRepository $user, TripRepository $trip): Response
    {
        $users = $user->findOneBy(['id' => $this->getUser()]);
        $trips = $trip->findAll();

        return $this->render('front/trip/mytrip.html.twig', [
            'users' => $users,
            'trips' => $trips,
        ]);
    }

    #[Route('/post/{postId<\d+>}/vote', name: 'post_vote', methods: ['POST'])]
    public function vote(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository, int $postId): JsonResponse
    {
        $post = $postRepository->find($postId);
        if (!$post) {
            return $this->json(['error' => 'Post not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $voteType = $data['voteType'];
        if ('up' === $voteType) {
            $post->setVote($post->getVote() + 1);
        } elseif ('down' === $voteType) {
            $post->setVote($post->getVote() - 1);
        }

        $entityManager->flush();

        return $this->json(['vote' => $post->getVote()]);
    }

    #[Route('/checklist/{checklistId<\d+>}/toggle-state', name: 'checklist_toggle_state', methods: ['POST'])]
    public function toggleChecklistState(Request $request, EntityManagerInterface $entityManager, ChecklistRepository $checklistRepository, int $checklistId): JsonResponse
    {
        $checklist = $checklistRepository->find($checklistId);
        if (!$checklist) {
            return $this->json(['error' => 'Checklist not found'], 404);
        }

        // Basculer l'état de la checklist
        $checklist->setState(!$checklist->isState());
        $entityManager->flush();

        return new JsonResponse(['state' => $checklist->isState()]);
    }
}
