<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
use Paydunya;
use App\Models\GeneralSetting;
use App\Models\landlord\Package;

class PaydunyaPayment implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        $general_setting = GeneralSetting::latest()->first();
        $package = Package::select('name')->find($request->package_id);
        //setting up Paydunya credentials
        Paydunya\Setup::setMasterKey($general_setting->paydunya_master_key);
        Paydunya\Setup::setPublicKey($general_setting->paydunya_public_key);
        Paydunya\Setup::setPrivateKey($general_setting->paydunya_secret_key);
        Paydunya\Setup::setToken($general_setting->paydunya_token);
        //Paydunya\Setup::setMode("test");

        Paydunya\Checkout\Store::setName($general_setting->site_title); // Only the name is required
        Paydunya\Checkout\Store::setPhoneNumber($general_setting->phone);
        Paydunya\Checkout\Store::setWebsiteUrl("https://".env('CENTRAL_DOMAIN'));
        Paydunya\Checkout\Store::setLogoUrl("https://".env('CENTRAL_DOMAIN')."/public/landlord/images/logo/".$general_setting->site_logo);

        Paydunya\Checkout\Store::setCallbackUrl("https://".env('CENTRAL_DOMAIN'));
        Paydunya\Checkout\Store::setReturnUrl("https://".env('CENTRAL_DOMAIN')."/payment_success");
        Paydunya\Checkout\Store::setCancelUrl("https://".env('CENTRAL_DOMAIN'));

        $invoice = new Paydunya\Checkout\CheckoutInvoice();
        $invoice->addItem($package->name, 1, $request->price, $request->price, $package->subscription_type." subscription.");
        $invoice->setTotalAmount($request->price);
        // Addition of several payment methods at a time
        //$invoice->addChannels(['card', 'jonijoni-senegal', 'orange-money-senegal']);
        if($invoice->create()) {
            return \Redirect::to($invoice->getInvoiceUrl());
        }else{
            echo $invoice->response_text;
        }
    }

    public function cancel()
    {
        return redirect('/');
    }
}

?>
