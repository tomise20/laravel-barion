<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects\Response;

use DateTime;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Attributes\MapToDateTime;
use Tomise\Barion\Enums\TransactionStatus;
use Tomise\Barion\Traits\Arrayable;

class ProcessedTransactionDto {
    use Arrayable;

    public function __construct(
        public ?string $posTransactionId = null,
        public ?string $transactionId = null,
		public ?float $total = null,

        #[MapTo(TransactionStatus::class)]
        public ?TransactionStatus $status = null,

        public ?string $currency = null,

        #[MapToDateTime(format: 'Y-m-d H:i:s')]
        public ?DateTime $transactionTime = null,
    )
    {
    }
}
