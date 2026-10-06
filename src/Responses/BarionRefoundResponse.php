<?php

declare(strict_types=1);

namespace Tomise\Barion\Responses;

use Illuminate\Support\Collection;
use Tomise\Barion\Attributes\MapToCollection;
use Tomise\Barion\DataTransferObjects\Response\RefundedTransactionDto;
use Tomise\Barion\Utils\TransformHelper;

/**
 * Response of Payment/Refund (the class name keeps its original spelling for compatibility).
 */
class BarionRefoundResponse
{
    public ?string $paymentId = null;

    /**
     * @var Collection<int, RefundedTransactionDto>
     */
    #[MapToCollection(RefundedTransactionDto::class)]
    public Collection $refundedTransactions;

    public function __construct()
    {
        $this->refundedTransactions = new Collection;
    }

    public static function createFromArray(array $rawResponse): BarionRefoundResponse
    {
        return TransformHelper::transformArray(new self, $rawResponse);
    }
}
