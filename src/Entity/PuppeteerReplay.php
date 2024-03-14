<?php

namespace App\Entity;

use App\Repository\PuppeteerReplayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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
    public ?File $theFile = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(type: 'puppeteer_replay_status', nullable: true)]
    private $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $last_error_message = null;

    #[ORM\OneToMany(targetEntity: PuppeteerReplayHookRecord::class, mappedBy: 'replay')]
    private Collection $puppeteerReplayHookRecords;

    public function __construct()
    {
        $this->puppeteerReplayHookRecords = new ArrayCollection();
    }

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

    public function getLastErrorMessage(): ?string
    {
        return $this->last_error_message;
    }

    public function setLastErrorMessage(?string $last_error_message): static
    {
        $this->last_error_message = $last_error_message;

        return $this;
    }

    /**
     * @return Collection<int, PuppeteerReplayHookRecord>
     */
    public function getPuppeteerReplayHookRecords(): Collection
    {
        return $this->puppeteerReplayHookRecords;
    }

    public function addPuppeteerReplayHookRecord(PuppeteerReplayHookRecord $puppeteerReplayHookRecord): static
    {
        if (!$this->puppeteerReplayHookRecords->contains($puppeteerReplayHookRecord)) {
            $this->puppeteerReplayHookRecords->add($puppeteerReplayHookRecord);
            $puppeteerReplayHookRecord->setReplay($this);
        }

        return $this;
    }

    public function removePuppeteerReplayHookRecord(PuppeteerReplayHookRecord $puppeteerReplayHookRecord): static
    {
        if ($this->puppeteerReplayHookRecords->removeElement($puppeteerReplayHookRecord)) {
            // set the owning side to null (unless already changed)
            if ($puppeteerReplayHookRecord->getReplay() === $this) {
                $puppeteerReplayHookRecord->setReplay(null);
            }
        }

        return $this;
    }

}
