<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Tomise\Barion\Utils\Amount;

/**
 * A transaction of a Reservation payment to finish, or of a DelayedCapture payment to capture, with the final amount
 * (Barion: TransactionToFinishModel / TransactionToCaptureModel). The final total can be lower than the original;
 * 0 releases the transaction.
 */
final readonly class TransactionToFinishDto implements Arrayable
{
    /**
     * @param  string  $transactionId  the Barion identifier of the transaction
     * @param  array<int, TransactionItemDto|array>  $items  the final items (optional)
     */
    public function __construct(
        public string $transactionId,
        public float $total,
        public ?string $comment = null,
        public array $items = [],
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'TransactionId' => $this->transactionId,
            'Total' => Amount::normalize($this->total),
            'Comment' => $this->comment,
            'Items' => $this->items === [] ? null : array_map(
                fn (TransactionItemDto|array $item): array => is_array($item) ? $item : $item->toArray(),
                $this->items,
            ),
        ], fn (mixed $value): bool => $value !== null);
    }
}
