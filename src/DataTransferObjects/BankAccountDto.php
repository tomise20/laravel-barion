<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class BankAccountDto implements Arrayable
{
    public string $country;
    public int $format;
    public string $accountNumber;

    public function toArray(): array
    {
        return [
            'Country' => $this->country,
            'Format' => $this->format,
            'AccountNumber' => $this->accountNumber,
        ];
    }
}