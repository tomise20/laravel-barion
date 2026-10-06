<?php

declare(strict_types=1);

namespace Tomise\Barion\Support;

use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Services\BarionPaymentService;
use Tomise\Barion\Services\BarionWalletService;

class BarionGateway
{
    /**
     * Resolved from the container, so the BarionAdapter binding (e.g. a mocked HTTP client in tests) applies.
     */
    public static function createPaymentGateway(): BarionPaymentService
    {
        return app(BarionPaymentService::class);
    }

    public static function createWalletGateway(BarionWalletDto $walletDto): BarionWalletService
    {
        return new BarionWalletService($walletDto);
    }
}
