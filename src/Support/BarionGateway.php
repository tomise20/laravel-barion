<?php

namespace Tomise\Barion\Support;

use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Services\BarionPaymentService;
use Tomise\Barion\Services\BarionWalletService;

class BarionGateway
{
    public static function createPaymentGateway(): BarionPaymentService
    {
        return new BarionPaymentService();
    }

    public static function createWalletGateway(BarionWalletDto $walletDto): BarionWalletService
    {
        return new BarionWalletService($walletDto);
    }
}
