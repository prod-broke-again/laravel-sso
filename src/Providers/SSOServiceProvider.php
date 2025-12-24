<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Providers;

use Packages\LaravelSSO\Contracts\SSOServiceInterface;
use Packages\LaravelSSO\Contracts\PartnerServiceInterface;
use Packages\LaravelSSO\Contracts\SSOAuthenticatorInterface;
use Packages\LaravelSSO\Services\SSOService;
use Packages\LaravelSSO\Services\PartnerService;
use Packages\LaravelSSO\Services\SSOAuthenticator;
use Illuminate\Support\ServiceProvider;

/**
 * SSO Service Provider
 *
 * Registers all SSO-related services and configurations.
 */
class SSOServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sso.php',
            'sso'
        );

        // Register services with their interfaces (DIP)
        $this->app->singleton(PartnerServiceInterface::class, PartnerService::class);
        $this->app->singleton(SSOAuthenticatorInterface::class, SSOAuthenticator::class);

        $this->app->singleton(SSOServiceInterface::class, function ($app) {
            return new SSOService(
                partnerService: $app->make(PartnerServiceInterface::class),
                authenticator: $app->make(SSOAuthenticatorInterface::class),
            );
        });

        // Register alias for backward compatibility
        $this->app->alias(SSOServiceInterface::class, 'sso');
        $this->app->alias(SSOServiceInterface::class, SSOService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/sso.php' => config_path('sso.php'),
        ], 'sso-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
        ], 'sso-migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/laravel-sso'),
        ], 'sso-views');

        // Load views
        $this->loadViewsFrom(
            __DIR__ . '/../../resources/views',
            'laravel-sso'
        );

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        // Register Blade components
        $this->loadViewComponentsAs('sso', [
            \Packages\LaravelSSO\View\Components\SsoButton::class,
        ]);

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Packages\LaravelSSO\Console\Commands\CleanupExpiredTokens::class,
            ]);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            SSOServiceInterface::class,
            PartnerServiceInterface::class,
            SSOAuthenticatorInterface::class,
            'sso',
        ];
    }
}
