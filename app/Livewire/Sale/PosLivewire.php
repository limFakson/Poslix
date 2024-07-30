<?php

namespace App\Livewire\Sale;

use App\Http\Controllers\SaleController;
use App\Mail\SaleDetails;
use App\Models\Account;
use App\Models\CashRegister;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\CustomField;
use App\Models\Extras;
use App\Models\GiftCard;
use App\Models\MailSetting;
use App\Models\Payment;
use App\Models\PaymentWithCheque;
use App\Models\PaymentWithCreditCard;
use App\Models\PaymentWithGiftCard;
use App\Models\PosSetting;
use App\Models\Product;
use App\Models\Product_Sale;
use App\Models\Product_Warehouse;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Models\RewardPointSetting;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\Variant;
use App\Models\WhatsappSmsSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Json;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Stripe\Stripe;
use Srmklive\PayPal\Services\ExpressCheckout;
use Twilio\Rest\Client;
use App\Events\Sale as SaleEvent;

class PosLivewire extends Component
{
    use WithFileUploads;

    public $lims_pos_setting_data, $lims_table_list, $lims_warehouse_list, $lims_biller_list, $lims_customer_list, $custom_fields, $lims_product_array, $general_setting, $options, $lims_customer_group_all;

    public $lims_reward_point_setting_data, $role_has_permissions_list, $permission_list, $lims_category_list, $lims_brand_list, $product_number, $lims_product_list, $alert_product_count;

    public $all_permission;

    public $customer_active, $keybord_active = 0, $lims_tax_list, $lims_coupon_list, $lims_sale_data, $lims_product_sale_data;

    public $warehouse_id, $table_id, $customer_id, $biller_id, $category_id, $brand_id;

    public $order_tax_rate = '0', $shipping_cost = 0, $coupon_code, $order_discount_type = 'Flat', $custom_methods, $order_discount_value = 0, $total_discount = 0;

    public $grand_total = 0;

    public $items = [];

    public $qtyIndex = 0;

    public $radioSelectedOption;

    public $checkboxSelectedOptions = [], $gift_card = [];

    public $hasExtra = false;

    public $added = false;

    public $loaded = false;

    public $changedAmount = false;

    public $payment_data = [];

    public $payment_method = 1, $recent_sale = [], $recent_draft = [], $order_tax = 0;

    public $sale_status = 1;

    public $change_amount = 0, $coupon;

    public $currency, $coupon_discount = 0, $customer_group_rate = 1;

    public $role_id;

    public $deposit = [], $points = [], $customer;

    public $lims_product_list_with_variant;

    public function mount($data)
    {
        $this->lims_pos_setting_data = $data['lims_pos_setting_data'];
        $this->lims_warehouse_list = $data['lims_warehouse_list'];
        $this->lims_table_list = $data['lims_table_list'];
        $this->lims_biller_list = $data['lims_biller_list'];
        $this->lims_customer_list = $data['lims_customer_list'];
        $this->custom_fields = $data['custom_fields'];
        $this->general_setting = $data['general_setting'];
        $this->options = $data['options'];
        $this->lims_reward_point_setting_data = $data['lims_reward_point_setting_data'];
        $this->role_has_permissions_list = $data['role_has_permissions_list'];
        $this->permission_list = $data['permission_list'];
        $this->lims_category_list = $data['lims_category_list'];
        $this->lims_brand_list = $data['lims_brand_list'];
        $this->product_number = $data['product_number'];
        $this->lims_tax_list = $data['lims_tax_list'];
        $this->lims_coupon_list = $data['lims_coupon_list'];
        $this->recent_sale = $data['recent_sale'];
        $this->recent_draft = $data['recent_draft'];
        $this->all_permission = $data['all_permission'];
        $this->lims_customer_group_all = $data['lims_customer_group_all'];
        //$this->lims_product_list_with_variant = $data['lims_product_list_with_variant'];
        $this->lims_product_list = $data['lims_product_list'];
        $this->role_id = Auth::user()->role_id;
        $this->biller_id = 1; //Auth::user()->biller_id;
        //Log::debug('biller id : ' . $this->biller_id);
        $this->customer_active = DB::table('permissions')
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where([
                ['permissions.name', 'customers-add'],
                ['role_id', Auth::user()->role_id]
            ])->first();

        $this->currency = Currency::find($this->general_setting->currency);
        $this->gift_card = GiftCard::where("is_active", true)->whereDate('expired_date', '>=', date("Y-m-d"))->get(['id', 'card_no', 'amount', 'expense']);
        //Log::debug('gift card count : ' . count($this->gift_card));
        $this->alert_product_count = DB::table('products')->where('is_active', true)->whereColumn('alert_quantity', '>', 'qty')->count();

        //Log::debug('alert product count : ');
        //Log::debug($this->alert_product_count);
        //Log::debug(count(Auth::user()->unreadNotifications));

        $this->options = $data['options'];
        $this->custom_methods = DB::table('custom_methods')->where('active', 1)->get();
        if (is_null($this->general_setting))
            $this->general_setting = DB::table('general_settings')->latest()->first();

        if ($this->lims_warehouse_list) {
            $this->warehouse_id = $this->lims_warehouse_list[0]->id;
        }

        if ($this->lims_customer_list) {
            $this->customer_id = $this->lims_customer_list[0]->id;
        }

        //dd($this->lims_customer_list);

        if ($this->lims_pos_setting_data)
            $this->keybord_active = $this->lims_pos_setting_data->keybord_active;
        else
            $this->keybord_active = 0;


        if (Auth::user()->role_id > 2 && config('staff_access') == 'own') {
            $recent_sale = Sale::select('id', 'reference_no', 'customer_id', 'grand_total', 'created_at')->where([
                ['sale_status', 1],
                ['user_id', Auth::id()]
            ])->orderBy('id', 'desc')->take(10)->get();
            $recent_draft = Sale::select('id', 'reference_no', 'customer_id', 'grand_total', 'created_at')->where([
                ['sale_status', 3],
                ['user_id', Auth::id()]
            ])->orderBy('id', 'desc')->take(10)->get();
        } else {
            $recent_sale = Sale::select('id', 'reference_no', 'customer_id', 'grand_total', 'created_at')->where('sale_status', 1)->orderBy('id', 'desc')->take(10)->get();
            $recent_draft = Sale::select('id', 'reference_no', 'customer_id', 'grand_total', 'created_at')->where('sale_status', 3)->orderBy('id', 'desc')->take(10)->get();
        }
        $lims_coupon_list = Cache::remember('coupon_list', 60 * 60 * 24 * 30, function () {
            return Coupon::where('is_active', true)->get();
        });



        $this->payment_method = 1;

        $this->payment_data = [
            'payment_method' => 1,
            'paying_amount' => 0,
            'paid_amount' => 0,
            'change' => 0,
            'cheque_no' => '',
            'paid_by_id' => '1',
            'gift_card_id' => '',
            'payment_note' => '',
            'sale_note' => '',
            'staff_note' => ''
        ];
    }

