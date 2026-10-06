<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Tomise\Barion\Utils\Amount;

/**
 * A transaction of a completed payment to refund, fully or partially (Barion: TransactionToRefundModel).
 */
final readonly class TransactionToRefundDto implements Arrayable
{
    /**
     * @param  string  $transactionId  the Barion identifier of the transaction
     * @param  string  $posTransactionId  your identifier of the transaction (POSTransactionId of the payment)
     * @param  float  $amountToRefund  at most the transaction's total
     */
    public function __construct(
        public string $transactionId,
        public string $posTransactionId,
        public float $amountToRefund,
        public ?string $comment = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'TransactionId' => $this->transactionId,
            'POSTransactionId' => $this->posTransactionId,
            'AmountToRefund' => Amount::normalize($this->amountToRefund),
            'Comment' => $this->comment,
        ], fn (mixed $value): bool => $value !== null);
    }
}
