<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;
use Tomise\Barion\Utils\Amount;

/**
 * An item of a payment transaction (Barion: ItemModel). Without an item total, quantity × unit price is sent.
 *
 * @method self setName(string $name)
 * @method self setDescription(string $description)
 * @method self setQuantity(float $quantity)
 * @method self setUnit(string $unit)
 * @method self setUnitPrice(float $unitPrice)
 * @method self setItemTotal(float $itemTotal)
 * @method self setSku(string $sku)
 * @method self setImageUrl(string $imageUrl)
 */
class TransactionItemDto
{
    use Arrayable { toArray as private propertiesToArray; }
    use HasSetter;

    public string $name;
    public string $description;
    public float $quantity = 1;
    public string $unit = 'db';
    public float $unitPrice;
    public float $itemTotal;
    public ?string $sku = null;
    public ?string $imageUrl = null;

    public function toArray(): array
    {
        $data = $this->propertiesToArray();

        if (! isset($data['ItemTotal']) && isset($this->unitPrice)) {
            $data['ItemTotal'] = Amount::normalize($this->quantity * $this->unitPrice);
        }

        if (isset($data['Sku'])) {
            $data['SKU'] = $data['Sku'];
            unset($data['Sku']);
        }

        return $data;
    }
}
