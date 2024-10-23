<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Enums\BarionWalletEndpoint;
use Tomise\Barion\Responses\BarionWalletResponse;

class BarionWalletService
{
    private BarionAdapter $adapter;

    public function __construct(private readonly BarionWalletDto $walletDto)
    {
        $this->adapter = new BarionAdapter();
    }

    public function getAccounts(): BarionWalletResponse
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::Accounts);
    }

    public function getHistory(): BarionWalletResponse
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::GetHistory);
    }

    public function bankTransfer(): BarionWalletResponse
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::Withdraw);
    }

    public function download(): string
    {
        return $this->adapter->sendDownload($this->walletDto);
    }

    public function sendMoney(): BarionWalletResponse
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::SendMoney);
    }
}