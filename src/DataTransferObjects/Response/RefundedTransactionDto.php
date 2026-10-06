<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Enums\TransactionStatus;
use Tomise\Barion\Traits\Arrayable;

/**
 * A refund transaction created by Payment/Refund.
 */
class RefundedTransactionDto
{
    use Arrayable;

    public function __construct(
        public ?string $transactionId = null,
        public ?string $posTransactionId = null,
        public ?float $total = null,
        public ?string $comment = null,

        #[MapTo(TransactionStatus::class)]
        public ?TransactionStatus $status = null,
    ) {}
}
