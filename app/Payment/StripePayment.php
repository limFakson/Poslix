<?php

namespace App\Payment;

use App\Contracts\Payble\PaybleContract;
// use App\Traits\PaymentTrait;
use Exception;
use Stripe\Stripe;

class StripePayment implements PaybleContract
{
    public function pay($request, $otherRequest)
    {
        try {
            $this->stripe($request->price * 100, $otherRequest['stripeToken']);
        } catch (Exception $e) {
            return response()->json(['errors' => $e->getMessage()]);
        }

        // $order_id = $this->orderStore($request);
        // $this->reduceProductQuantity($order_id);
        /*
        |----------------------------------------------------------
        | Store your data here.
        | This section is common for all. Suggest you to use Trait.
        |----------------------------------------------------------
        */

        return response()->json(['success' =>'done']);
    }

    public function cancel(){
        // $this->orderCancel();
        // return response()->json('success');

        /*
        |----------------------------------------------------------
        | Store your data here.
        | This section is common for all. Suggest you to use Trait.
        |----------------------------------------------------------
        */
    }

    protected function stripe($totalAmount, $stripeToken) {
        $general_setting = \App\Models\GeneralSetting::latest()->first();
        Stripe::setApiKey($general_setting->stripe_secret_key);
        $token = $stripeToken;
        $amount = $totalAmount;
        // Create a Customer:
        $customer = \Stripe\Customer::create([
            'source' => $token
        ]);
        // Charge the Customer instead of the card:
        $charge = \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => $general_setting->currency,
            'customer' => $customer->id
        ]);

        /*Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => (int)implode(explode(',',$totalAmount)),
                "currency" => env('STRIPE_CURRENCY'),
                "source" => $stripeToken,
                "description" => "Stripe Payment Successfull."
        ]);*/
    }
}

?>
