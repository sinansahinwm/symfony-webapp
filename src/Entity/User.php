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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use function Symfony\Component\Translation\t;

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

    /* DEPRECED
    #[Assert\Expression(
        "!(this.getPhone() contains ' ')",
        message: 'Telefon numaranızı hiçbir boşluk karakteri olmadan giriniz. Örn; +905555555555',
    )]
    #[Assert\Expression(
        "this.getPhone() starts with '+'",
        message: 'Telefon numarası + işaretiyle başlamalıdır. Lütfen telefon numaranızı ülke koduyla beraber yazın. Örn; +905555555555',
    )]
    #[Assert\Length(
        min: 10,
        max: 16,
    )]*/
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

    #[ORM\OneToMany(targetEntity: UserActivity::class, mappedBy: 'user')]
    private Collection $userActivities;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $preferred_theme = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $last_login = null;

    #[ORM\ManyToOne]
    private ?SubscriptionPlan $subscription_plan = null;

    #[ORM\Column(nullable: true)]
    private ?bool $trial_period_used = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $subscription_plan_valid_until = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last_ip_address = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?UserPreferences $preferences = null;

    public function __construct()
    {
        $this->teamInvites = new ArrayCollection();
        $this->abstractFiles = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->userActivities = new ArrayCollection();
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

    /**
     * @return Collection<int, UserActivity>
     */
    public function getUserActivities(): Collection
    {
        return $this->userActivities;
    }

    public function addUserActivity(UserActivity $userActivity): static
    {
        if (!$this->userActivities->contains($userActivity)) {
            $this->userActivities->add($userActivity);
            $userActivity->setUser($this);
        }

        return $this;
    }

    public function removeUserActivity(UserActivity $userActivity): static
    {
        if ($this->userActivities->removeElement($userActivity)) {
            // set the owning side to null (unless already changed)
            if ($userActivity->getUser() === $this) {
                $userActivity->setUser(null);
            }
        }

        return $this;
    }

    public function getPreferredTheme(): ?string
    {
        return $this->preferred_theme;
    }

    public function setPreferredTheme(?string $preferred_theme): static
    {
        $this->preferred_theme = $preferred_theme;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeImmutable $last_login): static
    {
        $this->last_login = $last_login;

        return $this;
    }

    public function getSubscriptionPlan(): ?SubscriptionPlan
    {
        return $this->subscription_plan;
    }

    public function setSubscriptionPlan(?SubscriptionPlan $subscription_plan): static
    {
        $this->subscription_plan = $subscription_plan;

        return $this;
    }

    public function isTrialPeriodUsed(): ?bool
    {
        return $this->trial_period_used;
    }

    public function setTrialPeriodUsed(?bool $trial_period_used): static
    {
        $this->trial_period_used = $trial_period_used;

        return $this;
    }

    public function getSubscriptionPlanValidUntil(): ?\DateTimeImmutable
    {
        return $this->subscription_plan_valid_until;
    }

    public function setSubscriptionPlanValidUntil(?\DateTimeImmutable $subscription_plan_valid_until): static
    {
        $this->subscription_plan_valid_until = $subscription_plan_valid_until;

        return $this;
    }

    public function getLastIpAddress(): ?string
    {
        return $this->last_ip_address;
    }

    public function setLastIpAddress(?string $last_ip_address): static
    {
        $this->last_ip_address = $last_ip_address;

        return $this;
    }

    public function getPreferences(): ?UserPreferences
    {
        return $this->preferences;
    }

    public function setPreferences(?UserPreferences $preferences): static
    {
        $this->preferences = $preferences;

        return $this;
    }

    public static function getUserPhoneConstraints(): array
    {
        return [
            new Assert\Callback(function (mixed $value, ExecutionContextInterface $context, mixed $payload) {
                if (str_contains($value, " ")) {
                    $context->buildViolation(t("Telefon numaranızı hiçbir boşluk karakteri olmadan giriniz. Örn; +905555555555"))
                        ->atPath('phone')
                        ->addViolation();
                }
            }),
            new Assert\Callback(function (mixed $value, ExecutionContextInterface $context, mixed $payload) {
                if (!str_starts_with($value, "+")) {
                    $context->buildViolation(t("Telefon numarası + işaretiyle başlamalıdır. Lütfen telefon numaranızı ülke koduyla beraber yazın. Örn; +905555555555"))
                        ->atPath('phone')
                        ->addViolation();
                }
            }),
            new Assert\Length(min: 10, max: 16),
        ];
    }
}
