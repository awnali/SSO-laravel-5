# Laravel SSO Client Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/awnali/laravel-sso-client.svg?style=flat-square)](https://packagist.org/packages/awnali/laravel-sso-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/awnali/laravel-sso-client/run-tests?label=tests)](https://github.com/awnali/laravel-sso-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/awnali/laravel-sso-client.svg?style=flat-square)](https://packagist.org/packages/awnali/laravel-sso-client)

Enable any Laravel application to authenticate users via Single Sign-On (SSO) with this easy-to-use client package.

## Features

- 🚀 **Easy Integration** - Add SSO authentication to any Laravel app
- 🔐 **Seamless Authentication** - Transparent user authentication
- 🎛️ **Flexible Configuration** - Customize to fit your needs
- 🔄 **Auto-Login** - Automatic authentication across applications
- 🛡️ **Secure Communication** - Encrypted communication with SSO server
- 🛠️ **Artisan Commands** - CLI tools for setup and testing
- 📊 **Session Management** - Robust session handling
- 🧪 **Fully Tested** - Comprehensive test coverage

## Installation

You can install the package via composer:

```bash
composer require awnali/laravel-sso-client
```

Run the setup command to configure the package:

```bash
php artisan sso:setup-client
```

This will:
- Publish the configuration file
- Update your `.env` file with SSO settings
- Configure authentication guards

## Configuration

Add these settings to your `.env` file:

```env
SSO_SERVER_URL=https://your-sso-server.com
SSO_BROKER_ID=your-app-id
SSO_BROKER_SECRET=your-secret-key
```

The package publishes a configuration file to `config/sso-client.php`:

```php
return [
    'server_url' => env('SSO_SERVER_URL'),
    'broker_id' => env('SSO_BROKER_ID'),
    'broker_secret' => env('SSO_BROKER_SECRET'),
    'auto_attach' => env('SSO_AUTO_ATTACH', true),
    'user_model' => env('SSO_USER_MODEL', App\Models\User::class),
    // ... more options
];
```

## Usage

### Basic Authentication

Protect routes with SSO authentication:

```php
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});
```

### Optional Authentication

For routes that benefit from SSO but don't require it:

```php
Route::middleware(['sso.optional'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
});
```

### User Model Integration

Add the SSO trait to your User model:

```php
use Awnali\LaravelSsoClient\Traits\HasSsoAuthentication;

class User extends Authenticatable
{
    use HasSsoAuthentication;
    
    // Your existing code...
}
```

### Manual Authentication

You can also manually authenticate users:

```php
use Awnali\LaravelSsoClient\Services\SsoClientService;

class AuthController extends Controller
{
    public function login(SsoClientService $sso)
    {
        if ($sso->isAttached()) {
            $user = $sso->getUserInfo();
            if ($user) {
                Auth::login($user);
                return redirect('/dashboard');
            }
        }
        
        return redirect($sso->getLoginUrl());
    }
}
```

### Testing Connection

Test your SSO connection:

```bash
php artisan sso:test-connection
```

### Clear SSO Sessions

Clear local SSO sessions:

```bash
php artisan sso:clear-sessions
```

## Authentication Guards

The package registers a custom authentication guard. You can use it in your `config/auth.php`:

```php
'guards' => [
    'sso' => [
        'driver' => 'sso',
        'provider' => 'users',
    ],
],
```

## Middleware

### `sso.auth`
Requires SSO authentication. Redirects to SSO server if not authenticated.

### `sso.optional`
Attempts SSO authentication but doesn't require it. Sets user if authenticated.

### `sso.attach`
Ensures the broker is attached to the SSO server session.

## Events

The package dispatches events for key actions:

```php
use Awnali\LaravelSsoClient\Events\SsoUserAuthenticated;
use Awnali\LaravelSsoClient\Events\SsoUserLoggedOut;

Event::listen(SsoUserAuthenticated::class, function ($event) {
    // Handle user authentication
    $user = $event->user;
});
```

## Advanced Usage

### Custom User Creation

Customize how users are created from SSO data:

```php
use Awnali\LaravelSsoClient\Services\SsoClientService;

class CustomSsoClientService extends SsoClientService
{
    protected function createUserFromSsoData(array $userData): User
    {
        return User::create([
            'name' => $userData['fullname'],
            'email' => $userData['email'],
            'sso_id' => $userData['username'],
            // Your custom fields
        ]);
    }
}

// Bind in your service provider
$this->app->singleton(SsoClientService::class, CustomSsoClientService::class);
```

### Custom Authentication Logic

```php
class CustomAuthController extends Controller
{
    public function handleSsoCallback(Request $request, SsoClientService $sso)
    {
        $userData = $sso->getUserInfo();
        
        if ($userData) {
            $user = $this->findOrCreateUser($userData);
            Auth::login($user);
            
            // Custom post-authentication logic
            $this->logUserActivity($user);
            
            return redirect()->intended('/dashboard');
        }
        
        return redirect('/login')->with('error', 'SSO authentication failed');
    }
}
```

## Configuration Options

### Server Communication

```php
// config/sso-client.php
'http' => [
    'timeout' => 30,
    'verify_ssl' => true,
    'headers' => [
        'User-Agent' => 'Laravel SSO Client',
    ],
],
```

### Session Configuration

```php
'session' => [
    'lifetime' => 3600,
    'cookie_name' => 'sso_session',
    'secure' => true,
    'same_site' => 'lax',
],
```

## Security Considerations

- Always use HTTPS in production
- Verify SSL certificates
- Use secure session configuration
- Regularly update broker secrets
- Monitor authentication logs

## Troubleshooting

### Common Issues

1. **Connection Refused**: Check SSO server URL and network connectivity
2. **Invalid Broker**: Verify broker ID and secret configuration
3. **Session Issues**: Check session configuration and cookie settings

### Debug Mode

Enable debug mode for detailed logging:

```env
SSO_DEBUG=true
```

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