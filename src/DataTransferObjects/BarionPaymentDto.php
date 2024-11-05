<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;
use Tomise\Barion\Enums\RecurrenceType;

class BarionPaymentDto implements Arrayable {
    private string $paymentType;
    private string $reservationPeriod;
    private string $paymentWindow;
    private bool $guestCheckOut = true;
    private array $fundingSources;
    private string $paymentRequestId;
    private ?string $payerHint = null;
    private ?string $cardHolderNameHint = null;
    private ?int $recurrenceType = null;
    private ?string $traceId = null;
    private ?AddressDto $shippingAddress = null;
    private string $redirectUrl;
    private string $callbackUrl;
    private array $transactions;
    private string $orderNumber;
    private Locale $locale;
    private Currency $currency;
    private ?string $payerPhoneNumber = null;
    private ?string $payerWorkPhoneNumber = null;
    private ?string $payerHomePhoneNumber = null;
    private string $phoneNumber;
    private AddressDto $billingAddress;
    private ?PurchaseInformation $purchaseInformation = null;

    private ?string $paymentId = null;

    public function __construct(private readonly string $posKey)
    {
    }

    public function getPosKey(): string
    {
        return $this->posKey;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): BarionPaymentDto

    {
        $this->paymentType = $paymentType;

        return $this;
    }

    public function getReservationPeriod(): string
    {
       return $this->reservationPeriod;
    }

    public function setReservationPeriod(string $reservationPeriod): BarionPaymentDto

    {
        $this->reservationPeriod = $reservationPeriod;

        return $this;
    }

    public function getPaymentWindow(): string
    {
        return $this->paymentWindow;
    }

    public function setPaymentWindow(string $paymentWindow): BarionPaymentDto

    {
        $this->paymentWindow = $paymentWindow;

        return $this;
    }

    public function getGuestCheckout(): bool
    {
        return $this->guestCheckOut;
    }

    public function setGuestCheckout(bool $guestCheckOut): BarionPaymentDto

    {
        $this->guestCheckOut = $guestCheckOut;

        return $this;
    }

    public function getFundingSources(): array
    {
        return $this->fundingSources;
    }

    public function setFundingSources(array $fundingSources): BarionPaymentDto

    {
        $this->fundingSources = $fundingSources;

        return $this;
    }

    public function getPaymentRequestId(): string
    {
        return $this->paymentRequestId;
    }

    public function setPaymentRequestId(string $paymentRequestId): BarionPaymentDto

    {
        $this->paymentRequestId = $paymentRequestId;

        return $this;
    }

    public function getPayerHint(): ?string
    {
        return $this->payerHint;
    }

    public function setPayerHint(string $payerHint): BarionPaymentDto

    {
        $this->payerHint = $payerHint;

        return $this;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(string $url): BarionPaymentDto

    {
        $this->redirectUrl = $url;

        return $this;
    }

    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    public function setCallbackUrl(string $url): BarionPaymentDto

    {
        $this->callbackUrl = $url;

        return $this;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function setTransactions(array $transactions): BarionPaymentDto

    {
        $this->transactions = $transactions;

        return $this;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): BarionPaymentDto

    {
        $this->orderNumber = $orderNumber;

        if(config('barion-gateway.sync_payment_request_id')) {
            $this->setPaymentRequestId($orderNumber);
        }

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale->value;
    }

    public function setLocale(Locale $locale): BarionPaymentDto

    {
        $this->locale = $locale;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function setCurrency(Currency $currency): BarionPaymentDto

    {
        $this->currency = $currency;

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): BarionPaymentDto

    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBillingAddress(): AddressDto
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(AddressDto $billingAddress): BarionPaymentDto

    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): BarionPaymentDto
    {
        $this->paymentId = $paymentId;

        return $this;
    }
    
    public function isGuestCheckOut(): bool
    {
        return $this->guestCheckOut;
    }
    
    public function getCardHolderNameHint(): ?string
    {
        return $this->cardHolderNameHint;
    }
    
    public function setCardHolderNameHint(?string $cardHolderNameHint): BarionPaymentDto
    {
        $this->cardHolderNameHint = $cardHolderNameHint;
        
        return $this;
    }
    
    public function getRecurrenceType(): ?int
    {
        return $this->recurrenceType;
    }
    
    public function setRecurrenceType(int|RecurrenceType $recurrenceType): BarionPaymentDto
    {
        if($recurrenceType instanceof RecurrenceType) {
            $recurrenceType = $recurrenceType->value;
        } else {
            $this->recurrenceType = $recurrenceType;
        }

        return $this;
    }
    
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }
    
    public function setTraceId(?string $traceId): BarionPaymentDto
    {
        $this->traceId = $traceId;

        return $this;
    }
    
    public function getShippingAddress(): ?AddressDto
    {
        return $this->shippingAddress;
    }
    
    public function setShippingAddress(?AddressDto $shippingAddress): BarionPaymentDto
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }
    
    public function getPayerPhoneNumber(): ?string
    {
        return $this->payerPhoneNumber;
    }
    
    public function setPayerPhoneNumber(?string $payerPhoneNumber): BarionPaymentDto
    {
        $this->payerPhoneNumber = $payerPhoneNumber;

        return $this;
    }
    
    public function getPayerWorkPhoneNumber(): ?string
    {
        return $this->payerWorkPhoneNumber;
    }
    
    public function setPayerWorkPhoneNumber(?string $payerWorkPhoneNumber): BarionPaymentDto
    {
        $this->payerWorkPhoneNumber = $payerWorkPhoneNumber;

        return $this;
    }
    
    public function getPayerHomePhoneNumber(): ?string
    {
        return $this->payerHomePhoneNumber;
    }
    
    public function setPayerHomePhoneNumber(?string $payerHomePhoneNumber): BarionPaymentDto
    {
        $this->payerHomePhoneNumber = $payerHomePhoneNumber;

        return $this;
    }

    public function getPurchaseInformation(): PurchaseInformation
    {
        return $this->purchaseInformation;
    }

    public function setPurchaseInformation(PurchaseInformation $purchaseInformation): BarionPaymentDto
    {
        $this->purchaseInformation = $purchaseInformation;

        return $this;
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $result[$key] = $value->toArray();
            } elseif (is_array($value)) {
                $result[$key] = array_map(fn($item) => method_exists($item, 'toArray') ? $item->toArray() : $item, $value);
            } elseif (is_object($value) && method_exists($value, 'getValue')) {
                $result[$key] = $value->getValue();
            } elseif($value !== null) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

}