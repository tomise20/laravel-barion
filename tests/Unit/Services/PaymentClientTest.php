<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit\Services;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\DataTransferObjects\PaymentTransactionDto;
use Tomise\Barion\DataTransferObjects\TransactionItemDto;
use Tomise\Barion\DataTransferObjects\TransactionToFinishDto;
use Tomise\Barion\DataTransferObjects\TransactionToRefundDto;
use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Enums\PaymentType;
use Tomise\Barion\Enums\RecurrenceType;
use Tomise\Barion\Exceptions\BarionPaymentException;
use Tomise\Barion\Services\PaymentClient;
use Tomise\Barion\Tests\Mocks\Response\BarionResponses;
use Tomise\Barion\Tests\Unit\BaseUnitTest;

class PaymentClientTest extends BaseUnitTest
{
    use BarionResponses;

    public function test_construct_throwsExceptionWithoutPosKey(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('BARION_POS_KEY');

        // Act
        new PaymentClient(new BarionPaymentDto(''), $this->app->make(BarionAdapter::class));
    }

    public function test_sendSinglePayment_sendsImmediatePayment(): void
    {
        // Arrange
        $this->respondWith($this->paymentStartResponse());
        $client = $this->paymentClient();
        $client->getPaymentData()
            ->setPaymentRequestId('ORDER-1')
            ->setOrderNumber('ORDER-1')
            ->setPayerHint('customer@example.test')
            ->setPayerPhoneNumber('+36 30 123 4567')
            ->addTransaction($this->transaction('ORDER-1', 16500));

        // Act
        $response = $client->sendSinglePayment();

        // Assert
        $body = $this->lastRequestBody();
        $this->assertSame('/v2/Payment/Start', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('POSKey', $body);
        $this->assertSame('Immediate', $body['PaymentType']);
        $this->assertArrayNotHasKey('ReservationPeriod', $body);
        $this->assertArrayNotHasKey('DelayedCapturePeriod', $body);
        $this->assertSame('36301234567', $body['PayerPhoneNumber']);
        $this->assertSame('https://shop.test/barion/return', $body['RedirectUrl']);
        $this->assertSame('https://shop.test/barion/callback', $body['CallbackUrl']);
        $this->assertSame([
            'POSTransactionId' => 'ORDER-1',
            'Payee' => 'shop@example.test',
            'Total' => 16500,
            'Items' => [['Name' => 'Sports massage', 'Description' => '60 minutes', 'Quantity' => 1, 'Unit' => 'pcs', 'UnitPrice' => 16500, 'ItemTotal' => 16500]],
        ], $body['Transactions'][0]);
        $this->assertSame('pay-123', $response->getPaymentId());
        $this->assertSame('trx-1', $response->getTransactionId());
        $this->assertSame(BarionStatus::Prepared, $response->getStatus());
        $this->assertSame('https://secure.test.barion.com/Pay?Id=pay-123', $response->getGatewayUrl());
    }

    #[DataProvider('scheduledPaymentTypeProvider')]
    public function test_sendSinglePayment_sendsPeriodOfScheduledPaymentType(PaymentType $type, string $expectedPeriodField, string $otherPeriodField): void
    {
        // Arrange
        $this->respondWith($this->paymentStartResponse());
        $client = $this->paymentClient();
        $client->getPaymentData()
            ->setPaymentType($type)
            ->setPaymentRequestId('ORDER-1')
            ->addTransaction($this->transaction('ORDER-1', 16500));

        // Act
        $client->sendSinglePayment();

        // Assert
        $body = $this->lastRequestBody();
        $this->assertSame($type->value, $body['PaymentType']);
        $this->assertSame('7.00:00:00', $body[$expectedPeriodField]);
        $this->assertArrayNotHasKey($otherPeriodField, $body);
    }

    public static function scheduledPaymentTypeProvider(): array
    {
        return [
            'reservation' => [PaymentType::Reservation, 'ReservationPeriod', 'DelayedCapturePeriod'],
            'delayed capture' => [PaymentType::DelayedCapture, 'DelayedCapturePeriod', 'ReservationPeriod'],
        ];
    }

    public function test_sendSinglePayment_sendsRecurrenceFields(): void
    {
        // Arrange
        $this->respondWith($this->paymentStartResponse(['Status' => 'Succeeded', 'RecurrenceResult' => 'Successful']));
        $client = $this->paymentClient();
        $client->getPaymentData()
            ->setPaymentRequestId('ORDER-2')
            ->setRecurrenceId('customer-42-card')
            ->setRecurrenceType(RecurrenceType::RecurringPayment)
            ->setTraceId('trace-1')
            ->addTransaction($this->transaction('ORDER-2', 5000));

        // Act
        $response = $client->sendSinglePayment();

        // Assert
        $body = $this->lastRequestBody();
        $this->assertSame('customer-42-card', $body['RecurrenceId']);
        $this->assertSame('RecurringPayment', $body['RecurrenceType']);
        $this->assertSame('trace-1', $body['TraceId']);
        $this->assertSame('Successful', $response->getRecurrenceResult());
    }

    public function test_sendSinglePayment_rejectsPaymentWithoutTransactions(): void
    {
        // Arrange
        $client = $this->paymentClient();
        $client->getPaymentData()->setPaymentRequestId('ORDER-1');

        // Assert
        $this->expectException(BarionPaymentException::class);

        // Act
        $client->sendSinglePayment();
    }

    public function test_sendPaymentState_parsesState(): void
    {
        // Arrange
        $this->respondWith($this->paymentStateResponse());

        // Act
        $state = $this->paymentClient()->sendPaymentState('pay-123');

        // Assert
        $this->assertSame('/v4/Payment/pay-123/PaymentState', $this->lastRequest()->getUri()->getPath());
        $this->assertTrue($state->isSucceeded());
        $this->assertSame('ORDER-1', $state->paymentRequestId);
    }

    public function test_sendRefund_sendsTransactionsToRefund(): void
    {
        // Arrange
        $this->respondWith($this->refundResponse());

        // Act
        $response = $this->paymentClient()->sendRefund(
            'pay-123',
            [new TransactionToRefundDto('trx-1', 'ORDER-1', 5000, 'Partial refund')],
            'refund-ORDER-1-1',
        );

        // Assert
        $this->assertSame('/v2/Payment/Refund', $this->lastRequest()->getUri()->getPath());
        $this->assertSame('refund-ORDER-1-1', $this->lastRequest()->getHeaderLine('Idempotency-Key'));
        $this->assertSame([
            'PaymentId' => 'pay-123',
            'TransactionsToRefund' => [['TransactionId' => 'trx-1', 'POSTransactionId' => 'ORDER-1', 'AmountToRefund' => 5000, 'Comment' => 'Partial refund']],
        ], $this->lastRequestBody());
        $this->assertSame('refund-1', $response->refundedTransactions->first()?->transactionId);
    }

    public function test_sendRefund_rejectsEmptyTransactions(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->paymentClient()->sendRefund('pay-123', []);
    }

    public function test_sendRefund_requiresPaymentId(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $this->paymentClient()->sendRefund(transactionsToRefund: [new TransactionToRefundDto('trx-1', 'ORDER-1', 5000)]);
    }

    public function test_sendFinishReservation_sendsFinalTotalOfPaymentIdSetBefore(): void
    {
        // Arrange
        $this->respondWith($this->commonPaymentResponse());
        $client = $this->paymentClient()->setPaymentId('pay-123');

        // Act
        $response = $client->sendFinishReservation(transactions: [new TransactionToFinishDto('trx-1', 12000)]);

        // Assert
        $this->assertSame('/v2/Payment/FinishReservation', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(['PaymentId' => 'pay-123', 'Transactions' => [['TransactionId' => 'trx-1', 'Total' => 12000]]], $this->lastRequestBody());
        $this->assertTrue($response->isSuccessful);
        $this->assertSame(BarionStatus::Succeeded, $response->status);
    }

    public function test_sendCapture_sendsCapturedTotal(): void
    {
        // Arrange
        $this->respondWith($this->commonPaymentResponse());

        // Act
        $this->paymentClient()->sendCapture('pay-123', [new TransactionToFinishDto('trx-1', 16500, 'Treatment done')]);

        // Assert
        $this->assertSame('/v2/Payment/Capture', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(['TransactionId' => 'trx-1', 'Total' => 16500, 'Comment' => 'Treatment done'], $this->lastRequestBody()['Transactions'][0]);
    }

    public function test_sendCancelAuthorization_sendsPaymentId(): void
    {
        // Arrange
        $this->respondWith($this->commonPaymentResponse('Canceled'));

        // Act
        $response = $this->paymentClient()->sendCancelAuthorization('pay-123');

        // Assert
        $this->assertSame('/v2/Payment/CancelAuthorization', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(['PaymentId' => 'pay-123'], $this->lastRequestBody());
        $this->assertSame(BarionStatus::Canceled, $response->status);
    }

    private function transaction(string $posTransactionId, float $total): PaymentTransactionDto
    {
        return (new PaymentTransactionDto)
            ->setPostTransactionId($posTransactionId)
            ->setPayee('shop@example.test')
            ->setTotal($total)
            ->setItems([
                (new TransactionItemDto)
                    ->setName('Sports massage')
                    ->setDescription('60 minutes')
                    ->setUnit('pcs')
                    ->setUnitPrice($total),
            ]);
    }
}
