<?php namespace App\Security;

use App\Config\MessageBusDelays;
use App\Entity\User;
use App\Message\AppEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\Translation\t;

class UserChecker implements UserCheckerInterface
{

    const EMAIL_VERIFICATION_LIMIT = 72;

    public function __construct(private MessageBusInterface $messageBus, private EmailVerifier $emailVerifier,)
    {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isIsPassive()) {
            $passiveMessage = t("Kullanıcı hesabı pasife alındı.");
            throw new CustomUserMessageAccountStatusException($passiveMessage);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isVerified() !== TRUE) {
            $userCreatedAt = $user->getCreatedAt()->getTimestamp();
            $timeDiffHour = ceil(((time() - $userCreatedAt) / 60) / 60);
            if ($timeDiffHour > self::EMAIL_VERIFICATION_LIMIT) {
                $emailVerificationErrorMessage = t("E-posta adresinizi onaylanamadınız. Hesabınıza giriş yapabilmek için e-posta adresinizi onaylamalısınız.");
                $this->sendEmailVerification($user);
                throw new CustomUserMessageAccountStatusException($emailVerificationErrorMessage);
            }
        }
    }

    private function sendEmailVerification(User $user): void
    {
        // Prepare email context & Send user email verification code by email.
        $myEmailContext = $this->emailVerifier->getConfirmationEmailContext(LoginFormAuthenticator::VERIFY_EMAIL_ROUTE, $user);
        $myCallToAction = [
            "url" => $myEmailContext["signedUrl"],
            "title" => t("E-Posta Adresimi Doğrula")
        ];
        $myEmailNotification = new AppEmailMessage("verify_email", $user->getEmail(), t('E-Posta Adresinizi Doğrulayın'), $myEmailContext, $myCallToAction);
        $this->messageBus->dispatch($myEmailNotification, [new DelayStamp(MessageBusDelays::SEND_VERIFY_EMAIL_AFTER_REGISTRATION)]);
    }
}