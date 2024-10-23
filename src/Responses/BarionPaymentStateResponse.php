<?php

namespace Tomise\Barion\Responses;

use Psr\Http\Message\ResponseInterface;
use Tomise\Barion\Enums\BarionStatus;

class BarionPaymentStateResponse
{
    public readonly bool $isSuccess;
    public readonly string $currency;
    public readonly BarionStatus $status;
    public readonly string $completedAt;

    private string $paymentId;
    private string $paymentRequestId;
    private string $transactionId;

    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody()->getContents(), true);
        
        $this->checkIsSuccess($responseBody);
        $this->convertResponse($responseBody);
    }

    private function convertResponse(array $response): void
    {
        $this->paymentId = $response['PaymentId'];
        $this->paymentRequestId = $response['PaymentRequestId'];
        $this->status = BarionStatus::from($response['Status']);
        $this->transactionId = $response['Transactions'][0]['TransactionId'];
        $this->currency = $response['Transactions'][0]['Currency'];
        $this->completedAt = $response['CompletedAt'];

    }

    private function checkIsSuccess(array $response): void
    {
        $this->isSuccess = BarionStatus::from($response['Status']) === BarionStatus::Succeeded;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getPaymentRequestId(): string
    {
        return $this->paymentRequestId;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}