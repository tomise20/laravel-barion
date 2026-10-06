<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use DateTimeInterface;
use Illuminate\Support\Collection;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Attributes\MapToCollection;
use Tomise\Barion\Attributes\MapToDateTime;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Response\FundingInformationDto;
use Tomise\Barion\DataTransferObjects\Response\ProcessedTransactionDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Enums\PaymentType;
use Tomise\Barion\Enums\RecurrenceType;
use Tomise\Barion\Enums\TransactionStatus;
use Tomise\Barion\Utils\TransformHelper;

/**
 * Response of the v4 PaymentState request.
 */
class BarionPaymentStatusResponse
{
    /**
     * Transaction types of the money paid by the customer (card, Barion wallet, bank transfer, reservation).
     */
    private const PAYMENT_TRANSACTION_TYPES = ['CardPayment', 'Shop', 'BankTransferPayment', 'Reserve'];

    public ?string $paymentId = null;
    public ?string $paymentRequestId = null;
    public ?string $orderNumber = null;
    public ?string $posId = null;
    public ?string $posName = null;
    public ?string $posOwnerEmail = null;
    public ?string $posOwnerCountry = null;

    #[MapTo(BarionStatus::class)]
    public ?BarionStatus $status = null;

    #[MapTo(PaymentType::class)]
    public ?PaymentType $paymentType = null;

    public ?array $allowedFundingSources = null;
    public ?string $fundingSource = null;

    #[MapTo(FundingInformationDto::class)]
    public ?FundingInformationDto $fundingInformation = null;

    public ?bool $guestCheckout = null;

    #[MapTo(RecurrenceType::class)]
    public ?RecurrenceType $recurrenceType = null;

    /**
     * @var Collection<int, ProcessedTransactionDto>
     */
    #[MapToCollection(itemType: ProcessedTransactionDto::class)]
    public Collection $transactions;

    public ?string $traceId = null;

    #[MapToDateTime]
    public ?DateTimeInterface $createdAt = null;

    #[MapToDateTime]
    public ?DateTimeInterface $validUntil = null;

    #[MapToDateTime]
    public ?DateTimeInterface $completedAt = null;

    #[MapToDateTime]
    public ?DateTimeInterface $reservedUntil = null;

    #[MapToDateTime]
    public ?DateTimeInterface $delayedCaptureUntil = null;

    public ?float $total = null;

    #[MapTo(Currency::class)]
    public ?Currency $currency = null;

    public ?string $suggestedLocale = null;
    public ?float $fraudRiskScore = null;
    public ?string $redirectUrl = null;
    public ?string $callbackUrl = null;
    public ?string $paymentMethod = null;

    public function __construct()
    {
        $this->transactions = new Collection;
    }

    public static function createFromArray(array $rawResponse): BarionPaymentStatusResponse
    {
        return TransformHelper::transformArray(new self, $rawResponse);
    }

    public function isSucceeded(): bool
    {
        return $this->status === BarionStatus::Succeeded;
    }

    /**
     * The customer's payment transactions; Barion also lists fee, refund and storno transactions here.
     *
     * @return Collection<int, ProcessedTransactionDto>
     */
    public function paymentTransactions(): Collection
    {
        return $this->transactions
            ->filter(fn (ProcessedTransactionDto $transaction): bool => in_array($transaction->transactionType, self::PAYMENT_TRANSACTION_TYPES, true)
                && $transaction->status !== TransactionStatus::Reversed)
            ->values();
    }

    /**
     * The payment transaction of one of your POSTransactionIds, e.g. to refund it.
     */
    public function transaction(string $posTransactionId): ?ProcessedTransactionDto
    {
        return $this->paymentTransactions()->first(fn (ProcessedTransactionDto $transaction): bool => $transaction->posTransactionId === $posTransactionId);
    }
}
