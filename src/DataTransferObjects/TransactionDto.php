<?php

declare(strict_types=1);

namespace Tomise\Barion\DataTransferObjects;

use Carbon\Carbon;
use Tomise\Barion\Traits\Arrayable;
use Tomise\Barion\Traits\HasSetter;


/**
 * @method self setUserId(int $userId)
 * @method self setReservationId(int $reservationId)
 * @method self setPaymentMethod(string $paymentMethod)
 * @method self setPaymentId(string $paymentId)
 * @method self setTransactionId(string $transactionId)
 * @method self setOwnStatus(string $ownStatus)
 * @method self setGatewayStatus(string $gatewayStatus)
 * @method self setCurrency(string $currency)
 * @method self setCompletedAt(\Carbon\Carbon $completedAt)
 */
class TransactionDto
{

    use Arrayable, HasSetter;

    public function __construct()
    {
        $this->bootHasSetters();
    }

    public ?int $userId;
    public int $reservationId;
    public string $paymentMethod;
    public string $paymentId;
    public string $transactionId;
    public string $ownStatus;
    public string $gatewayStatus;
    public string $currency;
    public Carbon $completedAt;
}
