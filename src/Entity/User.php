<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Bu e-posta adresiyle zaten daha önce kayıt yapılmış')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $display_name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToMany(targetEntity: TeamInvite::class, mappedBy: 'user')]
    private Collection $teamInvites;

    #[ORM\OneToMany(targetEntity: AbstractFile::class, mappedBy: 'uploaded_by')]
    private Collection $abstractFiles;

    #[ORM\Column(nullable: true)]
    private ?bool $dark_mode = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $locale = null;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'to_user')]
    private Collection $notifications;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Team $team = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_passive = null;

    public function __construct()
    {
        $this->teamInvites = new ArrayCollection();
        $this->abstractFiles = new ArrayCollection();
        $this->notifications = new ArrayCollection();
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
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
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

    public function getDisplayName(): ?string
    {
        return $this->display_name;
    }

    public function setDisplayName(?string $display_name): static
    {
        $this->display_name = $display_name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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
    public function setPrePersistValues(): void
    {
        // Set - Created At
        if ($this->created_at === NULL) {
            $this->created_at = new DateTimeImmutable();
        }
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

    public function setDefaults(): static
    {
        // Set Default Roles
        $this->setRoles(["ROLE_USER"]);
        $this->setDarkMode(FALSE);

        return $this;
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
            $teamInvite->setUser($this);
        }

        return $this;
    }

    public function removeTeamInvite(TeamInvite $teamInvite): static
    {
        if ($this->teamInvites->removeElement($teamInvite)) {
            // set the owning side to null (unless already changed)
            if ($teamInvite->getUser() === $this) {
                $teamInvite->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AbstractFile>
     */
    public function getAbstractFiles(): Collection
    {
        return $this->abstractFiles;
    }

    public function addAbstractFile(AbstractFile $abstractFile): static
    {
        if (!$this->abstractFiles->contains($abstractFile)) {
            $this->abstractFiles->add($abstractFile);
            $abstractFile->setUploadedBy($this);
        }

        return $this;
    }

    public function removeAbstractFile(AbstractFile $abstractFile): static
    {
        if ($this->abstractFiles->removeElement($abstractFile)) {
            // set the owning side to null (unless already changed)
            if ($abstractFile->getUploadedBy() === $this) {
                $abstractFile->setUploadedBy(null);
            }
        }

        return $this;
    }

    public function isDarkMode(): ?bool
    {
        return $this->dark_mode;
    }

    public function setDarkMode(?bool $dark_mode): static
    {
        $this->dark_mode = $dark_mode;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setToUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getToUser() === $this) {
                $notification->setToUser(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return ($this->getDisplayName() !== NULL) ? $this->getDisplayName() : $this->getEmail();
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function isIsPassive(): ?bool
    {
        return $this->is_passive;
    }

    public function setIsPassive(?bool $is_passive): static
    {
        $this->is_passive = $is_passive;

        return $this;
    }
}
