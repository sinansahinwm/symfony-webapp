<?php namespace App\CronTask;

use App\Config\WebScrapingRequestStatusType;
use App\Repository\WebScrapingRequestRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@hourly', jitter: 120)]
class WebScrapingRequestCleanerTask
{

    const WEB_SCRAPING_REQUEST_LIFETIME_MINS = 120;

    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(): void
    {
        $completedRequests = $this->webScrapingRequestRepository->findBy(['status' => WebScrapingRequestStatusType::COMPLETED]);
        foreach ($completedRequests as $completedRequest) {
            $timeNowStamp = (new DateTimeImmutable())->getTimestamp();
            $requestCreatedStamp = $completedRequest->getCreatedAt()->getTimestamp();
            $timeDiffSec = ($timeNowStamp - $requestCreatedStamp) / 60;
            if ($timeDiffSec > self::WEB_SCRAPING_REQUEST_LIFETIME_MINS) {
                $this->entityManager->remove($completedRequest);
            }
        }
        $this->entityManager->flush();
    }

}