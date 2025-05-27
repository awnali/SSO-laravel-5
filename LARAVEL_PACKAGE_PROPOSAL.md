# Laravel SSO Package Proposal

## Overview
Transform the current SSO implementation into installable Laravel packages that developers can easily integrate into their existing Laravel projects.

## Package Structure

### 1. Laravel SSO Server Package (`awnali/laravel-sso-server`)
A package that turns any Laravel application into an SSO server.

#### Installation
```bash
composer require awnali/laravel-sso-server
php artisan vendor:publish --provider="Awnali\LaravelSsoServer\SsoServerServiceProvider"
php artisan migrate
php artisan sso:setup-server
```

#### Features
- **Service Provider**: Auto-registers routes, middleware, and services
- **Config File**: `config/sso-server.php` with all SSO server settings
- **Middleware**: `SsoServerMiddleware` for handling SSO requests
- **Controllers**: `SsoServerController` with all SSO endpoints
- **Models**: `SsoBroker` model for managing registered brokers
- **Migrations**: Database tables for brokers and sessions
- **Artisan Commands**: Setup and management commands
- **Routes**: Automatic route registration for SSO endpoints

### 2. Laravel SSO Client Package (`awnali/laravel-sso-client`)
A package that enables any Laravel application to authenticate via SSO.

#### Installation
```bash
composer require awnali/laravel-sso-client
php artisan vendor:publish --provider="Awnali\LaravelSsoClient\SsoClientServiceProvider"
php artisan sso:setup-client
```

#### Features
- **Service Provider**: Auto-registers SSO client services
- **Config File**: `config/sso-client.php` with broker settings
- **Middleware**: `SsoClientMiddleware` for automatic authentication
- **Traits**: `HasSsoAuthentication` trait for User model
- **Guards**: Custom authentication guard for SSO
- **Controllers**: `SsoAuthController` for handling SSO callbacks
- **Artisan Commands**: Client setup and configuration

## Package Implementation Plan

### Phase 1: Extract Core Components

#### Server Package Structure
```
packages/laravel-sso-server/
├── src/
│   ├── SsoServerServiceProvider.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── SsoServerController.php
│   │   └── Middleware/
│   │       └── SsoServerMiddleware.php
│   ├── Models/
│   │   ├── SsoBroker.php
│   │   └── SsoSession.php
│   ├── Services/
│   │   └── SsoServerService.php
│   ├── Console/
│   │   └── Commands/
│   │       ├── SetupServerCommand.php
│   │       └── CreateBrokerCommand.php
│   └── Database/
│       └── Migrations/
├── config/
│   └── sso-server.php
├── routes/
│   └── sso-server.php
├── resources/
│   └── views/
│       └── login.blade.php
├── composer.json
└── README.md
```

#### Client Package Structure
```
packages/laravel-sso-client/
├── src/
│   ├── SsoClientServiceProvider.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── SsoAuthController.php
│   │   └── Middleware/
│   │       └── SsoClientMiddleware.php
│   ├── Services/
│   │   └── SsoClientService.php
│   ├── Traits/
│   │   └── HasSsoAuthentication.php
│   ├── Guards/
│   │   └── SsoGuard.php
│   └── Console/
│       └── Commands/
│           └── SetupClientCommand.php
├── config/
│   └── sso-client.php
├── routes/
│   └── sso-client.php
├── composer.json
└── README.md
```

### Phase 2: Configuration Examples

#### Server Config (`config/sso-server.php`)
```php
<?php

return [
    'brokers' => [
        // Registered brokers will be stored in database
        // This is just for initial setup
    ],
    
    'session_lifetime' => env('SSO_SESSION_LIFETIME', 3600),
    
    'routes' => [
        'prefix' => env('SSO_ROUTES_PREFIX', 'sso'),
        'middleware' => ['web'],
    ],
    
    'login_url' => env('SSO_LOGIN_URL', '/login'),
    
    'user_model' => env('SSO_USER_MODEL', App\Models\User::class),
    
    'broker_model' => env('SSO_BROKER_MODEL', Awnali\LaravelSsoServer\Models\SsoBroker::class),
];
```

#### Client Config (`config/sso-client.php`)
```php
<?php

return [
    'server_url' => env('SSO_SERVER_URL'),
    'broker_id' => env('SSO_BROKER_ID'),
    'broker_secret' => env('SSO_BROKER_SECRET'),
    
    'routes' => [
        'prefix' => env('SSO_CLIENT_ROUTES_PREFIX', 'auth/sso'),
        'middleware' => ['web'],
    ],
    
    'auto_attach' => env('SSO_AUTO_ATTACH', true),
    
    'user_model' => env('SSO_USER_MODEL', App\Models\User::class),
];
```

### Phase 3: Usage Examples

#### Server Setup
```php
// After installation, in any controller:
use Awnali\LaravelSsoServer\Models\SsoBroker;

// Create a new broker
SsoBroker::create([
    'broker_id' => 'my-app',
    'broker_secret' => 'secret-key',
    'broker_url' => 'https://my-app.com'
]);
```

#### Client Setup
```php
// In your User model:
use Awnali\LaravelSsoClient\Traits\HasSsoAuthentication;

class User extends Authenticatable
{
    use HasSsoAuthentication;
    
    // Your existing code...
}

// In routes/web.php:
Route::middleware(['sso.client'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
```

### Phase 4: Advanced Features

#### Artisan Commands
```bash
# Server commands
php artisan sso:create-broker {broker-id} {broker-secret} {broker-url}
php artisan sso:list-brokers
php artisan sso:revoke-broker {broker-id}

# Client commands
php artisan sso:test-connection
php artisan sso:clear-sessions
```

#### Middleware Integration
```php
// Automatic SSO authentication
Route::middleware(['sso.auth'])->group(function () {
    // Protected routes
});

// Optional SSO (try to authenticate but don't require)
Route::middleware(['sso.optional'])->group(function () {
    // Routes that benefit from SSO but don't require it
});
```

## Benefits of Package Approach

### 1. **Easy Integration**
- Single composer command installation
- Automatic service provider registration
- No need to modify existing application structure

### 2. **Configuration Management**
- Publishable config files
- Environment variable support
- Sensible defaults

### 3. **Flexibility**
- Can be added to existing Laravel applications
- Minimal code changes required
- Optional features can be enabled/disabled

### 4. **Maintenance**
- Centralized updates via composer
- Version management
- Backward compatibility

### 5. **Developer Experience**
- Clear documentation
- Artisan commands for setup
- IDE autocompletion support

## Implementation Timeline

### Week 1: Core Extraction
- Extract server logic into package structure
- Create service providers and basic configuration

### Week 2: Client Package
- Extract client/broker logic
- Create middleware and guards
- Implement authentication traits

### Week 3: Testing & Documentation
- Create comprehensive tests
- Write detailed documentation
- Create example applications

### Week 4: Publishing & Distribution
- Publish to Packagist
- Create installation guides
- Community feedback and iteration

## Conclusion

Creating Laravel packages would significantly improve the accessibility and adoption of this SSO solution. It transforms a complex multi-application setup into simple, installable packages that developers can integrate into their existing Laravel projects with minimal effort.

The package approach aligns with Laravel's ecosystem and best practices, making it more likely to be adopted by the Laravel community.