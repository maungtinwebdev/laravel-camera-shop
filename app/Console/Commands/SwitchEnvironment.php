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
    protected $signature = 'env:switch {mode : The environment mode (local_postgres, local_supabase, live_postgres, live_supabase)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch the environment configuration based on mode';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->argument('mode');
        
        // Define configurations for each mode
        $configs = [
            'local_postgres' => [
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
                'APP_URL' => 'http://localhost:8000',
                'FRONTEND_URL' => 'http://localhost:8000',
                'DB_CONNECTION' => 'pgsql',
                'GOOGLE_REDIRECT_URI' => 'http://localhost:8000/api/auth/google/callback',
            ],
            'local_supabase' => [
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
                'APP_URL' => 'http://localhost:8000',
                'FRONTEND_URL' => 'http://localhost:8000',
                'DB_CONNECTION' => 'supabase',
                'GOOGLE_REDIRECT_URI' => 'http://localhost:8000/api/auth/google/callback',
            ],
            'live_postgres' => [
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => 'https://camera-shop.laravel.cloud',
                'FRONTEND_URL' => 'https://camera-shop.laravel.cloud',
                'DB_CONNECTION' => 'pgsql',
                'GOOGLE_REDIRECT_URI' => 'https://camera-shop.laravel.cloud/api/auth/google/callback',
            ],
            'live_supabase' => [
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'APP_URL' => 'https://camera-shop.laravel.cloud',
                'FRONTEND_URL' => 'https://camera-shop.laravel.cloud',
                'DB_CONNECTION' => 'supabase',
                'GOOGLE_REDIRECT_URI' => 'https://camera-shop.laravel.cloud/api/auth/google/callback',
            ],
        ];

        if (!array_key_exists($mode, $configs)) {
            $this->error("Invalid mode: {$mode}");
            $this->info("Available modes:");
            foreach (array_keys($configs) as $key) {
                $this->line(" - {$key}");
            }
            return 1;
        }

        $settings = $configs[$mode];
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        $envContent = File::get($envPath);
        
        foreach ($settings as $key => $value) {
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);

        $this->info("Environment switched to: {$mode}");
        foreach ($settings as $key => $value) {
            $this->line(" - {$key}: {$value}");
        }
        
        $this->call('config:clear');

        return 0;
    }
}
