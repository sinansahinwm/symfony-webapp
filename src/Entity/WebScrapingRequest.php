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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $webhook_url = null;

    #[ORM\Column(type: 'web_scraping_request_status')]
    private $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consumed_screenshot = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consumed_content = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consumed_url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $consumed_remote_status = null;

    #[ORM\Column(type: 'web_scraping_request_completed_handle', nullable: true)]
    private $completed_handle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $steps = null;

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
        if ($this->getStatus() === NULL) {
            $this->setStatus(WebScrapingRequestStatusType::NEWLY_CREATED);
        }
        if ($this->getCreatedAt() === NULL) {
            $this->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function getConsumedScreenshot(): ?string
    {
        return $this->consumed_screenshot;
    }

    public function setConsumedScreenshot(?string $consumed_screenshot): static
    {
        $this->consumed_screenshot = $consumed_screenshot;

        return $this;
    }

    public function getConsumedContent(): ?string
    {
        return $this->consumed_content;
    }

    public function setConsumedContent(string $consumed_content): static
    {
        $this->consumed_content = $consumed_content;

        return $this;
    }

    public function getConsumedUrl(): ?string
    {
        return $this->consumed_url;
    }

    public function setConsumedUrl(string $consumed_url): static
    {
        $this->consumed_url = $consumed_url;

        return $this;
    }

    public function getConsumedRemoteStatus(): ?string
    {
        return $this->consumed_remote_status;
    }

    public function setConsumedRemoteStatus(string $consumed_remote_status): static
    {
        $this->consumed_remote_status = $consumed_remote_status;

        return $this;
    }

    public function getCompletedHandle()
    {
        return $this->completed_handle;
    }

    public function setCompletedHandle($completed_handle): static
    {
        $this->completed_handle = $completed_handle;

        return $this;
    }

    public function getSteps(): ?string
    {
        return $this->steps;
    }

    public function setSteps(?string $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

}
