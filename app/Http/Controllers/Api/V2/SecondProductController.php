<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\GeneralSetting;
use App\Models\landlord\Domain;
use App\Models\landlord\Tenant;
use App\Models\Product_Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\ProductCollection;
use App\Http\Resources\Api\V2\ProductResourceV;
use App\Http\Resources\Api\V2\ProductCollectionV;
use Illuminate\Http\Request;

class SecondProductController extends Controller {
    //

    public function index( Request $request ) {
        $tenantId = $request->input( 'tenant_id' );
        $warehouseId = $request->input( 'warehouse_id' );
        $tenant = Tenant::find( $tenantId );
        if ( !$tenant ) {
            return response()->json( [ 'message'=> 'Tenant not found' ], 404 );
        }

        // Connected to the tenant database
        $tenancyDb = $tenant->tenancy_db_name;

        config( [ 'database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config( 'app.db_username' ),
            'password' => config( 'app.db_password' ),
        ] ] );
        DB::purge( 'tenant' );
        DB::reconnect( 'tenant' );

        $is_stock = DB::connection( 'tenant' )->table( 'general_settings' )->first( 'without_stock' )->without_stock;
        if ( $is_stock != 'yes' ) {
            if ( !$warehouseId ) {
                return response( [ 'message'=>'Warehouse id is needed since you counting stock for products' ] );
            }

            $warehouse = DB::connection( 'tenant' )->table( 'warehouses' )->where( 'id', $warehouseId )->first();
            if ( !$warehouse ) {
                return response( [ 'message'=>'Warehosue not found' ], 404 );
            }

            $products = DB::connection( 'tenant' )->table( 'products' )
            ->leftjoin( 'taxes', 'products.tax_id', '=', 'taxes.id' )
            ->leftjoin( 'product_warehouse', 'products.id', '=', 'product_warehouse.product_id' )
            ->select(
                'products.*',
                'taxes.name as tax_name',
                'taxes.rate as tax_rate',
                'taxes.is_active as tax_is_active',
                'product_warehouse.variant_id as variant_id',
                'product_warehouse.qty as warehouse_qty'
            )
            ->where( 'product_warehouse.warehouse_id', $warehouseId )
            ->where( 'products.is_active', true )
            ->get();
        } else {
            $products = DB::connection( 'tenant' )->table( 'products' )
            ->leftjoin( 'taxes', 'products.tax_id', '=', 'taxes.id' )
            ->select(
                'products.*',
                'taxes.name as tax_name',
                'taxes.rate as tax_rate',
                'taxes.is_active as tax_is_active'
            )
            ->where( 'products.is_active', true )
            ->get();
        }

        $product_varients = DB::connection( 'tenant' )->table( 'product_variants' )->get();
        $extra = DB::connection( 'tenant' )->table( 'extras' )->get();

