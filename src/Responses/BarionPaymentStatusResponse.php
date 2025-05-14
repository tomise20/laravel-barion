<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Attributes\MapToCollection;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Response\FundingInformationDto;
use Tomise\Barion\DataTransferObjects\Response\ProcessedTransactionDto;
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

    #[MapTo(BarionStatus::class)]
    public ?BarionStatus $status = null;

    public ?string $paymentType = null;
    public ?array $allowedFundingSources = null;
    public ?string $fundingSource = null;

    #[MapTo(FundingInformationDto::class)]
    public ?FundingInformationDto $fundingInformation = null;

    #[MapTo(RecurrenceType::class)]
    public ?RecurrenceType $recurrenceType = null;

   #[MapToCollection(itemType: ProcessedTransactionDto::class)]
    public ?Collection $transactions = null;

    public ?string $traceId = null;
    public ?string $createdAt = null;
    public ?string $completedAt = null;
    public float $total;
    
    #[MapTo(Currency::class)]
    public Currency $currency;

    #[MapTo(PaymentMethod::class)]
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