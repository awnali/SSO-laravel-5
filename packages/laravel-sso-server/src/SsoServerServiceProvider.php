<?php

namespace Awnali\LaravelSsoServer;

use Illuminate\Support\ServiceProvider;
use Awnali\LaravelSsoServer\Console\Commands\SetupServerCommand;
use Awnali\LaravelSsoServer\Console\Commands\CreateBrokerCommand;
use Awnali\LaravelSsoServer\Http\Middleware\SsoServerMiddleware;

class SsoServerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sso-server.php', 'sso-server'
        );

        $this->app->singleton('sso-server', function ($app) {
            return new Services\SsoServerService($app['config']['sso-server']);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/sso-server.php' => config_path('sso-server.php'),
        ], 'sso-server-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/Database/Migrations' => database_path('migrations'),
        ], 'sso-server-migrations');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/sso-server'),
        ], 'sso-server-views');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/sso-server.php');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sso-server');

        // Register middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('sso.server', SsoServerMiddleware::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupServerCommand::class,
                CreateBrokerCommand::class,
            ]);
        }
    }
}