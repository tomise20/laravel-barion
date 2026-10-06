<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit\Responses;

use Tomise\Barion\Enums\BarionStatus;
use Tomise\Barion\Enums\PaymentType;
use Tomise\Barion\Responses\BarionPaymentStatusResponse;
use Tomise\Barion\Tests\Mocks\Response\BarionResponses;
use Tomise\Barion\Tests\Unit\BaseUnitTest;

class BarionPaymentStatusResponseTest extends BaseUnitTest
{
    use BarionResponses;

    public function test_createFromArray_mapsPaymentState(): void
    {
        // Act
        $state = BarionPaymentStatusResponse::createFromArray($this->paymentStateResponse());

        // Assert
        $this->assertSame(BarionStatus::Succeeded, $state->status);
        $this->assertSame(PaymentType::Immediate, $state->paymentType);
        $this->assertSame('Test shop', $state->posName);
        $this->assertSame(16500.0, $state->total);
        $this->assertSame('123456', $state->fundingInformation?->authorizationCode);
        $this->assertSame('2026-10-06 12:01:30', $state->completedAt?->format('Y-m-d H:i:s'));
        $this->assertCount(2, $state->transactions);
    }

    public function test_createFromArray_toleratesUnknownValues(): void
    {
        // Arrange
        $data = $this->paymentStateResponse(['SomethingNew' => 'value']);
        $data['Transactions'][] = ['TransactionId' => 'x-1', 'POSTransactionId' => 'ORDER-1', 'Total' => 0, 'Currency' => 'HUF', 'Status' => 'SomeFutureStatus', 'TransactionType' => 'SomeFutureType'];

        // Act
        $state = BarionPaymentStatusResponse::createFromArray($data);

        // Assert
        $this->assertCount(3, $state->transactions);
        $this->assertNull($state->transactions[2]->status);
    }

    public function test_transaction_returnsCustomerTransactionWithoutFees(): void
    {
        // Arrange
        $state = BarionPaymentStatusResponse::createFromArray($this->paymentStateResponse());

        // Act
        $transaction = $state->transaction('ORDER-1');

        // Assert
        $this->assertSame('trx-1', $transaction?->transactionId);
        $this->assertCount(1, $state->paymentTransactions());
        $this->assertNull($state->transaction('UNKNOWN'));
    }

    public function test_isSucceeded_isFalseForPendingPayment(): void
    {
        // Arrange
        $state = BarionPaymentStatusResponse::createFromArray($this->paymentStateResponse(['Status' => 'Prepared']));

        // Act
        $succeeded = $state->isSucceeded();

        // Assert
        $this->assertFalse($succeeded);
    }
}
