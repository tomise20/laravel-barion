<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\DataTransferObjects\Response\UserHistoryParticipantDto;
use Tomise\Barion\Utils\TransformHelper;

class BarionHistoryResponse {
    public string $id;
    public string $historyType;
    public string $happenedAtUtc;
    public int $concurrencyOrder;

    #[MapTo(UserHistoryParticipantDto::class)]
    public UserHistoryParticipantDto $sourceAccount;

    #[MapTo(UserHistoryParticipantDto::class)]
    public UserHistoryParticipantDto $targetAccount;
    
    public float $amount;
    public string $currency;
    public string $description;
    public bool $isInProgress;
    public string $balanceChangeType;

    public static function createFromArray(array $rawResponse): BarionHistoryResponse
    {
        $instance = new self();
        
        return TransformHelper::transformArray($instance, $rawResponse);
    }
}