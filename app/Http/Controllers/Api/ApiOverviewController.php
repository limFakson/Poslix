<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiOverviewController extends Controller
{
    //
    public function index()
    {
        $data = [
            'message'=>["Every data you send to the API should be in camelCase, and every data you recieve from the api should be camelCase. Message me if you facing anything 👍."],

            'Login' => [
                'loginApi' =>'auth/login',
                'method' =>'POST',
                'details' =>[
                    'domain' =>'domain of the user',
                    'identifier'=>'username',
                    'password' => 'password'
                ]
            ],
            'products' => [
                'method'=>'GET',
                'productApi' =>'/product/?tenant_id={}&warehouse_id={}, what will be given will be - products, product_variants, extras',
                'productApiById' =>'/product/{id}/?tenant_id={}&warehouse_id={}',
                'productApiByCategory' =>'/product/bycategory/{category_id}/?tenant_id={}&warehouse_id={}',
            ],
            'User' => [
                'method'=>'GET',
                'userApi' =>'/user, this one will get the superadmin',
                'userApiTenant' =>'/tenant?tenant_id={}, this will get the tenant by id',
                'userApiTenantUser' =>'/tenant/user/{id}?tenant_id={}, put the id of the auth user, this will get the auth users details',
            ],
            'category' => [
                'method'=>'GET',
                'categoryApi' =>'/category/?tenant_id={}, be given - products_categories and extra_categories',
                'categoryApiById' =>'/category/{id}/?tenant_id={}',
            ],
            'customers' => [
                'method'=>'GET',
                'customerApi' =>'/customer/?tenant_id={}, added customer group i.e you will get group details when you call all custoemrs data',
                'customerApiById' =>'/customer/{id}/?tenant_id={}',
                'customerApiByUser' =>'/customer/byuser/{user_id}/?tenant_id={}',
                'customerApiForPOST' => '/customer/?tenant_id={} method = POST',
                'customerApiForPUT' => '/customer/{id}?tenant_id={} method = PUT',
            ],
            'sales' => [
                'method'=>'GET',
                'saleApi' =>'/sale/?tenant_id={}, gets the deatils in sale and product_sales',
                'saleApiById' =>'/sale/{id}/?tenant_id={}, get the details of sale id provided and the details of product_sale with the sale id',
                'numerical_representation' =>[
                    'payment_status'=>[
                        1 =>'pending',
                        2=>'Due',
                        3=>'Partial',
                        4=>'Paid'
                    ],
                    'sale_status'=>[
                        1=>'Completed',
                        2=>'Pending',
                        3=>'Draft',
                        4=>'Returned'
                    ],
                ],
                'saleApiForPOST' => '/sale/?tenant_id={} method = POST, when you are posting you can post multiple sales at once and details for product_sale put it in the same request(it will be filtered out in the baackend)',
                'saleApiForPUT' => '/sale/{id}?tenant_id={} method = PUT',
            ],
            'return-sales' => [
                'method'=>'GET',
                'return-saleApi' =>'/return-sale/?tenant_id={}, gets the deatils in returns and product_returns',
                'return-saleApiById' =>'/return-sale/{id}/?tenant_id={}, get the details of return id provided and the details of product_returns with the returns id',
                'return-saleApiForPOST' => '/return-sale/?tenant_id={} method = POST, when you are posting you can post multiple return-sales at once and details for product_return put it in the same request(it will be filtered out in the baackend)',
            ],
            'cashRegister' => [
                'method'=>'GET',
                'cashregisterApi' =>'/cashregister/?tenant_id={}',
                'cashregisterApiById' =>'/cashregister/{id}/?tenant_id={} ',
                'cashregisterApiForPOST' => '/cashregister/?tenant_id={} method = POST',
                'cashregisterApiForPUT' => '/cashregister/{id}?tenant_id={} method = PUT',
            ],
            'warehouse' => [
                'method'=>'GET',
                'warehouseApi' =>'/warehouse/?tenant_id={}',
                'warehouseApiById' =>'/warehouse/{id}/?tenant_id={}',
            ],
            'biller' => [
                'method'=>'GET',
                'billerApi' =>'/biller/?tenant_id={}',
            ],
            'table' => [
                'method'=>'GET',
                'tableApi' =>'/table/?tenant_id={}',
            ],
            'pos-settings' => [
                'method'=>'GET',
                'posSettingsApi' =>'/pos-setting?tenant_id={}',
                'posSettingsApiForPUT' => '/pos-setting/{id=1}?tenant_id={} method = PUT',
            ],
            'general-settings' => [
                'method'=>'GET',
                'genSettingsApi' =>'/gensettings?tenant_id={}',
            ],
            'giftcard' => [
                'method'=>'GET',
                'giftcardApi' =>'/giftcard/?tenant_id={}',
                'giftcardApiById' =>'/giftcard/{id}/?tenant_id={}',
            ],
            'coupon' => [
                'method'=>'GET',
                'couponApi' =>'/coupon/?tenant_id={}',
                'couponApiById' =>'/coupon/{id}/?tenant_id={}',
            ],
            "How to send your data"=>[
                'customer'=>'{"customerGroupId":integer,"userId": integer,"name" : string,"companyName" : string,"email" :email,"phoneNumber" :integer,"address": string,"city" : string,"state" : string,"country": string,"isActive": boolean,"taxNo": integer,"postalCode":integer, "and more if there is"}',
                'possetting'=>'["customerId": integer, "warehouseId": integer, "billerId": integer, "productNumber": integer, "KeyboardActive": boolean, "isTable": boolean, "stripePublicKey": string, "stripeSecretKey": string, "paypalLiveApiUsername": string, "paypalLiveApiPassword": string, "paypalLiveApiSecret": string, "paymentOptions": string, "invoiceOption": string ]',
                'Sale $ ProductsSale'=>'[{"customerId": integer, "warehouseId": integer, "billerId": integer,"saleStatus": integer,"paymentStatus": integer,"paidAmount": integer,"saleNote": string,"staffNote": string},{"referenceNo": string, "userId": integer, "cashRegisterId": integer,"tableId": integer,"queue": integer,"productId": [1,2,1](array),"quatity":1(detail for product_sale),"customerId": 3}]'
            ],
            "Built-By"=>['7eak by LimFakson 👍✌']
        ];

        $phase2 = [
            'order'=>'/order?tenant_id={}&user_id={}  method get',
            'appearance'=>'/appearance?tenant_id={}&user_id={} method get',
            'menu_setting'=>'/menu-setting?tenant_id={} method get',
            'menu-product'=>'menu/products?tenant_id={}&warehouse_id={}  method get',
        ];

        return response()->json([$data, $phase2]);
    }

}
