<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Service\FriendService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendController extends AbstractController
{
    private FriendService $friendService;

    public function __construct(FriendService $friendService)
    {
        $this->friendService = $friendService;
    }

    #[Route('/add-friend/{id}', name: 'app_user_add_friend', methods: ['POST'])]
    public function addFriend(User $friend): JsonResponse
    {
        $result = $this->friendService->addFriend($friend);

        return new JsonResponse($result);
    }

    #[Route('/accept-friend/{friendshipId<\d+>}', name: 'accept_friend_request', methods: ['POST'])]
    public function acceptFriendRequest(int $friendshipId): JsonResponse
    {
        $response = $this->friendService->acceptFriendRequest($friendshipId);

        return new JsonResponse($response);
    }

    #[Route('/refuse-friend/{friendshipId}', name: 'refuse_friend_request', methods: ['POST'])]
    public function refuseFriendRequest(int $friendshipId): JsonResponse
    {
        $response = $this->friendService->refuseFriendRequest($friendshipId);

        return new JsonResponse($response);
    }

    #[Route('/my-friend', name: 'app_friends_list')]
    public function myFriend(FriendshipRepository $friendshipRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les amis acceptés
        $acceptedFriendships = $friendshipRepository->findAcceptedFriendshipsByUser($user);

        return $this->render('front/user/friendList.html.twig', [
            'acceptedFriendships' => $acceptedFriendships,
        ]);
    }
}
