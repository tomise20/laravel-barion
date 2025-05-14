<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Enums\TransactionStatus;
use Tomise\Barion\Traits\Arrayable;

class RefundedTransactionDto {

    use Arrayable;

    public function __construct(
        public string $transactionId,
        public string $posTransactionId,
        public float $total,
        public ?string $comment = null,
        public TransactionStatus $status,
    )
    {
    }
}
