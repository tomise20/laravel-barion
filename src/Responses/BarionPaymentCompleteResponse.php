<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Utils\TransformHelper;

/**
 * Response of Payment/Complete.
 */
class BarionPaymentCompleteResponse
{
    public ?string $paymentId = null;
    public ?string $paymentRequestId = null;

    #[MapTo(BarionStatus::class)]
    public ?BarionStatus $status = null;

    public ?bool $isSuccessful = null;
    public ?string $traceId = null;

    public static function createFromArray(array $rawResponse): BarionPaymentCompleteResponse
    {
        return TransformHelper::transformArray(new self, $rawResponse);
    }
}
