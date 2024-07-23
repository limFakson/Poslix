<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Models\landlord\Domain;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\ProductCollection;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        // $warehouseId = $request->input('warehouse_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 404);
        }
        // else if(!$warehouseId){
        //     return response(["message"=>"Warehouse id needed"], 500);
        // }
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

        // $warehouse = DB::connection('tenant')->table('warehouses')->where('id', $warehouseId)->first();
        // if(!$warehouse){
        //     return response(["message"=>"Warehosue not found"], 404);
        // }

        $products = DB::connection('tenant')->table('products')
        ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
        // ->leftjoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->select(
            'products.*',
            'taxes.name as tax_name',
            'taxes.rate as tax_rate',
            'taxes.is_active as tax_is_active'
        )
        // ->where('warehouse_id', $warehouseId)
        ->where('products.is_active', true)
        ->get();
        $product_varients = DB::connection('tenant')->table('product_variants')->get();
        $extra = DB::connection('tenant')->table('extras')->get();

        $productResources = ProductResource::Collection($products);
        return response()->json([
            'products'=>$productResources,
            'varient'=>$product_varients,
            'extra'=>$extra
        ]);
    }

    public function store(Request $request)
    {

    }

    public function show(Request $request, $id)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }
        // Connect to the tenant database
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

        // Retrieve a single product from the tenant database
        $product = DB::connection('tenant')->table('products')
        ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
        ->select(
            'products.*',
            'taxes.name as tax_name',
            'taxes.rate as tax_rate',
            'taxes.is_active as tax_is_active'
        )
        ->where('products.id',$id)
        ->where('products.is_active', true)
        ->get();
        $variant = DB::connection('tenant')->table('product_variants')
        ->where('product_id',$id)
        ->get();
        $products_extra_category = DB::connection('tenant')->table('product_extra_categories')
        ->where('product_id', $id)
        ->pluck('extra_category_id');
        $extra = DB::connection('tenant')->table('extras')
        ->wherein('extra_category_id',$products_extra_category)
        ->get();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        // Return the product in JSON format
        $productResource = ProductResource::Collection($product);

        return response()->json([
            "product"=>$productResource,
            "variant"=>$variant,
            "extra"=>$extra
        ],201);
    }

    public function showByCategoryId(Request $request, $category_id)
    {
        $tenantId = $request->input('tenant_id');
        $warehouseId = $request->input('warehouse_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 404);
        }else if(!$warehouseId){
            return response(["message"=>"Warehouse id needed"], 500);
        }
        // Connect to the tenant database
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

        $warehouse = DB::connection('tenant')->table('warehouses')->where('id', $warehouseId)->first();
        if(!$warehouse){
            return response(["message"=>"Warehosue not found"], 404);
        }

        // Retrieve a products by category from the tenant database
        $product = DB::connection('tenant')->table('products')
        ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
        ->leftjoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->select(
            'products.*',
            'taxes.name as tax_name',
            'taxes.rate as tax_rate',
            'taxes.is_active as tax_is_active',
            'product_warehouse.warehouse_id as warehouse_id',
            'product_warehouse.qty as warehouse_qty'
        )
        ->where('warehouse_id', $warehouseId)
        ->where('products.category_id', $category_id)
        ->where('products.is_active', true)
        ->get();

        if ($product->isEmpty()) {
            return response()->json(['error' => 'Product not found for this category'], 404);
        }

        return ProductResource::Collection($product);
    }

    public function menu_products(Request $request)
    {
        $subdomain = $request->input('subdomain');
        $warehouseId = $request->input('warehouse_id');
        if($subdomain){
            $subdomain_data = Domain::where('domain',$subdomain)->first();
            if(!$subdomain_data){
                return response(["message"=>"Domain not found"], 404);
            }
            $tenantId = $subdomain_data->tenant_id;
        }else{
            $tenantId = $request->input('tenant_id');
        }

        if(!$warehouseId){
            return response(["message"=>"Warehouse id needed"], 500);
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 404);
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

        $warehouse = DB::connection('tenant')->table('warehouses')->where('id', $warehouseId)->first();
        if(!$warehouse){
            return response(["message"=>"Warehosue not found"], 404);
        }

        $products = DB::connection('tenant')->table('products')
        ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
        ->leftjoin('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
        ->select(
            'products.*',
            'taxes.name as tax_name',
            'taxes.rate as tax_rate',
            'taxes.is_active as tax_is_active',
            'product_warehouse.warehouse_id as warehouse_id',
            'product_warehouse.qty as warehouse_qty'
        )
        ->where('warehouse_id', $warehouseId)
        ->where('products.is_active', true)
        ->where('products.is_online', true)
        ->get();
        $product_varients = DB::connection('tenant')->table('product_variants')->get();
        $extra = DB::connection('tenant')->table('extras')->get();

        $productResources = ProductResource::Collection($products);
        return response()->json([
            'products'=>$productResources,
            'varient'=>$product_varients,
            'extra'=>$extra
        ]);
    }

    public function update(Request $request, $product)
    {

    }

    public function destroy($product)
    {

    }
}
