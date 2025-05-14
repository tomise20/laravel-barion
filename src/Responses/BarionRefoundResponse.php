<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\DataTransferObjects\Response\RefundedTransactionDto;
use Tomise\Barion\Utils\TransformHelper;

class BarionRefoundResponse {
    public ?string $paymentId = null;

    #[MapTo(RefundedTransactionDto::class)]
    public ?RefundedTransactionDto $refundedTransactions = null;

    public static function createFromArray(array $rawResponse): BarionRefoundResponse
    {
        $instance = new self();

        return TransformHelper::transformArray($instance, $rawResponse);
    }
}