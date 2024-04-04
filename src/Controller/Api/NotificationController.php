<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    #[Route('/api/notifications/count', name: 'api_notifications_count')]
    public function unreadCount(NotificationRepository $notificationRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $count = $notificationRepository->countUnreadNotifications($user);

        return $this->json(['unreadCount' => $count]);
    }

    #[Route('/api/notifications', name: 'api_notifications')]
    public function notifications(NotificationRepository $notificationRepository): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = $notificationRepository->findBy(['recipient' => $user], ['createdAt' => 'DESC']);

        $data = array_map(function ($notification) {
            return [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'userId' => $notification->getSender()?->getId(),
                'username' => $notification->getSender()?->getUsername(),
                'createdAt' => $notification->getCreatedAt()->format('c'),
                'isRead' => $notification->isRead(),
            ];
        }, $notifications);

        return $this->json($data);
    }
}
