<?php namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TextTranslatorService
{

    const TRANSLATION_ENDPOINT = "https://translation.googleapis.com/language/translate/v2";

    private ?string $sourceLanguage = NULL;

    private ?string $targetLanguage = NULL;

    private ?string $query = NULL;

    public function __construct(private HttpClientInterface $httpClient, private ContainerBagInterface $containerBag)
    {
    }

    public function getSourceLanguage(): ?string
    {
        return $this->sourceLanguage;
    }

    public function setSourceLanguage(?string $sourceLanguage): self
    {
        $this->sourceLanguage = $sourceLanguage;
        return $this;
    }

    public function getTargetLanguage(): ?string
    {
        return $this->targetLanguage;
    }

    public function setTargetLanguage(?string $targetLanguage): self
    {
        $this->targetLanguage = $targetLanguage;
        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function translate(): string|null
    {
        $myURL = $this->prepareTranslateURL();

        try {
            $myTranslationRequest = $this->httpClient->request('GET', $myURL);
            if ($myTranslationRequest->getStatusCode() === 200) {
                $responseArray = $myTranslationRequest->toArray(FALSE);
                if (isset($responseArray["data"])) {
                    $translationsData = $responseArray["data"]["translations"];
                    if (count($translationsData) > 0) {
                        $firstTranslation = $translationsData[array_key_first($translationsData)];
                        if (isset($firstTranslation["translatedText"])) {
                            return $firstTranslation["translatedText"];
                        }
                    }

                }

            }
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|DecodingExceptionInterface) {
            return NULL;
        }
        return NULL;
    }

    private function prepareTranslateURL(): string
    {
        return self::TRANSLATION_ENDPOINT . "?" . $this->prepareHttpQuery();
    }

    private function prepareHttpQuery(): string
    {
        $apiKey = $this->containerBag->get('app.api_keys.google.translate');

        $queryParameters = [
            "key" => $apiKey,
            "q" => $this->getQuery()
        ];

        if ($this->getSourceLanguage() !== NULL) {
            $queryParameters["source"] = $this->getSourceLanguage();
        }

        if ($this->getTargetLanguage() !== NULL) {
            $queryParameters["target"] = $this->getTargetLanguage();
        }

        return http_build_query($queryParameters);
    }


}