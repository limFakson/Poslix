<?php

Route::get('/payment_cancel',function() {
    return 'payment_cancel';
});

Route::any('/clear',function(){
   \Artisan::call('optimize:clear');
   return 1;
});

Route::get('switch-theme/{theme}', 'HomeController@switchTheme')->name('switchTheme');
Route::post('contact-form', 'landlord\LandingPageController@contactForm')->name('contactForm');
Route::post('tenant-checkout', 'Payment\PaymentController@tenantCheckout')->name('tenant.checkout');
Route::get('payment_success', 'Payment\PaymentController@success')->name('payment.success');
Route::get('update-coupon', 'CouponController@updateCoupon');
Route::post('payment/process', 'Payment\PaymentController@paymentProcees')->name('payment.process');
Route::post('payment/{payment_method}/pay', 'Payment\PaymentController@paymentPayPage')->name('payment.pay.page');
Route::post('payment/{payment_method}/pay/confirm', 'Payment\PaymentController@paymentPayConfirm')->name('payment.pay.confirm');
Route::post('payment/{payment_method}/pay/cancel', 'Payment\PaymentController@paymentPayCancel')->name('payment.pay.cancel');

// Paystack
Route::get('/payment/paystack/pay/callback', 'Payment\PaymentController@handleGatewayCallback')->name('payment.pay.callback');


Route::get('clear',function() {
    Artisan::call('optimize:clear');
    cache()->forget('hero');
    cache()->forget('module_descriptions');
    cache()->forget('faq_descriptions');
    cache()->forget('tenant_signup_descriptions');
    dd('cleared');
});

Route::middleware(['cors'])->group(function () {
    Route::get('fetch-package-data/{id}', 'landlord\PackageController@fetchPackageData')->name('package.fetchData');
    Route::get('/documentation', 'HomeController@documentation')->name('documentation');
});

Route::controller(Auth\SuperAdminLoginController::class)->group(function () {
    Route::get('superadmin-login', 'login');
    Route::post('superadmin-login/store', 'store')->name('superadmin.login');
});

//landing page routes
Route::controller(landlord\LandingPageController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('sign-up', 'signUp')->name('signup');
    Route::get('reset-client-db', 'resetClientDB');
    Route::post('create-tenant', 'createTenant')->name('createTenant');
    Route::get('contact-for-renewal', 'contactForRenewal')->name('contactForRenewal');
    Route::post('renewal-subscription', 'renewSubscription')->name('renewSubscription');
    Route::get('backup-tenant-db', 'backupTenantDB')->name('superadmin.backupTenantDB');
    Route::get('superadmin/update-tenant-db', 'updateTenantDB')->name('superadmin.updateTenantDB');
    Route::get('superadmin/update-superadmin-db', 'updateSuperadminDB')->name('superadmin.updateSuperadminDB');
});
//blog routes
Route::controller(landlord\BlogController::class)->group(function () {
    Route::get('/blog', 'list');
    Route::get('blog/{slug}', 'details');
});
//page routes
Route::controller(landlord\PageController::class)->group(function () {
    Route::get('page/{slug}', 'details');
});
//language routes
Route::controller(LanguageController::class)->group(function () {
    Route::get('switch-landing-page-language/{lang_id}', 'switchLandingPageLanguage');
});


