<?php namespace App\ProductExtractor;

use App\Entity\Marketplace;
use App\Entity\Product;
use App\Entity\WebScrapingRequest;
use App\Repository\ProductRepository;
use App\Service\DomContentFramerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Symfony\Component\Translation\t;


class WebScrapingRequestExtractorHelper
{

    const ALLOWED_URL_PROTOCOLS = [
        'https'
    ];

    public function __construct(private EntityManagerInterface $entityManager, private ProductRepository $productRepository, private ValidatorInterface $validator, private DomContentFramerService $domContentFramerService, private LoggerInterface $logger)
    {
    }

    public function pushProduct(Product $myProduct, Marketplace $marketplace): false|Product
    {
        // Set Product Marketplace
        $myProduct->setMarketplace($marketplace);

        // Check Product Already Exist
        $productAlreadyExist = $this->productRepository->findOneBy(["marketplace" => $marketplace, "identity" => $myProduct->getIdentity()]);

        if ($productAlreadyExist === NULL) {

            // Validate Product Data
            $validateProductIdentity = $this->validateProductIdentity($myProduct, $marketplace);
            $validateProductName = $this->validateProductName($myProduct, $marketplace);
            $validateProductImage = $this->validateProductImage($myProduct, $marketplace);
            $validateProductURL = $this->validateProductURL($myProduct, $marketplace);

            // If Validation Success -> Add Product
            if ($validateProductIdentity === TRUE && $validateProductName === TRUE && $validateProductImage === TRUE && $validateProductURL === TRUE) {
                $this->entityManager->persist($myProduct);
                $this->entityManager->flush();
                return $myProduct;
            } else {
                $errorTextParts = [
                    t("Sayfadan çıkarılan ürün eklenemedi. Validasyon başarısız."),
                    t("Ürün Kimliği: " . json_encode($myProduct->getIdentity(), JSON_PRETTY_PRINT)),
                    t("Ürün Adı: " . json_encode($myProduct->getName(), JSON_PRETTY_PRINT)),
                    t("Ürün Görseli: " . json_encode($myProduct->getImage(), JSON_PRETTY_PRINT)),
                    t("Ürün URL: " . json_encode($myProduct->getUrl(), JSON_PRETTY_PRINT)),
                ];
                $this->logger->warning(implode(PHP_EOL, $errorTextParts));
            }

        }

        return FALSE;
    }

    private function validateProductIdentity(Product $myProduct, Marketplace $marketplace): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        return $this->validator->validate($myProduct->getIdentity(), $validationAsserts)->count() === 0;
    }

    private function validateProductName(Product $myProduct, Marketplace $marketplace): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        return $this->validator->validate($myProduct->getIdentity(), $validationAsserts)->count() === 0;
    }

    private function validateProductImage(Product $myProduct, Marketplace $marketplace): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Url(
                protocols: self::ALLOWED_URL_PROTOCOLS
            ),
        ];
        return $this->validator->validate($myProduct->getIdentity(), $validationAsserts)->count() === 0;
    }

    private function validateProductURL(Product $myProduct, Marketplace $marketplace): bool
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Url(
                protocols: self::ALLOWED_URL_PROTOCOLS
            ),
        ];
        return $this->validator->validate($myProduct->getIdentity(), $validationAsserts)->count() === 0;
    }

    public function getCrawler(WebScrapingRequest $webScrapingRequest, Marketplace $marketplace): Crawler|null
    {
        $rawHTML = base64_decode($webScrapingRequest->getConsumedContent());
        if ($rawHTML === FALSE) {
            return NULL;
        }

        $framedContent = $this->domContentFramerService->setHtml($rawHTML, FALSE)->setBaseURL($marketplace->getUrl())->getFramedContent(FALSE, FALSE, FALSE);
        return new Crawler($framedContent);
    }

    public function getXHRLog(WebScrapingRequest $webScrapingRequest, Marketplace $marketplace): null|array
    {
        $xhrLog = $webScrapingRequest->getXhrlog();
        if ($xhrLog !== NULL) {
            if (json_validate($xhrLog) === TRUE) {
                $decodedXHRLog = json_decode($xhrLog, TRUE, 512, JSON_OBJECT_AS_ARRAY);
                if (is_array($decodedXHRLog) === TRUE) {
                    return $decodedXHRLog;
                }
            }
        }
        return NULL;
    }

}