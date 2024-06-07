<?php

namespace App\Entity;

use App\Repository\MarketplaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketplaceRepository::class)]
class Marketplace
{

    const SEARCH_SELECTORS_SPLITTER = '<!-- AND -->';
    const SEARCH_URL_PLACEHOLDER_START = '{{';
    const SEARCH_URL_PLACEHOLDER_END = '}}';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $search_url = null;

    #[ORM\Column(type: 'marketplace_search_handler')]
    private $search_handler_type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $search_selectors = null;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getSearchUrl(): ?string
    {
        return $this->search_url;
    }

    public function getRealSearchUrl(array $dataContext = []): ?string
    {
        $myRawSearchURL = $this->getSearchUrl();
        foreach ($dataContext as $dataKey => $dataValue) {
            $theSelectorPlaceholder = self::SEARCH_URL_PLACEHOLDER_START . $dataKey . self::SEARCH_URL_PLACEHOLDER_END;
            $myRawSearchURL = str_replace($theSelectorPlaceholder, $dataValue, $myRawSearchURL);
        }
        return $myRawSearchURL;
    }

    public function setSearchUrl(string $search_url): static
    {
        $this->search_url = $search_url;

        return $this;
    }

    public function getSearchHandlerType()
    {
        return $this->search_handler_type;
    }

    public function setSearchHandlerType($search_handler_type): static
    {
        $this->search_handler_type = $search_handler_type;

        return $this;
    }

    public function getSearchSelectors(): ?string
    {
        return $this->search_selectors;
    }

    public function setSearchSelectors(?string $search_selectors): static
    {
        $this->search_selectors = $search_selectors;

        return $this;
    }
}
