<?php

declare(strict_types=1);

namespace Tomise\Barion\Enums;

enum BarionGatewayEndpoint: string
{
    case PaymentStart = 'v2/Payment/Start';
    case GetPaymentState = 'v2/Payment/GetPaymentState'; // deprecated, use PaymentState
    case PaymentState = 'v4/Payment/:paymentId/PaymentState';
    case Complete = 'v2/Payment/Complete';
    case FinishReservation = 'v2/Payment/FinishReservation';
    case Capture = 'v2/Payment/Capture';
    case CancelAuthorization = 'v2/Payment/CancelAuthorization';
    case Refound = 'v2/Payment/Refund';

    public static function getGETEndpoints(): array
    {
        return [
            self::GetPaymentState,
            self::PaymentState,
        ];
    }

    public function isGetEndpoint(): bool
    {
        return in_array($this, self::getGETEndpoints(), true);
    }

    /**
     * The path with its parameters filled in, e.g. ":paymentId".
     *
     * @param  array<string, string>  $parameters
     */
    public function path(array $parameters = []): string
    {
        $path = $this->value;

        foreach ($parameters as $name => $value) {
            $path = str_replace(':'.$name, rawurlencode($value), $path);
        }

        return $path;
    }
}
