# 🔐 Laravel 12 Single Sign-On (SSO) - Complete Multi-Application Authentication System

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Tests](https://github.com/awnali/SSO-laravel-5/workflows/SSO%20Laravel%2012%20Tests/badge.svg)](https://github.com/awnali/SSO-laravel-5/actions)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](CONTRIBUTING.md)

**A production-ready, enterprise-grade Single Sign-On (SSO) solution built with Laravel 12**. This comprehensive implementation enables seamless authentication across multiple applications with one login, featuring automatic session management, centralized user control, and robust security measures.

Perfect for **microservices architecture**, **multi-tenant applications**, **enterprise systems**, and **distributed web applications** requiring unified authentication.

## 🌟 Key Features & Benefits

### 🚀 **Core SSO Functionality**
- **🔑 Single Sign-On**: One login grants access to all connected applications
- **🔄 Auto-Login**: Seamless authentication across broker applications without re-entering credentials
- **🚪 Centralized Logout**: Logout from any application terminates sessions across all connected apps
- **🛡️ Session Management**: Secure, distributed session handling with token-based authentication
- **⚡ Real-time Sync**: Instant authentication state synchronization across all brokers

### 🏗️ **Modern Architecture**
- **📱 Laravel 12 Compatible**: Built for the latest Laravel framework with PHP 8.2+ support
- **🔧 Microservices Ready**: Designed for distributed application architectures
- **🗄️ Database Agnostic**: Works with SQLite, MySQL, PostgreSQL, and other Laravel-supported databases
- **🌐 API-First Design**: RESTful API endpoints for easy integration
- **📊 Scalable Design**: Handles multiple brokers with efficient resource management

### 🧪 **Enterprise-Grade Testing**
- **✅ 100% Test Coverage**: Comprehensive unit, feature, and integration tests
- **🤖 Automated Testing**: CI/CD ready with GitHub Actions workflow
- **🔍 Quality Assurance**: Automated test runner with detailed reporting
- **🛠️ Development Tools**: Built-in debugging and monitoring capabilities

## 🎯 Use Cases & Applications

### 🏢 **Enterprise Solutions**
- **Corporate Intranets**: Unified access to HR, CRM, and internal tools
- **Multi-Department Systems**: Finance, Operations, and Management portals
- **Employee Dashboards**: Single login for all company applications
- **B2B Platforms**: Partner portals with centralized authentication

### 🛒 **E-commerce & SaaS**
- **Multi-Store Platforms**: Unified customer accounts across multiple stores
- **SaaS Ecosystems**: Integrated suite of business applications
- **Customer Portals**: Account management, billing, and support systems
- **Marketplace Solutions**: Vendor and buyer authentication systems

### 🎓 **Educational & Healthcare**
- **Learning Management Systems**: Student and faculty unified access
- **Healthcare Portals**: Patient, doctor, and admin system integration
- **Research Platforms**: Multi-institutional collaboration tools
- **Campus Management**: Academic and administrative system integration

### 🔧 **Development & DevOps**
- **Microservices Architecture**: Service-to-service authentication
- **Development Environments**: Staging, testing, and production access
- **API Gateway Integration**: Centralized authentication for API access
- **Container Orchestration**: Kubernetes and Docker authentication

## 📋 System Requirements

### 🖥️ **Server Requirements**
- **PHP**: 8.2 or higher (8.3 recommended for optimal performance)
- **Laravel**: 12.0+ (tested with Laravel 12.15.0)
- **Composer**: Latest version for dependency management
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Memory**: Minimum 512MB RAM (1GB+ recommended for production)

### 🗄️ **Database Support**
- **SQLite**: 3.8+ (perfect for development and small deployments)
- **MySQL**: 5.7+ or 8.0+ (recommended for production)
- **PostgreSQL**: 10+ (excellent for enterprise applications)
- **MariaDB**: 10.3+ (MySQL-compatible alternative)

### 🔧 **PHP Extensions Required**
- `mbstring`, `xml`, `ctype`, `iconv`, `intl`
- `pdo_sqlite` (for SQLite) or `pdo_mysql` (for MySQL)
- `dom`, `filter`, `gd`, `json`
- `openssl` (for secure token generation)

### 🌐 **Browser Compatibility**
- **Modern Browsers**: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- **Mobile Support**: iOS Safari 14+, Chrome Mobile 90+
- **JavaScript**: ES6+ support required for optimal functionality

## 🏗️ Architecture

This SSO implementation consists of:
- **1 SSO Server**: Handles authentication and user management
- **Multiple Brokers**: Applications that delegate authentication to the SSO server

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   Broker1   │    │ SSO Server  │    │   Broker2   │
│             │◄──►│             │◄──►│             │
│ (Port 8001) │    │ (Port 8000) │    │ (Port 8002) │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 🚀 Quick Start

### 📦 **Two Installation Options**

#### Option 1: Laravel Packages (Recommended for Existing Projects)

Transform your existing Laravel applications into SSO-enabled systems using our Laravel packages:

**For SSO Server:**
```bash
composer require awnali/laravel-sso-server
php artisan sso:setup-server
```

**For Client Applications:**
```bash
composer require awnali/laravel-sso-client
php artisan sso:setup-client
```

[📖 **See detailed package documentation below**](#-laravel-packages-integration)

#### Option 2: Complete Project Installation

```bash
# Clone the repository
git clone https://github.com/awnali/SSO-laravel-5.git laravel-sso
cd laravel-sso

# Install dependencies for all applications
composer install
cd server && composer install && cp .env.example .env && php artisan key:generate && cd ..
cd broker1 && composer install && cp .env.example .env && php artisan key:generate && cd ..
cd broker2 && composer install && cp .env.example .env && php artisan key:generate && cd ..
```

### ⚙️ **Configuration**

Configure environment variables for each application:

**SSO Server (.env)**:
```env
APP_NAME="SSO Server"
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/server/database/database.sqlite
```

**Broker1 (.env)**:
```env
APP_NAME="Broker1"
APP_URL=http://localhost:8001
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker1
SSO_BROKER_SECRET=broker1_secret
```

**Broker2 (.env)**:
```env
APP_NAME="Broker2"
APP_URL=http://localhost:8002
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker2
SSO_BROKER_SECRET=broker2_secret
```

### 🗄️ **Database Setup**

```bash
# Create databases and run migrations
cd server && touch database/database.sqlite && php artisan migrate && cd ..
cd broker1 && touch database/database.sqlite && php artisan migrate && cd ..
cd broker2 && touch database/database.sqlite && php artisan migrate && cd ..
```

### 👤 **Create Test User**

```bash
cd server && php artisan tinker
```

```php
use App\Models\User;
User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password')
]);
exit
```

### 🚀 **Start Applications**

```bash
# Terminal 1 - SSO Server
cd server && php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2 - Broker1
cd broker1 && php artisan serve --host=0.0.0.0 --port=8001

# Terminal 3 - Broker2
cd broker2 && php artisan serve --host=0.0.0.0 --port=8002
```

## 🧪 Testing

### 🤖 **Automated Test Suite**

Run the comprehensive test suite covering all SSO functionality:

```bash
# Run all tests
./run-tests.sh

# Run specific test types
./run-tests.sh unit      # Unit tests only
./run-tests.sh feature   # Feature tests only
./run-tests.sh integration # Integration tests only
```

### ✅ **Test Coverage**

- **Unit Tests**: 30 tests covering SSO Server and Broker functionality
- **Feature Tests**: HTTP endpoints, authentication flows, middleware protection
- **Integration Tests**: 7 end-to-end tests with 28 assertions

### 🔍 **Manual Testing**

1. **Login Flow**: Visit `http://localhost:8001/login` → Login with `test@example.com` / `password`
2. **Auto-Login**: Open `http://localhost:8002/home` → Should auto-login
3. **Centralized Logout**: Logout from either broker → Both should require re-login

## 📦 Laravel Packages Integration

### 🎯 **Why Use Laravel Packages?**

Our Laravel packages make SSO integration **incredibly simple** for existing Laravel applications. Instead of setting up separate applications, you can transform your current Laravel projects into SSO-enabled systems with just a few commands.

**Benefits:**
- ✅ **Easy Integration**: Add to existing Laravel apps without restructuring
- ✅ **Composer Management**: Updates and maintenance via Composer
- ✅ **Laravel Ecosystem**: Full compatibility with Laravel features
- ✅ **Minimal Configuration**: Sensible defaults with customization options
- ✅ **Auto-Discovery**: Automatic service provider registration

### 🔧 **Laravel SSO Server Package**

Transform any Laravel application into an SSO authentication server.

#### **Installation**

```bash
composer require awnali/laravel-sso-server
php artisan sso:setup-server
```

#### **What It Does**
- 📋 Publishes configuration files to `config/sso-server.php`
- 🗄️ Runs database migrations for broker and session management
- 🛣️ Registers SSO routes automatically (`/sso/login`, `/sso/logout`, etc.)
- 🛡️ Adds middleware for SSO protection
- 🎛️ Provides Artisan commands for broker management

#### **Configuration**

The setup command updates your `.env` file:

```env
# Laravel SSO Server Configuration
SSO_SESSION_LIFETIME=3600
SSO_ROUTES_PREFIX=sso
SSO_LOGIN_URL=/login
SSO_CACHE_ENABLED=true
SSO_VERIFY_SSL=true
```

#### **Creating Brokers**

```bash
# Create a new broker application
php artisan sso:create-broker my-app secret-key https://my-app.com --name="My Application"

# List all brokers
php artisan sso:list-brokers

# Revoke a broker
php artisan sso:revoke-broker my-app
```

#### **Programmatic Broker Management**

```php
use Awnali\LaravelSsoServer\Models\SsoBroker;

// Create a broker
$broker = SsoBroker::create([
    'broker_id' => 'my-app',
    'broker_secret' => 'secret-key-123',
    'broker_name' => 'My Application',
    'broker_url' => 'https://my-app.com',
    'is_active' => true,
    'allowed_domains' => ['my-app.com', '*.my-app.com'],
]);

// Find active brokers
$activeBrokers = SsoBroker::active()->get();

// Verify broker credentials
if ($broker->verifySecret($providedSecret)) {
    // Valid broker
}
```

#### **Available Endpoints**

Once installed, these endpoints are automatically available:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/sso/login` | SSO login page |
| `POST` | `/sso/login` | Process login credentials |
| `POST` | `/sso/logout` | Logout and invalidate sessions |
| `GET` | `/sso/attach` | Attach broker to SSO session |
| `GET` | `/sso/userinfo` | Get authenticated user information |
| `POST` | `/sso/verify` | Verify SSO token validity |

### 🔗 **Laravel SSO Client Package**

Enable any Laravel application to authenticate via your SSO server.

#### **Installation**

```bash
composer require awnali/laravel-sso-client
php artisan sso:setup-client
```

#### **Configuration**

Add these settings to your `.env` file:

```env
# Laravel SSO Client Configuration
SSO_SERVER_URL=https://your-sso-server.com
SSO_BROKER_ID=your-app-id
SSO_BROKER_SECRET=your-secret-key
SSO_AUTO_ATTACH=true
SSO_CLIENT_ROUTES_PREFIX=auth/sso
```

#### **User Model Integration**

Add the SSO trait to your User model:

```php
use Awnali\LaravelSsoClient\Traits\HasSsoAuthentication;

class User extends Authenticatable
{
    use HasSsoAuthentication;
    
    // Your existing model code...
    
    /**
     * Create user from SSO data (optional customization)
     */
    public static function createFromSsoData(array $ssoData): self
    {
        return static::create([
            'name' => $ssoData['fullname'],
            'email' => $ssoData['email'],
            'sso_id' => $ssoData['username'],
            // Add any custom fields
        ]);
    }
}
```

#### **Route Protection**

Protect your routes with SSO authentication:

```php
// routes/web.php

// Require SSO authentication
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::resource('posts', PostController::class);
});

