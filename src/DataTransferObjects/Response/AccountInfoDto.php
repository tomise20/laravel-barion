<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;


class AccountInfoDto
{

    use Arrayable;

    public string $iban;
    public string $reference;
    public string $swiftCode;
}
