<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Psr\Http\Message\ResponseInterface;
use Tomise\Barion\DataTransferObjects\AccountDto;
use Tomise\Barion\DataTransferObjects\HistoryDto;

class BarionWalletResponse
{
    public bool $isSuccess;
    public array $error;

    private ?AccountDto $account = null;
    private ?HistoryDto $history = null;

    public function __construct(ResponseInterface $response)
    {
        $responseBody = json_decode($response->getBody()->getContents(), true);

        $this->checkIsSuccess($responseBody);
        $this->convertResponse($responseBody);
    }

    private function convertResponse(array $response): void
    {
        if($this->isSuccess) {
           
        } else {
            $this->error = $response['Errors'];
        }
    }

    private function checkIsSuccess(array $response): void
    {
        $this->isSuccess = empty($response['Errors']);
    }

    public function getAccount(): AccountDto
    {
        return $this->account;
    }

    public function getHistory(): HistoryDto
    {
        return $this->history;
    }
}