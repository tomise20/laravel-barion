<?php

declare(strict_types=1);

namespace Tomise\Barion\Utils;

/**
 * Amounts in the requests: whole numbers are sent as integers (e.g. 16500, not 16500.0), as HUF has no decimals.
 */
final class Amount
{
    public static function normalize(int|float $amount): int|float
    {
        return floor($amount) == $amount ? (int) $amount : round($amount, 2);
    }
}
