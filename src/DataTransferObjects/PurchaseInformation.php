<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Enums\AvailabilityIndicator;
use Tomise\Barion\Enums\DeliveryTimeframeType;
use Tomise\Barion\Enums\PurchaseType;
use Tomise\Barion\Enums\ShippingAddressIndicator;

class PurchaseInformation
{
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

    public function toArray(): array
    {
        return [
            'DeliveryTimeframeType' => $this->deliveryTimeframeType->value,
            'DeliveryEmailAddress' => $this->deliveryEmailAddress,
            'PreOrderDate' => $this->preOrderDate,
            'ReOrderIndicator' => $this->reOrderIndicator->value,
            'ShippingAddressIndicator' => $this->shippingAddressIndicator->value,
            'RecurringExpiry' => $this->recurringExpiry,
            'RecurringFrequency' => $this->recurringFrequency,
            'PurchaseType' => $this->purchaseType->value,
            'GiftCardPurchase' => $this->giftCardPurchase,
            'PurchaseDate' => $this->purchaseDate,
        ];
    }
}