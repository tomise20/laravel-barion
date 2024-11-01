<?php

declare(strict_types=1);

namespace Tomise\Barion\Services;

use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Responses\BarionCommonPaymentResponse;
use Tomise\Barion\Responses\BarionPaymentCompleteResponse;
use Tomise\Barion\Responses\BarionPaymentResponse;
use Tomise\Barion\Responses\BarionPaymentStatusResponse;
use Tomise\Barion\Responses\BarionRefoundResponse;

class PreparedPaymentService
{

    public function __construct(
        private BarionPaymentDto $paymentDto,
        private readonly BarionAdapter $adapter
    )
    {
    }

    public function sendSinglePayment(): BarionPaymentResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::PaymentStart);

        return new BarionPaymentResponse($response);
    }

    public function sendPaymentState(string $paymentId): BarionPaymentStatusResponse
    {
        $response = $this->adapter->sendGatewayRequest(
            $this->paymentDto,
            BarionGatewayEndpoint::PaymentState,
            ['paymentId' => $paymentId]
        );

        return BarionPaymentStatusResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function sendCompletePayment(): BarionPaymentCompleteResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Complete);

        return BarionPaymentCompleteResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function sendFinishReservation(): BarionCommonPaymentResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::FinishReservation);

        return BarionCommonPaymentResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function sendCapture(): BarionCommonPaymentResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Capture);

        return BarionCommonPaymentResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function sendCancelAuthorization(): BarionCommonPaymentResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::CancelAuthorization);

        return BarionCommonPaymentResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function sendRefund(): BarionRefoundResponse
    {
        $response = $this->adapter->sendGatewayRequest($this->paymentDto, BarionGatewayEndpoint::Refound);

        return BarionRefoundResponse::createFromArray(json_decode($response->getBody()->getContents(), true));
    }

    public function getPaymentData(): BarionPaymentDto
    {
        return $this->paymentDto;
    }

    public function setPaymentData(BarionPaymentDto $paymentData): PreparedPaymentService
    {
        $this->paymentDto = $paymentData;

        return $this;
    }
}