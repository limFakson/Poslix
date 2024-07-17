<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\CategoryCollection;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

        $categories = DB::connection('tenant')->table('categories')->where('is_active', true)->get();
        $extracategories = DB::connection('tenant')->table('extra_categories')->get();
        $productextracategories = DB::connection('tenant')->table('product_extra_categories')->get();

        $categoryResources = new CategoryCollection($categories);
        return Response()->json([
            'categories'=>$categoryResources,
            'extra_category'=>$extracategories,
            'product_extra_category'=>$productextracategories
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

        $category = DB::connection('tenant')->table('categories')
        ->find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return new CategoryResource($category);
    }

    public function update(Request $request, $category)
    {

    }

    public function destroy($category)
    {

    }
}
