<?php

declare(strict_types=1);

namespace Tomise\Barion\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tomise\Barion\Services\PaymentClient;

interface IBarionPaymentService
{
    public function startPayment(
        Collection|Model $order,
        Collection $items,
    ): PaymentClient;

    public function startPaymentManual(): PaymentClient;
}