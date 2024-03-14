<?php

namespace App\Entity;

use App\Config\PuppeteerReplayStatusType;
use App\Repository\PuppeteerReplayRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\HasLifecycleCallbacks]
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

    #[ORM\Column(type: 'puppeteer_replay_status')]
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

    public function getTheFile(): ?File
    {
        return $this->theFile;
    }

    public function setTheFile(?File $theFile): void
    {
        $this->theFile = $theFile;
    }

    #[ORM\PrePersist]
    public function setDefaultStatus()
    {
        if ($this->status === NULL) {
            $this->setStatus(PuppeteerReplayStatusType::UPLOAD);
        }
    }

}
