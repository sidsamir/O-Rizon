<?php

namespace App\Service;

use App\Entity\Friendship;
use App\Entity\Notification;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FriendService
{
    private EntityManagerInterface $entityManager;
    private HubInterface $hub;
    private FriendshipRepository $friendshipRepository;
    private Security $security;
    private TranslatorInterface $translator;
    private NotificationRepository $notificationRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        HubInterface $hub,
        FriendshipRepository $friendshipRepository,
        Security $security,
        TranslatorInterface $translator,
        NotificationRepository $notificationRepository
    ) {
        $this->entityManager = $entityManager;
        $this->hub = $hub;
        $this->friendshipRepository = $friendshipRepository;
        $this->security = $security;
        $this->translator = $translator;
        $this->notificationRepository = $notificationRepository;
    }

    public function addFriend(User $friend): array
    {
        /** @var UserInterface $currentUser */
        $currentUser = $this->security->getUser();

        if ($currentUser === $friend) {
            return ['success' => false, 'message' => $this->translator->trans('Vous ne pouvez pas vous ajouter en tant qu\'ami.')];
        }

        $existingFriendship = $this->friendshipRepository->findOneBy([
            'requester' => $currentUser,
            'receiver' => $friend,
        ]);

        if ($existingFriendship) {
            return ['success' => false, 'message' => $this->translator->trans('Une demande d\'amitié a déjà été envoyée.')];
        }

        $friendship = new Friendship();
        $friendship->setRequester($currentUser);
        $friendship->setReceiver($friend);
        $friendship->setStatus(Friendship::STATUS_PENDING);

        $this->entityManager->persist($friendship);
        $this->entityManager->flush();

        $this->sendNotification($friend, 'new_friend_request', [
            'message' => $this->translator->trans('Vous avez reçu une demande d\'ami de %username%.', ['%username%' => $currentUser->getUsername()]),
            'username' => $currentUser->getUsername(),
            'userId' => $currentUser->getId(),
        ]);

        return ['success' => true, 'message' => $this->translator->trans('Demande d\'ami envoyée avec succès.')];
    }

    private function sendNotification(User $recipient, string $type, array $data): void
    {
        $currentUser = $this->security->getUser();
        $notification = new Notification();
        $notification->setRecipient($recipient);
        $notification->setMessage($data['message']);
        $notification->setSender($currentUser);
        $notification->setCreatedAt(new \DateTime());
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $update = new Update(
            "https://mondomaine.com/user/{$recipient->getId()}/notifications",
            json_encode(['type' => $type, 'data' => $data])
        );
        $this->hub->publish($update);
    }

    public function acceptFriendRequest(int $friendshipId): JsonResponse
    {
        $currentUser = $this->security->getUser();
        $friendship = $this->entityManager->getRepository(Friendship::class)->find($friendshipId);

        if (!$friendship) {
            return new JsonResponse(['error' => 'Demande d\'amitié introuvable.'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($friendship->getReceiver() !== $currentUser) {
            return new JsonResponse(['error' => 'Action non autorisée.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Accepter la demande d'amitié
        $friendship->setStatus(Friendship::STATUS_ACCEPTED);
        $this->entityManager->flush();

        // Supprimer la notification associée à la demande d'amitié acceptée
        $recipient = $friendship->getReceiver() === $currentUser ? $friendship->getRequester() : $friendship->getReceiver();
        $notification = $this->notificationRepository->findOneBy(['recipient' => $recipient]);
        if ($notification) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        }

        // Envoi de la notification d'acceptation
        $this->sendNotification(
            $friendship->getRequester(),
            'friendship_accepted',
            ['message' => $this->translator->trans('%username% a accepté votre demande d\'amitié.', ['%username%' => $currentUser->getUsername()])]
        );

        return new JsonResponse(['message' => 'La demande d\'amitié a été acceptée avec succès.']);
    }

    public function refuseFriendRequest(int $friendshipId): JsonResponse
    {
        $currentUser = $this->security->getUser();
        $friendship = $this->entityManager->getRepository(Friendship::class)->find($friendshipId);

        if (!$friendship) {
            return new JsonResponse(['error' => 'Demande d\'amitié introuvable.'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($friendship->getReceiver() !== $currentUser) {
            return new JsonResponse(['error' => 'Action non autorisée.'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Refuser la demande d'amitié
        $friendship->setStatus(Friendship::STATUS_REFUSED);
        $this->entityManager->flush();

        // Supprimer la notification associée à la demande d'amitié refusée
        $notification = $this->notificationRepository->findOneBy(['friendship' => $friendship]);
        if ($notification) {
            $this->entityManager->remove($notification);
            $this->entityManager->flush();
        }

        // Envoi de la notification de refus
        $this->sendNotification(
            $friendship->getRequester(),
            'friendship_refused',
            ['message' => $this->translator->trans('%username% a refusé votre demande d\'amitié.', ['%username%' => $currentUser->getUsername()])]
        );

        return new JsonResponse(['message' => 'La demande d\'amitié a été refusée.']);
    }

}
