<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum ShippingAddressIndicator: string {
    case ShipToCardholdersBillingAddress = 'ShipToCardholdersBillingAddress';
    case ShipToAnotherVerifiedAddress = 'ShipToAnotherVerifiedAddress';
    case ShipToDifferentAddress = 'ShipToDifferentAddress';
    case ShipToStore = 'ShipToStore';
    case DigitalGoods = 'DigitalGoods';
    case TravelAndEventTickets = 'TravelAndEventTickets';
    case Other = 'Other';
}