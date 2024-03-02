<?php namespace App\Service\CrudTable;

class CrudTableAction
{
    private ?string $icon;

    private string $text;

    private string $url;

    public function __construct(string $text, string $url, ?string $icon = NULL)
    {
        $this->text = $text;
        $this->url = $url;
        $this->icon = $icon;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }


}