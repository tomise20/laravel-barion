<?php

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class TransactionItemDto implements Arrayable
{
    private string $name;
    private string $description;
    private int $quantity;
    private string $unit = 'db';
    private float $unitPrice;
    private float $itemTotal;
    private ?string $imageUrl = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TransactionItemDto
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): TransactionItemDto
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $url): TransactionItemDto
    {
        $this->imageUrl = $url;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $qty): TransactionItemDto
    {
        $this->quantity = $qty;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): TransactionItemDto
    {
        $this->unit = $unit;

        return $this;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): TransactionItemDto
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getItemTotal(): float
    {
        return $this->itemTotal;
    }

    public function setItemTotal(float $itemTotal): TransactionItemDto
    {
        $this->itemTotal = $itemTotal;

        return $this;
    }

	public function toArray(): array
    {
        return [
            'Name' => $this->name,
            'Description' => $this->description,
            'ImageUrl' => $this->imageUrl,
            'Quantity' => $this->quantity,
            'Unit' => $this->unit,
            'UnitPrice' => $this->unitPrice,
            'ItemTotal' => $this->itemTotal
        ];
    }
}
