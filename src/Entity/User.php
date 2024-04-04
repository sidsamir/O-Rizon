<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[\AllowDynamicProperties] #[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: "l'email est déjà utilisé")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\OneToMany(mappedBy: 'creatorTrip', targetEntity: Trip::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $creatorTrip;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Post::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Review::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Checklist::class)]
    private Collection $checklists;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Rating::class)]
    private Collection $ratings;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'requester', targetEntity: Friendship::class)]
    private Collection $friendships;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Friendship::class)]
    private Collection $receiverFriendships;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Notification::class, )]
    private Collection $senderNotifications;

    // #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'votes')]
    // private Collection $postsVoted;

    public function __construct()
    {
        $this->creatorTrip = new ArrayCollection();
        $this->participatingTrips = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->checklists = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->receiverFriendships = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->conversationsParticipants = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $this->senderNotifications = new ArrayCollection();
        // $this->postsVoted = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER'; // Assurez-vous que ROLE_USER est toujours attribué
        }
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getCreatorTrip(): Collection
    {
        return $this->creatorTrip;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->creatorTrip->contains($trip)) {
            $this->creatorTrip->add($trip);
            $trip->setCreator($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->creatorTrip->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getCreator() === $this) {
                $trip->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getParticipatingTrips(): Collection
    {
        return $this->participatingTrips;
    }

    public function addParticipatingTrip(Trip $trip): static
    {
        if (!$this->participatingTrips->contains($trip)) {
            $this->participatingTrips->add($trip);
        }

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setParticipant($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getParticipant() === $this) {
                $post->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setParticipant($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getParticipant() === $this) {
                $review->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Checklist>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): static
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
            $checklist->setParticipant($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): static
    {
        if ($this->checklists->removeElement($checklist)) {
            // set the owning side to null (unless already changed)
            if ($checklist->getParticipant() === $this) {
                $checklist->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setParticipant($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getParticipant() === $this) {
                $rating->setParticipant(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship): static
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships->add($friendship);
            $friendship->setRequester($this);
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship): static
    {
        if ($this->friendships->removeElement($friendship)) {
            // set the owning side to null (unless already changed)
            if ($friendship->getRequester() === $this) {
                $friendship->setRequester(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendship>
     */
    public function getReceiverFriendships(): Collection
    {
        return $this->receiverFriendships;
    }

    public function addReceiverFriendship(Friendship $receiverFriendship): static
    {
        if (!$this->receiverFriendships->contains($receiverFriendship)) {
            $this->receiverFriendships->add($receiverFriendship);
            $receiverFriendship->setReceiver($this);
        }

        return $this;
    }

    public function removeReceiverFriendship(Friendship $receiverFriendship): static
    {
        if ($this->receiverFriendships->removeElement($receiverFriendship)) {
            // set the owning side to null (unless already changed)
            if ($receiverFriendship->getReceiver() === $this) {
                $receiverFriendship->setReceiver(null);
            }
        }

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

    /**
     * @return Collection<int, Notification>
     */
    public function getSenderNotifications(): Collection
    {
        return $this->senderNotifications;
    }

    public function addSenderNotification(Notification $senderNotification): static
    {
        if (!$this->senderNotifications->contains($senderNotification)) {
            $this->senderNotifications->add($senderNotification);
            $senderNotification->setSender($this);
        }

        return $this;
    }

    public function removeSenderNotification(Notification $senderNotification): static
    {
        if ($this->senderNotifications->removeElement($senderNotification)) {
            // set the owning side to null (unless already changed)
            if ($senderNotification->getSender() === $this) {
                $senderNotification->setSender(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Post>
    //  */
    // public function getPostsVoted(): Collection
    // {
    //     return $this->postsVoted;
    // }

    // public function addPostsVoted(Post $postsVoted): static
    // {
    //     if (!$this->postsVoted->contains($postsVoted)) {
    //         $this->postsVoted->add($postsVoted);
    //         $postsVoted->addVote($this);
    //     }

    //     return $this;
    // }

    // public function removePostsVoted(Post $postsVoted): static
    // {
    //     if ($this->postsVoted->removeElement($postsVoted)) {
    //         $postsVoted->removeVote($this);
    //     }

    //     return $this;
    // }
}
