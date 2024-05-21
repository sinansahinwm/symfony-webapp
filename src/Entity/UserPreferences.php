<?php

namespace App\Entity;

use App\Repository\UserPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $receive_emails = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isReceiveEmails(): ?bool
    {
        return $this->receive_emails;
    }

    public function setReceiveEmails(?bool $receive_emails): static
    {
        $this->receive_emails = $receive_emails;

        return $this;
    }
}