// Optional SSO (try to authenticate but don't require)
Route::middleware(['sso.optional'])->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/about', [AboutController::class, 'index']);
});

// Ensure broker is attached to SSO server
Route::middleware(['sso.attach'])->group(function () {
    Route::get('/api/user', function () {
        return auth()->user();
    });
});
```

#### **Manual Authentication**

For custom authentication flows:

```php
use Awnali\LaravelSsoClient\Services\SsoClientService;

class AuthController extends Controller
{
    public function login(SsoClientService $sso)
    {
        // Check if already authenticated via SSO
        if ($sso->isAttached()) {
            $userData = $sso->getUserInfo();
            
            if ($userData) {
                // Find or create user
                $user = User::where('email', $userData['email'])->first();
                if (!$user) {
                    $user = User::createFromSsoData($userData);
                }
                
                // Login the user
                Auth::login($user);
                
                return redirect()->intended('/dashboard');
            }
        }
        
        // Redirect to SSO server for authentication
        return redirect($sso->getLoginUrl());
    }
    
    public function logout(SsoClientService $sso)
    {
        // Logout locally
        Auth::logout();
        
        // Logout from SSO server (affects all brokers)
        return redirect($sso->getLogoutUrl());
    }
}
```

#### **Testing SSO Connection**

```bash
# Test connection to SSO server
php artisan sso:test-connection

