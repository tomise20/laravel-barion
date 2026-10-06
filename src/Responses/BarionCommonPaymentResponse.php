<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\Attributes\MapTo;
use Tomise\Barion\Attributes\MapToCollection;
use Tomise\Barion\DataTransferObjects\Response\ProcessedTransactionDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Utils\TransformHelper;

/**
 * Response of FinishReservation, Capture and CancelAuthorization.
 */
class BarionCommonPaymentResponse
{
    public ?bool $isSuccessful = null;
    public ?string $paymentId = null;
    public ?string $paymentRequestId = null;

    #[MapTo(BarionStatus::class)]
    public ?BarionStatus $status = null;

    /**
     * @var Collection<int, ProcessedTransactionDto>
     */
    #[MapToCollection(ProcessedTransactionDto::class)]
    public Collection $transactions;

    public function __construct()
    {
        $this->transactions = new Collection;
    }

    public static function createFromArray(array $rawResponse): BarionCommonPaymentResponse
    {
        return TransformHelper::transformArray(new self, $rawResponse);
    }
}
