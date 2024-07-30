<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use App\Models\landlord\Tenant;
use App\Http\Resources\Api\SaleResource;
use App\Http\Resources\Api\SaleCollection;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Resources\Api\ProductSalesCollection;
use Illuminate\Http\Request;
use App\Events\Sale as SaleEvent;

class SaleController extends Controller
{
    //
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant){
            return response()->json(["message"=> "Tenant not found"], 400);
        }

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

        $sales = DB::connection('tenant')->table('sales')
        ->leftjoin('customers', 'sales.customer_id', '=', 'customers.id')
        ->select(
            'sales.*',
            'customers.name as customer_name',
            'customers.phone_number as customer_phone_number'
        )
        ->orderBy('sales.created_at', 'desc')
        ->get();
        $productSales = DB::connection('tenant')->table('product_sales')
        ->leftjoin('products', 'product_sales.product_id', '=', 'products.id')
        ->select(
            'product_sales.*',
            'products.name as product_name'
        )
        ->get();

        $saleResources = new SaleCollection($sales);
        $productSaleResources = new ProductSalesCollection($productSales);

        return Response()->json([
            'sales'=>$saleResources,
            'products_sale'=>$productSaleResources
        ]);
    }

    public function store(StoreSaleRequest $request)
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

        $saleDatas = $request->all();
        $saleDatas = array_filter($saleDatas, function($value) {
            return is_array($value);
        });
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $processedSales = [];
        foreach($saleDatas as $saleData){
            //Find the highest id in the table
            $highestId = DB::connection('tenant')->table('sales')->max('id');
            $newsaleid = $highestId + 1;
            $createData = [
                'id'=>$newsaleid,
                'user_id' => $saleData['userId'],
                'reference_no' => $saleData['referenceNo'],
                'cash_register_id' => $saleData['cashRegisterId'] ?? null,
                'table_id' => $saleData['tableId']?? null,
                'queue' => $saleData['queue']?? null,
                'customer_id' => $saleData['customerId'],
                'warehouse_id' => $saleData['warehouseId'],
                'biller_id' => $saleData['billerId'],
                'item' => $saleData['item'],
                'total_qty' => $saleData['totalQty']?? null,
                'total_discount' => $saleData['totalDiscount']?? null,
                'total_tax'=>$saleData['totalTax']?? null,
                'total_price'=>$saleData['totalPrice'],
                'grand_total'=>$saleData['grandTotal'],
                'currency_id'=>$saleData['currencyId']?? null,
                'exchange_rate'=>$saleData['exchangeRate']?? null,
                'order_tax_rate'=>$saleData['orderTaxRate']?? null,
                'order_tax'=>$saleData['orderTax']?? null,
                'order_discount_type'=>$saleData['orderDiscountType']?? null,
                'order_discount_value'=>$saleData['orderDiscountValue']?? null,
                'order_discount'=>$saleData['orderDiscount']?? null,
                'coupon_id'=>$saleData['couponId']?? null,
                'coupon_discount'=>$saleData['couponDiscount']?? null,
                'shipping_cost'=>$saleData['shippingCost']?? null,
                'sale_status'=>$saleData['saleStatus']?? null,
                'payment_status'=>$saleData['paymentStatus']?? null,
                'document'=>$saleData['document']?? null,
                'paid_amount'=>$saleData['paidAmount']?? null,
                'sale_type'=>$saleData['saleType'],
                'order_type'=>$saleData['orderType'],
                'payment_mode'=>$saleData['paymentMode']?? null,
                'sale_note'=>$saleData['saleNote']?? null,
                'staff_note'=>$saleData['staffNote']?? null,
                'created_at' => $now,
                'updated_at' => $now
            ];
            $createData = array_filter($createData, function($value) {
                return!is_null($value);
            });
            $productsell = $saleData['productSold'];

            $ProductSales = [];
            foreach ($productsell as $data) {
                $highestId = DB::connection('tenant')->table('product_sales')->max('id');
                $newId = $highestId + 1;

                $commonData = [
                    'id' => $newId,
                    'sale_id'=>$newsaleid,
                    'product_id' => $data['productId'],
                    'product_batch_id' => $data['productBatchId'] ?? null,
                    'variant_id' => $data['variantId'] ?? null,
                    'imei_number' => $data['imeiNumber'] ?? null,
                    'qty' => $data['quantity'],
                    'return_qty' => $data['returnQty'] ?? null,
                    'sale_unit_id' => $data['saleUnitId'],
                    'net_unit_price' => $data['netUnitPrice'] ?? null,
                    'discount' => $data['discount'] ?? null,
                    'tax_rate' => $data['taxRate'] ?? null,
                    'extras' => is_array($data['extras'] ?? null) ? json_encode($data["extras"] ?? null) : $data['extras'] ?? null,
                    'extra_names' => is_array($data['extraNames'] ?? null) ? json_encode($data['extraNames'] ?? null) : $data['extraNames'] ?? null,
                    'extra' => $data['extra'] ?? null,
                    'tax' => $data['tax'] ?? null,
                    'total' => $data['total'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now
                ];

                $commonData = array_filter($commonData, function ($value) {
                    return !is_null($value);
                });

                $productId = $data['productId'];
                $productQuantity = $data['quantity'];
                $product = DB::connection('tenant')->table('products')->where('id', $productId)->first();

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
                        ['warehouse_id', '=', $saleData['warehouseId']]
                    ])
                    ->first();

                    $newQuantity = $variant->qty - $productQuantity;
                    DB::connection('tenant')->table('product_variants')->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId]
                    ])->update(['qty' => $newQuantity]);

                    $newQuantity = $warehouse->qty - $productQuantity;
                    DB::connection('tenant')->table('product_warehouse')->where([
                        ['variant_id', '=', $variantId],
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $saleData['warehouseId']]
                    ])->update(['qty' => $newQuantity]);
                }else{
                    $warehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where([
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $saleData['warehouseId']]
                    ])
                    ->first();

                    $newQuantity = $warehouse->qty - $productQuantity;
                    DB::connection('tenant')->table('product_warehouse')->where([
                        ['product_id', '=', $productId],
                        ['warehouse_id', '=', $saleData['warehouseId']]
                    ])->update(['qty' => $newQuantity]);

                }
                if (!$product) {
                    return response()->json(['error' => 'Product not found'], 404);
                }
                $newQuantity = $product->qty - $productQuantity;
                DB::connection('tenant')->table('products')->where('id', $productId)->update(['qty' => $newQuantity]);

                $productSaleId = DB::connection('tenant')->table('product_sales')->insertGetId($commonData);
                $ProductSales[] = ["data"=>$commonData];
            }

            $sale = DB::connection('tenant')->table('sales')->insert($createData);

            $accountId =DB::connection('tenant')->table('accounts')->where('is_active', true)->first();

            $highestId = DB::connection('tenant')->table('payments')->max('id');
            $newPaymentId = $highestId + 1;
            $paymentData = [
                'id'=>$newPaymentId,
                'sale_id'=>$newsaleid,
                'cash_register_id' => $saleData['cashRegisterId'],
                'account_id'=> $accountId->id,
                'payment_reference'=>$saleData['paymentReference'],
                'user_id' => $saleData['userId'],
                'amount'=>$saleData['grandTotal'],
                'used_points'=>$saleData['userPoints']??null,
                'change'=>$saleData['change']??null,
                'paying_method'=>$saleData['payingMethod'],
                'payment_note'=>$saleData['payingNote']??null,
                'created_at' => $now,
                'updated_at' => $now
            ];
            $payment = DB::connection('tenant')->table('payments')->insert($paymentData);

            $sale = DB::connection('tenant')->table('sales')
                    ->leftjoin('customers', 'sales.customer_id', '=', 'customers.id')
                    ->select(
                        'sales.*',
                        'customers.name as customer_name',
                        'customers.phone_number as customer_phone_number'
                    )
                    ->where('sales.id',$newsaleid)
                    ->first();
            event(new SaleEvent(new SaleResource($sale), 'created', $tenantId));

            $responseForSale = ["Sale:" => $sale, "Product_Sale:"=>$ProductSales, "Payment_data:"=>$paymentData];
            $processedSales[] = $responseForSale;
        }

        return response()->json($processedSales, 201);
    }

    public function show(Request $request, $saleId)
    {
        $tenantId = $request->input('tenant_id');
        // $userId = $request->input('user_id');
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

        $sale = DB::connection('tenant')->table('sales')
        ->leftjoin('customers', 'sales.customer_id', '=', 'customers.id')
        ->select(
            'sales.*',
            'customers.name as customer_name',
            'customers.phone_number as customer_phone_number'
        )
        ->where('sales.id', $saleId)
        ->first();

        $product_sale = DB::connection('tenant')->table('product_sales')
        ->leftjoin('products', 'product_sales.product_id', '=', 'products.id')
        ->select(
            'product_sales.*',
            'products.name as product_name'
        )
        ->where('product_sales.sale_id', $saleId)
        ->get();

        $paymentData = DB::connection('tenant')->table('payments')->where("sale_id", $saleId)->get();

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        $saleResources = new SaleResource($sale);
        $productSaleResources = new ProductSalesCollection($product_sale);

        return response()->json([
            'sale'=>$saleResources,
            'product_sale'=>$productSaleResources,
            'payment'=>$paymentData
        ],201);
    }

    public function update(StoreSaleRequest $request, $saleId)
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

        $sale = DB::connection('tenant')->table('sales')->where('id', $saleId)->first();
        if(!$sale){
            return response(["message"=>"Sale not in existence"]);
        }

        $saleData = $request->all();
        $now = \Carbon\Carbon::now()->toDateTimeString();

        $updateData = [
            'reference_no' => $saleData['referenceNo'] ?? null,
            'user_id' => $saleData['userId'] ?? null,
            'cash_register_id' => $saleData['cashRegisterId'] ?? null,
            'table_id' => $saleData['tableId'] ?? null,
            'queue' => $saleData['queue'] ?? null,
            'customer_id' => $saleData['customerId'] ?? null,
            'warehouse_id' => $saleData['warehouseId'] ?? null,
            'biller_id' => $saleData['billerId'] ?? null,
            'item' => $saleData['item'] ?? null,
            'total_qty' => $saleData['totalQty'] ?? null,
            'total_discount' => $saleData['totalDiscount'] ?? null,
            'total_tax' => $saleData['totalTax'] ?? null,
            'total_price' => $saleData['totalPrice'] ?? null,
            'grand_total' => $saleData['grandTotal'] ?? null,
            'currency_id' => $saleData['currencyId'] ?? null,
            'exchange_rate' => $saleData['exchangeRate'] ?? null,
            'order_tax_rate' => $saleData['orderTaxRate'] ?? null,
            'order_tax' => $saleData['orderTax'] ?? null,
            'order_discount_type' => $saleData['orderDiscountType'] ?? null,
            'order_discount_value' => $saleData['orderDiscountValue'] ?? null,
            'order_discount' => $saleData['orderDiscount'] ?? null,
            'coupon_id' => $saleData['couponId'] ?? null,
            'coupon_discount' => $saleData['couponDiscount'] ?? null,
            'shipping_cost' => $saleData['shippingCost'] ?? null,
            'sale_status' => $saleData['saleStatus'] ?? null,
            'payment_status' => $saleData['paymentStatus'] ?? null,
            'paid_amount' => $saleData['paidAmount'] ?? null,
            'sale_type' => $saleData['saleType'] ?? null,
            'order_type' => $saleData['orderType'] ?? null,
            'payment_mode' => $saleData['paymentMode'] ?? null,
            'sale_note' => $saleData['saleNote'] ?? null,
            'staff_note' => $saleData['staffNote'] ?? null,
            'updated_at' => $now
        ];

        // Filter out null values
        $updateData = array_filter($updateData, function($value) {
            return !is_null($value);
        });

        // Step 3: Handle Product Sales Update
        $productsell = $saleData['productSold'];
        foreach ($productsell as $data) {
            // Find existing product sale entry
            $query = DB::connection('tenant')->table('product_sales')
            ->where('sale_id', $saleId)
            ->where('product_id', $data['productId']);

            if (isset($data['variantId'])) {
                $query->where('variant_id', $data['variantId']);
            }
            if(isset($data["extraNames"])){
                $query->whereJsonContains('extra_names', $data["extraNames"]);
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
                        'return_qty' => $data['returnQty'] ?? $existingProductSale->return_qty,
                        'sale_unit_id' => $data['saleUnitId'] ?? $existingProductSale->sale_unit_id,
                        'net_unit_price' => $data['netUnitPrice'] ?? $existingProductSale->net_unit_price,
                        'discount' => $data['discount'] ?? $existingProductSale->discount,
                        'tax_rate' => $data['taxRate'] ?? $existingProductSale->tax_rate,
                        'extras' => is_array($data['extras']) ? json_encode($data['extras']) : $data['extras'] ?? $existingProductSale->extras,
                        'extra_names' => is_array($data['extraNames']) ? json_encode($data['extraNames']) : $data['extraNames'] ?? $existingProductSale->extra_names,
                        'extra' => $data['extra'] ?? $existingProductSale->extra,
                        'tax' => $data['tax'] ?? $existingProductSale->tax,
                        'total' => $data['total'] ?? $existingProductSale->total,
                        'updated_at' => $now
                    ]);
            } else {
                $highestId = DB::connection('tenant')->table('product_sales')->max('id');
                $newId = $highestId + 1;

                $commonData = [
                    'id' => $newId,
                    'sale_id' => $saleId,
                    'product_id' => $data['productId'],
                    'product_batch_id' => $data['productBatchId'] ?? null,
                    'variant_id' => $data['variantId'] ?? null,
                    'imei_number' => $data['imeiNumber'] ?? null,
                    'qty' => $data['quantity'],
                    'return_qty' => $data['returnQty'] ?? null,
                    'sale_unit_id' => $data['saleUnitId'],
                    'net_unit_price' => $data['netUnitPrice'] ?? null,
                    'discount' => $data['discount'] ?? null,
                    'tax_rate' => $data['taxRate'] ?? null,
                    'extras' => is_array($data['extras'] ?? null) ? json_encode($data["extras"] ?? null) : $data['extras'] ?? null,
                    'extra_names' => is_array($data['extraNames'] ?? null) ? json_encode($data['extraNames'] ?? null) : $data['extraNames'] ?? null,
                    'extra' => $data['extra'] ?? null,
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
                    ->update(['qty' => $product->qty - $quantityDifference]);
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
                        ->update(['qty' => $productVariant->qty - $quantityDifference]);
                }

                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                ->where('warehouse_id', $saleData['warehouseId'])
                ->where('product_id', $data['productId'])
                ->where('variant_id', $data['variantId'])
                ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $saleData['warehouseId'])
                        ->where('product_id', $data['productId'])
                        ->where('variant_id', $data['variantId'])
                        ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
                }
            } else {
                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $saleData['warehouseId'])
                    ->where('product_id', $data['productId'])
                    ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $saleData['warehouseId'])
                        ->where('product_id', $data['productId'])
                        ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
                }
            }
        }

        // Step 4: Update Payment Information
        if (isset($saleData['payment'])) {
            $paymentData = [
                'paid_amount' => $saleData['payment']['paidAmount'] ?? null,
                'payment_status' => $saleData['payment']['paymentStatus'] ?? null,
                'payment_mode' => $saleData['payment']['paymentMode'] ?? null,
                'updated_at' => $now
            ];

            $paymentData = array_filter($paymentData, function($value) {
                return !is_null($value);
            });

            DB::connection('tenant')->table('payments')
                ->where('sale_id', $saleId)
                ->update($paymentData);
        }

        DB::connection('tenant')->table('sales')->where('id', $saleId)->update($updateData);

        // Step 5: Return updated sale and product sale information
        $data = DB::connection('tenant')->table('sales')
            ->leftjoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.*',
                'customers.name as customer_name',
                'customers.phone_number as customer_phone_number'
            )
            ->where('sales.id', $saleId)
            ->first();

        $product_sale = DB::connection('tenant')->table('product_sales')
            ->leftjoin('products', 'product_sales.product_id', '=', 'products.id')
            ->select(
                'product_sales.*',
                'products.name as product_name'
            )
            ->where('product_sales.sale_id', $saleId)
            ->get();

        $saleResources = new SaleResource($data);
        $productSaleResources = new ProductSalesCollection($product_sale);
        event(new SaleEvent($saleResources, 'updated', $tenantId));

        return response()->json([
            'sale' => $saleResources,
            'product_sale' => $productSaleResources
        ], 201);
    }

    public function destroyProductSale(Request $request, $id){
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

        $product_sale = DB::connection('tenant')->table('product_sales')
        ->where('id', $id)
        ->first();

        if (!$product_sale) {
            return response()->json(["message" => "Product sale not found"], 404);
        }

        $productId = $product_sale->product_id;
        $quantityDifference = $product_sale->qty;
        if(isset($product_sale->varient_id)){
            $varientId = $product_sale->varient_id;
        };

        $sale = DB::connection('tenant')->table('sales')
        ->where('id', $product_sale->sale_id)
        ->first();

        // Update stock levels in Product Warehouse and Product/Variant
        $product = DB::connection('tenant')->table('products')
        ->where('id', $productId)
        ->first();

        if ($product) {
            DB::connection('tenant')->table('products')
                ->where('id', $productId)
                ->update(['qty' => $product->qty - $quantityDifference]);
        }

        if (isset($varientId)) {
            $productVariant = DB::connection('tenant')->table('product_variants')
                ->where('product_id', $productId)
                ->where('variant_id', $varientId)
                ->first();

            if ($productVariant) {
                DB::connection('tenant')->table('product_variants')
                    ->where('product_id', $productId)
                    ->where('variant_id', $varientId)
                    ->update(['qty' => $productVariant->qty - $quantityDifference]);
            }

            $productWarehouse = DB::connection('tenant')->table('product_warehouse')
            ->where('warehouse_id', $sale->warehouse_id)
            ->where('product_id', $productId)
            ->where('variant_id', $varientId)
            ->first();

            if ($productWarehouse) {
                DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('product_id', $productId)
                    ->where('variant_id', $varientId)
                    ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
            }
        } else {
            $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                ->where('warehouse_id', $sale->warehouse_id)
                ->where('product_id', $productId)
                ->first();

            if ($productWarehouse) {
                DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('product_id', $productId)
                    ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
            }
        }

        $product_sale = DB::connection('tenant')->table('product_sales')
            ->where('id', $id)->delete();

        $psale = ["productSaleId"=>$id];
        event(new SaleEvent($psale, 'ProductSale-deleted', $tenantId));
        return response()->json(["message" => "Product sale successfully deleted"]);

    }

    public function destroy(Request $request, $saleId)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return response()->json(["message" => "Tenant not found"], 400);
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

        $sale = DB::connection('tenant')->table('sales')
            ->where('id', $saleId)
            ->first();

        if (!$sale) {
            return response()->json(["message" => "Sale not found"], 404);
        }

        $productSales = DB::connection('tenant')->table('product_sales')
            ->where('sale_id', $saleId)
            ->get();

        foreach ($productSales as $productSale) {
            $productId = $productSale->product_id;
            $quantityDifference = $productSale->qty;

            $product = DB::connection('tenant')->table('products')
                ->where('id', $productId)
                ->first();

            if ($product) {
                DB::connection('tenant')->table('products')
                    ->where('id', $productId)
                    ->update(['qty' => $product->qty - $quantityDifference]);
            }

            if (isset($productSale->varient_id)) {
                $variantId = $productSale->varient_id;

                $productVariant = DB::connection('tenant')->table('product_variants')
                    ->where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->first();

                if ($productVariant) {
                    DB::connection('tenant')->table('product_variants')
                        ->where('product_id', $productId)
                        ->where('variant_id', $variantId)
                        ->update(['qty' => $productVariant->qty - $quantityDifference]);
                }

                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('product_id', $productId)
                    ->where('variant_id', $variantId)
                    ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $sale->warehouse_id)
                        ->where('product_id', $productId)
                        ->where('variant_id', $variantId)
                        ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
                }
            } else {
                $productWarehouse = DB::connection('tenant')->table('product_warehouse')
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->where('product_id', $productId)
                    ->first();

                if ($productWarehouse) {
                    DB::connection('tenant')->table('product_warehouse')
                        ->where('warehouse_id', $sale->warehouse_id)
                        ->where('product_id', $productId)
                        ->update(['qty' => $productWarehouse->qty - $quantityDifference]);
                }
            }

            DB::connection('tenant')->table('product_sales')
                ->where('id', $productSale->id)
                ->delete();
        }

        DB::connection('tenant')->table('sales')
            ->where('id', $saleId)
            ->delete();
        $sale = ["id"=>$saleId];
        event(new SaleEvent($sale, 'deleted', $tenantId));
        return response()->json(["message" => "Sale successfully deleted"]);
    }

}