# Clear local SSO sessions
php artisan sso:clear-sessions

# Debug SSO communication
php artisan sso:debug --verbose
```

### 🔧 **Advanced Package Configuration**

#### **Custom Authentication Guards**

Register SSO guard in `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    'sso' => [
        'driver' => 'sso',
        'provider' => 'users',
    ],
],
```

Use the SSO guard:

```php
// In your controller
if (Auth::guard('sso')->check()) {
    $user = Auth::guard('sso')->user();
}
```

#### **Event Handling**

Listen for SSO events:

```php
// In EventServiceProvider.php
use Awnali\LaravelSsoServer\Events\UserLoggedIn;
use Awnali\LaravelSsoServer\Events\UserLoggedOut;
use Awnali\LaravelSsoClient\Events\SsoUserAuthenticated;

protected $listen = [
    UserLoggedIn::class => [
        'App\Listeners\LogSsoLogin',
    ],
    
    SsoUserAuthenticated::class => [
        'App\Listeners\HandleSsoAuthentication',
    ],
];
```

```php
// App\Listeners\LogSsoLogin.php
class LogSsoLogin
{
    public function handle(UserLoggedIn $event)
    {
        Log::info('User logged in via SSO', [
            'user_id' => $event->user->id,
            'broker_id' => $event->brokerId,
            'ip_address' => request()->ip(),
        ]);
    }
}
```

#### **Custom Service Extensions**

Extend the SSO services for custom logic:

```php
// Server-side customization
use Awnali\LaravelSsoServer\Services\SsoServerService;

