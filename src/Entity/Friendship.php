<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FriendshipRepository::class)]
class Friendship
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REFUSED = 'refused';
    public const STATUS_BLOCKED = 'blocked';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'friendships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $requester = null;

    #[ORM\ManyToOne(inversedBy: 'receiverFriendships')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $friendAt = null;

    #[ORM\ManyToMany(targetEntity: Trip::class, mappedBy: 'participant')]
    private Collection $participantTrip;

    #[ORM\Column(nullable: true)]
    private ?bool $accepted = null;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Notification::class)]
    private Collection $recipientNotifications;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->createdAt = new \DateTimeImmutable();
        $this->participantTrip = new ArrayCollection();
        $this->recipientNotifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRequester(): ?User
    {
        return $this->requester;
    }

    public function setRequester(?User $requester): static
    {
        $this->requester = $requester;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getFriendAt(): ?\DateTimeInterface
    {
        return $this->friendAt;
    }

    public function setFriendAt(?\DateTimeInterface $friendAt): static
    {
        $this->friendAt = $friendAt;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getParticipantTrip(): Collection
    {
        return $this->participantTrip;
    }

    public function addParticipantTrip(Trip $participantTrip): static
    {
        if (!$this->participantTrip->contains($participantTrip)) {
            $this->participantTrip->add($participantTrip);
            $participantTrip->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipantTrip(Trip $participantTrip): static
    {
        if ($this->participantTrip->removeElement($participantTrip)) {
            $participantTrip->removeParticipant($this);
        }

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;
        if (true === $accepted) {
            $this->status = self::STATUS_ACCEPTED;
        } elseif (false === $accepted) {
            $this->status = self::STATUS_REFUSED;
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getRecipientNotifications(): Collection
    {
        return $this->recipientNotifications;
    }

    public function addRecipientNotification(Notification $recipientNotification): static
    {
        if (!$this->recipientNotifications->contains($recipientNotification)) {
            $this->recipientNotifications->add($recipientNotification);
            $recipientNotification->setRecipient($this);
        }

        return $this;
    }

    public function removeRecipientNotification(Notification $recipientNotification): static
    {
        if ($this->recipientNotifications->removeElement($recipientNotification)) {
            // set the owning side to null (unless already changed)
            if ($recipientNotification->getRecipient() === $this) {
                $recipientNotification->setRecipient(null);
            }
        }

        return $this;
    }
}
