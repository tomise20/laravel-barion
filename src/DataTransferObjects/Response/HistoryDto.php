<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Traits\Arrayable;


class HistoryDto
{

    use Arrayable;

    public string $id;
    public string $historyType;
    public string $happenedAtUtc;
    public int $concurrencyOrder;
    public UserHistoryParticipantDto $sourceAccount;
    public UserHistoryParticipantDto $targetAccount;
    public float $amount;
    public string $currency;
    public string $description;
    public bool $isInProgress;
    public string $balanceChangeType;
}
