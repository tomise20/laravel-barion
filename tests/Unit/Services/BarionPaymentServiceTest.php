<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit\Services;

use Illuminate\Database\Eloquent\Model;
use Tomise\Barion\Exceptions\BarionCastException;
use Tomise\Barion\Services\BarionPaymentService;
use Tomise\Barion\Support\BarionGateway;
use Tomise\Barion\Tests\Mocks\Response\BarionResponses;
use Tomise\Barion\Tests\Unit\BaseUnitTest;

class BarionPaymentServiceTest extends BaseUnitTest
{
    use BarionResponses;

    public function test_startPayment_buildsPaymentFromModels(): void
    {
        // Arrange
        $this->respondWith($this->paymentStartResponse(['PaymentRequestId' => 'ORDER-2']));
        $order = $this->order(['reference' => 'ORDER-2', 'email' => 'customer@example.test', 'phone' => '+36 20 555 1234', 'price' => '9999.6']);
        $item = $this->orderItem(['name' => 'Complex treatment', 'description' => '90 minutes', 'quantity' => 1, 'unit' => 'session', 'unit_price' => 9999.6, 'item_total' => 9999.6]);

        // Act
        BarionGateway::createPaymentGateway()->startPayment($order, collect([$item]))->sendSinglePayment();

        // Assert
        $body = $this->lastRequestBody();
        $transaction = $body['Transactions'][0];
        $this->assertSame('ORDER-2', $body['PaymentRequestId']);
        $this->assertSame('ORDER-2', $body['OrderNumber']);
        $this->assertSame('customer@example.test', $body['PayerHint']);
        $this->assertSame('36205551234', $body['PayerPhoneNumber']);
        $this->assertSame('shop@example.test', $transaction['Payee']);
        $this->assertSame(10000, $transaction['Total']);
        $this->assertSame(10000, $transaction['Items'][0]['UnitPrice']);
        $this->assertSame('session', $transaction['Items'][0]['Unit']);
    }

    public function test_startPayment_throwsExceptionWithoutBarionCasts(): void
    {
        // Arrange
        $order = new class extends Model {};

        // Assert
        $this->expectException(BarionCastException::class);

        // Act
        $this->app->make(BarionPaymentService::class)->startPayment($order, collect());
    }

    public function test_startPaymentManual_generatesPaymentRequestIdAndUsesConfig(): void
    {
        // Act
        $paymentData = $this->paymentClient()->getPaymentData();

        // Assert
        $this->assertNotEmpty($paymentData->getPaymentRequestId());
        $this->assertSame('test-pos-key', $paymentData->getPosKey());
        $this->assertSame('Immediate', $paymentData->getPaymentType());
    }

    private function order(array $attributes): Model
    {
        $order = new class extends Model
        {
            public $barion_casts = [
                'payment_request_id' => 'reference',
                'payer_hint' => 'email',
                'order_number' => 'reference',
                'phone_number' => 'phone',
                'total' => 'price',
            ];
        };

        return $order->forceFill($attributes);
    }

    private function orderItem(array $attributes): Model
    {
        $item = new class extends Model
        {
            public $barion_casts = [
                'name' => 'name',
                'description' => 'description',
                'quantity' => 'quantity',
                'unit' => 'unit',
                'unit_price' => 'unit_price',
                'item_total' => 'item_total',
            ];
        };

        return $item->forceFill($attributes);
    }
}
