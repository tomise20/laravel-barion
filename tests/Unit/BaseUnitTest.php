<?php

declare(strict_types=1);

namespace Tomise\Barion\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use Psr\Http\Message\RequestInterface;
use ReflectionMethod;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\Providers\BarionServiceProvider;
use Tomise\Barion\Services\BarionPaymentService;
use Tomise\Barion\Services\PaymentClient;

/**
 * A Laravel app with the package and a mocked Barion API: queue responses with respondWith(),
 * inspect what was sent with lastRequest() / lastRequestBody().
 */
abstract class BaseUnitTest extends TestCase
{
    protected MockHandler $barion;

    /**
     * @var list<array{request: RequestInterface}>
     */
    protected array $requests = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->barion = new MockHandler;
        $stack = HandlerStack::create($this->barion);
        $stack->push(Middleware::history($this->requests));

        $this->app->instance(BarionAdapter::class, new BarionAdapter(new Client([
            'handler' => $stack,
            'base_uri' => 'https://api.test.barion.com',
            'http_errors' => false,
        ])));
    }

    protected function getPackageProviders($app): array
    {
        return [BarionServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('barion-gateway.environment', 'test');
        $app['config']->set('barion-gateway.posKey', 'test-pos-key');
        $app['config']->set('barion-gateway.payee', 'shop@example.test');
        $app['config']->set('barion-gateway.redirectUrl', 'https://shop.test/barion/return');
        $app['config']->set('barion-gateway.callbackUrl', 'https://shop.test/barion/callback');
    }

    protected function respondWith(array $data, int $status = 200): void
    {
        $this->barion->append(new Response($status, ['Content-Type' => 'application/json'], json_encode($data)));
    }

    protected function lastRequest(): RequestInterface
    {
        return end($this->requests)['request'];
    }

    protected function lastRequestBody(): ?array
    {
        return json_decode((string) $this->lastRequest()->getBody(), true);
    }

    protected function paymentClient(): PaymentClient
    {
        return $this->app->make(BarionPaymentService::class)->startPaymentManual();
    }

    /**
     * Call a private or protected method of the given object.
     *
     * @param  array<int, mixed>  $parameters
     */
    protected function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        return (new ReflectionMethod($object, $methodName))->invokeArgs($object, $parameters);
    }
}
