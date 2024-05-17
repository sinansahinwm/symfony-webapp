<?php

namespace App\Entity;

use App\Repository\UserPaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPaymentRepository::class)]
class UserPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $raw_result = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bin_number_details = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getRawResult(): ?string
    {
        return $this->raw_result;
    }

    public function setRawResult(string $raw_result): static
    {
        $this->raw_result = $raw_result;

        return $this;
    }

    public function getBinNumberDetails(): ?string
    {
        return $this->bin_number_details;
    }

    public function setBinNumberDetails(?string $bin_number_details): static
    {
        $this->bin_number_details = $bin_number_details;

        return $this;
    }
}
