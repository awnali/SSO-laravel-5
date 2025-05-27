# Laravel SSO Server Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awnali/laravel-sso-server.svg?style=flat-square)](https://packagist.org/packages/awnali/laravel-sso-server)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/awnali/laravel-sso-server/run-tests?label=tests)](https://github.com/awnali/laravel-sso-server/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/awnali/laravel-sso-server/Check%20&%20fix%20styling?label=code%20style)](https://github.com/awnali/laravel-sso-server/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awnali/laravel-sso-server.svg?style=flat-square)](https://packagist.org/packages/awnali/laravel-sso-server)

Transform any Laravel application into a powerful Single Sign-On (SSO) authentication server with this easy-to-use package.

## Features

- 🚀 **Easy Installation** - Add SSO server functionality to any Laravel app
- 🔐 **Secure Authentication** - Industry-standard security practices
- 🎛️ **Flexible Configuration** - Customize to fit your needs
- 📊 **Broker Management** - Easy client application management
- 🔄 **Session Management** - Robust session handling across applications
- 🛠️ **Artisan Commands** - CLI tools for setup and management
- 📝 **Comprehensive Logging** - Track authentication events
- 🧪 **Fully Tested** - Comprehensive test coverage

## Installation

You can install the package via composer:

```bash
composer require awnali/laravel-sso-server
```

Run the setup command to configure the package:

```bash
php artisan sso:setup-server
```

This will:
- Publish the configuration file
- Run necessary migrations
- Create an example broker for testing
- Update your `.env` file with SSO settings

## Configuration

The package publishes a configuration file to `config/sso-server.php`. Here are the key settings:

```php
return [
    'session_lifetime' => env('SSO_SESSION_LIFETIME', 3600),
    'routes' => [
        'prefix' => env('SSO_ROUTES_PREFIX', 'sso'),
        'middleware' => ['web'],
    ],
    'login_url' => env('SSO_LOGIN_URL', '/login'),
    'user_model' => env('SSO_USER_MODEL', App\Models\User::class),
    // ... more options
];
```

## Usage

### Creating Brokers

Create client applications (brokers) that can authenticate via your SSO server:

```bash
php artisan sso:create-broker my-app secret-key https://my-app.com --name="My Application"
```

### Managing Brokers Programmatically

```php
use Awnali\LaravelSsoServer\Models\SsoBroker;

// Create a broker
$broker = SsoBroker::create([
    'broker_id' => 'my-app',
    'broker_secret' => 'secret-key',
    'broker_name' => 'My Application',
    'broker_url' => 'https://my-app.com',
    'is_active' => true,
]);

// List all active brokers
$activeBrokers = SsoBroker::active()->get();
```

### SSO Endpoints

The package automatically registers these endpoints:

- `GET /sso/login` - SSO login endpoint
- `POST /sso/login` - Process SSO login
- `POST /sso/logout` - SSO logout
- `GET /sso/attach` - Attach broker session
- `GET /sso/userinfo` - Get user information
- `POST /sso/verify` - Verify SSO token

### Middleware

Protect your SSO endpoints with the included middleware:

```php
Route::middleware(['sso.server'])->group(function () {
    // Your SSO-protected routes
});
```

### Custom User Model

If you're using a custom User model, update the configuration:

```php
// config/sso-server.php
'user_model' => App\Models\CustomUser::class,
```

Your User model should implement the standard Laravel authentication contracts.

## Client Integration

To integrate client applications, use the companion package:

```bash
composer require awnali/laravel-sso-client
```

## Advanced Usage

### Custom Authentication Logic

You can extend the SSO server service to customize authentication:

```php
use Awnali\LaravelSsoServer\Services\SsoServerService;

class CustomSsoServerService extends SsoServerService
{
    protected function authenticate($username, $password)
    {
        // Your custom authentication logic
        return parent::authenticate($username, $password);
    }
}

// Bind in your service provider
$this->app->singleton('sso-server', CustomSsoServerService::class);
```

### Event Handling

The package dispatches events for key actions:

```php
use Awnali\LaravelSsoServer\Events\UserLoggedIn;
use Awnali\LaravelSsoServer\Events\UserLoggedOut;

// Listen for SSO events
Event::listen(UserLoggedIn::class, function ($event) {
    // Handle user login
});
```

## Security Considerations

- Always use HTTPS in production
- Regularly rotate broker secrets
- Monitor authentication logs
- Configure proper CORS settings
- Use strong session configuration

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Awan Ali](https://github.com/awnali)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.