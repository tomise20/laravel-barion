<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tomise\Barion\DataTransferObjects\Currency;
use Tomise\Barion\DataTransferObjects\Locale;
use Tomise\Barion\DataTransferObjects\PaymentTransactionDto;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;
use Tomise\Barion\DataTransferObjects\TransactionToRefundDto;
use Tomise\Barion\Exceptions\BarionConnectionException;
use Tomise\Barion\Exceptions\BarionPaymentException;
use Tomise\Barion\Support\BarionGateway;

class OrderController extends Controller
{
    public function createOrder()
    {
        /**
         * The following properties are assigned from the config: POSKey, PaymentType, ReservationPeriod,
         * DelayedCapturePeriod, PaymentWindow, GuestCheckOut, FundingSources, RedirectUrl, CallbackUrl, Locale, Currency.
         */
        $preparedPayment = BarionGateway::createPaymentGateway()->startPaymentManual();

        $preparedPayment->getPaymentData()
            // Your unique identifier of the payment (a UUID is generated when it is missing).
            ->setPaymentRequestId('ORDER-01')
            ->setOrderNumber('ORDER-01')
            ->setPayerHint('customer@example.com')
            ->setPayerPhoneNumber('+36 30 123 4567')
            // Without these the configured defaults are used.
            ->setLocale(Locale::Hu)
            ->setCurrency(Currency::Huf)
            ->addTransaction($this->createTransaction());

        try {
            $response = $preparedPayment->sendSinglePayment();
        } catch (BarionPaymentException $exception) {
            // Barion rejected the request: $exception->getErrors() has Barion's errors.
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        } catch (BarionConnectionException) {
            return response()->json(['success' => false, 'message' => 'Barion is not available.'], 503);
        }

        // Store $response->getPaymentId() and $response->getTransactionId() with the order:
        // the callback and the refund need them.

        return response()->json(['success' => true, 'url' => $response->getGatewayUrl()]);
    }

    /**
     * BARION_CALLBACK_URL: Barion only sends the PaymentId, the state has to be queried.
     */
    public function callback(Request $request)
    {
        $state = BarionGateway::createPaymentGateway()->startPaymentManual()->sendPaymentState((string) $request->input('paymentId'));

        if ($state->isSucceeded()) {
            // Mark the order with $state->paymentRequestId as paid.
        }

        return response()->noContent();
    }

    public function refund(string $paymentId, string $transactionId)
    {
        $response = BarionGateway::createPaymentGateway()->startPaymentManual()->sendRefund(
            $paymentId,
            [new TransactionToRefundDto($transactionId, 'Trs-01', 100, 'Partial refund')],
            // The same key for a retried request: Barion refunds only once.
            idempotencyKey: 'refund-ORDER-01-1',
        );

        return response()->json(['refunded' => $response->refundedTransactions->count()]);
    }

    private function createTransaction(): PaymentTransactionDto
    {
        $transactionItem = (new TransactionItemDto)
            ->setName('Test product')
            ->setDescription('test description')
            ->setQuantity(1)
            ->setUnit('db')
            ->setUnitPrice(100)
            // optional
            ->setImageUrl('https://example.com/image.jpg');

        return (new PaymentTransactionDto)
            ->setPostTransactionId('Trs-01')
            ->setPayee('test@example.com')
            ->setTotal(100)
            ->setItems([$transactionItem]);
    }
}
