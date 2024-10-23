<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tomise\Barion\Builders\BarionParamsBuilder;
use Tomise\Barion\Contracts\IBarionPaymentService;
use Tomise\Barion\Exceptions\BarionPaymentException;

class BarionPaymentService extends AbstractBarionService implements IBarionPaymentService
{
    /**
     * @param Collection<Model>|Model $order
     * @param Collection<Model> $items
     * 
     * @throws BarionPaymentException If the model does not have the required Barion properties
     */
    public function startPayment(
        Collection|Model $order,
        Collection $items,
    ): PreparedPaymentService
    {
        $this->checkModelBarionProperty($order);

        $paymentData = (new BarionParamsBuilder())
            ->setOrder($order)
            ->setItems($items)
            ->build();

        return new PreparedPaymentService($paymentData);
    }

    public function startPaymentManual(): PreparedPaymentService
    {
        $paymentData = (new BarionParamsBuilder())->buildManual();

        return new PreparedPaymentService($paymentData);
    }    
}