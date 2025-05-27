# Laravel SSO (Single Sign-On) Implementation

A complete Single Sign-On (SSO) implementation using Laravel 12 with one SSO server and multiple broker applications. This allows users to log in once and access all connected applications seamlessly.

## 🚀 Features

- **Single Sign-On**: Log in once, access all applications
- **Auto-login**: Automatic authentication across broker applications
- **Centralized logout**: Logout from one application logs out from all
- **Laravel 12 compatible**: Fully updated for the latest Laravel version
- **Easy setup**: Simple configuration and deployment

## 📋 Requirements

- PHP ^8.2
- Laravel ^12.0
- Composer
- SQLite (or MySQL/PostgreSQL)

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

## 🧪 Testing the SSO Flow

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
SSO-laravel-5/
├── server/          # SSO Server application
│   ├── app/
│   │   ├── MySSOServer.php
│   │   └── Http/Controllers/MyServerController.php
│   └── routes/api.php
├── broker1/         # First broker application
│   ├── app/
│   │   ├── MyBroker.php
│   │   └── Http/Controllers/Auth/LoginController.php
│   └── routes/web.php
└── broker2/         # Second broker application
    ├── app/
    │   ├── MyBroker.php
    │   └── Http/Controllers/Auth/LoginController.php
    └── routes/web.php
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