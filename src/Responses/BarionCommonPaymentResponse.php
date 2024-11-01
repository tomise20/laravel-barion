<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\DataTransferObjects\ProcessedTransactionDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Utils\TransformHelper;

class BarionCommonPaymentResponse {
    public string $isSuccessful;
    public string $paymentId;
    public string $paymentRequestId;
    public BarionStatus $status;
    /**
     * @var Collection<ProcessedTransactionDto>
     */
    public readonly Collection $transactions;

    public static function createFromArray(array $rawResponse): BarionCommonPaymentResponse
    {
        $instance = new self();

        return TransformHelper::transformArray($instance, $rawResponse);
    }
}