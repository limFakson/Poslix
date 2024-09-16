<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Resources\Api\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\Api\CustomerCollection;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $customers = DB::connection('tenant')->table('customers')
        ->where('is_active', true)
        ->get();
        $customer_group = DB::connection('tenant')->table('customer_groups')
        ->where('is_active', true)
        ->get();

        $customerResources = new CustomerCollection($customers);

        return response()->json([
            'customers' => $customerResources,
            'customerGroups' => $customer_group
        ]);
    }

    public function store(StoreCustomerRequest $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }

        // Connected to the tenant database
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

        $customer_datas = DB::connection('tenant')->table('customers')->get();


        $customerData = $request->validated();
        $now = \Carbon\Carbon::now()->toDateTimeString();

        // Check for existing customer
        $existingCustomer = DB::connection('tenant')->table('customers')
            ->where(function($query) use ($customerData) {
                $query->where('name', '=', strtolower($customerData['name']));
            })
            ->first();

        if ($existingCustomer) {
            $customerResources = new CustomerResource($existingCustomer);
            return response()->json($customerResources, 200);
        }

        // Handle user creation if not provided
        $user_id = $customerData['userId'] ?? $this->getOrCreateGuestUser($tenantId);

        //Find the highest id in the table
        $highestId = DB::connection('tenant')->table('customers')->max('id');
        $newid = $highestId + 1;
        $createData = [
            'id'=>$newid,
            'name' => $customerData['name'],
            'email' => $customerData['email']??null,
            'user_id' => $user_id,
            'company_name' => $customerData['companyName']?? null,
            'phone_number' => $customerData['phoneNumber']??null,
            'city' => $customerData['city']?? null,
            'tax_no' => $customerData['taxNo']?? null,
            'address' => $customerData['address']?? null,
            'state' => $customerData['state']?? null,
            'country' => $customerData['country']?? null,
            'customer_group_id' => $customerData['customerGroupId']?? null,
            'postal_code' => $customerData['postalCode']?? null,
            'points' => $customerData['points']?? null,
            'deposit' => $customerData['deposit']?? null,
            'expense' => $customerData['expence']?? null,
            'wishlist' => $customerData['wishlist']?? null,
            'is_active' => $customerData['isActive'],
            'created_at'=>$now,
            'updated_at' => $now
        ];

        // Attempt to find or create a new customer record
        $customer = DB::connection('tenant')->table('customers')->insert($createData);
        $creatData = DB::connection('tenant')->table('customers')->find($newid);

        $customerResoources = new CustomerResource($creatData);
        return response()->json($customerResoources, 201);
    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $customer = DB::connection('tenant')->table('customers')
        ->find($id);

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return new CustomerResource($customer);
    }

    public function showByUserId(Request $request, $user_id)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connected to the tenant database
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

        $customer = DB::connection('tenant')->table('customers')
        ->where('user_id', $user_id)
        ->get();

        if ($customer->isEmpty()) {
            return response()->json(['error' => 'Customer not found for this user'], 404);
        }

        return CustomerResource::Collection($customer);
    }

    public function update(UpdateCustomerRequest $request, $customerId)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }

        // Connected to the tenant database
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

        $customerData = $request;
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $updateData = [
            'name' => $customerData['name']?? null,
            'email' => $customerData['email']?? null,
            'user_id' => $customerData['userId']?? null,
            'company_name' => $customerData['companyName']?? null,
            'phone_number' => $customerData['phoneNumber']?? null,
            'city' => $customerData['city']?? null,
            'tax_no' => $customerData['taxNo']?? null,
            'address' => $customerData['address']?? null,
            'state' => $customerData['state']?? null,
            'country' => $customerData['country']?? null,
            'customer_group_id' => $customerData['customerGroupId']?? null,
            'postal_code' => $customerData['postalCode']?? null,
            'points' => $customerData['points']?? null,
            'deposit' => $customerData['deposit']?? null,
            'expense' => $customerData['expence']?? null,
            'wishlist' => $customerData['wishlist']?? null,
            'is_active' => $customerData['isActive']?? null,
            'updated_at' => $now
        ];

        // Filter out null values
        $updateData = array_filter($updateData, function($value) {
            return!is_null($value);
        });
        // Update the customer details directly in the database
        DB::connection('tenant')->table('customers')
            ->where('id', $customerId)
            ->update($updateData);

        return response()->json($updateData, 200);
    }

    public function getOrCreateGuestUser($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return response()->json(["message" => "Tenant not found"], 400);
        }

        // Connect to the tenant's database
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

        // Check if a guest user exists
        $guest = DB::connection('tenant')->table('users')->where('name', 'Guest')->first();

        // If guest user doesn't exist, create one
        if (!$guest) {
            $guestId = DB::connection('tenant')->table('users')->insertGetId([
                'name' => 'Guest',
                'email' => 'guest@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('guest_password'),
                'phone' => '12345678',
                'role_id' => 5,
            ]);

            // Retrieve the newly created guest user
            $guest = DB::connection('tenant')->table('users')->find($guestId);
        }
        $guest = $guest->id;

        return $guest;
    }

    public function destroy($customer)
    {

    }
}
