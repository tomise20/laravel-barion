<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit\Adapters;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\Enums\BarionGatewayEndpoint;
use Tomise\Barion\Exceptions\BarionConnectionException;
use Tomise\Barion\Exceptions\BarionPaymentException;
use Tomise\Barion\Tests\Mocks\Response\BarionResponses;
use Tomise\Barion\Tests\Unit\BaseUnitTest;

class BarionAdapterTest extends BaseUnitTest
{
    use BarionResponses;

    public function test_send_postsJsonBodyWithPosKeyHeader(): void
    {
        // Arrange
        $this->respondWith($this->paymentStartResponse());
        $adapter = $this->app->make(BarionAdapter::class);

        // Act
        $response = $adapter->send(BarionGatewayEndpoint::PaymentStart, 'test-pos-key', ['PaymentType' => 'Immediate'], idempotencyKey: 'key-1');

        // Assert
        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/v2/Payment/Start', $request->getUri()->getPath());
        $this->assertSame('test-pos-key', $request->getHeaderLine('x-pos-key'));
        $this->assertSame('key-1', $request->getHeaderLine('Idempotency-Key'));
        $this->assertSame(['PaymentType' => 'Immediate'], $this->lastRequestBody());
        $this->assertSame('pay-123', $response['PaymentId']);
    }

    public function test_send_getRequestUsesPathParametersWithoutBody(): void
    {
        // Arrange
        $this->respondWith($this->paymentStateResponse());
        $adapter = $this->app->make(BarionAdapter::class);

        // Act
        $adapter->send(BarionGatewayEndpoint::PaymentState, 'test-pos-key', ['Ignored' => true], ['paymentId' => 'pay-123']);

        // Assert
        $request = $this->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/v4/Payment/pay-123/PaymentState', $request->getUri()->getPath());
        $this->assertSame('test-pos-key', $request->getHeaderLine('x-pos-key'));
        $this->assertSame('', (string) $request->getBody());
    }

    #[DataProvider('errorResponseProvider')]
    public function test_send_throwsPaymentExceptionOnBarionError(Response $response, string $expectedMessage, int $expectedCode, ?string $expectedErrorCode): void
    {
        // Arrange
        $this->barion->append($response);
        $adapter = $this->app->make(BarionAdapter::class);

        // Act
        try {
            $adapter->send(BarionGatewayEndpoint::Refound, 'test-pos-key', ['PaymentId' => 'pay-123']);
            $this->fail('BarionPaymentException was not thrown.');
        } catch (BarionPaymentException $exception) {
            // Assert
            $this->assertSame($expectedMessage, $exception->getMessage());
            $this->assertSame($expectedCode, $exception->getCode());
            $this->assertSame($expectedErrorCode, $exception->getErrorCode());
        }
    }

    public static function errorResponseProvider(): array
    {
        $errors = fn (string $code, string $title, string $description): string => json_encode(['Errors' => [['ErrorCode' => $code, 'Title' => $title, 'Description' => $description]]]);

        return [
            'HTTP 400 with errors' => [new Response(400, [], $errors('InvalidPosKey', 'Invalid POS key', 'The POS key is not valid.')), 'Invalid POS key: The POS key is not valid.', 400, 'InvalidPosKey'],
            'errors in a HTTP 200 response' => [new Response(200, [], $errors('ModelValidationError', 'Invalid request', 'Transactions is required')), 'Invalid request: Transactions is required', 200, 'ModelValidationError'],
            'HTTP 500 without JSON' => [new Response(500, [], '<html>Server error</html>'), 'Barion request failed with HTTP 500.', 500, null],
        ];
    }

    public function test_send_throwsConnectionExceptionWhenBarionIsUnreachable(): void
    {
        // Arrange
        $this->barion->append(new ConnectException('Connection timed out', new Request('GET', 'v4/Payment/pay-123/PaymentState')));
        $adapter = $this->app->make(BarionAdapter::class);

        // Assert
        $this->expectException(BarionConnectionException::class);
        $this->expectExceptionMessage('Connection timed out');

        // Act
        $adapter->send(BarionGatewayEndpoint::PaymentState, 'test-pos-key', pathParameters: ['paymentId' => 'pay-123']);
    }

    #[DataProvider('environmentProvider')]
    public function test_gatewayUrl_followsEnvironment(string $environment, string $expectedUrl): void
    {
        // Arrange
        config(['barion-gateway.environment' => $environment]);

        // Act
        $url = BarionAdapter::gatewayUrl('pay-123');

        // Assert
        $this->assertSame($expectedUrl, $url);
    }

    public static function environmentProvider(): array
    {
        return [
            'sandbox' => ['test', 'https://secure.test.barion.com/Pay?Id=pay-123'],
            'production' => ['prod', 'https://secure.barion.com/Pay?Id=pay-123'],
        ];
    }
}
