<?php

namespace App\Entity;

use App\Repository\HelpCenterArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HelpCenterArticleRepository::class)]
class HelpCenterArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'helpCenterArticles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?HelpCenterCategory $article_category = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $markdown_content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleCategory(): ?HelpCenterCategory
    {
        return $this->article_category;
    }

    public function setArticleCategory(?HelpCenterCategory $article_category): static
    {
        $this->article_category = $article_category;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMarkdownContent(): ?string
    {
        return $this->markdown_content;
    }

    public function setMarkdownContent(string $markdown_content): static
    {
        $this->markdown_content = $markdown_content;

        return $this;
    }
}
