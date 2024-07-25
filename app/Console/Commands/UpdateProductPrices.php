<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;

class UpdateProductPrices extends Command
{
    protected $signature = 'update:product-prices';
    protected $description = 'Update product prices from a CSV file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $csvFile = fopen(public_path('/Perfume_product.csv'), 'r');
        fgetcsv($csvFile); // Skip the header row

        while (($data = fgetcsv($csvFile, 3930, ",")) !== FALSE) {
            $id = $data[9];
            $code = $data[1];
            $price1 = $data[6];
            $price2 = $data[7];
            $price3 = $data[8];

            // Update the product in the database
            $tenantId = "boulevard";
            $tenant = Tenant::find($tenantId);
            $tenancyDb = $tenant->tenancy_db_name;

            config(['database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'database' => $tenancyDb,
                'username' => config('app.db_username'),
                'password' => config('app.db_password'),
            ]]);
            DB::purge('tenant');
            DB::reconnect('tenant');

            DB::connection('tenant')->table('products')
                ->where('id', $id)
                ->Where('code', $code)
                ->update([
                    'price1' => $price1,
                    'price2' => $price2,
                    'price3' => $price3,
                ]);
        }

        fclose($csvFile);

        $this->info('Product prices updated successfully.');
    }
}