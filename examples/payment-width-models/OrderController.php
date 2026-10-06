<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Tomise\Barion\Support\BarionGateway;

class OrderController extends Controller
{
    public function createOrder(Order $order)
    {
        // Build the Barion payment data from your models (both with $barion_casts).
        $preparedPayment = BarionGateway::createPaymentGateway()->startPayment($order, $order->items);

        // Optional extra data before sending, e.g. $preparedPayment->getPaymentData()->setBillingAddress($address);

        // Throws BarionPaymentException (rejected) or BarionConnectionException (not reachable).
        $response = $preparedPayment->sendSinglePayment();

        $order->update(['barion_payment_id' => $response->getPaymentId()]);

        return redirect($response->getGatewayUrl());
    }
}
