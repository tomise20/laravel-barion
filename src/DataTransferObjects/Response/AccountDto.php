<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;


class AccountDto
{
    use Arrayable;

    public string $accountId;
    public BalanceDto $balance;
    public string $currency;
    public AccountInfoDto $accountInfo;
}
