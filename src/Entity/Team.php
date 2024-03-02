<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
   // DEPRECED FOR FIXTURES LOADING #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(targetEntity: TeamInvite::class, mappedBy: 'team')]
    private Collection $teamInvites;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'team')]
    private Collection $users;

    public function __construct()
    {
        $this->teamInvites = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->created_at === NULL) {
            $this->created_at = new DateTimeImmutable();
        }
    }

    /**
     * @return Collection<int, TeamInvite>
     */
    public function getTeamInvites(): Collection
    {
        return $this->teamInvites;
    }

    public function addTeamInvite(TeamInvite $teamInvite): static
    {
        if (!$this->teamInvites->contains($teamInvite)) {
            $this->teamInvites->add($teamInvite);
            $teamInvite->setTeam($this);
        }

        return $this;
    }

    public function removeTeamInvite(TeamInvite $teamInvite): static
    {
        if ($this->teamInvites->removeElement($teamInvite)) {
            // set the owning side to null (unless already changed)
            if ($teamInvite->getTeam() === $this) {
                $teamInvite->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

        return $this;
    }
}
