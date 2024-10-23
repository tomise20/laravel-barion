<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;

class BarionPaymentDto implements Arrayable {
    private string $posKey;
    private string $paymentType;
    private string $reservationPeriod;
    private string $paymentWindow;
    private bool $guestCheckOut = true;
    private array $fundingSources;
    private string $paymentRequestId;
    private ?string $payerHint = null;
    private string $redirectUrl;
    private string $callbackUrl;
    private array $transactions;
    private string $orderNumber;
    private Locale $locale;
    private Currency $currency;
    private string $phoneNumber;
    private AddressDto $billingAddress;

    private ?string $paymentId = null;

    public function getPosKey(): string
    {
        return $this->posKey;
    }

    public function setPosKey(string $posKey): BarionPaymentDto

    {
        $this->posKey = $posKey;

        return $this;
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

    public function toArray(): array
    {
        $result = [];
        foreach ($this as $key => $value) {
            if (is_object($value) && method_exists($value, 'toArray')) {
                $result[$key] = $value->toArray();
            } elseif (is_object($value) && method_exists($value, 'getValue')) {
                $result[$key] = $value->getValue();
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
}