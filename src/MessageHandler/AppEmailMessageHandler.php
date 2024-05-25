<?php

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\AppEmailMessage;
use App\Repository\UserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsMessageHandler]
final class AppEmailMessageHandler
{

    const UNEXPECTED_LOCALHOST_URLS = [
        'https://localhost',
        'https://localhost:8000',
        'https://127.0.0.1:8000',
        'https://127.0.0.1',
        'http://localhost',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://127.0.0.1',
    ];

    const EMAIL_BLOCKED_KEYWORDS = [
        'Connection could not be established with host'
    ];

    public function __construct(private Environment $twig, private MailerInterface $mailer, private LoggerInterface $logger, private ContainerBagInterface $containerBag, private UserRepository $userRepository)
    {
    }

    public function __invoke(AppEmailMessage $message)
    {

        // Check Email Receiving Blocked
        $emailUser = $this->userRepository->findBy(["email" => $message->getEmailTo()]);
        if ($emailUser instanceof User) {
            $userPreferences = $emailUser->getUserPreferences();
            if ($userPreferences->isReceiveEmails() !== TRUE) {
                return;
            }
        }

        // Prepare email foundation.
        $myEmail = new Email();

        // Prepare & set email data.
        $myEmail->to($message->getEmailTo());
        $myEmail->subject($message->getEmailSubject());

        // Send Email
        try {

            // Render & fix html errors.
            $mailRenderedHTML = $this->getRenderedMailHTML($message->getTemplateName(), $message->getEmailContext(), $message->getEmailSubject(), $message->getCallToAction());
            $fixedMailHTML = $this->fixLocalhostProblem($mailRenderedHTML);

            // Set mail html content & send via symfony's mailer.
            $myEmail->html($fixedMailHTML);

            // Check Rendered Context
            $renderedContextCheck = $this->renderedContextIsNotBlocked($fixedMailHTML);
            if ($renderedContextCheck === TRUE) {
                $this->mailer->send($myEmail);
            }


        } catch (TransportExceptionInterface|LoaderError|RuntimeError|SyntaxError $e) {
            $this->logger->error($e);
        }

    }

    private function renderedContextIsNotBlocked(string $renderedContext): bool
    {
        foreach (self::EMAIL_BLOCKED_KEYWORDS as $blockedKeyword) {
            if (str_contains($renderedContext, $blockedKeyword)) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function getRenderedMailHTML(string $templateName, array $emailContext = [], string $emailSubject = NULL, array $callToAction = []): string
    {
        // Prepare boilerplate context
        $emailContext["title"] = $emailSubject;
        $emailContext["htmlLang"] = "en";
        $emailContext["subject"] = $emailSubject;
        if (isset($callToAction["title"]) and isset($callToAction["url"])) {
            $emailContext["action"] = $callToAction;
        }

        // Prepare initial context
        $mailContext = ["context" => $emailContext];
        $templatePath = "email/templates/$templateName.md.twig";

        // Render & return rendered email.
        return $this->twig->render($templatePath, $mailContext);
    }

    public function fixLocalhostProblem(string $renderedHTML): string
    {
        try {
            $expectedUrl = $this->containerBag->get('app.defaultDomain');
            foreach (self::UNEXPECTED_LOCALHOST_URLS as $unExpectedLocalhostUrl) {
                $renderedHTML = str_replace($unExpectedLocalhostUrl, $expectedUrl, $renderedHTML);
            }
        } catch (Exception $exception) {
            return $renderedHTML;
        }

        return $renderedHTML;
    }
}
