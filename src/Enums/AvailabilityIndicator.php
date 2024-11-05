<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum AvailabilityIndicator: string {
    case MerchandiseAvailable = 'MerchandiseAvailable';
    case FutureAvailability = 'FutureAvailability';
}