class CustomSsoServerService extends SsoServerService
{
    protected function authenticate($username, $password)
    {
        // Add custom authentication logic
        $user = User::where('email', $username)->first();
        
        if ($user && $this->validateCustomCriteria($user)) {
            return parent::authenticate($username, $password);
        }
        
        return ValidationResult::error('Custom validation failed');
    }
    
    private function validateCustomCriteria($user): bool
    {
        // Your custom validation logic
        return $user->is_active && $user->email_verified_at;
    }
}

// Register in AppServiceProvider
public function register()
{
    $this->app->singleton('sso-server', CustomSsoServerService::class);
}
```

```php
// Client-side customization
use Awnali\LaravelSsoClient\Services\SsoClientService;

class CustomSsoClientService extends SsoClientService
{
    protected function createUserFromSsoData(array $userData): User
    {
        return User::create([
            'name' => $userData['fullname'],
            'email' => $userData['email'],
            'sso_id' => $userData['username'],
            'department' => $userData['department'] ?? null,
            'role' => $userData['role'] ?? 'user',
            'last_sso_login' => now(),
        ]);
    }
}
```

### 🚀 **Package Usage Examples**

#### **E-commerce Multi-Store Setup**

```php
// Main store (SSO Server)
composer require awnali/laravel-sso-server
php artisan sso:setup-server
php artisan sso:create-broker store2 secret123 https://store2.example.com
php artisan sso:create-broker admin secret456 https://admin.example.com

// Store 2 (Client)
composer require awnali/laravel-sso-client
# Configure .env with SSO_BROKER_ID=store2
php artisan sso:setup-client

// Admin Panel (Client)
composer require awnali/laravel-sso-client
# Configure .env with SSO_BROKER_ID=admin
php artisan sso:setup-client
```

#### **Microservices Architecture**

```php
// API Gateway (SSO Server)
composer require awnali/laravel-sso-server
php artisan sso:create-broker user-service secret1 https://users.api.com
php artisan sso:create-broker order-service secret2 https://orders.api.com
php artisan sso:create-broker payment-service secret3 https://payments.api.com

