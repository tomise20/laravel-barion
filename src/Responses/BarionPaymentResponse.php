<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\Contracts\PaymentResponse;
use Tomise\Barion\DataTransferObjects\Response\ProcessedTransactionDto;
use Tomise\Barion\Enums\BarionStatus;

/**
 * Response of Payment/Start. Errors are thrown as BarionPaymentException by the adapter, so a response object is
 * always a started payment.
 */
class BarionPaymentResponse implements PaymentResponse
{
    public readonly bool $isSuccess;
    public readonly ?string $currency;
    public readonly ?BarionStatus $status;

    private ?string $paymentId;
    private ?string $paymentRequestId;
    private ?string $transactionId;
    private ?string $gatewayUrl;
    private ?string $traceId;
    private ?string $qrUrl;
    private ?string $recurrenceResult;
    private array $error;

    /**
     * @var Collection<int, ProcessedTransactionDto>
     */
    private Collection $transactions;

    public function __construct(private readonly array $raw)
    {
        $this->error = $raw['Errors'] ?? [];
        $this->isSuccess = empty($this->error);
        $this->paymentId = $raw['PaymentId'] ?? null;
        $this->paymentRequestId = $raw['PaymentRequestId'] ?? null;
        $this->status = BarionStatus::tryFrom((string) ($raw['Status'] ?? ''));
        $this->transactions = Collection::make($raw['Transactions'] ?? [])
            ->map(fn (array $transaction): ProcessedTransactionDto => ProcessedTransactionDto::fromArray($transaction))
            ->values();
        $this->transactionId = $this->transactions->first()?->transactionId;
        $this->currency = $this->transactions->first()?->currency ?? ($raw['Currency'] ?? null);
        $this->gatewayUrl = $raw['GatewayUrl'] ?? ($this->paymentId ? BarionAdapter::gatewayUrl($this->paymentId) : null);
        $this->traceId = $raw['TraceId'] ?? null;
        $this->qrUrl = $raw['QRUrl'] ?? null;
        $this->recurrenceResult = $raw['RecurrenceResult'] ?? null;
    }

    public function getPaymentId(): string
    {
        return (string) $this->paymentId;
    }

    public function getPaymentRequestId(): string
    {
        return (string) $this->paymentRequestId;
    }

    public function getStatus(): ?BarionStatus
    {
        return $this->status;
    }

    public function getGatewayUrl(): string
    {
        return (string) $this->gatewayUrl;
    }

    /**
     * The Barion identifier of the first transaction (needed for refunds, finishing and capturing).
     */
    public function getTransactionId(): string
    {
        return (string) $this->transactionId;
    }

    /**
     * @return Collection<int, ProcessedTransactionDto>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * Keep it for the merchant initiated payments of a recurring payment.
     */
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }

    public function getQrUrl(): ?string
    {
        return $this->qrUrl;
    }

    /**
     * Result of a recurring payment: None, Successful, Failed, NotFound or ThreeDSAuthenticationRequired.
     */
    public function getRecurrenceResult(): ?string
    {
        return $this->recurrenceResult;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getErrorMessage(): string
    {
        return (string) ($this->error[0]['Description'] ?? $this->error[0]['Title'] ?? '');
    }

    public function getRaw(): array
    {
        return $this->raw;
    }
}
