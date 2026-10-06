<?php

declare(strict_types=1);

namespace Tomise\Barion\Adapters;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Tomise\Barion\DataTransferObjects\BarionWalletDto;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Enums\BarionWalletEndpoint;
use Tomise\Barion\Exceptions\BarionConnectionException;
use Tomise\Barion\Exceptions\BarionPaymentException;

/**
 * HTTP layer of the Barion API. The POS key goes in the x-pos-key header (Smart Gateway), the API key in x-api-key
 * (Wallet). Barion's error responses (400, 409, 422 and errors in a 200 response) become BarionPaymentException.
 */
class BarionAdapter
{
    private const SANDBOX_URL = 'https://api.test.barion.com';

    private const PRODUCTION_URL = 'https://api.barion.com';

    private const SANDBOX_GATEWAY_URL = 'https://secure.test.barion.com/Pay';

    private const PRODUCTION_GATEWAY_URL = 'https://secure.barion.com/Pay';

    private ClientInterface $client;

    public function __construct(?ClientInterface $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => self::isSandbox() ? self::SANDBOX_URL : self::PRODUCTION_URL,
            'timeout' => (float) config('barion-gateway.timeout', 30),
            'connect_timeout' => 10,
            // Error responses are handled here, with Barion's own error messages.
            'http_errors' => false,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public static function isSandbox(): bool
    {
        return config('barion-gateway.environment', 'test') !== 'prod';
    }

    /**
     * The Barion payment page of a payment (when the response has no GatewayUrl).
     */
    public static function gatewayUrl(string $paymentId): string
    {
        return (self::isSandbox() ? self::SANDBOX_GATEWAY_URL : self::PRODUCTION_GATEWAY_URL).'?Id='.rawurlencode($paymentId);
    }

    /**
     * Send a Smart Gateway request and return the decoded response.
     *
     * @param  array<string, mixed>  $body  JSON body of POST requests
     * @param  array<string, string>  $pathParameters  e.g. ['paymentId' => '...'] for PaymentState
     * @param  string|null  $idempotencyKey  makes a retried POST safe (Barion runs it only once)
     *
     * @throws BarionPaymentException when Barion rejects the request
     * @throws BarionConnectionException when Barion cannot be reached
     */
    public function send(
        BarionGatewayEndpoint $endpoint,
        string $posKey,
        array $body = [],
        array $pathParameters = [],
        ?string $idempotencyKey = null,
    ): array {
        $options = ['headers' => array_filter([
            'x-pos-key' => $posKey,
            'Idempotency-Key' => $idempotencyKey,
        ])];

        if ($endpoint->isGetEndpoint()) {
            if ($endpoint === BarionGatewayEndpoint::GetPaymentState) {
                $options['query'] = ['PaymentId' => $pathParameters['paymentId'] ?? ''];
            }
        } else {
            $options['json'] = $body;
        }

        return $this->decode($this->request($endpoint->isGetEndpoint() ? 'GET' : 'POST', $endpoint->path($pathParameters), $options));
    }

    /**
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendWalletRequest(BarionWalletDto $walletDto, BarionWalletEndpoint $endpoint): ResponseInterface
    {
        $options = ['headers' => ['x-api-key' => $walletDto->getApiKey()]];

        if ($endpoint->isGetEndpoint()) {
            $options['query'] = $walletDto->toArray();
        } else {
            $options['json'] = $walletDto->toArray();
        }

        $response = $this->request($endpoint->isGetEndpoint() ? 'GET' : 'POST', ltrim($endpoint->value, '/'), $options);
        // Throws on Barion errors; the response stays readable for the caller.
        $this->decode($response);

        return $response;
    }

    /**
     * Download a statement into the configured disk; returns the path on that disk.
     *
     * @throws BarionPaymentException
     * @throws BarionConnectionException
     */
    public function sendDownload(BarionWalletDto $walletDto): string
    {
        $response = $this->request('GET', BarionWalletEndpoint::Download->value, [
            'headers' => ['x-api-key' => $walletDto->getApiKey()],
            'query' => $walletDto->toArray(),
        ]);

        if ($response->getStatusCode() !== 200) {
            $this->decode($response);
        }

        $directory = trim((string) (config('barion-gateway.downloadPath') ?? 'barion'), '/');
        $path = $directory.'/download_'.Carbon::now()->format('Ymd_His').($walletDto->getDay() ? '.xlsx' : '.pdf');

        Storage::put($path, (string) $response->getBody());

        return $path;
    }

    /**
     * @throws BarionConnectionException
     */
    private function request(string $method, string $path, array $options): ResponseInterface
    {
        try {
            return $this->client->request($method, $path, $options);
        } catch (GuzzleException $exception) {
            throw new BarionConnectionException('Barion is not available: '.$exception->getMessage());
        }
    }

    /**
     * @throws BarionPaymentException
     */
    private function decode(ResponseInterface $response): array
    {
        $raw = (string) $response->getBody();
        $data = json_decode($raw, true);
        $data = is_array($data) ? $data : [];
        $errors = $data['Errors'] ?? [];

        if ($response->getStatusCode() >= 400 || ! empty($errors)) {
            $error = $errors[0] ?? [];
            $message = trim(($error['Title'] ?? '').(isset($error['Description']) ? ': '.$error['Description'] : ''), ': ');

            throw (new BarionPaymentException(
                $message !== '' ? $message : 'Barion request failed with HTTP '.$response->getStatusCode().'.',
                $response->getStatusCode(),
            ))->setErrors($errors)->setResponseData($data ?: ['raw' => $raw]);
        }

        return $data;
    }
}
