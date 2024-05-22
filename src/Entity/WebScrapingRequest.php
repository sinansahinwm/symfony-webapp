<?php

namespace App\Entity;

use App\Config\WebScrapingRequestStatusType;
use App\Repository\WebScrapingRequestRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: WebScrapingRequestRepository::class)]
class WebScrapingRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $navigate_url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $webhook_url = null;

    #[ORM\Column(type: 'web_scraping_request_status')]
    private $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getNavigateUrl(): ?string
    {
        return $this->navigate_url;
    }

    public function setNavigateUrl(string $navigate_url): static
    {
        $this->navigate_url = $navigate_url;

        return $this;
    }

    public function getWebhookUrl(): ?string
    {
        return $this->webhook_url;
    }

    public function setWebhookUrl(string $webhook_url): static
    {
        $this->webhook_url = $webhook_url;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;

        return $this;
    }

    #[ORM\PrePersist]
    private function prePersist(): void
    {
        if ($this->status === NULL) {
            $this->setStatus(WebScrapingRequestStatusType::CREATED);
        }
        if ($this->created_at === NULL) {
            $this->setCreatedAt(new DateTimeImmutable());
        }
    }
}
