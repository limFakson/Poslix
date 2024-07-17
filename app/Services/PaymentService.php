<?php

namespace App\Services;

use App\Payment\PaypalPayment;
use App\Payment\RazorpayPayment;
use App\Payment\StripePayment;
use App\Payment\PaystackPayment;
use App\Payment\PaydunyaPayment;

class PaymentService
{
    public function initialize($payment_type)
    {
        switch ($payment_type) {
            case 'stripe':
                return new StripePayment();
            case 'paypal':
                return new PaypalPayment();
            case 'razorpay':
                return new RazorpayPayment();
            case 'paystack':
                return new PaystackPayment();
            case 'paydunya':
                return new PaydunyaPayment();
            default:
                break;
        }
    }
}
?>