    public function render()
    {
        $this->total_discount = 0;
        $this->hasExtra = false;
        $this->added = false;
        foreach ($this->items as $index => $item) {
            //Log::debug('tax rate : ' . $item['tax_rate']);
            $item['price'] = $item['original_price'];

            if ($item['discount'] > 0) {
                $item['price'] = $item['price'] - $item['discount'];
            }

            if ($item['tax_rate'] !== '0') {
                $tax_rate = explode('|', $item['tax_rate']);
                $item['tax'] = $tax_rate[0];
                $item['total_tax'] = ($item['price'] * ($tax_rate[0] / 100));
                $item['price'] = $item['price'] + $item['total_tax'];
            }

            $this->items[$index]['tax'] = $item['tax'];
            $this->items[$index]['total_tax'] = $item['total_tax'] * $item['qty'];
            $this->items[$index]['price'] = $item['price'];
            $this->items[$index]['net_unit_price'] = $item['display_unit_price'] - $item['discount'];;
            $this->items[$index]['total'] = $item['qty'] * $item['price'];
            //$this->total_discount += $item['discount'];

            // if ($this->qtyIndex == $index && count($item['extra_categories']) > 0) {
            //     $this->hasExtra = true;
            //     $this->added = true;
            // }
            $extraIds = $item['extras'];
            if (isset($item['single_extras']) && count([$item['single_extras']]) > 0 && !empty($item['single_extras']))
                $extraIds = array_merge($item['extras'], [$item['single_extras']]);

            $totalExtra = 0;
            if (count($extraIds) > 0) {
                $extras = Extras::whereIn('id', $extraIds)->get();
                $this->items[$index]['extra_names'] = [];

                if ($extras) {
                    foreach ($extras as $extra) {
                        $tax = 0;
                        $tax_rate = 0;
                        if ($item['tax_rate'] !== '0') {
                            $tax_rate = explode('|', $item['tax_rate']);
                            $tax = $extra->price * ($tax_rate[0] / 100);
                            //Log::debug('extra tax  : ' . $tax);
                        }

                        // if (!empty($this->items[$index]['extra_names']))
                        //     $this->items[$index]['extra_names'] .= ",";
                        $item['extra'] = $item['qty'] * ($extra->price + $tax);
                        $totalExtra += $item['extra'];
                        if(is_array($tax_rate))
                            $this->items[$index]['extra_names'][] =  $extra->name . ' ('.$this->items[$index]['qty'] . ' x '.$extra->price.') + [Tax('.$tax_rate[0].'%) : '.$tax.'] = '.($item['qty'] * ($extra->price + $tax));
                        else
                            $this->items[$index]['extra_names'][] =  $extra->name . ' ('.$this->items[$index]['qty'] . ' x '.$extra->price.') = '.($item['qty'] * ($extra->price + $tax));
                    }

                    $this->items[$index]['extra'] = $totalExtra;
                    $this->items[$index]['all_extra_ids'] = $extraIds;
                }
            }
            $this->items[$index]['total'] +=  $totalExtra;
        }

        if ($this->order_discount_type == "Flat") {
            $this->total_discount = $this->order_discount_value;
        } else {
            $this->total_discount = (collect($this->items)->sum('total') * $this->order_discount_value / 100);
        }
        $this->grand_total = (collect($this->items)->sum('total') + $this->shipping_cost) - ($this->total_discount + $this->coupon_discount);

        $this->order_tax = 0;
        if ($this->order_tax_rate !== '0') {
            $tax_rate_order = explode('|', $this->order_tax_rate);
            $this->order_tax = collect($this->items)->sum('total') * ($tax_rate_order[0] / 100);
        }

        $this->grand_total = ($this->grand_total + $this->order_tax);
        if($this->changedAmount == false)
            $this->payment_data['paying_amount'] = $this->grand_total;
        $this->payment_data['paid_amount'] = $this->grand_total;
        $this->updatedPaymentData();
        return view('livewire.sale.pos-livewire');
    }

