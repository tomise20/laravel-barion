<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Mocks\Response;

/**
 * Barion API responses in the documented shape.
 */
trait BarionResponses
{
    protected function paymentStartResponse(array $overrides = []): array
    {
        return array_merge([
            'PaymentId' => 'pay-123',
            'PaymentRequestId' => 'ORDER-1',
            'Status' => 'Prepared',
            'QRUrl' => 'https://api.test.barion.com/qr/generate?paymentId=pay-123',
            'RecurrenceResult' => 'None',
            'TraceId' => 'trace-1',
            'GatewayUrl' => 'https://secure.test.barion.com/Pay?Id=pay-123',
            'Transactions' => [[
                'POSTransactionId' => 'ORDER-1',
                'TransactionId' => 'trx-1',
                'Status' => 'Prepared',
                'Currency' => 'HUF',
                'TransactionTime' => '2026-10-06T12:00:00.123Z',
                'RelatedId' => null,
            ]],
            'Errors' => [],
        ], $overrides);
    }

    protected function paymentStateResponse(array $overrides = []): array
    {
        return array_merge([
            'PaymentId' => 'pay-123',
            'PaymentRequestId' => 'ORDER-1',
            'OrderNumber' => 'ORDER-1',
            'POSId' => 'pos-1',
            'POSName' => 'Test shop',
            'POSOwnerEmail' => 'shop@example.test',
            'Status' => 'Succeeded',
            'PaymentType' => 'Immediate',
            'FundingSource' => 'BankCard',
            'FundingInformation' => [
                'BankCard' => ['MaskedPan' => '1234', 'BankCardType' => 'Visa', 'ValidThruYear' => '2030', 'ValidThruMonth' => '12'],
                'AuthorizationCode' => '123456',
                'ProcessResult' => 'Successful',
            ],
            'GuestCheckout' => true,
            'CreatedAt' => '2026-10-06T12:00:00.123Z',
            'CompletedAt' => '2026-10-06T12:01:30.5Z',
            'Total' => 16500,
            'Currency' => 'HUF',
            'SuggestedLocale' => 'hu-HU',
            'TraceId' => 'trace-1',
            'Transactions' => [
                ['TransactionId' => 'fee-1', 'POSTransactionId' => null, 'TransactionTime' => '2026-10-06T12:01:30', 'Total' => 150, 'Currency' => 'HUF', 'Status' => 'Succeeded', 'TransactionType' => 'CardProcessingFee'],
                ['TransactionId' => 'trx-1', 'POSTransactionId' => 'ORDER-1', 'TransactionTime' => '2026-10-06T12:01:30', 'Total' => 16500, 'Currency' => 'HUF', 'Status' => 'Succeeded', 'TransactionType' => 'CardPayment'],
            ],
        ], $overrides);
    }

    protected function refundResponse(): array
    {
        return [
            'PaymentId' => 'pay-123',
            'RefundedTransactions' => [[
                'TransactionId' => 'refund-1',
                'POSTransactionId' => 'ORDER-1',
                'Total' => -5000,
                'Comment' => 'Partial refund',
                'Status' => 'Succeeded',
            ]],
            'Errors' => [],
        ];
    }

    protected function commonPaymentResponse(string $status = 'Succeeded'): array
    {
        return [
            'IsSuccessful' => true,
            'PaymentId' => 'pay-123',
            'PaymentRequestId' => 'ORDER-1',
            'Status' => $status,
            'Transactions' => [[
                'POSTransactionId' => 'ORDER-1',
                'TransactionId' => 'trx-1',
                'Status' => $status,
                'Currency' => 'HUF',
                'TransactionTime' => '2026-10-07T10:00:00',
            ]],
            'Errors' => [],
        ];
    }

    protected function errorResponse(string $errorCode, string $title, string $description): array
    {
        return ['Errors' => [['ErrorCode' => $errorCode, 'Title' => $title, 'Description' => $description]]];
    }
}
