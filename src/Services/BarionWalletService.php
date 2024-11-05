<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Enums\BarionWalletEndpoint;
use Tomise\Barion\Responses\BarionAccountResponse;
use Tomise\Barion\Responses\BarionHistoryResponse;
use Tomise\Barion\Responses\BarionWalletResponse;

class BarionWalletService
{
    private BarionAdapter $adapter;

    public function __construct(private readonly BarionWalletDto $walletDto)
    {
        $this->adapter = new BarionAdapter();
    }

    public function getAccounts(): BarionAccountResponse
    {
        $response = $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::Accounts);

        return BarionAccountResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function getHistory(): BarionHistoryResponse
    {
        $response = $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::GetHistory);

        return BarionHistoryResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function bankTransfer(): mixed
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::Withdraw);
    }

    public function download(): string
    {
        return $this->adapter->sendDownload($this->walletDto);
    }

    public function sendMoney(): mixed
    {
        return $this->adapter->sendWalletRequest($this->walletDto, BarionWalletEndpoint::SendMoney);
    }
}