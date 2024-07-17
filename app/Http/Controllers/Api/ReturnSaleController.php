<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductReturnsResource;
use App\Http\Resources\Api\ProductReturnsCollection;
use App\Http\Resources\Api\ReturnSaleResource;
use App\Http\Resources\Api\ReturnSaleCollection;
use App\Http\Requests\StoreReturnRequest;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use Illuminate\Http\Request;

class ReturnSaleController extends Controller
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

        $returns = DB::connection('tenant')->table('returns')->get();
        $product_returns = DB::connection('tenant')->table('product_returns')->get();

        return response()->json([
            'returns'=>new ReturnSaleCollection($returns),
            'product_returns'=>new ProductReturnsCollection($product_returns)
        ]);
    }

    public function store(StoreReturnRequest $request)
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

        $returnDatas = $request->all();
        $returnDatas = array_filter($returnDatas, function($value) {
            return is_array($value);
        });
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $account =DB::connection('tenant')->table('accounts')->where('is_active', true)->first();

        $processedreturns = [];
        foreach($returnDatas as $returnData){
            //Find the highest id in the table
            $highestId = DB::connection('tenant')->table('returns')->max('id');
            $newreturnid = $highestId + 1;
            $createData = [
                'id'=>$newreturnid,
                'user_id' => $returnData['userId'],
                'reference_no' => $returnData['referenceNo'],
                'cash_register_id' => $returnData['cashregisterId'],
                'sale_id' => $returnData['saleId'],
                'account_id' => $account->id,
                'customer_id' => $returnData['customerId']?? null,
                'warehouse_id' => $returnData['warehouseId']?? null,
                'currency_id' => $returnData['currencyId']?? null,
                'biller_id' => $returnData['billerId']?? null,
                'item' => $returnData['item']?? null,
                'total_qty' => $returnData['totalQty'],
                'total_discount' => $returnData['totalDiscount']?? null,
                'total_tax'=>$returnData['totalTax']?? null,
                'total_price'=>$returnData['totalPrice']?? null,
                'grand_total'=>$returnData['grandTotal']?? null,
                'document'=>$returnData['document']?? null,
                'exchange_rate'=>$returnData['exchangeRate']?? null,
                'order_tax_rate'=>$returnData['orderTaxRate']?? null,
                'order_tax'=>$returnData['orderTax']?? null,
                'return_note'=>$returnData['returnNote']?? null,
                'staff_note'=>$returnData['staffNote']?? null,
                'created_at' => $now,
                'updated_at' => $now
            ];
            $createData = array_filter($createData, function($value) {
                return!is_null($value);
            });
            $productreturn = $returnData['productReturn'];

            $Productreturns = [];
            foreach ($productreturn as $data) {
                $highestId = DB::connection('tenant')->table('product_returns')->max('id');
                $newId = $highestId + 1;

                $commonData = [
                    'id' => $newId,
                    'return_id'=>$newreturnid,
                    'product_id' => $data['productId'],
                    'product_batch_id' => $data['productBatchId'] ?? null,
                    'variant_id' => $data['variantId'] ?? null,
                    'imei_number' => $data['imeiNumber'] ?? null,
                    'qty' => $data['quantity'],
                    'sale_unit_id' => $data['saleUnitId'] ?? null,
                    'net_unit_price' => $data['netUnitPrice'] ?? null,
                    'discount' => $data['discount'] ?? null,
                    'tax_rate' => $data['taxRate'] ?? null,
                    'tax' => $data['tax'] ?? null,
                    'total' => $data['total'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $commonData = array_filter($commonData, function ($value) {
                    return !is_null($value);
                });

                $productId = $data['productId'];
                $returnQuantity = $data['quantity'];
                $product = DB::connection('tenant')->table('products')->where('id', $productId)->first();

                $productsale = DB::connection('tenant')->table('product_sales')
                ->where([
                    ['product_id', '=', $productId],
                    ['sale_id', '=', $returnData['saleId']],
                ])
                ->update([
                    'return_qty'=>$returnQuantity,
                    'updated_at'=>$now
                ]);

                if(isset($commonData['variant_id'])){
                    $variantId = $commonData['variant_id'];
                    $variant = DB::connection('tenant')->table('product_variants')
                    ->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId]
                    ])
                    ->first();

                    $warehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $returnData['warehouseId']]
                    ])
                    ->first();

                    $newQuantity = $variant->qty + $returnQuantity;
                    DB::connection('tenant')->table('product_variants')->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId]
                    ])->update(['qty' => $newQuantity]);

                    $newQuantity = $warehouse->qty + $returnQuantity;
                    DB::connection('tenant')->table('product_warehouse')->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $returnData['warehouseId']]
                    ])->update(['qty' => $newQuantity]);
                } else{
                    $warehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where([
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $returnData['warehouseId']]
                    ])
                    ->first();

                    $newQuantity = $warehouse->qty + $returnQuantity;
                    DB::connection('tenant')->table('product_warehouse')->where([
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $returnData['warehouseId']]
                    ])->update(['qty' => $newQuantity]);
                }

                if (!$product) {
                    return response()->json(['error' => 'Product not found'], 404);
                }
                $newQuantity = $product->qty + $returnQuantity;
                DB::connection('tenant')->table('products')->where('id', $productId)->update(['qty' => $newQuantity]);

                $productSaleId = DB::connection('tenant')->table('product_returns')->insertGetId($commonData);
                $Productreturns[] = ["data"=>$commonData];
            }

            $returnid = DB::connection('tenant')->table('returns')->insertGetId($createData);

            $Return = ["data" => $createData];
            $responseForReturn = ["Return:" => $Return, "Product_Return:"=>$Productreturns];
            $processedreturns[] = $responseForReturn;
        }


        return response()->json($processedreturns, 201);
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
        $return = DB::connection('tenant')->table('returns')
        ->where('id',$id)
        ->first();
        $product_returns = DB::connection('tenant')->table('product_returns')
        ->where('return_id',$id)
        ->get();

        if (!$return) {
            return response()->json(['error' => 'Return not found'], 404);
        }
        // Return the product in JSON format
        // $productResource = ProductResource::Collection($product);

        return response()->json([
            "return"=> new ReturnSaleResource($return),
            "product_returns"=> new ProductReturnsCollection($product_returns)
        ],201);
    }

    public function update(StoreReturnRequest $request, $id)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return response()->json(["message" => "Tenant not found"], 400);
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

        $return = DB::connection('tenant')->table('returns')->where('id', $id)->first();
        if(!$return){
            return response(["message"=>"Returns not in existence"]);
        }

        $returnData = $request->all();
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $updateData = [
            'user_id' => $returnData['userId'] ?? null,
            'reference_no' => $returnData['referenceNo'] ?? null,
            'cash_register_id' => $returnData['cashregisterId'] ?? null,
            'sale_id' => $returnData['saleId'] ?? null,
            'customer_id' => $returnData['customerId']?? null,
            'warehouse_id' => $returnData['warehouseId']?? null,
            'currency_id' => $returnData['currencyId']?? null,
            'biller_id' => $returnData['billerId']?? null,
            'item' => $returnData['item']?? null,
            'total_qty' => $returnData['totalQty'] ?? null,
            'total_discount' => $returnData['totalDiscount']?? null,
            'total_tax'=>$returnData['totalTax']?? null,
            'total_price'=>$returnData['totalPrice']?? null,
            'grand_total'=>$returnData['grandTotal']?? null,
            'document'=>$returnData['document']?? null,
            'exchange_rate'=>$returnData['exchangeRate']?? null,
            'order_tax_rate'=>$returnData['orderTaxRate']?? null,
            'order_tax'=>$returnData['orderTax']?? null,
            'return_note'=>$returnData['returnNote']?? null,
            'staff_note'=>$returnData['staffNote']?? null,
            'created_at' => $now,
            'updated_at' => $now
        ];

        // Filter out null values
        $updateData = array_filter($updateData, function($value) {
            return !is_null($value);
        });

        // Step 3: Handle Product Sales Update
        $productsell = $returnData['productReturn'];
        foreach ($productsell as $data) {
            // Find existing product sale entry
            $query = DB::connection('tenant')->table('product_returns')
            ->where('return_id', $id)
            ->where('product_id', $data['productId']);

            if (isset($data['variantId'])) {
                $query->where('variant_id', $data['variantId']);
            }

            $existingProductSale = $query->first();

            // Calculate quantity difference for stock adjustment
            $quantityDifference = $data['quantity'] - ($existingProductSale->qty ?? 0);

            if ($existingProductSale) {
                // Update existing product sale
                DB::connection('tenant')->table('product_sales')
                    ->where('id', $existingProductSale->id)
                    ->update([
                        'product_batch_id' => $data['productBatchId'] ?? $existingProductSale->product_batch_id,
                        'variant_id' => $data['variantId'] ?? $existingProductSale->variant_id,
                        'imei_number' => $data['imeiNumber'] ?? $existingProductSale->imei_number,
                        'qty' => $data['quantity'],
                        'sale_unit_id' => $data['saleUnitId'] ?? $existingProductSale->sale_unit_id,
                        'net_unit_price' => $data['netUnitPrice'] ?? $existingProductSale->net_unit_price,
                        'discount' => $data['discount'] ?? $existingProductSale->discount,
                        'tax_rate' => $data['taxRate'] ?? $existingProductSale->tax_rate,
                        'tax' => $data['tax'] ?? $existingProductSale->tax,
                        'total' => $data['total'] ?? $existingProductSale->total,
                        'updated_at' => $now
                    ]);
            } else {
                $highestId = DB::connection('tenant')->table('product_returns')->max('id');
                $newId = $highestId + 1;

                $commonData = [
                    'id' => $newId,
                    'return_id'=>$id,
                    'product_id' => $data['productId'],
                    'product_batch_id' => $data['productBatchId'] ?? null,
                    'variant_id' => $data['variantId'] ?? null,
                    'imei_number' => $data['imeiNumber'] ?? null,
                    'qty' => $data['quantity'],
                    'sale_unit_id' => $data['saleUnitId'] ?? null,
                    'net_unit_price' => $data['netUnitPrice'] ?? null,
                    'discount' => $data['discount'] ?? null,
                    'tax_rate' => $data['taxRate'] ?? null,
                    'tax' => $data['tax'] ?? null,
                    'total' => $data['total'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $commonData = array_filter($commonData, function ($value) {
                    return !is_null($value);
                });

                DB::connection('tenant')->table('product_sales')->insert($commonData);
            }

            // Update stock levels in Product Warehouse and Product/Variant
            $product = DB::connection('tenant')->table('products')
                ->where('id', $data['productId'])
                ->first();

            if ($product) {
                DB::connection('tenant')->table('products')
                    ->where('id', $data['productId'])
                    ->update(['qty' => $product->qty + $quantityDifference]);
            }

            if (isset($data['variantId'])) {
                $productVariant = DB::connection('tenant')->table('product_variants')
                    ->where('product_id', $data['productId'])
                    ->where('variant_id', $data['variantId'])
                    ->first();

                if ($productVariant) {
                    DB::connection('tenant')->table('product_variants')
                        ->where('product_id', $data['productId'])
                        ->where('variant_id', $data['variantId'])
                        ->update(['qty' => $productVariant->qty + $quantityDifference]);
                }

                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                ->where('warehouse_id', $returnData['warehouseId'])
                ->where('product_id', $data['productId'])
                ->where('variant_id', $data['varientId'])
                ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $returnData['warehouseId'])
                        ->where('product_id', $data['productId'])
                        ->where('variant_id', $data['varientId'])
                        ->update(['qty' => $productWarehouse->qty + $quantityDifference]);
                }
            } else {
                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $returnData['warehouseId'])
                    ->where('product_id', $data['productId'])
                    ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $returnData['warehouseId'])
                        ->where('product_id', $data['productId'])
                        ->update(['qty' => $productWarehouse->qty + $quantityDifference]);
                }
            }
        }

        DB::connection('tenant')->table('returns')->where('id', $id)->update($updateData);

        // Step 5: Return updated sale and product sale information
        $return = DB::connection('tenant')->table('returns')
        ->where('id',$id)
        ->first();
        $product_returns = DB::connection('tenant')->table('product_returns')
        ->where('return_id',$id)
        ->get();

        $saleResources = new ReturnSaleResource($data);
        $productSaleResources = new ProductReturnsCollection($product_sale);

        return response()->json([
            'sale' => $saleResources,
            'product_sale' => $productSaleResources
        ], 201);
    }

    public function destroy($product)
    {

    }
}