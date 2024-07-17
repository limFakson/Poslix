<?php

namespace App\Livewire\Sale;

use App\Http\Controllers\SaleController;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AutocompleteSearch extends Component
{
    public $searchTerm;
    public $searchResults;

    public function mount($warehouse_id)
    {

        $query = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id');
        if (config('without_stock') == 'no') {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id],
                ['product_warehouse.qty', '>', 0]
            ]);
        } else {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id]
            ]);
        }

        $lims_product_warehouse_data = $query->whereNull('product_warehouse.variant_id')
            ->whereNull('product_warehouse.product_batch_id')
            ->select('product_warehouse.*',  'products.is_embeded')
            ->get();

        config()->set('database.connections.mysql.strict', false);
        DB::reconnect(); //important as the existing connection if any would be in strict mode

        $query = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id');

        if (config('without_stock') == 'no') {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id],
                ['product_warehouse.qty', '>', 0]
            ]);
        } else {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id]
            ]);
        }

        $lims_product_with_batch_warehouse_data = $query->whereNull('product_warehouse.variant_id')
            ->whereNotNull('product_warehouse.product_batch_id')
            ->select('product_warehouse.*', 'products.is_embeded')
            ->groupBy('product_warehouse.product_id')
            ->get();

        //now changing back the strict ON
        config()->set('database.connections.mysql.strict', true);
        DB::reconnect();

        $query = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id');
        if (config('without_stock') == 'no') {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id],
                ['product_warehouse.qty', '>', 0]
            ]);
        } else {
            $query = $query->where([
                ['products.is_active', true],
                ['product_warehouse.warehouse_id', $warehouse_id],
            ]);
        }

        $lims_product_with_variant_warehouse_data = $query->whereNotNull('product_warehouse.variant_id')
            ->select('product_warehouse.*', 'products.is_embeded')
            ->get();
        //Log::debug($lims_product_with_batch_warehouse_data);

        //product without variant
        $searchResults = [];
        foreach ($lims_product_warehouse_data as $product_warehouse) {
            $product = Product::find($product_warehouse->product_id);
            if ($product) {
                $product->batch = ProductBatch::select('id', 'batch_no', 'expired_date')->find($product_warehouse->product_batch_id);
                if ($product->batch)
                    $product->expired_date = date(config('date_format'), strtotime($product->batch->expired_date));
                if ($product_warehouse->is_embeded)
                    $product->is_embeded = $product_warehouse->is_embeded;
                else
                    $product->is_embeded = 0;

                $product->variant = ProductVariant::select('item_code')->FindExactProduct($product_warehouse->product_id, $product_warehouse->variant_id)->first();
                if ($product->variant) {
                    $product->item_code = $product->variant->item_code;
                    $product->name = htmlspecialchars($product->name);
                }

                $product->label= $product->code .' ('. $product->name.')';
                $product->value=$product->code;

                $searchResults[] = $product;
            }
        }
        //retrieve product with type of digital, combo and service
        $products = Product::whereNotIn('type', ['standard'])->where('is_active', true)->get();

        foreach ($products as $product) {
            $searchResults[] = $product;
        }
        $this->searchResults = $searchResults;

        /*$results = [];
        foreach ($searchResults as $result) {
            $results[] = [
                'label' => $result->name, // Use 'label' for the autocomplete suggestion text
                'value' => $result->code // Use 'value' for the autocomplete value
            ];
        }*/

        //$this->searchResults = $results;
        //$this->dispatch('searchResultsLoaded', $results);
        /*$controller = new SaleController();
        $this->searchResults = $controller->getProduct($warehouse_id);        
        Log::debug($this->searchResults);*/
    }

    public function updatedSearchTerm($value)
    {

        //$this->searchResults = YourModel::where('name', 'like', '%'.$value.'%')->limit(10)->get();
    }


    public function render()
    {
        return view('livewire.sale.autocomplete-search');
    }
}
