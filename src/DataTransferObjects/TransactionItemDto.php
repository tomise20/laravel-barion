<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Support\Traits\Macroable;
use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setName(string $name)
 * @method self setDescription(string $description)
 * @method self setQuantity(int $quantity)
 * @method self setUnit(string $unit)
 * @method self setUnitPrice(float $unitPrice)
 * @method self setItemTotal(float $itemTotal)
 * @method self setImageUrl(string $imageUrl)
 */
class TransactionItemDto
{
    use Macroable, Arrayable, HasSetter;

	public function __construct()
	{
		self::bootHasSetters();
	}

    public string $name;
    public string $description;
    public int $quantity;
    public string $unit = 'db';
    public float $unitPrice;
    public float $itemTotal;
    public ?string $imageUrl = null;
}
