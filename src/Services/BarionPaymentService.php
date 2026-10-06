<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\Builders\BarionParamsBuilder;
use Tomise\Barion\Contracts\IBarionPaymentService;
use Tomise\Barion\Exceptions\BarionPaymentException;

class BarionPaymentService extends AbstractBarionService implements IBarionPaymentService
{
    private BarionAdapter $adapter;

    public function __construct(?BarionAdapter $adapter = null)
    {
        $this->adapter = $adapter ?? new BarionAdapter;
    }

    /**
     * A payment built from an order model and its items (both with $barion_casts).
     *
     * @param  Collection<int, Model>  $items
     *
     * @throws BarionPaymentException If the model does not have the required Barion properties
     */
    public function startPayment(Model $order, Collection $items): PaymentClient
    {
        $this->checkModelBarionProperty($order);
        $items->each(fn (Model $item) => $this->checkModelBarionProperty($item));

        $paymentData = (new BarionParamsBuilder)
            ->setOrder($order)
            ->setItems($items)
            ->build();

        return new PaymentClient($paymentData, $this->adapter);
    }

    /**
     * An empty payment with the configured defaults; also the client of the follow-up requests (state, refund...).
     *
     * @throws BarionPaymentException
     */
    public function startPaymentManual(): PaymentClient
    {
        return new PaymentClient((new BarionParamsBuilder)->buildManual(), $this->adapter);
    }
}
