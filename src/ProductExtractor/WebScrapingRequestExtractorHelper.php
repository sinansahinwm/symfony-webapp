<?php namespace App\ProductExtractor;

use App\Entity\Marketplace;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class WebScrapingRequestExtractorHelper
{

    public function __construct(private EntityManagerInterface $entityManager, private ProductRepository $productRepository, private ValidatorInterface $validator)
    {
    }

    public function flushExtractedProducts(ArrayCollection $extractedProducts, Marketplace $marketplace): bool
    {
        if ($extractedProducts->count() > 0) {
            foreach ($extractedProducts as $extractedProduct) {
                if ($extractedProduct instanceof Product) {
                    $extractedProduct->setMarketplace($marketplace);
                    $checkProductExist = $this->productRepository->findOneBy(["marketplace" => $marketplace, "identity" => $extractedProduct->getIdentity()]);
                    $checkProductIdentity = $this->checkProductIdentity($extractedProduct->getIdentity());
                    $checkProductName = $this->checkProductName($extractedProduct->getName());
                    $checkProductImage = $this->checkProductImage($extractedProduct->getImage());
                    $checkProductURL = $this->checkProductURL($extractedProduct->getUrl());
                    if ($checkProductExist === NULL && $checkProductIdentity === TRUE && $checkProductName === TRUE && $checkProductImage === TRUE && $checkProductURL === TRUE) {
                        $this->entityManager->persist($extractedProduct);
                    }
                }
            }
            $this->entityManager->flush();
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function checkProductIdentity($productIdentity): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        $validationErrors = $this->validator->validate($productIdentity, $validationAsserts);
        return $validationErrors->count() === 0;
    }

    private function checkProductImage($productImage): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
        ];
        $validationErrors = $this->validator->validate($productImage, $validationAsserts);
        return $validationErrors->count() === 0;
    }

    private function checkProductName($productName): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        $validationErrors = $this->validator->validate($productName, $validationAsserts);
        return $validationErrors->count() === 0;
    }

    private function checkProductURL($productURL): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
        ];
        $validationErrors = $this->validator->validate($productURL, $validationAsserts);
        return $validationErrors->count() === 0;
    }

}