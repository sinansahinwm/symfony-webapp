<?php namespace App\Service;

use Exception;
use Matecat\XliffParser\XliffParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Yaml\Yaml;

class TranslationsParser
{

    private $errors = [];

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $errorText): self
    {
        $this->errors[] = $errorText;
        return $this;
    }

    public function __construct(private ContainerBagInterface $containerBag, private LoggerInterface $logger)
    {
    }

    public function parseAll(): null|array
    {

        $parsedTranslations = [];

        // Translations : [ XLF ]
        $xlfTranslations = $this->findTranslationFilesByExtension();
        foreach ($xlfTranslations as $xlfTranslation) {
            $parsedFile = $this->parseTranslationFileByName($xlfTranslation);
            if ($parsedFile !== NULL) {
                $parsedXLF = $this->tryToParseXLF($xlfTranslation);
                if ($parsedXLF !== NULL) {
                    $parsedTranslations[] = [
                        'XLF', // Translation Format
                        $xlfTranslation, // File Path
                        $parsedFile, // Parsed File Details (domain, locale etc.)
                        $parsedXLF // Parsed XLF Data
                    ];
                }
            }
        }

        // Translations : [YML]
        $ymlTranslations = $this->findTranslationFilesByExtension('yml');
        foreach ($ymlTranslations as $ymlTranslation) {
            $parsedFile = $this->parseTranslationFileByName($ymlTranslation);
            if ($parsedFile !== NULL) {
                $parsedYML = $this->tryToParseYML($ymlTranslation);
                if ($parsedYML !== NULL) {
                    $parsedTranslations[] = [
                        'YML', // Translation Format
                        $ymlTranslation, // File Path
                        $parsedFile, // Parsed File Details (domain, locale etc.)
                        $parsedYML // Parsed YML Data
                    ];
                }
            }
        }

        return $parsedTranslations;
    }

    private function getTranslationsDirectory(): string
    {
        return $this->containerBag->get('app.projectDir') . "/translations";
    }

    private function findTranslationFilesByExtension(string $fileExtension = 'xlf'): array
    {
        $filePattern = $this->getTranslationsDirectory() . "/*." . $fileExtension;
        return glob($filePattern);
    }

    private function parseTranslationFileByName($translationFilePath): null|array
    {
        $myBasename = basename($translationFilePath);
        $explodedBasename = explode(".", $myBasename);
        if ($explodedBasename >= 3) {
            $theExtension = $explodedBasename[array_key_last($explodedBasename)];
            $theLocale = $explodedBasename[array_key_last($explodedBasename) - 1];
            $theDomain = $explodedBasename[array_key_last($explodedBasename) - 2];
            $theIntl = str_ends_with($theDomain, 'intl-icu');
            return [$theLocale, $theDomain, $theExtension, $theIntl, $myBasename];
        }
        return NULL;
    }

    private function tryToParseXLF(string $filePath): null|array
    {
        $myParser = new XliffParser($this->logger);
        try {
            $xLiffContent = file_get_contents($filePath);
            return $myParser->xliffToArray($xLiffContent);
        } catch (Exception $exception) {
            $errorMessages = [
                "Unable to parse " . $filePath . " file.",
                $exception->getMessage()
            ];
            $this->addError(implode(PHP_EOL, $errorMessages));
            return NULL;
        }
    }

    private function tryToParseYML(string $filePath): null|array
    {
        $parsedYML = Yaml::parseFile($filePath, Yaml::PARSE_OBJECT);
        if (is_array($parsedYML)) {
            return $parsedYML;
        }
        return NULL;
    }

}