<?php

namespace App\Entity;

use App\Repository\PuppeteerReplayHookRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuppeteerReplayHookRecordRepository::class)]
class PuppeteerReplayHookRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'puppeteerReplayHookRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PuppeteerReplay $replay = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $step = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $screenshot = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    private ?string $phase = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReplay(): ?PuppeteerReplay
    {
        return $this->replay;
    }

    public function setReplay(?PuppeteerReplay $replay): static
    {
        $this->replay = $replay;

        return $this;
    }

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(string $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getScreenshot(): ?string
    {
        return $this->screenshot;
    }

    public function setScreenshot(string $screenshot): static
    {
        $this->screenshot = $screenshot;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(string $phase): static
    {
        $this->phase = $phase;

        return $this;
    }
}
