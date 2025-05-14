<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setName(string $name)
 * @method self setAddress(\Tomise\Barion\DataTransferObjects\AddressDto $address)
 */
class RecipientDto
{

    use Arrayable, HasSetter;

    public string $name;
    public AddressDto $address;
}
