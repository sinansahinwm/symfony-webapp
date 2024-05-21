<?php

namespace App\Entity;

use App\Repository\HelpCenterCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HelpCenterCategoryRepository::class)]
class HelpCenterCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?string $category_id = null;

    #[ORM\OneToMany(targetEntity: HelpCenterArticle::class, mappedBy: 'article_category')]
    private Collection $helpCenterArticles;

    public function __construct()
    {
        $this->helpCenterArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getCategoryId(): ?string
    {
        return $this->category_id;
    }

    public function setCategoryId(string $category_id): static
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection<int, HelpCenterArticle>
     */
    public function getHelpCenterArticles(): Collection
    {
        return $this->helpCenterArticles;
    }

    public function addHelpCenterArticle(HelpCenterArticle $helpCenterArticle): static
    {
        if (!$this->helpCenterArticles->contains($helpCenterArticle)) {
            $this->helpCenterArticles->add($helpCenterArticle);
            $helpCenterArticle->setArticleCategory($this);
        }

        return $this;
    }

    public function removeHelpCenterArticle(HelpCenterArticle $helpCenterArticle): static
    {
        if ($this->helpCenterArticles->removeElement($helpCenterArticle)) {
            // set the owning side to null (unless already changed)
            if ($helpCenterArticle->getArticleCategory() === $this) {
                $helpCenterArticle->setArticleCategory(null);
            }
        }

        return $this;
    }
}
