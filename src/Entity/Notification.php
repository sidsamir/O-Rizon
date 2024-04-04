<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: Friendship::class)]
    private ?Friendship $friendship = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $message = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sentNotifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function getFriendship(): ?Friendship
    {
        return $this->friendship;
    }

    public function setFriendship(?Friendship $friendship): self
    {
        $this->friendship = $friendship;
        return $this;
    }
    // Setters
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function setRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
}
