<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\DataTransferObjects\Response\ProcessedTransactionDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Utils\TransformHelper;

class BarionCommonPaymentResponse {
    public string $isSuccessful;
    public string $paymentId;
    public string $paymentRequestId;

    #[MapTo(BarionStatus::class)]
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