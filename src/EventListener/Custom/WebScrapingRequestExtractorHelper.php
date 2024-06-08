<?php namespace App\EventListener\Custom;

use App\Entity\Marketplace;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class WebScrapingRequestExtractorHelper
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function flushExtractedProducts(ArrayCollection $extractedProducts, Marketplace $marketplace): bool
    {
        if ($extractedProducts->count() > 0) {
            foreach ($extractedProducts as $extractedProduct) {
                if ($extractedProduct instanceof Product) {
                    $extractedProduct->setMarketplace($marketplace);
                    $this->entityManager->persist($extractedProduct);
                }
            }
            $this->entityManager->flush();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}