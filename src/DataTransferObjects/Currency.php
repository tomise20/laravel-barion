<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

enum Currency: string
{
    case Huf = 'HUF';
    case Eur = 'EUR';
    case Czk = 'CZK';
    case Usd = 'USD';
    case Ron = 'RON';
    case Pln = 'PLN';

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Number of decimals Barion accepts in the amounts (HUF has none).
     */
    public function decimals(): int
    {
        return $this === self::Huf ? 0 : 2;
    }
}
