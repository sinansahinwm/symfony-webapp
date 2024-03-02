<?php

namespace App\Controller;

use App\Config\MessageBusDelays;
use App\Entity\TeamInvite;
use App\Entity\User;
use App\Form\Auth\AuthChangePasswordFormType;
use App\Form\Auth\AuthResetPasswordRequestFormType;
use App\Form\Auth\AuthSigninType;
use App\Form\Auth\AuthSignupType;
use App\Message\AppEmailMessage;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\TeamInviteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use function Symfony\Component\Translation\t;

#[Route(path: '/auth', name: 'app_auth_')]
class AuthController extends AbstractController
{

    use ResetPasswordControllerTrait;

    public function __construct(private EmailVerifier $emailVerifier, private ResetPasswordHelperInterface $resetPasswordHelper, private EntityManagerInterface $entityManager)
    {

    }

    #[Route(path: '/signin', name: 'signin')]
    public function authSignIn(AuthenticationUtils $authenticationUtils): Response
    {

        // If user is already logged in, redirect to dashboard.
        if ($this->getUser()) {
            return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
        }

        // If authentication has error, show its by message.
        $lastAuthError = $authenticationUtils->getLastAuthenticationError();

        // If user is already logged in, fetch user's logged username. (Works if upper if block is commented)
        $lastLoggedUsername = $authenticationUtils->getLastUsername();

        // Create login form for rendering.
        $myLoginForm = $this->createForm(AuthSigninType::class);

        return $this->render('auth/signin.html.twig', ['last_username' => $lastLoggedUsername, 'error' => $lastAuthError, 'form' => $myLoginForm]);
    }

    #[Route(path: '/signout', name: 'signout')]
    public function authSignOut(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall.
    }

    #[Route('/signup', name: 'signup')]
    public function authSignUp(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager, MessageBusInterface $messageBus, UrlGeneratorInterface $urlGenerator): Response
    {
        // Create new user & create user registration form for registration.
        $user = new User();
        $form = $this->createForm(AuthSignupType::class, $user);
        $form->handleRequest($request);

        // If form is submitted, set user defaults and persist it.
        if ($form->isSubmitted() && $form->isValid()) {

            // Convert plain password text to hashed password.
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Set User Defaults
            $user->setDefaults();

            // Persist & create user.
            $entityManager->persist($user);
            $entityManager->flush();

            // Prepare email context & Send user email verification code by email.
            $myEmailContext = $this->emailVerifier->getConfirmationEmailContext(LoginFormAuthenticator::VERIFY_EMAIL_ROUTE, $user);
            $myCallToAction = [
                "url" => $myEmailContext["signedUrl"],
                "title" => t("E-Posta Adresimi Doğrula")
            ];
            $myEmailNotification = new AppEmailMessage("verify_email", $user->getEmail(), t('E-Posta Adresinizi Doğrulayın'), $myEmailContext, $myCallToAction);
            $messageBus->dispatch($myEmailNotification, [new DelayStamp(MessageBusDelays::SEND_VERIFY_EMAIL_AFTER_REGISTRATION)]);

            $this->addFlash('pageNotificationSuccess', t('Kullanıcı kaydı başarılı, lütfen e-posta adresinize gönderilen doğrulama mailini onaylayın.'));

            // Return user authenticator after registration.
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        // Render registration form.
        return $this->render('auth/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify_email', name: 'verify_email')]
    public function authVerifyEmail(Request $request, #[CurrentUser] User $loggedUser, MessageBusInterface $messageBus): Response
    {
        // If user is not logged in, verify email doesn't work properly.
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Try to verify email.
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $loggedUser);
            $welcomeEmail = new AppEmailMessage("welcome", $loggedUser->getEmail(), t('Hoşgeldin!'));
            $messageBus->dispatch($welcomeEmail, [new DelayStamp(MessageBusDelays::SEND_WELCOME_EMAIL_AFTER_EMAIL_VERIFICATION)]);
        } catch (VerifyEmailExceptionInterface $exception) {
            $failedReasonTranslated = t($exception->getReason(), [], 'VerifyEmailBundle');
            $this->addFlash('pageNotificationError', $failedReasonTranslated);
            return $this->redirectToRoute(LoginFormAuthenticator::SIGNUP_ROUTE);
        }

        // Show email verified notification.
        $this->addFlash('pageNotificationSuccess', t('E-Posta adresiniz başarıyla doğrulandı.'));

        // If user's email is successfully verified, redirect request to dashboard.
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/reset_password_request', name: 'reset_password_request')]
    public function authResetPasswordRequest(Request $request, MessageBusInterface $messageBus, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator): Response
    {
        $form = $this->createForm(AuthResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->sendPasswordRequestEmail(
                $form->get('email')->getData(),
                $messageBus,
                $translator,
                $urlGenerator
            );
        }

        return $this->render('auth/reset_password_request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset_password_check_email', name: 'reset_password_check_email')]
    public function authResetPasswordCheckEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }
        return $this->render('auth/reset_password_check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    #[Route('/reset_password_reset/{token}', name: 'reset_password_reset')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_auth_reset_password_reset');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException($translator->trans('Bu şifre sıfırlama bağlantısı geçersiz.'));
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_auth_reset_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(AuthChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_auth_signin');
        }

