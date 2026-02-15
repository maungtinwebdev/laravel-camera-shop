<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SwitchEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:switch {environment : The environment to switch to (local/live/supabase)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch the environment configuration (DB, APP_URL, Google OAuth) in .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $environment = $this->argument('environment');

        // Normalize input
        $environment = strtolower($environment);
        if ($environment === 'supabase' || $environment === 'production') {
            $environment = 'live';
        }

        if (!in_array($environment, ['local', 'live'])) {
            $this->error('Invalid environment. Use "local" or "live" (alias: supabase).');
            return 1;
        }

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        $envContent = File::get($envPath);
        
        // Define settings for each environment
        $settings = [];
        if ($environment === 'local') {
            $settings = [
                'DB_CONNECTION' => 'pgsql',
                'APP_URL' => 'http://localhost:8000',
                'FRONTEND_URL' => 'http://localhost:8000',
                'GOOGLE_REDIRECT_URI' => 'http://localhost:8000/api/auth/google/callback',
            ];
        } else {
            // Live / Supabase
            $settings = [
                'DB_CONNECTION' => 'supabase',
                'APP_URL' => 'https://camera-shop.laravel.cloud',
                'FRONTEND_URL' => 'https://camera-shop.laravel.cloud',
                'GOOGLE_REDIRECT_URI' => 'https://camera-shop.laravel.cloud/api/auth/google/callback',
            ];
        }

        // Apply updates
        foreach ($settings as $key => $value) {
            if (preg_match("/^{$key}=(.*)$/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=(.*)$/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);

        $this->info("Environment switched to: {$environment}");
        foreach ($settings as $key => $value) {
            $this->line(" - {$key}: {$value}");
        }
        
        // Clear config cache
        $this->call('config:clear');

        return 0;
    }
}
