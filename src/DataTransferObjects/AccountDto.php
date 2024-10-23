<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

class AccountDto
{
    public string $accountId;
    public BalanceDto $balance;
    public string $currency;
    public AccountInfoDto $accountInfo;
}