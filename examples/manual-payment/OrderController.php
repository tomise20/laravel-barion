<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;
use Tomise\Barion\Support\BarionGateway;
use Tomise\Barion\DataTransferObjects\PaymentTransactionDto;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;

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

        /**
         * The following properties automatic assigned: POSKey,PaymentType,
         * ReservationPeriod,PaymentWindow,GuestCheckOut,FundingSources,RedirectUrl,CallbackUrl
         */ 
        $preparedPayment = $gateway->startPaymentManual();
        $paymentDto = $preparedPayment->getPaymentData();
        $paymentDto->setPaymentId('TEST-01');
        $paymentDto->setTransactions([$this->createTransaction()]);
        $paymentDto->setOrderNumber('ORDER-01');

        // If you don't set the locale and currency, the default values will be used from config
        $paymentDto->setLocale(Locale::Hu);
        $paymentDto->setCurrency(Currency::Huf);

        // This step is not necessary, because the paymentDto is a reference
        $preparedPayment->setPaymentData($paymentDto);

        // Send data to Barion
        $response = $preparedPayment->sendSinglePayment();

        if($response->isSuccess) {
            return response()->json(['success' => true, 'url' => $response->getGatewayUrl()]);
        }

        return response()->json(['success' => false, 'message' => $response->getErrorMessage()]);

    }

    private function createTransaction(): PaymentTransactionDto
    {
        $transactionItems = collect();
        $transactionItem = new TransactionItemDto();
        $transactionItem->setName('Test product');
        $transactionItem->setDescription('test description');
        $transactionItem->setQuantity(1);
        $transactionItem->setUnit('db');
        $transactionItem->setUnitPrice(100.25);
        // optional
        $transactionItem->setImageUrl('http://example.com/image.jpg');
        $transactionItems->push($transactionItem);

        $transaction = new PaymentTransactionDto();
        $transaction->setPostTransactionId('Trs-01');
        $transaction->setPayee("test@example.com");
        $transaction->setTotal(100.25);
        $transaction->setItems($transactionItems);

        return $transaction;
    }
}