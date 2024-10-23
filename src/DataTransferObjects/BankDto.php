<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class BankDto implements Arrayable
{
    public string $swiftCode;

    public function toArray(): array
    {
        return [
            'SwiftCode' => $this->swiftCode,
        ];
    }
}