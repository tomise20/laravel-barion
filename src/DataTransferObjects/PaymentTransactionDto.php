<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PaymentTransactionDto implements Arrayable
{
    private string $postTransactionId;
    private string $payee;
    private float $total;
    private ?string $comment;
    private Collection $items;

    public function getPostTransactionId(): string
    {
        return $this->postTransactionId;
    }

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

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setItems(Collection $items): PaymentTransactionDto
    {
        $this->items = $items;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'POSTransactionId' => $this->postTransactionId,
            'Payee' => $this->payee,
            'Total' => $this->total,
            'Items' => $this->items->toArray()
        ];
    }
}