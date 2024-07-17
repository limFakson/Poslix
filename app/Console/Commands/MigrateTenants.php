<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\landlord\Tenant;

class MigrateAllTenants extends Command
{
    protected $signature = 'migrate:all {path}';
    protected $description = 'Migrate all tenants';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all tenants
        $tenants = Tenant::all();
        $path = $this->argument('path');
        $messages = []; // Initialize an array to hold messages

        // Check if the file exists
        if (!File::exists($path)) {
            $messages[] = 'Migration file does not exist.';
        }

        foreach ($tenants as $tenant) {
            try {
                // Set the tenant database name dynamically
                config(['database.connections.tenant' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'database' => $tenant->tenancy_db_name,
                    'username' => config('app.db_username'),
                    'password' => config('app.db_password'),
                ]]);
                // Purge the existing tenant database connection and reconnect
                DB::purge('tenant');
                DB::reconnect('tenant');

                $this->info('Migrating tenant: ' . $tenant->name . ' (Database: ' . $tenant->tenancy_db_name . ')');

                // Run the specific migration file
                $this->call('migrate', [
                    '--path' => $path,
                    '--database' => 'tenant',
                    '--force' => true,
                ]);

                $this->info('Migration completed for tenant: ' . $tenant->name);
            } catch (\Exception $e) {
                $messages[] = "Error migrating tenant {$tenant->name}: {$e->getMessage()}";
            }
        }

        // After processing all tenants, decide how to display the messages
        // For example, flash them to the session or print them to the console
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $this->line($message); // Print each message to the console
            }
        }

        $this->info('Process completed.');
    }
}
