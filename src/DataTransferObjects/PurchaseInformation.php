<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Enums\AvailabilityIndicator;
use Tomise\Barion\Enums\DeliveryTimeframeType;
use Tomise\Barion\Enums\PurchaseType;
use Tomise\Barion\Enums\ShippingAddressIndicator;
use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setDeliveryTimeframeType(\Tomise\Barion\Enums\DeliveryTimeframeType $deliveryTimeframeType)
 * @method self setDeliveryEmailAddress(string $deliveryEmailAddress)
 * @method self setPreOrderDate(string $preOrderDate)
 * @method self setReOrderIndicator(\Tomise\Barion\Enums\AvailabilityIndicator $reOrderIndicator)
 * @method self setShippingAddressIndicator(\Tomise\Barion\Enums\ShippingAddressIndicator $shippingAddressIndicator)
 * @method self setRecurringExpiry(string $recurringExpiry)
 * @method self setRecurringFrequency(int $recurringFrequency)
 * @method self setPurchaseType(\Tomise\Barion\Enums\PurchaseType $purchaseType)
 * @method self setGiftCardPurchase(array $giftCardPurchase)
 * @method self setPurchaseDate(string $purchaseDate)
 */
class PurchaseInformation
{

    use Arrayable, HasSetter;

    public DeliveryTimeframeType $deliveryTimeframeType;
    public string $deliveryEmailAddress;
    public string $preOrderDate;
    public AvailabilityIndicator $reOrderIndicator;
    public ShippingAddressIndicator $shippingAddressIndicator;
    public string $recurringExpiry;
    public int $recurringFrequency;
    public PurchaseType $purchaseType;
    public array $giftCardPurchase;
    public string $purchaseDate;
}
