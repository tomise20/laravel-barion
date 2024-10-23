<?php

declare(strict_types=1);

namespace Tomise\Barion\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tomise\Barion\Services\PreparedPaymentService;

interface IBarionPaymentService
{
    public function startPayment(
        Collection|Model $order,
        Collection $items,
    ): PreparedPaymentService;

    public function startPaymentManual(): PreparedPaymentService;
}