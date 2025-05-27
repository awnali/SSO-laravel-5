# Quick Start Guide - Laravel 12 SSO

Get your Laravel 12 Single Sign-On system up and running in under 10 minutes!

## 🚀 Prerequisites

Before you begin, ensure you have:

- **PHP 8.2+** installed
- **Composer** for dependency management
- **Git** for version control
- **Web server** (Apache/Nginx) or use Laravel's built-in server

## 📦 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/awnali/SSO-laravel-5.git laravel-sso
cd laravel-sso
```

### 2. Install Dependencies

Install dependencies for all three applications:

```bash
# Root dependencies (for integration tests)
composer install

# SSO Server
cd server
composer install
cp .env.example .env
php artisan key:generate

# Broker 1
cd ../broker1
composer install
cp .env.example .env
php artisan key:generate

# Broker 2
cd ../broker2
composer install
cp .env.example .env
php artisan key:generate

# Return to root
cd ..
```

### 3. Configure Environment Variables

#### SSO Server Configuration

Edit `server/.env`:

```env
APP_NAME="SSO Server"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/server/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

#### Broker 1 Configuration

Edit `broker1/.env`:

```env
APP_NAME="Broker 1"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8001

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/broker1/database/database.sqlite

# SSO Configuration
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker1
SSO_BROKER_SECRET=broker1_secret_key_here

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

#### Broker 2 Configuration

Edit `broker2/.env`:

```env
APP_NAME="Broker 2"
APP_ENV=local
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=true
APP_URL=http://localhost:8002

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/broker2/database/database.sqlite

# SSO Configuration
SSO_SERVER_URL=http://localhost:8000/api/server
SSO_BROKER_ID=broker2
SSO_BROKER_SECRET=broker2_secret_key_here

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 4. Setup Databases

Create SQLite databases and run migrations:

```bash
# SSO Server
cd server
touch database/database.sqlite
php artisan migrate
php artisan db:seed  # Optional: creates sample data

# Broker 1
cd ../broker1
touch database/database.sqlite
php artisan migrate

# Broker 2
cd ../broker2
touch database/database.sqlite
php artisan migrate

cd ..
```

### 5. Create Test User

Add a test user to the SSO server:

```bash
cd server
php artisan tinker
```

In the Tinker console:

```php
use App\Models\User;

User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('password123')
]);

User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('admin123')
]);

exit
```

### 6. Start the Applications

Open three terminal windows and start each application:

**Terminal 1 - SSO Server:**
```bash
cd server
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 - Broker 1:**
```bash
cd broker1
php artisan serve --host=0.0.0.0 --port=8001
```

**Terminal 3 - Broker 2:**
```bash
cd broker2
php artisan serve --host=0.0.0.0 --port=8002
```

## ✅ Verify Installation

### 1. Check Application Status

Visit these URLs to ensure all applications are running:

- **SSO Server**: http://localhost:8000
- **Broker 1**: http://localhost:8001
- **Broker 2**: http://localhost:8002

### 2. Test SSO Functionality

#### Test Login Flow:

1. **Visit Broker 1**: Go to http://localhost:8001/login
2. **Login**: Use credentials `test@example.com` / `password123`
3. **Verify Dashboard**: You should see the dashboard with user information

#### Test Auto-Login:

1. **Open New Tab**: Visit http://localhost:8002/home
2. **Automatic Login**: You should be automatically logged in
3. **Check User**: Same user should appear in both applications

#### Test Centralized Logout:

1. **Logout**: Click logout in either application
2. **Check Both Apps**: Visit both broker URLs
3. **Verify Logout**: Both should redirect to login page

### 3. Run Tests

Verify everything works with the test suite:

```bash
# Run all tests
./run-tests.sh all

# Expected output:
# ✅ Server Unit Tests: 10 tests, 22 assertions
# ✅ Broker1 Unit Tests: 10 tests
# ✅ Broker2 Unit Tests: 10 tests
# ✅ Server Feature Tests: All passing
# ✅ Broker1 Feature Tests: All passing
# ✅ Broker2 Feature Tests: All passing
# ✅ Integration Tests: 7 tests, 28 assertions
```

## 🎯 Next Steps

### Production Deployment

1. **Environment Setup**: Configure production environment variables
2. **Database**: Set up MySQL/PostgreSQL for production
3. **SSL/HTTPS**: Configure SSL certificates
4. **Web Server**: Set up Apache/Nginx with proper configuration
5. **Security**: Review security settings and enable production mode

### Customization

1. **Branding**: Customize the UI and branding for your organization
2. **Additional Brokers**: Add more broker applications as needed
3. **User Management**: Implement user registration and profile management
4. **Permissions**: Add role-based access control if needed

### Monitoring

1. **Logging**: Set up centralized logging
2. **Monitoring**: Implement application monitoring
3. **Alerts**: Configure alerts for authentication failures
4. **Backups**: Set up automated database backups

## 🆘 Troubleshooting

### Common Issues

#### Port Already in Use
```bash
# Check what's using the port
lsof -i :8000

# Kill the process
kill -9 PID
```

#### Database Permission Issues
```bash
# Fix SQLite permissions
chmod 664 database/database.sqlite
chmod 775 database/
```

#### Composer Issues
```bash
# Clear composer cache
composer clear-cache

# Update dependencies
composer update
```

#### Session Issues
```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Getting Help

- **Documentation**: Check the [full documentation](../README.md)
- **Issues**: Create a [GitHub issue](https://github.com/awnali/SSO-laravel-5/issues)
- **Community**: Join our [discussions](https://github.com/awnali/SSO-laravel-5/discussions)

## 🎉 Success!

You now have a fully functional Laravel 12 SSO system! Your users can log in once and access all connected applications seamlessly.

**What's Next?**
- Explore the [API documentation](../api/endpoints.md)
- Learn about [advanced configuration](../configuration/advanced.md)
- Check out [real-world examples](../examples/use-cases.md)