Route::group(['prefix' => 'superadmin', 'middleware' => ['superadminauth']], function() {
    Route::controller(landlord\DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('superadmin.dashboard');
    });

    Route::controller(Auth\SuperAdminLoginController::class)->group(function () {
        Route::post('logout', 'logout')->name('superadmin.logout');
    });
    //setting routes
    Route::controller(SettingController::class)->group(function () {
        Route::get('general-setting', 'superadminGeneralSetting')->name('superadminGeneralSetting');
        Route::post('general-setting/store', 'superadminGeneralSettingStore')->name('superadminGeneralSetting.store');
        Route::get('mail_setting', 'superadminMailSetting')->name('superadminMailSetting');
        Route::post('mail_setting_store', 'superadminMailSettingStore')->name('superadminMailSettingStore');
    });
    //user routes
    Route::controller(UserController::class)->group(function () {
        Route::get('user/profile/{id}','superadminProfile')->name('user.superadminProfile');
        Route::put('user/update_profile/{id}', 'profileUpdate')->name('user.superadminProfileUpdate');
        Route::put('user/changepass/{id}', 'changePassword')->name('user.superadminPassword');
    });
    //client routes
    Route::controller(landlord\ClientController::class)->group(function () {
        Route::get('clients', 'index')->name('clients.index');
        Route::post('clients/store', 'store')->name('clients.store');
        Route::delete('clients/destroy/{id}', 'destroy')->name('clients.destroy');
        Route::post('clients/renew', 'renew')->name('clients.renew');
        Route::post('clients/change-package', 'changePackage')->name('clients.changePackage');
    });
    //hero routes
    Route::controller(landlord\HeroController::class)->group(function () {
        Route::get('hero-section', 'index');
        Route::post('hero-section/store', 'store')->name('heroSection.store');
    });
    //faq routes
    Route::controller(landlord\FaqController::class)->group(function () {
        Route::get('faq-section', 'index');
        Route::post('faq-section/store', 'store')->name('faq.store');
        Route::post('faq-section/update', 'update')->name('faq.update');
        Route::post('faq-section/sort', 'sort');
        Route::post('faq-section/delete/{id}', 'delete')->name('faq.delete');
    });
    //module routes
    Route::controller(landlord\ModuleController::class)->group(function () {
        Route::get('module-section', 'index');
        Route::post('module-section/store', 'store')->name('module.store');
        Route::post('module-section/update', 'update')->name('module.update');
        Route::post('module-section/sort', 'sort');
        Route::post('module-section/delete/{id}', 'delete')->name('module.delete');
    });
    //features routes
    Route::controller(landlord\FeaturesController::class)->group(function () {
        Route::get('feature-section', 'index');
        Route::post('feature-section/store', 'store')->name('feature.store');
        Route::post('feature-section/update', 'update')->name('feature.update');
        Route::post('feature-section/sort', 'sort');
        Route::post('feature-section/delete/{id}', 'delete')->name('feature.delete');
    });
    //testimonial routes
    Route::controller(landlord\TestimonialController::class)->group(function () {
        Route::get('testimonial-section', 'index');
        Route::post('testimonial-section/store', 'store')->name('testimonial.store');
        Route::post('testimonial-section/update', 'update')->name('testimonial.update');
        Route::post('testimonial-section/sort', 'sort');
        Route::post('testimonial-section/delete/{id}', 'delete')->name('testimonial.delete');
    });
    //tenant signup description routes
    Route::controller(landlord\TenantSignupDescriptionController::class)->group(function () {
        Route::get('tenant-signup-description', 'index');
        Route::post('tenant-signup-description/store', 'store')->name('tenantSignupDescription.store');
    });
    //blog routes
    Route::controller(landlord\BlogController::class)->group(function () {
        Route::get('blog-section', 'index');
        Route::post('blog-section/store', 'store')->name('blog.store');
        Route::get('blog-section/edit/{id}', 'edit');
        Route::post('blog-section/update', 'update')->name('blog.update');
        Route::post('blog-section/sort', 'sort');
        Route::post('blog-section/delete/{id}', 'delete')->name('blog.delete');
    });
    //page routes
    Route::controller(landlord\PageController::class)->group(function () {
        Route::get('page-section', 'index');
        Route::post('page-section/store', 'store')->name('page.store');
        Route::get('page-section/edit/{id}', 'edit');
        Route::post('page-section/update', 'update')->name('page.update');
        Route::post('page-section/sort', 'sort');
        Route::post('page-section/delete/{id}', 'delete')->name('page.delete');
    });
    //social routes
    Route::controller(landlord\SocialController::class)->group(function () {
        Route::get('social-section', 'index');
        Route::post('social-section/store', 'store')->name('social.store');
        Route::post('social-section/update', 'update')->name('social.update');
        Route::post('social-section/sort', 'sort');
        Route::post('social-section/delete/{id}', 'delete')->name('social.delete');
    });

    //package routes
    Route::resource('packages', landlord\PackageController::class);
    //payment routes
    Route::resource('payments', landlord\PaymentController::class);
    //language routes
    Route::controller(LanguageController::class)->group(function () {
        Route::get('languages', 'index')->name('languages.index');
        Route::post('languages', 'store')->name('languages.store');
        Route::post('languages/update', 'update')->name('languages.update');
        Route::post('languages/delete', 'destroy')->name('languages.destroy');
    });
    //support system routes
    Route::controller(landlord\SupportController::class)->group(function () {
        Route::get('support', 'index');
        Route::get('support/ticket/{id}', 'ticket');
        Route::post('support/ticket/tenant/', 'tenant');
        Route::post('support/ticket/store', 'store')->name('support.store');
    });
    //ticket routes
    Route::controller(landlord\TicketsController::class)->group(function () {
        Route::get('tickets', 'index');
        Route::get('ticket/{id}', 'ticket');
        Route::post('ticket/store', 'store')->name('ticket.store');
        Route::post('ticket/update', 'update')->name('ticket.update');
    });
});

use App\Http\Controllers\landlord\UploadController;
Route::get('/flutter/upload', [UploadController::class, 'index']);
Route::get('/download/{file}', [UploadController::class, 'download'])->name('download');
Route::post('/upload', [UploadController::class, 'upload']);