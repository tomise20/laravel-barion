<?php

namespace Tomise\Barion\Contracts;

interface PaymentResponse
{
    public function getPaymentId(): string;

    public function getPaymentRequestId(): string;

    public function getGatewayUrl(): string;

    public function getTransactionId(): string;

    public function getError(): array;

    public function getErrorMessage(): string;
}