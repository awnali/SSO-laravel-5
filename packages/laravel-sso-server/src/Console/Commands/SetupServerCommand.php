<?php

namespace Awnali\LaravelSsoServer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sso:setup-server 
                            {--force : Overwrite existing configuration}';

    /**
     * The console command description.
     */
    protected $description = 'Set up the Laravel SSO Server package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Setting up Laravel SSO Server...');

        // Publish configuration
        $this->publishConfiguration();

        // Run migrations
        $this->runMigrations();

        // Create example broker
        $this->createExampleBroker();

        // Update environment file
        $this->updateEnvironment();

        $this->info('✅ Laravel SSO Server setup completed successfully!');
        $this->newLine();
        $this->info('Next steps:');
        $this->info('1. Configure your .env file with SSO settings');
        $this->info('2. Create brokers using: php artisan sso:create-broker');
        $this->info('3. Add SSO routes to your application');

        return self::SUCCESS;
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfiguration(): void
    {
        $this->info('Publishing configuration...');

        $configExists = File::exists(config_path('sso-server.php'));

        if ($configExists && !$this->option('force')) {
            if (!$this->confirm('Configuration file already exists. Overwrite?')) {
                $this->info('Skipping configuration publishing.');
                return;
            }
        }

        $this->call('vendor:publish', [
            '--tag' => 'sso-server-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('✓ Configuration published');
    }

    /**
     * Run the package migrations.
     */
    protected function runMigrations(): void
    {
        $this->info('Running migrations...');

        $this->call('vendor:publish', [
            '--tag' => 'sso-server-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->call('migrate');

        $this->info('✓ Migrations completed');
    }

    /**
     * Create an example broker for testing.
     */
    protected function createExampleBroker(): void
    {
        if ($this->confirm('Create an example broker for testing?', true)) {
            $this->call('sso:create-broker', [
                'broker-id' => 'example-app',
                'broker-secret' => 'example-secret-key',
                'broker-url' => 'http://localhost:8001',
                '--name' => 'Example Application',
            ]);
        }
    }

    /**
     * Update the environment file with SSO settings.
     */
    protected function updateEnvironment(): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->warn('No .env file found. Please create one and add SSO configuration.');
            return;
        }

        $envContent = File::get($envPath);

        $ssoSettings = [
            'SSO_SESSION_LIFETIME=3600',
            'SSO_ROUTES_PREFIX=sso',
            'SSO_LOGIN_URL=/login',
            'SSO_CACHE_ENABLED=true',
            'SSO_VERIFY_SSL=true',
        ];

        $needsUpdate = false;

        foreach ($ssoSettings as $setting) {
            $key = explode('=', $setting)[0];
            if (!str_contains($envContent, $key)) {
                $needsUpdate = true;
                break;
            }
        }

        if ($needsUpdate && $this->confirm('Add SSO configuration to .env file?', true)) {
            $envContent .= "\n\n# Laravel SSO Server Configuration\n";
            $envContent .= implode("\n", $ssoSettings) . "\n";

            File::put($envPath, $envContent);
            $this->info('✓ Environment file updated');
        }
    }
}