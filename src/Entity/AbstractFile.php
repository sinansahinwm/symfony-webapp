<?php

namespace App\Entity;

use App\Repository\AbstractFileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: AbstractFileRepository::class)]
class AbstractFile
{

    const FILE_PROPERTY = 'theFile';
    const FILENAME_PROPERTY = 'fileName';
    public const ALLOWED_MAX_FILE_SIZE_MB = 50;
    public const ALLOWED_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // Microsoft Excel (XLSX)
        'application/vnd.ms-excel', // Microsoft Excel (XLS)
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // Microsoft Word (DOCX)
        'application/msword', // Microsoft Word (DOC)
        'image/jpeg', // JPEG Image
        'image/png', // PNG Image
        'application/pdf', // PDF
        'application/text', // Plain Text
        'application/zip', // Zip File
        'application/vnd.rar', // Rar File
        'text/plain' // Plain Text
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $uploaded_at = null;

    #[ORM\ManyToOne(inversedBy: 'abstractFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $uploaded_by = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mime_type = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[Assert\File(
        maxSize: self::ALLOWED_MAX_FILE_SIZE_MB . "M",
        mimeTypes: self::ALLOWED_MIME_TYPES
    )]
    #[Vich\UploadableField(mapping: 'abstract_file', fileNameProperty: self::FILENAME_PROPERTY, size: 'size', mimeType: 'mime_type')]
    public ?File $theFile;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadedAt(): ?DateTimeImmutable
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(DateTimeImmutable $uploaded_at): static
    {
        $this->uploaded_at = $uploaded_at;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploaded_by;
    }

    public function setUploadedBy(?User $uploaded_by): static
    {
        $this->uploaded_by = $uploaded_by;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    public function setMimeType(?string $mime_type): static
    {
        $this->mime_type = $mime_type;

        return $this;
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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function setTheFile(?File $theFile = NULL): void
    {
        $this->theFile = $theFile;
        if (NULL !== $theFile) {
            $this->updated_at = new DateTimeImmutable();
        }
    }

    public function getTheFile(): ?File
    {
        return $this->theFile;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

}
