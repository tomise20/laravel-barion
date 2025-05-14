<?php

namespace Tomise\Barion\Responses;

use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Contracts\PaymentResponse;
use Tomise\Barion\Enums\BarionStatus;

class BarionPaymentResponse implements PaymentResponse
{
    public readonly bool $isSuccess;
    public readonly string $currency;

    #[MapTo(BarionStatus::class)]
    public readonly BarionStatus $status;

    private ?string $paymentId = null;
    private ?string $paymentRequestId = null;
    private ?string $transactionId = null;
    private ?string $gatewayUrl = null;
    private ?string $traceId = null;
    private ?string $qrUrl = null;

    private ?array $error = null;

    public function __construct(array $response)
    {
        $this->checkIsSuccess($response);
        $this->convertResponse($response);
    }

    private function convertResponse(array $response): void
    {
        if($this->isSuccess) {
            $this->paymentId = $response['PaymentId'];
            $this->paymentRequestId = $response['PaymentRequestId'];
            $this->status = BarionStatus::from($response['Status']);
            $this->transactionId = $response['Transactions'][0]['TransactionId'];
            $this->gatewayUrl = $response['GatewayUrl'];
            $this->currency = $response['Transactions'][0]['Currency'];
            $this->traceId = $response['TraceId'] ?? null;
            $this->qrUrl = $response['QRUrl'];
        } else {
            $this->error = $response['Errors'];
        }
    }

    private function checkIsSuccess(array $response): void
    {
        $this->isSuccess = empty($response['Errors']);
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getPaymentRequestId(): string
    {
        return $this->paymentRequestId;
    }

    public function getGatewayUrl(): string
    {
        return $this->gatewayUrl;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getTraceId(): string
    {
        return $this->traceId;
    }

    public function getQrUrl(): string
    {
        return $this->qrUrl;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getErrorMessage(): string
    {
        return $this->error['Description'];
    }
}