<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

class AccountInfoDto
{
    public string $iban;
    public string $reference;
    public string $swiftCode;
}