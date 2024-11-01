<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Tomise\Barion\Enums\TransactionStatus;

class RefundedTransactionDto {
    public function __construct(
        public  string $transactionId,
        public  string $posTransactionId,
        public  float $total,
        public  ?string $comment = null,
        public  TransactionStatus $status,
    )
    {
    }

    public static function createFromArray(array $data): RefundedTransactionDto
    {
        return new RefundedTransactionDto(
            transactionId: $data['TransactionId'],
            posTransactionId: $data['POSTransactionId'],
            total: $data['Total'],
            comment: $data['Comment'],
            status: TransactionStatus::from($data['Status'])
        );
    }
}