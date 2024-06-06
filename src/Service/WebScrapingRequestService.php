<?php namespace App\Service;

use App\Config\WebScrapingRequestCompletedHandleType;
use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use App\Service\WebScrapingRequestStep\WebScrapingRequestStepInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use function Symfony\Component\Translation\t;

class WebScrapingRequestService
{

    const BLOCKED_HOSTS = [
        'localhost',
        '127.0.0.1'
    ];

    private array $mySteps = [];

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createRequest(string $requestURL, string|WebScrapingRequestCompletedHandleType $completedHandle = WebScrapingRequestCompletedHandleType::HANDLE_NULL): WebScrapingRequest
    {
        // Check Request URL
        $this->throwOnRequestURLFailed($requestURL);

        // Create Web Scraping Request
        $stepsData = $this->getStepsJSON();
        $myReq = new WebScrapingRequest();
        $myReq->setCreatedAt(new DateTimeImmutable());
        $myReq->setStatus(WebScrapingRequestStatusType::NEWLY_CREATED);
        $myReq->setNavigateUrl($requestURL);
        $myReq->setCompletedHandle($completedHandle);
        $myReq->setSteps($stepsData);
        $this->entityManager->persist($myReq);
        $this->entityManager->flush();
        return $myReq;
    }

    private function getStepsJSON(): null|string
    {
        if (count($this->mySteps) > 0) {
            $allStepData = [];
            foreach ($this->mySteps as $theStep) {
                if ($theStep instanceof WebScrapingRequestStepInterface) {
                    $allStepData[] = $theStep->getStepData();
                }
            }
            $theJSON = json_encode($allStepData);
            if ($theJSON !== FALSE) {
                return $theJSON;
            }
        }
        return NULL;
    }

    public function addStep(WebScrapingRequestStepInterface $scrapingRequestStep): self
    {
        $this->mySteps[] = $scrapingRequestStep;
        return $this;
    }

    public function clearSteps(): self
    {
        $this->mySteps = [];
        return $this;
    }

    private function throwOnRequestURLFailed(string $requestURL): void
    {
        $parsedHost = parse_url($requestURL, PHP_URL_HOST);
        $parsedPort = parse_url($requestURL, PHP_URL_PORT);
        if (in_array($parsedHost, self::BLOCKED_HOSTS) === TRUE or $parsedPort !== NULL) {
            throw new Exception(t("URL Host or Port Not Allowed"));
        }
    }

}