        return $this->render('auth/reset_password_reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    #[Route('/send_team_invite_email/{id}', name: 'send_team_invite_email')]
    public function authSendTeamInviteEmail(#[CurrentUser] User $loggedUser, TeamInvite $teamInvite, TeamRepository $teamRepository, TeamInviteService $teamInviteService): RedirectResponse
    {
        // Only team owners can invite other users.
        if ($teamInvite->getTeam()->getOwner()->getId() === $loggedUser->getId()) {
            // Check collaborator already exist.
            $alreadyExist = $teamRepository->collaboratorExist($teamInvite->getTeam(), $teamInvite->getUser(), $teamInvite->getEmailAddress());

            if ($alreadyExist === NULL) {
                if ($teamInvite->getEmailAddress() === NULL) {
                    // Send invite mail immediately.
                    $teamInviteService->sendTeamInviteMail($teamInvite);
                    $this->addFlash('pageNotificationSuccess', t('Takım daveti kullanıcıya e-posta olarak gönderildi.'));
                } else {
                    // Do not send invite mail. Mail is persisted when user first login.
                    $this->addFlash('pageNotificationSuccess', t('Takım daveti kullanıcıya e-posta olarak gönderildi. Davetlinin isteği kabul etmesi için öncelikle kaydolması gerekecektir.'));
                }
                $this->addFlash('pageNotificationSuccess', t('Takım daveti kullanıcıya e-posta olarak gönderildi.'));
            } else {
                $this->addFlash('pageNotificationError', t('Davet edilen kullanıcı zaten bu takımda katılımcı. Yeniden davet gönderilemez.'));
            }
        } else {
            $this->addFlash('pageNotificationError', t('Yalnızca takım kurucuları başka üyeleri takıma davet edebilir.'));
        }
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    #[Route('/accept_team_invite_email/{id}', name: 'accept_team_invite_email')]
    public function authAcceptTeamInviteEmail(#[CurrentUser] User $loggedUser, TeamInvite $teamInvite, TeamRepository $teamRepository, TeamInviteService $teamInviteService): RedirectResponse
    {
        // Only invited users can accept the invite.
        if (($teamInvite->getUser() !== NULL && $teamInvite->getUser()->getId() === $loggedUser->getId()) || ($teamInvite->getEmailAddress() === $loggedUser->getEmail())) {
            // Check collaborator already exist.
            $alreadyExist = $teamRepository->collaboratorExist($teamInvite->getTeam(), $teamInvite->getUser(), $teamInvite->getEmailAddress());
            if ($alreadyExist === NULL) {
                $teamInviteService->acceptTeamInviteMail($teamInvite);
                $this->addFlash('pageNotificationSuccess', t('Takım daveti kabul edildi.'));
            } else {
                $this->addFlash('pageNotificationError', t('Bu takımda zaten katılımcısınız. Daveti yeniden kabul edemezsiniz.'));
            }
        } else {
            $this->addFlash('pageNotificationError', t('Bu takım daveti size gönderilmedi.'));
        }
        return $this->redirectToRoute(LoginFormAuthenticator::SIGNIN_REDIRECT_AFTER_ROUTE);
    }

    private function sendPasswordRequestEmail(string $emailFormData, MessageBusInterface $messageBus, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator): RedirectResponse
    {
        $theUser = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$theUser) {
            return $this->redirectToRoute('app_auth_reset_password_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($theUser);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_auth_reset_password_check_email');
        }

        $myMailContext = [
            'resetToken' => [
                'token' => $resetToken->getToken(),
                'expirationMessageData' => $resetToken->getExpirationMessageData(),
                'expiresAt' => $resetToken->getExpiresAt(),
                'expirationMessageKey' => $resetToken->getExpirationMessageKey(),
                'expiresAtIntervalInstance' => $resetToken->getExpiresAtIntervalInstance(),
            ],
            'action' => [
                'url' => $urlGenerator->generate('app_auth_reset_password_reset', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                'title' => $translator->trans('Şifremi Sıfırla')
            ]
        ];

        $resetEmailMessage = new AppEmailMessage('reset_password', $emailFormData, $translator->trans('Şifrenizi Sıfırlayın'), $myMailContext);
        $messageBus->dispatch($resetEmailMessage, [new DelayStamp(MessageBusDelays::SEND_RESET_PASSWORD_EMAIL_AFTER_REQUESTED)]);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_auth_reset_password_check_email');
    }

}
