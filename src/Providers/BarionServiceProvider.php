<?php

declare(strict_types=1);

namespace Tomise\Barion\Providers;

use Illuminate\Support\ServiceProvider;
use Tomise\Barion\Adapters\BarionAdapter;
use Tomise\Barion\Contracts\IBarionPaymentService;
use Tomise\Barion\Services\BarionPaymentService;

class BarionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/barion-gateway.php', 'barion-gateway');

        $this->app->bind(BarionAdapter::class, fn (): BarionAdapter => new BarionAdapter);
        $this->app->bind(IBarionPaymentService::class, fn ($app): BarionPaymentService => new BarionPaymentService($app->make(BarionAdapter::class)));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/barion-gateway.php' => config_path('barion-gateway.php'),
            ], 'config');
        }
    }
}
