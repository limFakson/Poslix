<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\Api\ProductWarehouseCollection;
use App\Http\Resources\Api\ProductWarehouseResource;
use App\Http\Resources\Api\ProductWarehouse;
use App\Http\Controllers\Controller;
use App\Models\Product_Warehouse;
use Illuminate\Http\Request;

class ProductWarehouseController extends Controller {
    //

    public function index( Request $request ) {
        $warehouse_id = $request->input( 'warehouse_id' );

        // Gets the product in each warehouse if warehouse id is provided or not
        $query = Product_Warehouse::query();
        if ( isset( $warehouse_id ) ) {
            $query->where( 'warehouse_id', $warehouse_id );
        }
        $product_warehouse = $query->get();

        return response()->json( new ProductWarehouseCollection( $product_warehouse ), 200 );
    }

    public function show( Request $request, $id ) {
        $warehouse_id = $request->input( 'warehouse_id' );

        // Gets the product in each warehouse if warehouse id is provided or not
        $query = Product_Warehouse::where( 'product_id', $id );
        if ( isset( $warehouse_id ) ) {
            $query->where( 'warehouse_id', $warehouse_id );
        }
        $product_warehouse = $query->first();

        if ( $product_warehouse == null ) {
            return response()->json( [ 'message'=>'product not available in the warehouse or not in stock' ], 404 );
        }

        return response()->json( new ProductWarehouseResource( $product_warehouse ), 200 );
    }
}