<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Tomise\Barion\Support\BarionGateway;

class OrderController extends Controller
{
    public function createOrder()
    {
        // Your order
        $order = Order::find(1);
        // Your order items
        $items = $order->items;

        // Get payment gateway.
        $gateway = BarionGateway::createPaymentGateway();

        // Build barion payment data you models
        $preparedPayment = $gateway->startPayment($order, $items);
        // Send data to Barion

        return redirect($response->getGatewayUrl());
    }
}