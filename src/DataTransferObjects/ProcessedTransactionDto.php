<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use DateTime;
use Tomise\Barion\Enums\TransactionStatus;

class ProcessedTransactionDto {
    public function __construct(
        public  string $posTransactionId = null,
        public  string $transactionId = null,
        public  TransactionStatus $status = null,
        public  string $currency = null,
        public  DateTime $transactionTime = null,
    )
    {
    }

    public static function createFromArray(array $data): ProcessedTransactionDto
    {
        return new ProcessedTransactionDto(
            posTransactionId: $data['POSTransactionId'],
            transactionId: $data['TransactionId'],
            status: TransactionStatus::from($data['Status']),
            currency: $data['Currency'],
            transactionTime: new DateTime($data['TransactionTime'])
        );
    }
}