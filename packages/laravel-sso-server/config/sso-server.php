<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Server Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Laravel SSO Server
    | package. You can customize these settings to match your application's
    | requirements.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | The lifetime of SSO sessions in seconds. After this time, users will
    | need to re-authenticate.
    |
    */
    'session_lifetime' => env('SSO_SESSION_LIFETIME', 3600),

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for the SSO server endpoints.
    |
    */
    'routes' => [
        'prefix' => env('SSO_ROUTES_PREFIX', 'sso'),
        'middleware' => ['web'],
        'name' => 'sso.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Login URL
    |--------------------------------------------------------------------------
    |
    | The URL where users should be redirected for authentication.
    |
    */
    'login_url' => env('SSO_LOGIN_URL', '/login'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The User model that should be used for authentication.
    |
    */
    'user_model' => env('SSO_USER_MODEL', App\Models\User::class),

    /*
    |--------------------------------------------------------------------------
    | Broker Model
    |--------------------------------------------------------------------------
    |
    | The model used to store registered SSO brokers.
    |
    */
    'broker_model' => env('SSO_BROKER_MODEL', Awnali\LaravelSsoServer\Models\SsoBroker::class),

    /*
    |--------------------------------------------------------------------------
    | Session Model
    |--------------------------------------------------------------------------
    |
    | The model used to store SSO sessions.
    |
    */
    'session_model' => env('SSO_SESSION_MODEL', Awnali\LaravelSsoServer\Models\SsoSession::class),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for SSO sessions and broker information.
    |
    */
    'cache' => [
        'enabled' => env('SSO_CACHE_ENABLED', true),
        'ttl' => env('SSO_CACHE_TTL', 3600),
        'prefix' => env('SSO_CACHE_PREFIX', 'sso_'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings for the SSO server.
    |
    */
    'security' => [
        'verify_ssl' => env('SSO_VERIFY_SSL', true),
        'allowed_domains' => env('SSO_ALLOWED_DOMAINS', '*'),
        'rate_limiting' => env('SSO_RATE_LIMITING', true),
    ],
];