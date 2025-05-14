<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Mocks\Response;

use Tomise\Barion\Responses\BarionPaymentResponse;

trait SinglePayment {

    private function mockCreatePreparedPaymentResponse(): BarionPaymentResponse
    {
        $response = [
            'PaymentId' => '1234-567',
            'PaymentRequestId' => '1',
            'Status' => 'Succeeded',
            'Transactions' => [
                'POSTransactionId' => '123456',
                'TransactionId' => '1',
                'Currency' => 'HUF'
            ],
            'QRUrl' => 'https://test.barion.com/qr-url',
            'Errors' => null,
            'GatewayUrl' => 'https://test.barion.com/prepared?id=1234'
        ];

        return new BarionPaymentResponse($response);
    }
}