<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Utils\TransformHelper;

class BarionPaymentCompleteResponse {
    public string $paymentId;
    public string $paymentRequestId;
    public BarionStatus $status;
    public bool $isSuccessful;
    public ?string $traceId = null;

    public static function createFromArray(array $rawResponse): BarionPaymentCompleteResponse
    {
        $instance = new self();

        return TransformHelper::transformArray($instance, $rawResponse);
    }
}