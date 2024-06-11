<?php namespace App\MessageHandler;

use App\Entity\WebScrapingRequest;
use App\Message\HandleWebScrapingRequestAfterCompletedMessage;
use App\Repository\WebScrapingRequestRepository;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function Symfony\Component\Translation\t;

#[AsMessageHandler]
class HandleWebScrapingRequestAfterCompletedMessageHandler
{

    public function __construct(private WebScrapingRequestRepository $webScrapingRequestRepository, private EventDispatcherInterface $eventDispatcher, private LoggerInterface $logger)
    {
    }

    public function __invoke(HandleWebScrapingRequestAfterCompletedMessage $myMessage)
    {
        $myWebScrapingRequest = $this->webScrapingRequestRepository->find($myMessage->getWebScrapingRequestID());

        if ($myWebScrapingRequest instanceof WebScrapingRequest) {
            $eventDispatcherName = self::getEventDispatcherEventName($myWebScrapingRequest);
            try {
                $this->eventDispatcher->dispatch($myWebScrapingRequest, self::getEventDispatcherEventName($myWebScrapingRequest));
            } catch (Exception $e) {
                $errorTexts = [
                    t("Ürün çıkarma işlemi sevk edilirken bir sorunla karşılaşıldı."),
                    t("Sevk Edici:") . " " . $eventDispatcherName,
                    t("Hata Açıklaması:") . " " . $e->getMessage(),
                    t("Çıkarma İşlemi ID:") . " " . $myWebScrapingRequest->getId(),
                    t("Çıkarma İşlemi URL:") . " " . $myWebScrapingRequest->getNavigateUrl(),
                    t("Çıkarma İşlemi Durum:") . " " . $myWebScrapingRequest->getStatus(),
                    t("Çıkarma İşlemi Hatası:") . " " . $myWebScrapingRequest->getLastErrorMessage(),
                    t("Çıkarma İşlemi Kontrolcüsü:") . " " . $myWebScrapingRequest->getCompletedHandle(),
                ];
                $this->logger->error(implode(PHP_EOL, $errorTexts));
            }
        }

    }

    public static function getEventDispatcherEventName(WebScrapingRequest $webScrapingRequest): string
    {
        return 'scraper.' . strtolower($webScrapingRequest->getCompletedHandle());
    }

}