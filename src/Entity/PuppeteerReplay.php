<?php

namespace App\Entity;

use App\Repository\PuppeteerReplayRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PuppeteerReplayRepository::class)]
class PuppeteerReplay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\File(
        maxSize: "1M",
        mimeTypes: "application/json"
    )]
    #[Vich\UploadableField(mapping: 'puppeteer_replay', fileNameProperty: "fileName")]
    public ?File $theFile;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(type: 'puppeteer_replay_status', nullable: true)]
    private $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

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

}
