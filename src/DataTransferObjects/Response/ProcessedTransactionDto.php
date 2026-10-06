<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use DateTimeInterface;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Attributes\MapToDateTime;
use Tomise\Barion\Enums\TransactionStatus;
use Tomise\Barion\Traits\Arrayable;

/**
 * A transaction in a Barion response (Payment/Start, PaymentState, FinishReservation, Capture...).
 */
class ProcessedTransactionDto
{
    use Arrayable;

    public function __construct(
        public ?string $posTransactionId = null,
        public ?string $transactionId = null,
        public ?float $total = null,

        #[MapTo(TransactionStatus::class)]
        public ?TransactionStatus $status = null,

        public ?string $currency = null,

        #[MapToDateTime]
        public ?DateTimeInterface $transactionTime = null,

        public ?string $transactionType = null,
        public ?string $relatedId = null,
        public ?string $comment = null,
    ) {}
}