// Each microservice (Client)
composer require awnali/laravel-sso-client
# Configure with respective broker credentials
```

### 🔍 **Package Troubleshooting**

#### **Common Issues & Solutions**

**1. Connection Refused**
```bash
# Check SSO server URL and network connectivity
php artisan sso:test-connection
```

**2. Invalid Broker Credentials**
```bash
# Verify broker configuration
php artisan sso:list-brokers
```

**3. Session Issues**
```php
// Clear sessions and cache
php artisan sso:clear-sessions
php artisan cache:clear
php artisan session:flush
```

**4. CORS Issues**
```php
// In SSO server config/cors.php
'allowed_origins' => ['https://broker1.com', 'https://broker2.com'],
'allowed_headers' => ['*'],
'allowed_methods' => ['*'],
```

#### **Debug Mode**

Enable detailed logging:

```env
SSO_DEBUG=true
LOG_LEVEL=debug
```

View SSO logs:
```bash
tail -f storage/logs/laravel.log | grep SSO
```

### 📊 **Package vs Complete Project Comparison**

| Feature | Laravel Packages | Complete Project |
|---------|------------------|------------------|
| **Setup Time** | ⚡ 5 minutes | 🕐 15-30 minutes |
| **Integration** | ✅ Add to existing apps | 🔧 Separate applications |
| **Maintenance** | 📦 Composer updates | 🛠️ Manual updates |
| **Customization** | 🎛️ Configuration files | 📝 Direct code editing |
| **Learning Curve** | 📈 Minimal | 📚 Moderate |
| **Production Ready** | ✅ Yes | ✅ Yes |
| **Testing** | 🧪 Package tests | 🧪 Full test suite |
| **Documentation** | 📖 Package docs | 📚 Complete docs |

### 🎯 **When to Use Each Approach**

#### **Use Laravel Packages When:**
- ✅ You have existing Laravel applications
- ✅ You want quick integration with minimal setup
- ✅ You prefer Composer-managed dependencies
- ✅ You need to add SSO to multiple existing projects
- ✅ You want automatic updates and maintenance

#### **Use Complete Project When:**
- ✅ You're building new applications from scratch
- ✅ You need full control over the codebase
- ✅ You want to understand the complete SSO implementation
- ✅ You need extensive customization beyond configuration
- ✅ You're learning SSO concepts and implementation

### 🚀 **Getting Started with Packages**

#### **Quick Start for Existing Laravel App**

```bash
# 1. Install the appropriate package
composer require awnali/laravel-sso-server  # For SSO server
# OR
composer require awnali/laravel-sso-client  # For client app

# 2. Run setup command
php artisan sso:setup-server  # For server
# OR
php artisan sso:setup-client  # For client

# 3. Configure .env file (setup command provides guidance)

# 4. Create brokers (server only)
php artisan sso:create-broker my-app secret-key https://my-app.com

# 5. Protect routes with middleware
# Add 'sso.auth' middleware to your routes

# 6. Test the integration
php artisan sso:test-connection  # For clients
```

#### **Real-World Implementation Example**

```php
// Example: Adding SSO to an existing e-commerce Laravel app

// 1. Install client package
composer require awnali/laravel-sso-client

// 2. Update User model
class User extends Authenticatable
{
    use HasSsoAuthentication;
    
    protected $fillable = [
        'name', 'email', 'password', 'sso_id'
    ];
}

// 3. Protect customer routes
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/account', [AccountController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
});

// 4. Optional SSO for public pages
Route::middleware(['sso.optional'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/categories', [CategoryController::class, 'index']);
});

