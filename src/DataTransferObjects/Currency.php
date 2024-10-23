<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

enum Currency: string {
    case Huf = 'HUF';
    case Eur = 'EUR';
    case Czk = 'CZK';
    case Usd = 'USD';

    public function getValue(): string
    {
        return $this->value;
    }
}