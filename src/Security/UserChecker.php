<?php namespace App\Security;

use App\Config\MessageBusDelays;
use App\Entity\User;
use App\Message\AppEmailMessage;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use function Symfony\Component\Translation\t;

class UserChecker implements UserCheckerInterface
{

    const EMAIL_VERIFICATION_LIMIT = 72;

    public function __construct(private MessageBusInterface $messageBus, private EmailVerifier $emailVerifier, private CacheInterface $cache)
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

                $cacheKey = "CACHE_SEND_VERIFY_EMAIL_" . $user->getEmail();

                try {
                    $this->cache->get($cacheKey, function (ItemInterface $item) use ($user): string {
                        $item->expiresAfter(self::EMAIL_VERIFICATION_LIMIT * 60 * 60);
                        // Send Verification Email If User Is Not Verified
                        $this->sendEmailVerification($user);
                        return TRUE;
                    });
                } catch (InvalidArgumentException $e) {
                    return;
                }

                // DEPRECED
                // DEPRECED This feature has been disabled because it is required to log in for email confirmation.
                // DEPRECED $emailVerificationErrorMessage = t("E-posta adresinizi onaylanamadınız. Hesabınıza giriş yapabilmek için e-posta adresinizi onaylamalısınız.");
                // DEPRECED throw new CustomUserMessageAccountStatusException($emailVerificationErrorMessage);

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