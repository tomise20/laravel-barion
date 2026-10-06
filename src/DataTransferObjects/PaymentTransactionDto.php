<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Tomise\Barion\Utils\Amount;

/**
 * A transaction of a payment (Barion: PaymentTransactionModel).
 */
class PaymentTransactionDto implements Arrayable
{
    private string $postTransactionId;
    private string $payee;
    private float $total;
    private ?string $comment = null;

    /**
     * @var Collection<int, TransactionItemDto|array>
     */
    private Collection $items;

    public function __construct()
    {
        $this->items = new Collection;
    }

    public function getPostTransactionId(): string
    {
        return $this->postTransactionId;
    }

    /**
     * Your own, unique identifier of the transaction (Barion: POSTransactionId).
     */
    public function setPostTransactionId(string $id): PaymentTransactionDto
    {
        $this->postTransactionId = $id;

        return $this;
    }

    public function getPayee(): string
    {
        return $this->payee;
    }

    public function setPayee(string $email): PaymentTransactionDto
    {
        $this->payee = $email;

        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): PaymentTransactionDto
    {
        $this->total = $total;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): PaymentTransactionDto
    {
        $this->comment = $comment;

        return $this;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param  Collection<int, TransactionItemDto|array>|array<int, TransactionItemDto|array>  $items
     */
    public function setItems(Collection|array $items): PaymentTransactionDto
    {
        $this->items = Collection::make($items);

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'POSTransactionId' => $this->postTransactionId,
            'Payee' => $this->payee,
            'Total' => Amount::normalize($this->total),
            'Comment' => $this->comment,
            'Items' => $this->items
                ->map(fn (TransactionItemDto|array $item): array => is_array($item) ? $item : $item->toArray())
                ->values()
                ->all(),
        ], fn (mixed $value): bool => $value !== null);
    }
}