    public function updatedPaymentData()
    {
        // if($this->changedAmount == false)
        //     $this->payment_data['paying_amount'] = $this->grand_total;
        //$this->changeCash(0);
        // if ($this->payment_data['paying_amount'] == 0)
        //     $this->payment_data['paying_amount'] = $this->grand_total;
        //$this->payment_data['paid_amount'] = $this->grand_total;
        //$this->payment_data['change'] = $this->payment_data['paying_amount'] - $this->payment_data['paid_amount'];
    }

    public function updatedWarehouseId()
    {
        // $controller = new SaleController();
        // $data = $controller->getProduct($this->warehouse_id);

        //$response = Http::get('/sales/getproduct/'.$this->warehouse_id);
        //dd($data);
        //$this->lims_product_array = $data;
        //$this->dispatch('bind-products',$data);
        $this->dispatch('initialized');
    }

    public function updatedCustomerId()
    {

        $this->customer = Customer::find($this->customer_id);
        $this->deposit = $this->customer->expense;
        $this->points = $this->customer->points;

        $lims_customer_group_data = CustomerGroup::find($this->customer->customer_group_id);
        $this->customer_group_rate = $lims_customer_group_data->percentage / 100;
        if ($this->customer_group_rate == 0)
            $this->customer_group_rate = 1;
    }

    function searchItemByCode($items, $code)
    {
        foreach ($items as $index => $item) {
            if ($item['code'] === $code) {
                return $index;
            }
        }
        return false; // If item with code is not found
    }

    #[On('addToCart')]
    public function addToCart($product)
    {
        //Log::debug($product);
        //dd($product);
        $product = json_decode(json_encode($product));

        //Log::debug($product);
        $items = $this->items;
        $index =  false; //$this->searchItemByCode($this->items, $product->code);

        if ($index !== false) {
            $items[$index]['qty']++;
            $items[$index]['total'] = $items[$index]['price'] * $items[$index]['qty'];
        } else {
            $items[] = [
                'product_id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'in_stock' => $product->in_stock,
                'is_batch' => $product->is_batch,
                'batch' => ($product->is_batch) ? $product->batch : '',
                'qty' => 1,
                'price' => $product->price,
                'net_unit_price' => $product->price,
                'display_unit_price' => $product->price,
                'original_price' => $product->price * $this->customer_group_rate * $this->currency->exchange_rate,
                'discount' => 0,
                'total' => 1 * ($product->price * $this->customer_group_rate * $this->currency->exchange_rate),
                'coupon_discount' => 0,
                'tax' => 0,
                'total_tax' => 0,
                'tax_rate' => '0',
                'extra_categories' => [],
                'extras' => [],
                'single_extras' => [],
                'all_extra_ids' => [],
                'extra_names' => [],
                'extra' => 0,
                'units' => []
            ];
        }


        if ($index !== false) {
            $this->qtyIndex = $index;
        } else {
            $this->qtyIndex = count($items) - 1;
        }

        if ($product->type == 'standard') {
            $units = Unit::where("base_unit", $product->unit_id)
                ->orWhere('id', $product->unit_id)
                ->get();

            $items[$this->qtyIndex]['units'] = $units;
        }

        //Log::debug($items[$this->qtyIndex]['extras']);

        $prod = Product::with('extraCategories')->where('code', $product->code)->first();
        if ($prod->extraCategories->count() > 0) {

            // dd($this->qtyIndex);
            $this->hasExtra = true;
            $items[$this->qtyIndex]['extra_categories'] = $prod->extraCategories;
            // dd($items['extra_categories']);
        } else
            $items[$this->qtyIndex]['extra_categories'] = [];


        $this->items = $items;
        $this->grand_total =  collect($this->items)->sum('total');
        $this->loaded = true;
        $this->added = true;

        //Log::debug('has Extra ? '.$this->hasExtra);

        if($this->hasExtra)
            $this->dispatch('open-qty-modal', index: $this->qtyIndex);
        //$this->dispatch('open-qty-modal-browser',index:$this->qtyIndex);
        //Log::debug($this->items);
    }

    #[On('update-cart')]
    public function updateCart($index)
    {
        $this->validate([
            'items.*.discount' => 'lt:' . $this->items[$index]['original_price']
        ]);
        //Log::debug('update cart : ');
        //Log::debug($index);
        //Log::debug($this->items[$index]);
        //$this->items = $items;
        //dd($this->items);
        //$this->updatedItems();
        $this->loaded = false;
        $this->added = false;
        $this->hasExtra = false;
        $this->dispatch('close-qty-modal', index: $index);
    }