// 5. Custom authentication handling
class AuthController extends Controller
{
    public function login(SsoClientService $sso)
    {
        if ($sso->isAttached()) {
            $userData = $sso->getUserInfo();
            if ($userData) {
                $user = User::firstOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['fullname'],
                        'sso_id' => $userData['username'],
                    ]
                );
                
                Auth::login($user);
                return redirect('/account');
            }
        }
        
        return redirect($sso->getLoginUrl());
    }
}
```

## 📚 Documentation

### 📖 **Complete Documentation**
- **[Quick Start Guide](docs/installation/quick-start.md)**: Get up and running in 10 minutes
- **[API Documentation](docs/api/endpoints.md)**: Complete API reference
- **[Configuration Guide](docs/configuration/advanced.md)**: Advanced configuration options
- **[Security Guide](SECURITY.md)**: Security best practices and policies
- **[Contributing Guide](CONTRIBUTING.md)**: How to contribute to the project

### 🔧 **Advanced Topics**
- **[Adding More Brokers](docs/examples/adding-brokers.md)**: Scale to multiple applications
- **[Production Deployment](docs/deployment/production.md)**: Deploy to production environments
- **[Troubleshooting](docs/troubleshooting/common-issues.md)**: Common issues and solutions
- **[Performance Optimization](docs/performance/optimization.md)**: Optimize for high traffic

## 🔍 How It Works

1. **Broker Attachment**: When a user visits a broker, it attaches to the SSO server using a unique session token
2. **Authentication**: Login credentials are sent to the SSO server for validation
3. **Session Sharing**: The SSO server creates a shared session that all brokers can access
4. **Auto-Login**: When visiting other brokers, they check with the SSO server for existing authentication
5. **Centralized Logout**: Logout requests are sent to the SSO server, invalidating the shared session

## 📁 Project Structure

```
laravel-sso/
├── server/                    # SSO Server application
│   ├── app/MySSOServer.php   # Core SSO server logic
│   ├── app/Http/Controllers/MyServerController.php
│   ├── tests/Unit/           # Unit tests
│   ├── tests/Feature/        # Feature tests
│   └── routes/api.php        # API endpoints
├── broker1/                  # First broker application
│   ├── app/MyBroker.php     # Broker client logic
│   ├── app/Http/Controllers/Auth/LoginController.php
│   ├── tests/Unit/          # Unit tests
│   ├── tests/Feature/       # Feature tests
│   └── routes/web.php       # Web routes
├── broker2/                 # Second broker application
│   └── [same structure as broker1]
├── tests/Integration/       # End-to-end integration tests
├── docs/                    # Comprehensive documentation
├── .github/                 # GitHub templates and workflows
└── run-tests.sh            # Automated test runner
```

## 🛡️ Security Features

- **🔐 Token-based Authentication**: Secure session tokens with cryptographic signatures
- **🛡️ CSRF Protection**: Cross-site request forgery protection
- **🔒 Session Isolation**: Isolated sessions between different brokers
- **⏰ Automatic Expiration**: Configurable session timeouts
- **🔑 Secure Password Hashing**: Bcrypt with configurable cost factor
- **🌐 HTTPS Support**: SSL/TLS encryption for production environments

## 🚀 Performance & Scalability

- **⚡ Optimized Queries**: Efficient database queries with proper indexing
- **🗄️ Caching Support**: Redis/Memcached integration for session storage
- **📊 Load Balancer Ready**: Stateless design for horizontal scaling
- **🔄 Connection Pooling**: Efficient database connection management
- **📈 Monitoring Ready**: Built-in logging and monitoring capabilities

## 🌍 SEO & Discoverability

### 🔍 **Keywords**
Laravel SSO, Single Sign-On Laravel, Laravel 12 Authentication, PHP SSO, Multi-Application Authentication, Laravel Security, Enterprise SSO, Microservices Authentication, Laravel Session Management, SSO Implementation, Laravel Package, Composer Package, SSO Server Package, SSO Client Package, Laravel Middleware, Authentication Package

### 🏷️ **Tags**
`laravel` `sso` `single-sign-on` `authentication` `php` `security` `microservices` `enterprise` `session-management` `api` `oauth` `saml` `identity-management` `access-control` `laravel-12` `composer-package` `laravel-package` `middleware` `service-provider`

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details on:

- 🐛 **Bug Reports**: How to report issues
- 💡 **Feature Requests**: Suggesting new features
- 🔧 **Pull Requests**: Contributing code
- 📝 **Documentation**: Improving documentation
- 🧪 **Testing**: Adding tests

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- **Laravel Framework**: Built with the amazing [Laravel](https://laravel.com/) framework
- **jasny/sso**: Powered by the [jasny/sso](https://github.com/jasny/sso) package
- **Community**: Thanks to all contributors and the Laravel community

## 📞 Support & Community

- **📖 Documentation**: Comprehensive guides and API reference
- **🐛 Issues**: [GitHub Issues](https://github.com/awnali/SSO-laravel-5/issues) for bug reports
- **💬 Discussions**: [GitHub Discussions](https://github.com/awnali/SSO-laravel-5/discussions) for questions
- **🔒 Security**: Email security@example.com for security issues
- **⭐ Star**: Star this repository if you find it useful!

---

**Ready to implement enterprise-grade SSO?** 

🚀 **For Existing Laravel Apps**: `composer require awnali/laravel-sso-server` or `composer require awnali/laravel-sso-client`

📚 **For New Projects**: [Get started now](docs/installation/quick-start.md) and have your multi-application authentication system running in minutes!

**Keywords**: Laravel 12 SSO, Single Sign-On, PHP Authentication, Enterprise Security, Microservices, Multi-Application Login, Session Management, API Authentication, Laravel Security, SSO Implementation, Laravel Package, Composer Package, Authentication Middleware