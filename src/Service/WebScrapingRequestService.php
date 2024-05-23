<?php namespace App\Service;

use App\Config\WebScrapingRequestCompletedHandleType;
use App\Config\WebScrapingRequestStatusType;
use App\Entity\WebScrapingRequest;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class WebScrapingRequestService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createRequest(string $requestURL, string|WebScrapingRequestCompletedHandleType $completedHandle = WebScrapingRequestCompletedHandleType::HANDLE_NULL): WebScrapingRequest
    {
        $myReq = new WebScrapingRequest();
        $myReq->setCreatedAt(new DateTimeImmutable());
        $myReq->setStatus(WebScrapingRequestStatusType::NEWLY_CREATED);
        $myReq->setNavigateUrl($requestURL);
        $myReq->setCompletedHandle($completedHandle);
        $this->entityManager->persist($myReq);
        $this->entityManager->flush();
        return $myReq;
    }

}