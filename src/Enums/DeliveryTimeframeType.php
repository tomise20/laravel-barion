<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum DeliveryTimeframeType: string {
    case ElectronicDelivery = 'ElectronicDelivery';
    case SameDayShipping = 'SameDayShipping';
    case OvernightShipping = 'OvernightShipping';
    case TwoDayOrMoreShipping = 'TwoDayOrMoreShipping';
}