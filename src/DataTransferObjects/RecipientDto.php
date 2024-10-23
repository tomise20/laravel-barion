<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class RecipientDto implements Arrayable
{
    public string $name;
    public AddressDto $address;

    public function toArray(): array
    {
        return [
            'Name' => $this->name,
            'Address' => $this->address->toArray(),
        ];
    }
}