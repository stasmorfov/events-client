<?php

namespace App\Providers;

use App\Contracts\TicketGatewayInterface;
use App\Integrations\TicketGateways\PrimaryTicketGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TicketGatewayInterface::class,
            PrimaryTicketGateway::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}