<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum BarionWalletEndpoint: string {
    case Accounts = 'v2/Accounts';
    case GetHistory = '/v3/UserHistory/GetHistory';
    case Withdraw = 'v3/Withdraw/BankTransfer';
    case Download = 'v2/Statement/Download';
    case SendMoney = 'v2/Transfer/Email';

    public static function getGETEndpoints(): array {
        return [
            self::Accounts,
            self::GetHistory,
            self::Download
        ];
    }

    public function isGetEndpoint(): bool
    {
        return in_array($this, self::getGETEndpoints());
    }
}