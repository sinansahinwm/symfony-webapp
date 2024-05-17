<?php namespace App\Service;

use App\Entity\SubscriptionPlan;
use App\Entity\User;
use App\Entity\UserPayment;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriptionPlanCheckoutService
{

    private ?string $lastPaymentError = NULL;
    private bool $paymentSuccess = FALSE;

    public function isPaymentSuccess(): bool
    {
        return $this->paymentSuccess;
    }

    public function setPaymentSuccess(bool $paymentSuccess): void
    {
        $this->paymentSuccess = $paymentSuccess;
    }


    public function __construct(private IyzicoPaymentService $iyzicoPaymentService, private TranslatorInterface $translator, private EntityManagerInterface $entityManager)
    {
    }

    public function checkoutWithForm(User $theUser, SubscriptionPlan $subscriptionPlan, FormInterface $checkoutForm, string|Locale $responseLocale = Locale::TR): self
    {

        try {

            $selectedLocale = ($responseLocale === Locale::TR) ? Locale::TR : Locale::EN;
            $selectedCurrency = $subscriptionPlan->getCurrency();
            $selectedAmount = $subscriptionPlan->getAmount();
            $selectedBasketID = Uuid::v1();;
            $selectedConversationID = Uuid::v1();
            $selectedCardData = $this->getFormData($checkoutForm);

            if ($selectedCardData !== NULL) {

                [$myCardholderName, $myCardNumber, $expDateMonth, $expDateYear, $myCVV] = $selectedCardData;


                // --- Checkout Start --- //
                $iyzicoPayment = $this->iyzicoPaymentService
                    ->preparePaymentRequest($selectedLocale, $selectedCurrency, $selectedAmount, $selectedAmount, $selectedBasketID, $selectedConversationID)
                    ->setCreditCard($myCardholderName, $myCardNumber, $expDateMonth, $expDateYear, $myCVV)
                    ->setBuyerUser($theUser)
                    ->setUserAddress($theUser)
                    ->addBasketItem($subscriptionPlan->getId(), $subscriptionPlan->getName(), BasketItemType::VIRTUAL, $selectedAmount)
                    ->checkout();

                if ($iyzicoPayment !== NULL) {
                    $paymentStatus = $iyzicoPayment->getStatus();
                    $paymentErrorMessage = $iyzicoPayment->getErrorMessage();
                    $paymentStatusBool = $paymentStatus === "success";

                    if ($paymentStatusBool === TRUE) {

                        // Log Payment
                        $this->logUserPayment($theUser, $iyzicoPayment->getRawResult());

                        // Set Payment Success
                        $this->setPaymentSuccess(TRUE);

                    } else {
                        $this->setLastPaymentError($paymentErrorMessage);
                    }

                } else {
                    $this->setLastPaymentError($this->translator->trans("Girilen kart bilgileri ile ödeme yapılamıyor."));
                }

                // --- Checkout End --- //

            } else {
                $this->setLastPaymentError($this->translator->trans("Girilen kart bilgileri geçersiz."));
            }

        } catch (Exception) {
            $this->setLastPaymentError($this->translator->trans("Bilinmeyen bir hata oluştu"));
        }

        return $this;
    }

    private function logUserPayment(User $user, string $rawResult): UserPayment
    {
        $userPayment = new UserPayment();
        $userPayment->setUser($user);
        $userPayment->setRawResult($rawResult);
        $userPayment->setCreatedAt(new DateTimeImmutable());
        $this->entityManager->persist($userPayment);
        $this->entityManager->flush();
        return $userPayment;
    }

    private function getFormData(FormInterface $checkoutForm): null|array
    {
        try {
            $myFormData = $checkoutForm->getData();
            $myCardholderName = $myFormData["name"];
            $myCardNumber = $myFormData["card_number"];
            $myExpDate = $myFormData["exp_date"];
            $myCVV = $myFormData["cvv"];
            $cardNumberJustNumbers = preg_replace('[\D]', '', $myCardNumber);
            if ($cardNumberJustNumbers !== FALSE and $cardNumberJustNumbers !== NULL) {
                $explodedExpDate = explode("/", $myExpDate);
                if (count($explodedExpDate) === 2) {
                    $expDateMonth = $explodedExpDate[0];
                    $expDateYear = $explodedExpDate[1];
                    return [$myCardholderName, $cardNumberJustNumbers, $expDateMonth, $expDateYear, $myCVV];
                }
            }
        } catch (Exception) {
            return NULL;
        }
        return NULL;
    }

    public function getLastPaymentError(): ?string
    {
        return $this->lastPaymentError;
    }

    public function setLastPaymentError(?string $lastPaymentError): void
    {
        $this->lastPaymentError = $lastPaymentError;
    }


}