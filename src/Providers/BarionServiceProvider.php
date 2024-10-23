<?php

declare(strict_types=1);

namespace Tomise\Barion\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Tomise\Barion\Contracts\IBarionPaymentService;
use Tomise\Barion\Services\BarionPaymentService;

class BarionServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../../config/barion-gateway.php' => config_path('barion-gateway.php'),
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(IBarionPaymentService::class, BarionPaymentService::class);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/barion-gateway.php', 'barion-gateway'
        );
    }

    public function provides(): array
    {
        return [
            IBarionPaymentService::class
        ];
    }
}