    public function minus($index)
    {
        $this->items[$index]['qty']--;
        $this->items[$index]['total'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
        if ($this->items[$index]['qty'] <= 0)
            unset($this->items[$index]);

        $this->grand_total =  collect($this->items)->sum('total');
    }

    public function plus($index)
    {
        $this->items[$index]['qty']++;
        $this->items[$index]['total'] = $this->items[$index]['price'] * $this->items[$index]['qty'];
        $this->grand_total =  collect($this->items)->sum('total');
    }

    public function updatedItems()
    {
    }

    public function updated()
    {
        //$this->updatedItems();
    }

    public function getProductByFilter($category_id, $brand_id)
    {
        $lims_product_list = [];
        if (($category_id != 0) && ($brand_id != 0)) {
            $lims_product_list = DB::table('products')
                // ->join('categories', 'products.category_id', '=', 'categories.id')
                ->where([
                    ['is_active', true],
                    ['category_id', $category_id],
                    ['brand_id', $brand_id]
                ])
                ->get();
        } elseif (($category_id != 0) && ($brand_id == 0)) {
            $lims_product_list = DB::table('products')
                ->where([
                    ['is_active', true],
                    ['category_id', $category_id],
                ])
                ->get();
        } elseif (($category_id == 0) && ($brand_id != 0)) {
            $lims_product_list = Product::where([
                ['brand_id', $brand_id],
                ['is_active', true]
            ])
                ->select('products.id', 'products.name', 'products.code', 'products.image', 'products.is_variant')
                ->get();
        } else {
            $lims_product_list  = Product::where('is_active', true)->get();
        }

        $index = 0;
        foreach ($lims_product_list as $product) {
            if ($product->is_variant) {
                $lims_product_data = Product::select('id')->find($product->id);
                $lims_product_variant_data = $lims_product_data->variant()->orderBy('position')->get();
                foreach ($lims_product_variant_data as $key => $variant) {
                    $images = explode(",", $product->image);
                    $product->base_image = $images[0];
                    $index++;
                }
            } else {
                $images = explode(",", $product->image);
                // dd($lims_product_list);
                if ($images[0])
                    $product->base_image = $images[0];
                else
                    $product->base_image = 'zummXD2dvAtI.png';
            }

            Log::debug($product->base_image);
        }
        //Log::debug($lims_product_list);
        $this->lims_product_list = $lims_product_list;
        $this->product_number = count($this->lims_product_list);
        //Log::debug($this->lims_product_list);
    }

    public function delete($index)
    {
        unset($this->items[$index]);
    }

    #[On('saveCart')]
    public function saveCart($sale_status = 1)
    {
        $this->sale_status = $sale_status;
        /*array (
            '_token' => 'mueRKr5g2nAfXMOBzEAil4mzo7maiXv8zzpRevGP',
            'warehouse_id_hidden' => '1',
            'warehouse_id' => $this->warehouse_id,
            'table_id' => $this->table_id,
            'biller_id_hidden' => '1',
            'biller_id' => '1',
            'customer_id_hidden' => '1',
            'customer_id' => $this->customer_id,
            'product_code_name' => NULL,
            'product_batch_id' =>
            array (
              0 => NULL,
            ),
            'qty' =>
            array (
              0 => '1',
            ),
            'product_code' =>
            array (
              0 => '22692843',
            ),
            'product_id' =>
            array (
              0 => '2',
            ),
            'sale_unit' =>
            array (
              0 => 'n/a',
            ),
            'net_unit_price' =>
            array (
              0 => '2.000',
            ),
            'discount' =>
            array (
              0 => '0.000',
            ),
            'tax_rate' =>
            array (
              0 => '0.000',
            ),
            'tax' =>
            array (
              0 => '0.000',
            ),
            'subtotal' =>
            array (
              0 => '2.000',
            ),
            'imei_number' =>
            array (
              0 => NULL,
            ),
            'total_qty' => collect($this->items)->sum('total_qty'),
            'total_discount' => collect($this->items)->sum('total_discount'),
            'total_tax' => '0.000',
            'total_price' => collect($this->items)->sum('total'),
            'item' => count($this->items),
            'order_tax' => '0.000',
            'grand_total' => $this->grand_total,
            'used_points' => NULL,
            'coupon_discount' => collect($this->items)->sum('total'),
            'sale_status' => '1',
            'coupon_active' => NULL,
            'coupon_id' => NULL,
            'pos' => '1',
            'draft' => '0',
            'paying_amount' => $this->payment_data['sale_note'],
            'paid_amount' =>  $this->payment_data['paid_amount'],
            'paid_by_id' => $this->payment_data['payment_method'],
            'gift_card_id' => $this->payment_data['gift_card_id'],
            'cheque_no' =>  $this->payment_data['cheque_no'],
            'payment_note' => $this->payment_data['payment_note'],
            'sale_note' => $this->payment_data['sale_note'],
            'staff_note' => $this->payment_data['staff_note'],
            'order_discount_type' => $this->order_discount_type,
            'order_discount_value' => $this->order_discount_value,
            'order_discount' => $this->total_discount,
            'order_tax_rate' => '0',
            'shipping_cost' => $this->shipping_cost,
          )
*/

        $data = [
            'total_qty' => collect($this->items)->sum('qty'),
            'total_discount' => $this->total_discount,
            'total_tax' => collect($this->items)->sum('total_tax'),
            'total_price' => collect($this->items)->sum('total'),
            'item' => count($this->items),
            'order_tax' => $this->order_tax,
            'grand_total' => $this->grand_total,
            'used_points' => NULL,
            'coupon_discount' => $this->coupon_discount,
            'sale_status' => '1',
            'coupon_active' => ($this->coupon) ? 1 : null,
            'coupon_id' => ($this->coupon) ? $this->coupon->id : null,
            'order_type'=>"Pickup",
            'sale_type'=> "WebPos",
            'pos' => '1',
            'draft' => '0',
            'paying_amount' => $this->payment_data['sale_note'],
            'paid_amount' =>  $this->payment_data['paid_amount'],
            'paid_by_id' => $this->payment_data['payment_method'],
            'gift_card_id' => $this->payment_data['gift_card_id'],
            'cheque_no' =>  $this->payment_data['cheque_no'],
            'payment_note' => $this->payment_data['payment_note'],
            'sale_note' => $this->payment_data['sale_note'],
            'staff_note' => $this->payment_data['staff_note'],
            'order_discount_type' => $this->order_discount_type,
            'order_discount_value' => $this->order_discount_value,
            'order_discount' => collect($this->items)->sum('discount'),
            'order_tax_rate' => $this->order_tax_rate,
            'shipping_cost' => $this->shipping_cost
        ];


        $lims_customer_data = Customer::find($this->customer_id);
        $data['warehouse_id'] = $this->warehouse_id;
        $data['user_id'] = Auth::id();
        $data['customer_id'] = $this->customer_id;
        $data['biller_id'] = $this->biller_id;
        $cash_register_data = CashRegister::where([
            ['user_id', $data['user_id']],
            ['warehouse_id', $data['warehouse_id']],
            ['status', true]
        ])->first();

        if ($cash_register_data)
            $data['cash_register_id'] = $cash_register_data->id;
        if (isset($data['created_at']))
            $data['created_at'] = date("Y-m-d H:i:s", strtotime($data['created_at']));
        else
            $data['created_at'] = date("Y-m-d H:i:s");

        $data['grand_total'] = $this->grand_total;
        $data['paid_amount'] = $this->payment_data['paid_amount'];
        $data['pos'] = 1;
        $data['draft'] = 0; //($sale_status == 1) ? 0 : 1;
        $data['sale_status'] = $this->sale_status;
        if ($data['pos']) {
            // dd($data);
            if (!isset($data['reference_no']))
                $data['reference_no'] = 'posr-' . date("Ymd") . '-' . date("his");

            $balance = $data['grand_total'] - $data['paid_amount'];
            if ($balance > 0 || $balance < 0)
                $data['payment_status'] = 2;
            else
                $data['payment_status'] = 4;

            if ($data['draft']) {
                $lims_sale_data = Sale::find($data['sale_id']);
                $lims_product_sale_data = Product_Sale::where('sale_id', $data['sale_id'])->get();
                foreach ($lims_product_sale_data as $product_sale_data) {
                    $product_sale_data->delete();
                }
                $lims_sale_data->delete();
            }
        } else {
            if (!isset($data['reference_no']))
                $data['reference_no'] = 'sr-' . date("Ymd") . '-' . date("his");
        }

        if ($data['coupon_active']) {
            $lims_coupon_data = Coupon::find($data['coupon_id']);
            $lims_coupon_data->used += 1;
            $lims_coupon_data->save();
        }

        if (isset($data['table_id'])) {
            $latest_sale = Sale::whereNotNull('table_id')->whereDate('created_at', date('Y-m-d'))->where('warehouse_id', $data['warehouse_id'])->select('queue')->orderBy('id', 'desc')->first();
            if ($latest_sale)
                $data['queue'] = $latest_sale->queue + 1;
            else
                $data['queue'] = 1;
        }

        //inserting data to sales table
        $lims_sale_data = Sale::create($data);
        $sale = $lims_sale_data;
        //inserting data for custom fields
        $custom_field_data = [];
        $custom_fields = CustomField::where('belongs_to', 'sale')->select('name', 'type')->get();
        foreach ($custom_fields as $type => $custom_field) {
            $field_name = str_replace(' ', '_', strtolower($custom_field->name));
            if (isset($data[$field_name])) {
                if ($custom_field->type == 'checkbox' || $custom_field->type == 'multi_select')
                    $custom_field_data[$field_name] = implode(",", $data[$field_name]);
                else
                    $custom_field_data[$field_name] = $data[$field_name];
            }
        }
        if (count($custom_field_data))
            DB::table('sales')->where('id', $lims_sale_data->id)->update($custom_field_data);

        $lims_reward_point_setting_data = RewardPointSetting::latest()->first();
        //checking if customer gets some points or not
        if ($lims_reward_point_setting_data && $lims_reward_point_setting_data->is_active &&  $data['grand_total'] >= $lims_reward_point_setting_data->minimum_amount) {
            $point = (int)($data['grand_total'] / $lims_reward_point_setting_data->per_point_amount);
            $lims_customer_data->points += $point;
            $lims_customer_data->save();
        }

        //collecting male data
        $mail_data['email'] = $lims_customer_data->email;
        $mail_data['reference_no'] = $lims_sale_data->reference_no;
        $mail_data['sale_status'] = $lims_sale_data->sale_status;
        $mail_data['payment_status'] = $lims_sale_data->payment_status;
        $mail_data['total_qty'] = $lims_sale_data->total_qty;
        $mail_data['total_price'] = $lims_sale_data->total_price;
        $mail_data['order_tax'] = $lims_sale_data->order_tax;
        $mail_data['order_tax_rate'] = $lims_sale_data->order_tax_rate;
        $mail_data['order_discount'] = $lims_sale_data->order_discount;
        $mail_data['shipping_cost'] = $lims_sale_data->shipping_cost;
        $mail_data['grand_total'] = $lims_sale_data->grand_total;
        $mail_data['paid_amount'] = $lims_sale_data->paid_amount;

        // $product_id = $data['product_id'];
        // $product_batch_id = $data['product_batch_id'];
        // $imei_number = $data['imei_number'];
        // $product_code = $data['product_code'];
        // $qty = $data['qty'];
        $sale_unit = [];
        $imei_number = [];
        // $net_unit_price = $data['net_unit_price'];
        // $discount = $data['discount'];
        // $tax_rate = $data['tax_rate'];
        // $tax = $data['tax'];
        // $total = $data['subtotal'];
        $product_sale = [];
        foreach ($this->items as $i => $item) {
            $lims_product_data = Product::where('id', $item['product_id'])->first();
            $sale_unit[] = $lims_product_data->sale_unit_id;
            $product_sale['variant_id'] = null;
            $product_sale['product_batch_id'] = null;
            if ($lims_product_data->type == 'combo' && $data['sale_status'] == 1) {
                $product_list = explode(",", $lims_product_data->product_list);
                $variant_list = explode(",", $lims_product_data->variant_list);
                if ($lims_product_data->variant_list)
                    $variant_list = explode(",", $lims_product_data->variant_list);
                else
                    $variant_list = [];
                $qty_list = explode(",", $lims_product_data->qty_list);
                $price_list = explode(",", $lims_product_data->price_list);

                foreach ($product_list as $key => $child_id) {
                    $child_data = Product::find($child_id);
                    if (count($variant_list) && $variant_list[$key]) {
                        $child_product_variant_data = ProductVariant::where([
                            ['product_id', $child_id],
                            ['variant_id', $variant_list[$key]]
                        ])->first();

                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['variant_id', $variant_list[$key]],
                            ['warehouse_id', $data['warehouse_id']],
                        ])->first();

                        $child_product_variant_data->qty -= $qty[$i] * $qty_list[$key];
                        $child_product_variant_data->save();
                    } else {
                        $child_warehouse_data = Product_Warehouse::where([
                            ['product_id', $child_id],
                            ['warehouse_id', $data['warehouse_id']],
                        ])->first();
                    }

                    $child_data->qty -= $qty[$i] * $qty_list[$key];
                    $child_warehouse_data->qty -= $qty[$i] * $qty_list[$key];

                    $child_data->save();
                    $child_warehouse_data->save();
                }
            }

            if (count($sale_unit) > 0 && isset($sale_unit[$i]) && $sale_unit[$i] != '0') {
                $lims_sale_unit_data  = Unit::where('id', $sale_unit[$i])->first();
                $sale_unit_id = $sale_unit[$i];
                if ($lims_product_data->is_variant) {
                    $lims_product_variant_data = ProductVariant::select('id', 'variant_id', 'qty')->FindExactProductWithCode($item['product_id'], $item['code'])->first();
                    $product_sale['variant_id'] = $lims_product_variant_data->variant_id;
                }
                if ($lims_product_data->is_batch && $item['product_batch_id']) {
                    $product_sale['product_batch_id'] = $item['product_batch_id'];
                }

                if ($data['sale_status'] == 1) {
                    if ($lims_sale_unit_data->operator == '*')
                        $quantity = $item['qty'] * $lims_sale_unit_data->operation_value;
                    elseif ($lims_sale_unit_data->operator == '/')
                        $quantity = $item['qty'] / $lims_sale_unit_data->operation_value;
                    //deduct quantity
                    $lims_product_data->qty = $lims_product_data->qty - $quantity;
                    $lims_product_data->save();
                    //deduct product variant quantity if exist
                    if ($lims_product_data->is_variant) {
                        $lims_product_variant_data->qty -= $quantity;
                        $lims_product_variant_data->save();
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithVariant($item['product_id'], $lims_product_variant_data->variant_id, $data['warehouse_id'])->first();
                    } elseif ($lims_product_data->is_batch && $item['product_batch_id']) {
                        $lims_product_warehouse_data = Product_Warehouse::where([
                            ['product_batch_id', $item['product_batch_id']],
                            ['warehouse_id', $data['warehouse_id']]
                        ])->first();
                        $lims_product_batch_data = ProductBatch::find($item['product_batch_id']);
                        //deduct product batch quantity
                        $lims_product_batch_data->qty -= $quantity;
                        $lims_product_batch_data->save();
                    } else {
                        $lims_product_warehouse_data = Product_Warehouse::FindProductWithoutVariant($item['product_id'], $data['warehouse_id'])->first();
                    }
                    //deduct quantity from warehouse
                    $lims_product_warehouse_data->qty -= $quantity;
                    $lims_product_warehouse_data->save();
                }
            } else
                $sale_unit_id = 0;

            if ($product_sale['variant_id']) {
                $variant_data = Variant::select('name')->find($product_sale['variant_id']);
                $mail_data['products'][$i] = $lims_product_data->name . ' [' . $variant_data->name . ']';
            } else
                $mail_data['products'][$i] = $lims_product_data->name;
            //deduct imei number if available
            if (count($imei_number) > 0) {
                $imei_numbers = explode(",", $imei_number[$i]);
                $all_imei_numbers = explode(",", $lims_product_warehouse_data->imei_number);
                foreach ($imei_numbers as $number) {
                    if (($j = array_search($number, $all_imei_numbers)) !== false) {
                        unset($all_imei_numbers[$j]);
                    }
                }
                $lims_product_warehouse_data->imei_number = implode(",", $all_imei_numbers);
                $lims_product_warehouse_data->save();
            }
            if ($lims_product_data->type == 'digital')
                $mail_data['file'][$i] = url('/public/product/files') . '/' . $lims_product_data->file;
            else
                $mail_data['file'][$i] = '';
            if ($sale_unit_id)
                $mail_data['unit'][$i] = $lims_sale_unit_data->unit_code;
            else
                $mail_data['unit'][$i] = '';

            $product_sale['sale_id'] = $lims_sale_data->id;
            $product_sale['product_id'] = $item['product_id'];
            $product_sale['imei_number'] = count($imei_number) ? $imei_number[$i] : '';
            $product_sale['qty'] = $mail_data['qty'][$i] = $item['qty'];
            $product_sale['sale_unit_id'] = $sale_unit_id;
            $product_sale['net_unit_price'] = $item['net_unit_price'];
            $product_sale['discount'] = $item['discount'];
            $product_sale['tax_rate'] = $item['tax'];
            $product_sale['tax'] = $item['total_tax'];
            $product_sale['total'] = $mail_data['total'][$i] = $item['total'];
            if(count($item['extra_names']) > 0)
            {
                Log::debug($item['extra_names']);
                $product_sale['extra_names'] = json_encode($item['extra_names']) ;
                $product_sale['extras'] = json_encode($item['all_extra_ids']);
                $product_sale['extra'] =$item['extra'];
            }


            Product_Sale::create($product_sale);
        }
        if ($data['sale_status'] == 3)
            $message = 'Sale successfully added to draft';
        else
            $message = ' Sale created successfully';
        $mail_setting = MailSetting::latest()->first();
        if ($mail_data['email'] && $data['sale_status'] == 1 && $mail_setting) {
            $this->setMailInfo($mail_setting);
            try {
                Mail::to($mail_data['email'])->send(new SaleDetails($mail_data));
                /*$log_data['message'] = Auth::user()->name . ' has created a sale. Reference No: ' .$lims_sale_data->reference_no;
                $admin_email = 'ashfaqdev.php@gmail.com';
                Mail::to($admin_email)->send(new LogMessage($log_data));*/
            } catch (\Exception $e) {
                $message = ' Sale created successfully. Please setup your <a href="setting/mail_setting">mail setting</a> to send mail.';
            }
        }

