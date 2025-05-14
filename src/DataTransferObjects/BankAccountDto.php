<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setCountry(string $country)
 * @method self setFormat(int $format)
 * @method self setAccountNumber(string $accountNumber)
 */
class BankAccountDto
{

    use Arrayable, HasSetter;

    public string $country;
    public int $format;
    public string $accountNumber;
}
