<?php

namespace App\Message;

final class AppEmailMessage
{


    public function __construct(private string $templateName, private string $emailTo, private ?string $emailSubject = NULL, private array $emailContext = [], private array $callToAction = [])
    {
    }

    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    public function setTemplateName(string $templateName): void
    {
        $this->templateName = $templateName;
    }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    public function setEmailTo(string $emailTo): void
    {
        $this->emailTo = $emailTo;
    }

    public function getEmailSubject(): ?string
    {
        return $this->emailSubject;
    }

    public function setEmailSubject(?string $emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }

    public function getEmailContext(): array
    {
        return $this->emailContext;
    }

    public function setEmailContext(array $emailContext): void
    {
        $this->emailContext = $emailContext;
    }

    public function getCallToAction(): array
    {
        return $this->callToAction;
    }

    public function setCallToAction(array $emailContext): void
    {
        $this->callToAction = $emailContext;
    }

}
