<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\AddressDto;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Responses\BarionPaymentResponse;

class PreparedPaymentService
{
    private BarionAdapter $adapter;

    public function __construct(
        private BarionPaymentDto $paymentDto,
    )
    {
        $this->adapter = new BarionAdapter();
    }

    public function sendSinglePayment(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::PaymentStart);
    }

    public function sendPaymentState(string $paymentId): BarionPaymentResponse
    {
        $path = BarionGatewayEndpoint::PaymentState->value;
        $path = str_replace(':paymentId', $paymentId, $path);

        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::PaymentState);
    }

    public function sendCompletePayment(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Complete);
    }

    public function sendFinishReservation(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::FinishReservation);
    }

    public function sendCapture(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Capture);
    }

    public function sendCancelAuthorization(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::CancelAuthorization);
    }

    public function sendRefund(): BarionPaymentResponse
    {
        return $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Refound);
    }

    public function setPaymentId(string $paymentId): self
    {
        $this->paymentDto->setPaymentId($paymentId);

        return $this;
    }

    public function getTransactions(): array
    {
        return $this->paymentDto->getTransactions();
    }

    public function setTransactions(array $transactions): self
    {
        $this->paymentDto->setTransactions($transactions);

        return $this;
    }

    public function getOrderNumber(): string
    {
        return $this->paymentDto->getOrderNumber();
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->paymentDto->setOrderNumber($orderNumber);

        return $this;
    }

    public function getLocale(): string
    {
        return $this->paymentDto->getLocale();
    }

    public function setLocale(Locale $locale): self
    {
        $this->paymentDto->setLocale($locale);

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->paymentDto->getCurrency();
    }

    public function setCurrency(Currency $currency): self
    {
        $this->paymentDto->setCurrency($currency);

        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->paymentDto->getPhoneNumber();
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->paymentDto->setPhoneNumber($phoneNumber);

        return $this;
    }

    public function getBillingAddress(): AddressDto
    {
        return $this->paymentDto->getBillingAddress();
    }

    public function setBillingAddress(AddressDto $billingAddress): self
    {
        $this->paymentDto->setBillingAddress($billingAddress);

        return $this;
    }
}