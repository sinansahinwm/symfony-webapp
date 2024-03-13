<?php namespace App\Service\NodeApp;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

class NodeAppPackagerService
{

    const APP_PACKAGE_GLOB = '/*.js';
    const APP_PACKAGE_INDEX_ENTRYPOINT = 'index.js';
    const APPS_DIRECTORY = "/assets/server/apps/";

    public function __construct(private Filesystem $myFileSystem, private ContainerBagInterface $containerBag, private LoggerInterface $logger)
    {
    }

    public function packageApp(string $appName, string $entryPointContent, string $entryPointFile = self::APP_PACKAGE_INDEX_ENTRYPOINT): string
    {
        $packageZipFileName = $this->myFileSystem->tempnam('/tmp', $appName . "_", '.zip');
        $appBaseDirectory = $this->getAppsBaseDirectory($appName);
        $appPackageZip = new ZipArchive();
        if ($appPackageZip->open($packageZipFileName, ZipArchive::CREATE) === TRUE) {
            // Add Other App Files
            foreach (glob($appBaseDirectory . self::APP_PACKAGE_GLOB) as $packageFile) {
                $basename = basename($packageFile);
                $fileContents = file_get_contents($packageFile);
                if ($fileContents !== FALSE) {
                    $appPackageZip->addFromString($basename, file_get_contents($packageFile));
                }
            }
            // Add Entry Point File
            $appPackageZip->addFromString($entryPointFile, $this->capsulateEntrypointContent($entryPointContent, $appName));
            $appPackageZip->close();
        }
        return $packageZipFileName;
    }

    private function capsulateEntrypointContent(string $appName, ?string $entrypointContent): string
    {
        $entrypointParts = [
            '// NODE APP [START] : ' . $appName,
            PHP_EOL,
            $entrypointContent ?? '',
            PHP_EOL,
            '// NODE APP [END] : ' . $appName,
        ];
        return implode(PHP_EOL, $entrypointParts);
    }

    private function getAppsBaseDirectory(string $nodeAppName): string
    {
        try {
            return $this->containerBag->get("app.projectDir") . self::APPS_DIRECTORY . $nodeAppName;
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $this->logger->error($e->getMessage());
            throw new FileNotFoundException();
        }
    }
}