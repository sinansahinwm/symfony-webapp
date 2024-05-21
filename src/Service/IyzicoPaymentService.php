<?php namespace App\Service;

use App\Config\IyzicoDefaultConfig;
use App\Entity\User;
use Exception;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\BinNumber;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Payment;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Request\RetrieveBinNumberRequest;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class IyzicoPaymentService
{

    private ?PaymentCard $paymentCard = NULL;
    private array $baskedItems = [];
    private ?Buyer $basketBuyer = NULL;
    private ?CreatePaymentRequest $myPaymentRequest = NULL;
    private ?Address $buyerAddress = NULL;

    public function __construct(private ContainerBagInterface $containerBag)
    {
    }

    public function setCreditCard(string $cardHolderName, string $cardNumber, string $expireMonth, string $expireYear, string $cvvNumber, bool $registerCard = FALSE): self
    {
        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($cardHolderName);
        $paymentCard->setCardNumber($cardNumber);
        $paymentCard->setExpireMonth($expireMonth);
        $paymentCard->setExpireYear($expireYear);
        $paymentCard->setCvc($cvvNumber);
        $paymentCard->setRegisterCard($registerCard === TRUE ? 1 : 0);
        $this->paymentCard = $paymentCard;
        return $this;
    }

    public function addBasketItem(string $itemID, string $itemName, BasketItemType|string $itemType, float $itemPrice, string $itemCategory = 'Software'): self
    {
        $myBasketItem = new BasketItem();
        $myBasketItem->setId($itemID);
        $myBasketItem->setName($itemName);
        $myBasketItem->setCategory1($itemCategory);
        $myBasketItem->setItemType($itemType);
        $myBasketItem->setPrice($itemPrice);
        $this->baskedItems[] = $myBasketItem;
        return $this;
    }

    public function setUserAddress(User $theUser): self
    {
        $myAddress = new Address();
        $myAddress->setContactName($theUser->getDisplayName() ?? $theUser->getEmail());
        $myAddress->setCity(IyzicoDefaultConfig::DEFAULT_USER_ADDRESS_CITY);
        $myAddress->setCountry(IyzicoDefaultConfig::DEFAULT_USER_ADDRESS_COUNTRY);
        $myAddress->setAddress(IyzicoDefaultConfig::DEFAULT_USER_ADDRESS_ADDRESS);
        $myAddress->setZipCode(IyzicoDefaultConfig::DEFAULT_USER_ADDRESS_ZIPCODE);
        $this->buyerAddress = $myAddress;
        return $this;
    }

    public function getBinNumberDetails(string $cardNumber, Locale|string $locale = Locale::TR): null|BinNumber
    {
        $iyzicoOptions = $this->getIyzipayOptions();
        $cardBinNumber = substr($cardNumber, 0, 8);
        $myBinNumberRequest = new RetrieveBinNumberRequest();
        $myBinNumberRequest->setBinNumber($cardBinNumber);
        $myBinNumberRequest->setLocale(Locale::TR);
        $retrievedBinNumber = BinNumber::retrieve($myBinNumberRequest, $iyzicoOptions);

        $binNumberStatus = $retrievedBinNumber->getStatus();
        $binNumberStatusSuccess = ($binNumberStatus === "success");

        if ($binNumberStatusSuccess === TRUE) {
            return $retrievedBinNumber;
        }
        return NULL;
    }

    public function setBuyerUser(User $user): self
    {
        $myBuyer = new Buyer();
        $myBuyer->setId($user->getId());
        $myBuyer->setName($this->getUserDisplayNamePartsOrNull($user) ?? IyzicoDefaultConfig::DEFAULT_USER_FULLNAME_NAME);
        $myBuyer->setSurname($this->getUserDisplayNamePartsOrNull($user, TRUE) ?? IyzicoDefaultConfig::DEFAULT_USER_FULLNAME_LASTNAME);
        $myBuyer->setGsmNumber($user->getPhone() ?? IyzicoDefaultConfig::DEFAULT_USER_PHONE);
        $myBuyer->setEmail($user->getEmail());
        $myBuyer->setIdentityNumber(IyzicoDefaultConfig::DEFAULT_USER_IDENTITY_NUMBER);
        $myBuyer->setLastLoginDate(IyzicoDefaultConfig::DEFAULT_USER_LAST_LOGIN_DATE);
        $myBuyer->setRegistrationDate(IyzicoDefaultConfig::DEFAULT_USER_REGISTRATION_DATE);
        $myBuyer->setRegistrationAddress(IyzicoDefaultConfig::DEFAULT_USER_REGISTRATION_ADDRESS);
        $myBuyer->setIp($user->getLastIpAddress() ?? IyzicoDefaultConfig::DEFAULT_USER_IP);
        $myBuyer->setCity(IyzicoDefaultConfig::DEFAULT_USER_CITY);
        $myBuyer->setCountry(IyzicoDefaultConfig::DEFAULT_USER_COUNTRY);
        $myBuyer->setZipCode(IyzicoDefaultConfig::DEFAULT_USER_ZIPCODE);
        $this->basketBuyer = $myBuyer;
        return $this;
    }

    private function getUserDisplayNamePartsOrNull(User $user, $lastName = FALSE): null|string
    {
        $userDisplayName = $user->getDisplayName();
        if ($userDisplayName !== NULL) {
            $explodedDisplayName = explode(" ", $user->getDisplayName());
            if (count($explodedDisplayName) > 1) {
                $userFirstname = $explodedDisplayName[array_key_first($explodedDisplayName)];
                $userLastname = $explodedDisplayName[array_key_last($explodedDisplayName)];
                if ($lastName === TRUE) {
                    return $userLastname;
                } else {
                    return $userFirstname;
                }
            }
        }
        return NULL;
    }

    private function getIyzipayOptions(): Options
    {
        $myOptions = new Options();
        $myOptions->setApiKey($this->containerBag->get("app.api_keys.iyzico.api_key"));
        $myOptions->setSecretKey($this->containerBag->get("app.api_keys.iyzico.api_secret"));
        $myOptions->setBaseUrl($this->containerBag->get("app.api_keys.iyzico.endpoint"));
        return $myOptions;
    }

    public function preparePaymentRequest(Locale|string $paymentLocale, Currency|string $paymentCurrency, float $price, float $paidPrice, string $basketID, string $conversationID, int $installmentsCount = 1, PaymentChannel|string $paymentChannel = PaymentChannel::WEB, PaymentGroup|string $paymentGroup = PaymentGroup::PRODUCT): self
    {
        $myRequest = new CreatePaymentRequest();
        $myRequest->setLocale($paymentLocale);
        $myRequest->setConversationId($basketID);
        $myRequest->setPrice($price);
        $myRequest->setPaidPrice($paidPrice);
        $myRequest->setCurrency($paymentCurrency);
        $myRequest->setInstallment($installmentsCount);
        $myRequest->setBasketId($conversationID);
        $myRequest->setPaymentChannel($paymentChannel);
        $myRequest->setPaymentGroup($paymentGroup);
        $this->myPaymentRequest = $myRequest;
        return $this;
    }


    public function checkout(): Payment|null
    {
        try {

            $myPaymentRequest = $this->myPaymentRequest;
            $myPaymentCard = $this->paymentCard;
            $myBuyer = $this->basketBuyer;
            $myBuyerAddress = $this->buyerAddress;

            // Get Iyzico Configuration
            $myOptions = $this->getIyzipayOptions();

            // Prepare Request
            $myPaymentRequest->setPaymentCard($myPaymentCard);
            $myPaymentRequest->setBuyer($myBuyer);
            $myPaymentRequest->setBasketItems($this->baskedItems);
            $myPaymentRequest->setShippingAddress($myBuyerAddress);
            $myPaymentRequest->setBillingAddress($myBuyerAddress);

            // Make Payment & Return
            return Payment::create($myPaymentRequest, $myOptions);

        } catch (Exception $exception) {
            return NULL;
        }
    }
}