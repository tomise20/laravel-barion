<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum BarionGatewayEndpoint: string {
    case PaymentStart = 'v2/Payment/Start';
    case GetPaymentState = 'v2/Payment/GetPaymentState'; // deprecated
    case PaymentState = 'v4/Payment/:paymentId/paymentstate';
    case Complete = 'v2/Payment/Complete';
    case FinishReservation = 'v2/Payment/FinishReservation';
    case Capture = 'v2/Payment/Capture';
    case CancelAuthorization = 'v2/Payment/CancelAuthorization';
    case Refound = 'v2/Payment/Refund';

    public static function getGETEndpoints(): array {
        return [
            self::GetPaymentState,
            self::PaymentState,
        ];
    }

    public function isGetEndpoint(): bool
    {
        return in_array($this, self::getGETEndpoints());
    }
}