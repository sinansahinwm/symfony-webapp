<?php

namespace App\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;
use function Symfony\Component\Translation\t;

final class FirebaseScraperRequestParser extends AbstractRequestParser
{

    const REQUIRED_PAYLOAD_PARAMS = ["instanceID", "screenshot", "content", "status", "url"];

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            // Add RequestMatchers to fit your needs
        ]);
    }

    /**
     * @throws JsonException
     */
    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        // Get Token
        $authToken = $request->headers->get('Authorization');
        $extractedAuthToken = $this->extractToken($authToken);

        // Check Extracted Token
        if ($extractedAuthToken === NULL) {
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, t('Yetkilendirme tokeni gönderilmedi.'));
        }

        // Check Token Value
        if ($extractedAuthToken !== $secret) {
            throw new RejectWebhookException(Response::HTTP_UNAUTHORIZED, t('Geçersiz yetkilendirme tokeni.'));
        }

        // Validate Request Payload
        $requestPayload = $request->getPayload();
        foreach (self::REQUIRED_PAYLOAD_PARAMS as $payloadParamNameRequired) {
            $payloadParameterValue = $requestPayload->get($payloadParamNameRequired);
            if ($payloadParameterValue === NULL) {
                throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, t('Tüm zorunlu parametreler gönderilmelidir. Şu parametre gönderilmedi: ') . $payloadParamNameRequired);
            }
        }

        // Validate Payload Params
        $payloadScreenshotDecoded = base64_decode($requestPayload->get("screenshot"));
        $payloadContentDecoded = base64_decode($requestPayload->get("content"));
        $payloadStatusCodeIsValid = is_int($requestPayload->get("status"));

        if (($payloadScreenshotDecoded === FALSE) || ($payloadContentDecoded === FALSE) || ($payloadStatusCodeIsValid !== TRUE)) {
            throw new RejectWebhookException(Response::HTTP_BAD_REQUEST, t('Veriler kabul edilmedi.'));
        }

        // Parse Payload
        $myPayload = $request->getPayload()->all();

        // Return Remote Event
        return new RemoteEvent(
            'FIREBASE_SCRAPER',
            'FIREBASE_SCRAPER',
            $myPayload,
        );
    }

    private function extractToken(null|string $authHeader): string|null
    {
        if ($authHeader !== NULL) {
            if (str_starts_with($authHeader, "Bearer")) {
                $bearerToken = str_replace(["Bearer", " "], ["", ""], $authHeader);
                if (is_string($bearerToken)) {
                    return $bearerToken;
                }
            }
        }
        return NULL;
    }
}