        if ($data['payment_status'] == 3 || $data['payment_status'] == 4 || ($data['payment_status'] == 2 && $data['pos'] && $data['paid_amount'] > 0)) {

            $lims_payment_data = new Payment();
            $lims_payment_data->user_id = Auth::id();

            $data['paid_by_id'] = $this->payment_data['payment_method'];
            if ($data['paid_by_id'] == 1)
                $paying_method = 'Cash';
            elseif ($data['paid_by_id'] == 2) {
                $paying_method = 'Gift Card';
            } elseif ($data['paid_by_id'] == 3)
                $paying_method = 'Credit Card';
            elseif ($data['paid_by_id'] == 4)
                $paying_method = 'Cheque';
            elseif ($data['paid_by_id'] == 5)
                $paying_method = 'Paypal';
            elseif ($data['paid_by_id'] == 6)
                $paying_method = 'Deposit';
            elseif ($data['paid_by_id'] == 7) {
                $paying_method = 'Points';
                $lims_payment_data->used_points = $data['used_points'];
            } else {
                $paying_method = $data['paid_by_id'];
            }

            $data['paying_amount'] = $this->payment_data['paying_amount'];
            $data['payment_note'] = $this->payment_data['payment_note'];
            if ($cash_register_data)
                $lims_payment_data->cash_register_id = $cash_register_data->id;
            $lims_account_data = Account::where('is_default', true)->first();
            $lims_payment_data->account_id = $lims_account_data->id;
            $lims_payment_data->sale_id = $lims_sale_data->id;
            $data['payment_reference'] = 'spr-' . date("Ymd") . '-' . date("his");
            $lims_payment_data->payment_reference = $data['payment_reference'];
            $lims_payment_data->amount = $data['paid_amount'];
            $lims_payment_data->change = $data['paying_amount'] - $data['paid_amount'];
            $lims_payment_data->paying_method = $paying_method;
            $lims_payment_data->payment_note = $data['payment_note'];
            $lims_payment_data->save();

            $lims_payment_data = Payment::latest()->first();
            $data['payment_id'] = $lims_payment_data->id;
            $lims_pos_setting_data = PosSetting::latest()->first();
            if ($paying_method == 'Credit Card' && (strlen($lims_pos_setting_data->stripe_public_key) > 0) && (strlen($lims_pos_setting_data->stripe_secret_key) > 0)) {

                Stripe::setApiKey($lims_pos_setting_data->stripe_secret_key);
                $token = $data['stripeToken'];
                $grand_total = $data['grand_total'];

                $lims_payment_with_credit_card_data = PaymentWithCreditCard::where('customer_id', $data['customer_id'])->first();

                if (!$lims_payment_with_credit_card_data) {
                    // Create a Customer:
                    $customer = \Stripe\Customer::create([
                        'source' => $token
                    ]);

                    // Charge the Customer instead of the card:
                    $charge = \Stripe\Charge::create([
                        'amount' => $grand_total * 100,
                        'currency' => 'usd',
                        'customer' => $customer->id
                    ]);
                    $data['customer_stripe_id'] = $customer->id;
                } else {
                    $customer_id =
                        $lims_payment_with_credit_card_data->customer_stripe_id;

                    $charge = \Stripe\Charge::create([
                        'amount' => $grand_total * 100,
                        'currency' => 'usd',
                        'customer' => $customer_id, // Previously stored, then retrieved
                    ]);
                    $data['customer_stripe_id'] = $customer_id;
                }
                $data['charge_id'] = $charge->id;
                PaymentWithCreditCard::create($data);
            } elseif ($paying_method == 'Gift Card') {
                $lims_gift_card_data = GiftCard::find($data['gift_card_id']);
                $lims_gift_card_data->expense += $data['paid_amount'];
                $lims_gift_card_data->save();
                PaymentWithGiftCard::create($data);
            } elseif ($paying_method == 'Cheque') {
                PaymentWithCheque::create($data);
            } elseif ($paying_method == 'Paypal') {

                $provider = new ExpressCheckout;
                $paypal_data = [];
                $paypal_data['items'] = [];
                foreach ($data['product_id'] as $key => $product_id) {
                    $lims_product_data = Product::find($product_id);
                    $paypal_data['items'][] = [
                        'name' => $lims_product_data->name,
                        'price' => ($data['subtotal'][$key] / $data['qty'][$key]),
                        'qty' => $data['qty'][$key]
                    ];
                }
                $paypal_data['items'][] = [
                    'name' => 'Order Tax',
                    'price' => $data['order_tax'],
                    'qty' => 1
                ];
                $paypal_data['items'][] = [
                    'name' => 'Order Discount',
                    'price' => $data['order_discount'] * (-1),
                    'qty' => 1
                ];
                $paypal_data['items'][] = [
                    'name' => 'Shipping Cost',
                    'price' => $data['shipping_cost'],
                    'qty' => 1
                ];
                if ($data['grand_total'] != $data['paid_amount']) {
                    $paypal_data['items'][] = [
                        'name' => 'Due',
                        'price' => ($data['grand_total'] - $data['paid_amount']) * (-1),
                        'qty' => 1
                    ];
                }
                //return $paypal_data;
                $paypal_data['invoice_id'] = $lims_sale_data->reference_no;
                $paypal_data['invoice_description'] = "Reference # {$paypal_data['invoice_id']} Invoice";
                $paypal_data['return_url'] = url('/sale/paypalSuccess');
                $paypal_data['cancel_url'] = url('/sale/create');

                $total = 0;
                foreach ($paypal_data['items'] as $item) {
                    $total += $item['price'] * $item['qty'];
                }

                $paypal_data['total'] = $total;
                $response = $provider->setExpressCheckout($paypal_data);
                // This will redirect user to PayPal
                return redirect($response['paypal_link']);
            } elseif ($paying_method == 'Deposit') {
                $lims_customer_data->expense += $data['paid_amount'];
                $lims_customer_data->save();
            } elseif ($paying_method == 'Points') {
                $lims_customer_data->points -= $data['used_points'];
                $lims_customer_data->save();
            }

            $wssetting = WhatsappSmsSetting::first();
            if ($wssetting) {
                if ($wssetting->sms == 1) {
                    $account_sid = env('ACCOUNT_SID');
                    $auth_token = env('AUTH_TOKEN');
                    $twilio_phone_number = env('Twilio_Number');
                    $wsphone = $lims_customer_data->phone_number;
                    $messagew = $wssetting->template . " " . url("view/sales/" . $lims_sale_data->id);
                    try {
                        $client = new Client($account_sid, $auth_token);
                        $client->messages->create(
                            $wsphone,
                            array(
                                "from" => $twilio_phone_number,
                                "body" => $messagew
                            )
                        );
                    } catch (\Exception $e) {
                        // dd($e);
                        //return $e;
                        // return redirect()->back()->with('not_permitted', 'Please setup your <a href="sms_setting">SMS Setting</a> to send SMS.');
                    }
                }
            }
            if ($lims_sale_data->sale_status == '1')
                return redirect('sales/gen_invoice/' . $lims_sale_data->id)->with('message', $message);
            elseif ($data['pos'])
                return redirect('pos', ['newpos' => 1])->with('message', $message);
            else
                return redirect('sales')->with('message', $message);
        }
    }

    #[On('clearCash')]
    public function clearCash()
    {
        //dd($this->grand_total);
        $this->change_amount = 0;
        $this->changedAmount = false;
        $this->payment_data['paying_amount'] = $this->grand_total;
        $this->payment_data['change'] = $this->payment_data['paid_amount'] - $this->payment_data['paying_amount'];
    }

    public function changeCash($change)
    {
        $this->changedAmount = true;
        $this->change_amount += $change;
        // if ($this->payment_data['paying_amount'] == 0 && $change == 0) {
        //     $this->change_amount = $this->grand_total;
        // } else {


        // }
        //Log::debug('change : '.$this->change_amount);
        $this->payment_data['paying_amount'] = $this->change_amount;
        $this->payment_data['change'] = $this->change_amount - $this->payment_data['paid_amount'];
    }

    public function couponApply()
    {
        $coupon = Coupon::where('code', $this->coupon_code)->first();
        $message = $this->coupon_code;
        if ($coupon) {
            //Log::debug($coupon);
            if ($coupon->quantity <= $coupon->used) {
                $message = "This Coupon is no longer available";
            } else if (Carbon::now()->format('Y-m-d')  > Carbon::parse($coupon->expired_date)->format('Y-m-d')) {
                $message = "This Coupon has expired!";
            } else if ($coupon->type == 'fixed') {
                if ($this->grand_total >= $coupon->minimum_amount) {
                    $this->coupon_discount = $coupon->amount * $this->currency->exchange_rate;
                    $message = 'Congratulation! You got ' . $this->coupon_discount . ' ' . $this->currency->code . ' discount';
                } else {
                    $message = 'Grand Total is not sufficient for discount! Required ' + $coupon->minimum_amount . ' ' . $this->currency->code;
                }
            } else {
                $this->coupon_discount = $this->grand_total * ($coupon->amount / 100);
                $message = 'Congratulation! You got '  . $coupon->amount . '% discount';
            }
            //Log::debug($message);
        } else {
            $message = "Invalid coupon code!";
            $this->coupon_code = '';
        }

        $this->coupon = $coupon;
        $this->dispatch('coupon-application', $message);
    }

    public function giftCard()
    {
        $giftCard = GiftCard::find($this->payment_data['gift_card_id']);
        $balance = $giftCard->amount - $giftCard->expense;
        if ($this->payment_data['paid_amount'] > $balance) {
            $message = 'Amount exceeds card balance! Gift Card balance: ' + $balance;
            $this->dispatch('gift-card-application', $message);
        }
        // var balance = gift_card_amount[$(this).val()] - gift_card_expense[$(this).val()];
        //     $('#add-payment input[name="gift_card_id"]').val($(this).val());
        //     if ($('input[name="paid_amount"]').val() > balance) {
        //         alert('Amount exceeds card balance! Gift Card balance: ' + balance);
        //     }
    }
}