<?php

namespace App\Command;

use App\Service\TextTranslatorService;
use App\Service\TranslationsParser;
use Exception;
use Matecat\XliffParser\XliffParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Twig\Environment;

#[AsCommand(
    name: 'translation:auto-translate',
    description: 'It translate translation files automatically.',
)]
class AutoTranslateTranslations extends Command
{


    public function __construct(private TranslationsParser $translationsParser, private TextTranslatorService $textTranslatorService, private ContainerBagInterface $containerBag, private LoggerInterface $logger)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $parsedTranslations = $this->translationsParser->parseAll();
        exit(var_dump($parsedTranslations));

        if ($parsedTranslations === NULL or count($parsedTranslations) === 0) {
            $errorsArr = $this->translationsParser->getErrors();
            foreach ($errorsArr as $errorText) {
                $io->error($errorText);
            }
        } else {

            $xLiffParser = new XliffParser($this->logger);

            foreach ($parsedTranslations as $parsedTranslation) {

                [$translationFormat, $xlfTranslation, $parsedFile, $parsedXLF] = $parsedTranslation;

                // Translate with [ XLF ]
                if (strtoupper($translationFormat) === 'XLF') {

                    [$theLocale, $theDomain, $theExtension, $theIntl, $myBasename] = $parsedFile;

                    $xlfFiles = $parsedXLF["files"];
                    foreach ($xlfFiles as $index => $xlfFile) {
                        $fileAttributes = $xlfFile["attr"];
                        $fileTransUnits = $xlfFile["trans-units"];

                        $unitsSourceLanguage = $fileAttributes["source-language"];
                        $unitsTargetLanguage = $fileAttributes["target-language"];

                        foreach ($fileTransUnits as $fileTransUnit) {
                            $transUnitSource = $fileTransUnit["source"];
                            $transUnitTarget = $fileTransUnit["target"];

                            $targetRawContent = $transUnitTarget["raw-content"];
                            $sourceRawContent = $transUnitSource["raw-content"];

                            $translatedRawContentText = $this->textTranslatorService->setQuery($sourceRawContent)->setSourceLanguage($unitsSourceLanguage)->setTargetLanguage($unitsTargetLanguage)->translate();

                            if ($translatedRawContentText !== NULL) {

                                exit("ÇEVRİLDİ");

                            }

                        }


                    }

                }

            }
        }


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }


    private function getTranslationFiles(SymfonyStyle $symfonyStyle, string $format = 'xlf',): array
    {
        $myFiles = [];
        $translationFilesPattern = $this->containerBag->get('app.projectDir') . "/translations/*." . $format;
        foreach (glob($translationFilesPattern) as $translationFile) {


            try {

                $fileBasename = basename($translationFile);
                $explodedBasename = explode(".", $fileBasename);
                $translationFileExtension = $explodedBasename[array_key_last($explodedBasename)];
                $translationFileLocale = $explodedBasename[array_key_last($explodedBasename) - 1];
                $translationFileDomain = $explodedBasename[array_key_last($explodedBasename) - 2];

                $parsedContent = NULL;

                // Parse with XLF
                if ($format === "xlf") {
                    $myParser = new XliffParser($this->logger);
                    $parsedContent = $myParser->xliffToArray(file_get_contents($translationFile));
                }

                $myFiles[] = [
                    "path" => $translationFile,
                    "basename" => $fileBasename,
                    "format" => $format,
                    "extension" => $translationFileExtension,
                    "locale" => $translationFileLocale,
                    "domain" => $translationFileDomain,
                    "intl" => str_ends_with($translationFileDomain, "intl-icu"),
                    "parsed" => $parsedContent
                ];
            } catch (Exception $exception) {
                $symfonyStyle->error($exception->getMessage());
            }
        }
        return $myFiles;
    }

}
