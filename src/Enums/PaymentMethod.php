<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum PaymentMethod: string {
    case Unknown = 'Unknown';
    case BarionWallet = 'BarionWallet';
    case BankCard = 'BankCard';
    case GooglePay = 'GooglePay';
    case ApplePay = 'ApplePay';
    case OpenBanking = 'OpenBanking';
    case Other = 'Other';
}