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
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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

    public function pushProducts(ArrayCollection $myProducts, Marketplace $marketplace): false|ArrayCollection
    {
        // Return NULL If No Products Extracted
        if ($myProducts->count() === 0) {
            return FALSE;
        }

        // Create Array Collection For Pushed Products
        $flushedProducts = new ArrayCollection();

        // Loop Extracted Products
        foreach ($myProducts as $myProduct) {

            // Set Product Marketplace
            $myProduct->setMarketplace($marketplace);

            // Check Product Already Exist -> Check By Identity and Marketplace
            $productAlreadyExist = $this->productRepository->findOneBy(["marketplace" => $marketplace, "identity" => $myProduct->getIdentity()]);

            if ($productAlreadyExist === NULL) {

                // Validate Product Data
                $validateProductIdentity = $this->validateProductIdentity($myProduct, $marketplace);
                $validateProductName = $this->validateProductName($myProduct, $marketplace);
                $validateProductImage = $this->validateProductImage($myProduct, $marketplace);
                $validateProductURL = $this->validateProductURL($myProduct, $marketplace);


                if ($validateProductIdentity->count() === 0 && $validateProductName->count() === 0 && $validateProductImage->count() === 0 && $validateProductURL->count() === 0) {

                    // Persist Product If Validation Success
                    $this->entityManager->persist($myProduct);
                    $flushedProducts->add($myProduct);

                } else {

                    // Add Error If Validation Fails
                    $errorTextParts = [
                        t("Sayfadan çıkarılan ürün eklenemedi. Validasyon başarısız."),
                        t("Ürün Kimliği:" . " " . json_encode($myProduct->getIdentity())),
                        t("Ürün Kimliği Hataları:") . " " . $validateProductIdentity,
                        t("Ürün Adı:" . " " . json_encode($myProduct->getName())),
                        t("Ürün Adı Hatası:") . " " . $validateProductName,
                        t("Ürün Görseli:" . " " . json_encode($myProduct->getImage())),
                        t("Ürün Görseli Hatası:") . " " . $validateProductImage,
                        t("Ürün URL:" . " " . json_encode($myProduct->getUrl())),
                        t("Ürün URL Hatası:") . " " . $validateProductURL,
                    ];
                    $this->logger->warning(implode(PHP_EOL, $errorTextParts));
                }
            }
        }

        if ($flushedProducts->count() > 0) {

            $this->entityManager->flush();
            return $flushedProducts;

        }

        return FALSE;
    }

    private function validateProductIdentity(Product $myProduct, Marketplace $marketplace): ConstraintViolationListInterface
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        return $this->validator->validate($myProduct->getIdentity(), $validationAsserts);
    }

    private function validateProductName(Product $myProduct, Marketplace $marketplace): ConstraintViolationListInterface
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\NoSuspiciousCharacters(),
        ];
        return $this->validator->validate($myProduct->getName(), $validationAsserts);
    }

    private function validateProductImage(Product $myProduct, Marketplace $marketplace): ConstraintViolationListInterface
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Callback(function (mixed $value, ExecutionContextInterface $context, mixed $payload) {
                if (in_array(parse_url($value, PHP_URL_SCHEME), self::ALLOWED_URL_PROTOCOLS) === FALSE) {
                    $context->addViolation(t("İzin verilmeyen URL protokolü."));
                }
            }),
        ];
        return $this->validator->validate($myProduct->getImage(), $validationAsserts);
    }

    private function validateProductURL(Product $myProduct, Marketplace $marketplace): ConstraintViolationListInterface
    {
        $validationAsserts = [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\Callback(function (mixed $value, ExecutionContextInterface $context, mixed $payload) {
                if (in_array(parse_url($value, PHP_URL_SCHEME), self::ALLOWED_URL_PROTOCOLS) === FALSE) {
                    $context->addViolation(t("İzin verilmeyen URL protokolü."));
                }
            }),
        ];
        return $this->validator->validate($myProduct->getUrl(), $validationAsserts);
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