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

## 🛠️ Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/awnali/SSO-laravel-5.git
cd SSO-laravel-5
```

### 2. Install Dependencies

Install dependencies for all applications:

```bash
# Server
cd server
composer install
cp .env.example .env
php artisan key:generate

# Broker1
cd ../broker1
composer install
cp .env.example .env
php artisan key:generate

# Broker2
cd ../broker2
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Configure Environment

#### SSO Server (.env)
```env
APP_NAME="SSO Server"
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/server/database/database.sqlite
```

#### Broker1 (.env)
```env
APP_NAME="Broker1"
APP_URL=http://localhost:8001
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/broker1/database/database.sqlite

# SSO Configuration
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker1
SSO_BROKER_SECRET=broker1_secret
```

#### Broker2 (.env)
```env
APP_NAME="Broker2"
APP_URL=http://localhost:8002
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/broker2/database/database.sqlite

# SSO Configuration
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker2
SSO_BROKER_SECRET=broker2_secret
```

### 4. Setup Databases

Create SQLite databases and run migrations:

```bash
# Server
cd server
touch database/database.sqlite
php artisan migrate

# Broker1
cd ../broker1
touch database/database.sqlite
php artisan migrate

# Broker2
cd ../broker2
touch database/database.sqlite
php artisan migrate
```

### 5. Create Test User

Add a test user to the SSO server:

```bash
cd server
php artisan tinker
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

### 6. Start Applications

Start all three applications in separate terminals:

```bash
# Terminal 1 - SSO Server
cd server
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2 - Broker1
cd broker1
php artisan serve --host=0.0.0.0 --port=8001

# Terminal 3 - Broker2
cd broker2
php artisan serve --host=0.0.0.0 --port=8002
```

## 🧪 Testing

### Automated Test Suite

This project includes a comprehensive test suite covering all SSO functionality:

#### Running All Tests
```bash
# Run the complete test suite
./run-tests.sh

# Or run specific test types
./run-tests.sh unit      # Unit tests only
./run-tests.sh feature   # Feature tests only
./run-tests.sh integration # Integration tests only (requires all apps running)
```

#### Test Coverage

**Unit Tests:**
- **SSO Server**: Broker validation, authentication, session management, token generation
- **Broker**: Initialization, configuration, getUserInfo, login functionality

**Feature Tests:**
- **Server API endpoints**: attach, login, userInfo, logout, multi-broker sessions
- **Broker authentication**: login/logout flows, auto-login, validation, middleware

**Integration Tests:**
- **Complete SSO flow**: End-to-end testing across all applications
- **Reverse flow testing**: Authentication from different brokers
- **Invalid credentials**: Error handling and security validation
- **Session isolation**: Multi-broker session management

#### Manual Testing

### 1. Test Login Flow
1. Visit `http://localhost:8001/login` (Broker1)
2. Login with: `test@example.com` / `password`
3. You should be redirected to the dashboard

### 2. Test Auto-Login
1. Open a new tab and visit `http://localhost:8002/home` (Broker2)
2. You should be automatically logged in without entering credentials
3. The same user should appear in the navigation

### 3. Test Logout
1. Logout from either Broker1 or Broker2
2. Try accessing `http://localhost:8001/home` or `http://localhost:8002/home`
3. You should be redirected to login on both applications

## 🔧 Configuration Details

### SSO Server Configuration

The SSO server is configured in `server/app/MySSOServer.php` with broker credentials:

```php
protected $brokers = [
    'broker1' => ['secret' => 'broker1_secret'],
    'broker2' => ['secret' => 'broker2_secret'],
];
```

### Broker Configuration

Each broker uses the `MyBroker` class to communicate with the SSO server. The configuration is loaded from environment variables.

## 📁 Project Structure

```
SSO-laravel-12/
├── server/          # SSO Server application
│   ├── app/
│   │   ├── MySSOServer.php
│   │   └── Http/Controllers/MyServerController.php
│   ├── tests/
│   │   ├── Unit/MySSOServerTest.php
│   │   └── Feature/SSOServerControllerTest.php
│   └── routes/api.php
├── broker1/         # First broker application
│   ├── app/
│   │   ├── MyBroker.php
│   │   └── Http/Controllers/Auth/LoginController.php
│   ├── tests/
│   │   ├── Unit/MyBrokerTest.php
│   │   └── Feature/SSOAuthenticationTest.php
│   └── routes/web.php
├── broker2/         # Second broker application
│   ├── app/
│   │   ├── MyBroker.php
│   │   └── Http/Controllers/Auth/LoginController.php
│   ├── tests/
│   │   ├── Unit/MyBrokerTest.php
│   │   └── Feature/SSOAuthenticationTest.php
│   └── routes/web.php
├── tests/
│   └── Integration/SSOIntegrationTest.php  # End-to-end tests
└── run-tests.sh     # Automated test runner
```

## 🔍 How It Works

1. **Broker Attachment**: When a user visits a broker, it attaches to the SSO server using a unique session token
2. **Authentication**: Login credentials are sent to the SSO server for validation
3. **Session Sharing**: The SSO server creates a shared session that all brokers can access
4. **Auto-Login**: When visiting other brokers, they check with the SSO server for existing authentication
5. **Centralized Logout**: Logout requests are sent to the SSO server, invalidating the shared session

## 🐛 Troubleshooting

### Common Issues

1. **CSRF Token Errors**: SSO endpoints are in `routes/api.php` to bypass CSRF protection
2. **Database Connection**: Ensure SQLite files exist and have proper permissions
3. **Port Conflicts**: Make sure ports 8000, 8001, and 8002 are available
4. **Environment Variables**: Double-check SSO configuration in `.env` files

### Debug Mode

Enable debug logging in `MyBroker.php` by uncommenting the debug lines to see detailed request/response information.

## 📝 Adding More Brokers

To add additional broker applications:

1. Create a new Laravel application
2. Install the jasny/sso package: `composer require jasny/sso:^0.2.3`
3. Copy the `MyBroker.php` and `LoginController.php` from an existing broker
4. Add the new broker to the SSO server's broker list
5. Configure the `.env` file with unique broker ID and secret

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com/)
- Uses [jasny/sso](https://github.com/jasny/sso) package for SSO functionality
- Upgraded to Laravel 12 for modern PHP compatibility