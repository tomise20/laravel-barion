<?php

declare(strict_types=1);

namespace Tomise\Barion\Adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Tomise\Barion\DataTransferObjects\BarionPaymentDto;
use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Enums\BarionWalletEndpoint;
use Tomise\Barion\Exceptions\BarionConnectionException;
use Tomise\Barion\Exceptions\BarionPaymentException;

class BarionAdapter
{
    private Client $client;

    private const SUCCESS_STATUS_CODE = 200;

    private const SANDBOX_URL = 'https://api.test.barion.com';

    private const PRODUCTION_URL = 'https://api.barion.com';

    private const GET_TYPE = 'GET';

    private const POST_TYPE = 'POST';

    public function __construct()
    {
        $baseUrl = config('barion-gateway.environment') === 'test' ? self::SANDBOX_URL : self::PRODUCTION_URL;

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 3.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->checkConnection();
    }

    public function sendGatewayRequest(BarionPaymentDto $paymentDto, BarionGatewayEndpoint $endpoint, array $extras = null): ResponseInterface
    {
        $requestType = $this->getRequestType($endpoint);

        if($endpoint === BarionGatewayEndpoint::PaymentState) {
            $path = str_replace(':paymentId', $extras['paymentId'], $endpoint->value);
        } else {
            $path = $endpoint->value;
        }
        
        try {
            $rawResponse = $this->client->request($requestType, $path, [
                'json' => $paymentDto->toArray(),
                'headers' => [
                    'x-pos-key' => $paymentDto->getPosKey(),
                ]
            ]);
        } catch(RequestException $e) {
            if($e->hasResponse()) {
                throw new BarionPaymentException('Request failed: '.$e->getMessage());
            }

            throw new BarionPaymentException($e->getMessage());
        }

        return $rawResponse;
    }

    public function sendWalletRequest(BarionWalletDto $walletDto, BarionWalletEndpoint $endpoint): ResponseInterface
    {
        $requestType = $this->getRequestType($endpoint);
        
        try {
            $rawResponse = $this->client->request($requestType, $endpoint->value, [
                'json' => $walletDto->toArray(),
                'headers' => [
                    'x-api-key' => $walletDto->getApiKey(),
                ]
            ]);
        } catch(RequestException $e) {
            if($e->hasResponse()) {
                throw new BarionPaymentException('Request failed: '.$e->getMessage());
            }

            throw new BarionPaymentException($e->getMessage());
        }

        return $rawResponse;
    }

    public function sendDownload(BarionWalletDto $walletDto): string
    {
        try {
            $rawResponse = $this->client->request(self::GET_TYPE, 'v2/Statement/Download', [
                'json' => $walletDto->toArray(),
                'headers' => [
                    'x-api-key' => $walletDto->getApiKey(),
                ]
            ]);

            if($rawResponse->getStatusCode() === self::SUCCESS_STATUS_CODE) {
                $now = Carbon::now()->toDateTimeString();
                $extension = $this->getExtension($walletDto);
                $filePath = storage_path('app/barion/download_'.$now.$extension);

                Storage::put($filePath, $rawResponse->getBody());
            }
        } catch(RequestException $e) {
            if($e->hasResponse()) {
                throw new BarionPaymentException($e->getMessage());
            }

            throw new BarionPaymentException($e->getMessage());
        }

        return $filePath;
    }

    private function getExtension(BarionWalletDto $walletDto): string
    {
        if($walletDto->getDay()) {
            return '.xlsx';
        }

        return '.pdf';
    }

    private function checkConnection(): void
    {
        try {
            $response = $this->client->get(self::SANDBOX_URL);

            if($response->getStatusCode() !== self::SUCCESS_STATUS_CODE) {
                throw new BarionConnectionException('Barion gateway is not available');
            }
        } catch(RequestException $e) {
            throw new BarionConnectionException('Barion gateway is not available');
        }
    }

    private function getRequestType(BarionGatewayEndpoint|BarionWalletEndpoint $endpoint): string
    {
        return $endpoint->isGetEndpoint() ? self::GET_TYPE : self::POST_TYPE;
    }
}