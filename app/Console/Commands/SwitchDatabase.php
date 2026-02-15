<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SwitchDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:switch {connection : The database connection to switch to (local/supabase)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch the database connection in .env file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = $this->argument('connection');

        if (!in_array($connection, ['local', 'supabase'])) {
            $this->error('Invalid connection. Use "local" or "supabase".');
            return 1;
        }

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            $this->error('.env file not found.');
            return 1;
        }

        $envContent = File::get($envPath);
        $newConnection = ($connection === 'local') ? 'pgsql' : 'supabase';

        // Update DB_CONNECTION
        if (preg_match('/^DB_CONNECTION=(.*)$/m', $envContent)) {
            $envContent = preg_replace('/^DB_CONNECTION=(.*)$/m', "DB_CONNECTION={$newConnection}", $envContent);
        } else {
            $envContent .= "\nDB_CONNECTION={$newConnection}";
        }

        File::put($envPath, $envContent);

        $this->info("Database connection switched to: {$connection} ({$newConnection})");
        
        // Clear config cache
        $this->call('config:clear');

        return 0;
    }
}