        $productResources = ProductResource::Collection( $products );
        return response()->json( [
            'products'=>$productResources,
            'varient'=>$product_varients,
            'extra'=>$extra
        ] );
    }

    public function store( Request $request ) {

    }

    public function show( Request $request, $id ) {
        $tenantId = $request->input( 'tenant_id' );
        $warehouseId = $request->input( 'warehouse_id' );
        $tenant = Tenant::find( $tenantId );
        if ( !$tenant ) {
            return response()->json( [ 'message'=> 'Tenant not found' ], 400 );
        }
        // Connect to the tenant database
        $tenancyDb = $tenant->tenancy_db_name;

        config( [ 'database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config( 'app.db_username' ),
            'password' => config( 'app.db_password' ),
        ] ] );
        DB::purge( 'tenant' );
        DB::reconnect( 'tenant' );

        $is_stock = DB::connection( 'tenant' )->table( 'general_settings' )->first( 'without_stock' )->without_stock;
        if ( $is_stock != 'yes' ) {
            if ( !$warehouseId ) {
                return response( [ 'message'=>'Warehouse id is needed since you counting stock for products' ] );
            }

            $warehouse = DB::connection( 'tenant' )->table( 'warehouses' )->where( 'id', $warehouseId )->first();
            if ( !$warehouse ) {
                return response( [ 'message'=>'Warehosue not found' ], 404 );
            }

            $product = DB::connection( 'tenant' )->table( 'products' )
            ->leftjoin( 'taxes', 'products.tax_id', '=', 'taxes.id' )
            ->leftjoin( 'product_warehouse', 'products.id', '=', 'product_warehouse.product_id' )
            ->select(
                'products.*',
                'taxes.name as tax_name',
                'taxes.rate as tax_rate',
                'taxes.is_active as tax_is_active',
                'product_warehouse.variant_id as variant_id',
                'product_warehouse.qty as warehouse_qty'
            )
            ->where( 'product_warehouse.warehouse_id', $warehouseId )
            ->where( 'products.id', $id )
            ->where( 'products.is_active', true )
            ->first();
        } else {
            $product = DB::connection( 'tenant' )->table( 'products' )
            ->leftjoin( 'taxes', 'products.tax_id', '=', 'taxes.id' )
            ->select(
                'products.*',
                'taxes.name as tax_name',
                'taxes.rate as tax_rate',
                'taxes.is_active as tax_is_active'
            )
            ->where( 'products.id', $id )
            ->where( 'products.is_active', true )
            ->first();
        }
        $variant = DB::connection( 'tenant' )->table( 'product_variants' )
        ->where( 'product_id', $id )
        ->get();
        $products_extra_category = DB::connection( 'tenant' )->table( 'product_extra_categories' )
        ->where( 'product_id', $id )
        ->pluck( 'extra_category_id' );
        $extra = DB::connection( 'tenant' )->table( 'extras' )
        ->wherein( 'extra_category_id', $products_extra_category )
        ->get();

        if ( !$product ) {
            return response()->json( [ 'error' => 'Product not found' ], 404 );
        }
        // Return the product in JSON format
        $productResource = new ProductResource( $product );

        return response()->json( [
            'product'=>$productResource,
            'variant'=>$variant,
            'extra'=>$extra
        ], 201 );
    }

    public function showByCategoryId( Request $request, $category_id ) {
        $tenantId = $request->input( 'tenant_id' );
        $warehouseId = $request->input( 'warehouse_id' );
        $tenant = Tenant::find( $tenantId );
        if ( !$tenant ) {
            return response()->json( [ 'message'=> 'Tenant not found' ], 404 );
        } else if ( !$warehouseId ) {
            return response( [ 'message'=>'Warehouse id needed' ], 500 );
        }
        // Connect to the tenant database
        $tenancyDb = $tenant->tenancy_db_name;

        config( [ 'database.connections.tenant' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => $tenancyDb,
            'username' => config( 'app.db_username' ),
            'password' => config( 'app.db_password' ),
        ] ] );
        DB::purge( 'tenant' );
        DB::reconnect( 'tenant' );

        $warehouse = DB::connection( 'tenant' )->table( 'warehouses' )->where( 'id', $warehouseId )->first();
        if ( !$warehouse ) {
            return response( [ 'message'=>'Warehosue not found' ], 404 );
        }

        // Retrieve a products by category from the tenant database
        $product = DB::connection( 'tenant' )->table( 'products' )
        ->leftjoin( 'taxes', 'products.tax_id', '=', 'taxes.id' )
        ->leftjoin( 'product_warehouse', 'products.id', '=', 'product_warehouse.product_id' )
        ->select(
            'products.*',
            'taxes.name as tax_name',
            'taxes.rate as tax_rate',
            'taxes.is_active as tax_is_active',
            'product_warehouse.warehouse_id as warehouse_id',
            'product_warehouse.qty as warehouse_qty'
        )
        ->where( 'warehouse_id', $warehouseId )
        ->where( 'products.category_id', $category_id )
        ->where( 'products.is_active', true )
        ->get();

        if ( $product->isEmpty() ) {
            return response()->json( [ 'error' => 'Product not found for this category' ], 404 );
        }

        return ProductResource::Collection( $product );
    }

    public function menu_products( Request $request ) {
        dd(GeneralSetting::first( 'without_stock' )->without_stock);
        if ( GeneralSetting::first( 'without_stock' )->without_stock != 'yes' ) {
            $warehouse_id = $request->input( 'warehouse_id' );

            if ( !$warehouse_id ) {
                return response()->json( [ 'error' => 'Warehouse ID is required' ], 400 );
            }
            $warehouse = Warehouse::where( 'id', $warehouse_id )->first();
            if ( !$warehouse ) {
                return response( [ 'message'=>'Warehosue not found' ], 404 );
            }

            $allProducts = collect();

            Product::where('is_active', true)
            ->where('is_online', true)
            ->orderBy('id')
            ->chunk(1000, function ($products) use ($warehouse_id, &$allProducts) {
                // Eager load the relationships for the current chunk
                $products->load(['category', 'productVariants', 'extraCategories']);

                foreach ($products as $product) {
                    $quantity = 0;

                    if ($product->is_variant) {
                        $productWarehouse = Product_Warehouse::select(DB::raw('SUM(qty) as qty'))
                            ->where([
                                ['product_id', '=', $product->id],
                                ['warehouse_id', '=', $warehouse_id]
                            ])
                            ->groupBy('product_id')
                            ->first();

                        $quantity = $productWarehouse ? $productWarehouse->qty : 0;
                    } else {
                        $productWarehouse = Product_Warehouse::where([
                            ['product_id', '=', $product->id],
                            ['warehouse_id', '=', $warehouse_id]
                        ])->first();

                        $quantity = $productWarehouse ? $productWarehouse->qty : 0;
                    }

                    // Add warehouse_qty to each product
                    $product->warehouse_qty = $quantity;
                }

                // Merge this chunk with the main collection
                $allProducts = $allProducts->merge($products);
            });

            return response()->json(new ProductCollectionV($allProducts));
        } else {
            // If 'without_stock' is 'yes', fetch products with their relationships
            $products = Product::where( [
                [ 'is_active', '=', true ],
                [ 'is_online', '=', true ]
            ] )
            ->with( 'category', 'productVariants', 'extraCategories' )
            ->get();

            return response()->json(new ProductCollectionV($products));
        }

    }

    public function update( Request $request, $product ) {

    }

    public function destroy( $product ) {

    }
}
