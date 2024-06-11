<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const SIGNIN_ROUTE = 'app_auth_signin';
    public const SIGNUP_ROUTE = 'app_auth_signup';
    public const VERIFY_EMAIL_ROUTE = 'app_auth_verify_email';
    public const SIGNIN_REDIRECT_AFTER_ROUTE = 'app_admin_dashboard';

    public const REDIRECT_ROUTE_AFTER_SUBSCRIPTION_COMPLETED = 'app_admin_dashboard';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private LoggerInterface $logger)
    {
    }

    public function authenticate(Request $request): Passport
    {

        $signInFormData = $request->get('auth_signin');
        $formDataEmail = $signInFormData["email"] ?? '';
        $formDataPassword = $signInFormData["password"] ?? '';
        $formDataCSRFToken = $request->get('_csrf_token');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $formDataEmail);
        return new Passport(
            new UserBadge($formDataEmail),
            new PasswordCredentials($formDataPassword),
            [
                new CsrfTokenBadge('authenticate', $formDataCSRFToken),
                // DEPRECED new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }
        return new RedirectResponse($this->urlGenerator->generate(self::SIGNIN_REDIRECT_AFTER_ROUTE));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::SIGNIN_ROUTE);
    }

    public function supports(Request $request): bool
    {
        $validSigninPaths = [
            $this->getLoginUrl($request), // Its needed to login by signin route
            '/', // Its needed to login by index route
        ];
        return $request->isMethod('POST') && in_array($request->getPathInfo(), $validSigninPaths);
    }

}
