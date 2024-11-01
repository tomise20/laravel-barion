<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\FundingInformationDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Enums\PaymentMethod;
use Tomise\Barion\Enums\RecurrenceType;
use Tomise\Barion\Utils\TransformHelper;

class BarionPaymentStatusResponse {
    public string $paymentId;
    public string $paymentRequestId;
    public ?string $posId = null;
    public ?string $posName = null;
    public ?string $posOwnerName = null;
    public ?string $posOwnerEmail = null;
    public ?string $posOwnerCountry = null;
    public ?BarionStatus $status = null;
    public ?string $paymentType = null;
    public ?array $allowedFundingSources = null;
    public ?string $fundingSource = null;
    public ?FundingInformationDto $fundingInformation = null;
    public ?RecurrenceType $recurrenceType = null;
    public ?string $traceId = null;
    public ?string $createdAt = null;
    public ?string $completedAt = null;
    public float $total;
    public Currency $currency;
    public PaymentMethod $paymentMethod;

    public function __construct()
    {
    }

    public static function createFromArray(array $rawResponse): BarionPaymentStatusResponse
    {
        $instance = new self();
        
        return TransformHelper::transformArray($instance, $rawResponse);
    }
}