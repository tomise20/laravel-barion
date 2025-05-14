<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setSwiftCode(string $swiftCode)
 */
class BankDto
{
    use Arrayable, HasSetter;

    public string $swiftCode;
}
