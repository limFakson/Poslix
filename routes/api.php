<?php

use App\Http\Controllers\DemoAutoUpdateController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductWarehouseController;
use App\Http\Controllers\Api\V2\SecondProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ApiOverviewController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\PosSettingsController;
use App\Http\Controllers\Api\GenSettingsController;
use App\Http\Controllers\Api\BillerController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\GiftCardController;
use App\Http\Controllers\Api\ButtonController;
use App\Http\Controllers\Api\CashRegisterController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\ReturnSaleController;
use App\Http\Controllers\Api\VariantController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\landlord\UploadController;
use Modules\Ecommerce\Http\Controllers\OrderController;
use Modules\Ecommerce\Http\Controllers\AppearanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(DemoAutoUpdateController::class)->group(function () {
    Route::get('fetch-data-general', 'fetchDataGeneral')->name('fetch-data-general');
    Route::get('fetch-data-upgrade', 'fetchDataForAutoUpgrade')->name('data-read');
    Route::get('fetch-data-bugs', 'fetchDataForBugs')->name('fetch-data-bugs');
});



Route::post('auth/login', [LoginController::class, 'Login'])->middleware('tenant.auth');
Route::apiResource('product', ProductController::class);
Route::middleware(['tenant.init'])->group(function () {
    Route::apiResource('variant', VariantController::class);
    Route::apiResource('notification', NotificationController::class);
    Route::apiResource('action-button', ButtonController::class);
    Route::get('menu/product', [SecondProductController::class, 'menu_products']);
    Route::apiResource('coupon', CouponController::class);
    Route::apiResource('giftcard', GiftCardController::class);
    Route::apiResource('warehouse/product', ProductWarehouseController::class);
    Route::get('/order', [OrderController::class, 'orderapi']);
    Route::get('/appearance', [AppearanceController::class, 'appearanceapi']);
});
Route::apiResource('sale', SaleController::class);
Route::apiResource('customer', CustomerController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('user', UserController::class);
Route::apiResource('', ApiOverviewController::class);
Route::apiResource('biller', BillerController::class);
Route::apiResource('warehouse', WarehouseController::class);
Route::apiResource('pos-setting', PosSettingsController::class);
Route::apiResource('cashregister', CashRegisterController::class);
Route::apiResource('table', TableController::class);
Route::apiResource('gensettings', GenSettingsController::class);
Route::apiResource('return-sale', ReturnSaleController::class);

Route::get('tenant', [UserController::class, 'tenant']);
Route::post('create-customer', [CustomerController::class, 'storeCustomer']);
Route::get('tenant/user/{id}', [UserController::class, 'tenantuser']);
Route::get('product/bycategory/{category_id}', [ProductController::class, 'showByCategoryId']);
Route::get('customer/byuser/{user_id}', [CustomerController::class, 'showByUserId']);
Route::delete('sale/product-sale/{id}', [SaleController::class, 'destroyProductSale']);

Route::get('menu-setting', [PosSettingsController::class, 'menu_settings']);

Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    return "Cache is cleared";
});
Route::get('/config-cache', function() {
    Artisan::call('config:cache');
    return "Config cache cleared and re-cached";
});
Route::get('/check-env', function() {
    return response()->json([
        'URL' => env('APP_URL'),
        'APP_NAME' => env('APP_NAME')
    ]);
});

// Route::get('/grant-privileges', [DatabaseController::class, 'grantPrivileges']);
Route::get('/xml-response', [UploadController::class, 'xmlDoc']);

Route::get('dom', [TableController::class, 'domtoPdf']);