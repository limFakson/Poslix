@extends('payment.master')
@section('payment')


        <form action="{{route('payment.process')}}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$request->id}}">
            <input type="hidden" name="package_id" value="{{$request->package_id}}">
            <input type="hidden" name="subscription_type" value="{{$request->subscription_type}}">
            <input type="hidden" name="price" value="{{$request->price}}">
            <input type="hidden" name="company_name" value="{{$request->company_name}}">
            <input type="hidden" name="phone_number" value="{{$request->phone_number}}">
            <input type="hidden" name="email" value="{{$request->email}}">
            <input type="hidden" name="name" value="{{$request->name}}">
            <input type="hidden" name="password" value="{{$request->password}}">
            <input type="hidden" name="tenant" value="{{$request->tenant}}">
            <input type="hidden" name="expiry_date" value="{{date('Y-m-d', strtotime('+'.$request->numberOfDaysToExpired.' days'))}}">
            <input type="hidden" name="allDomains" value="{{$request->allDomains}}">
            <input type="hidden" name="permission_ids" value="{{$request->permission_ids}}">
            <input type="hidden" name="abandoned_permission_ids" value="{{$request->abandoned_permission_ids}}">
            <input type="hidden" name="renewal" value="{{$request->renewal}}">

            <h4 class="mt-3">Payable Amount : {{$request->price}}</h4>
            <div class="mt-5">
                @if(in_array("stripe", $active_payment_gateway))
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='stripe' value="stripe">
                    <span class="sm-heading">Stripe</span>
                </label>
                @endif
                @if(in_array("paypal", $active_payment_gateway))
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='paypal' value="paypal">
                    <span class="sm-heading">Paypal</span>
                </label>
                @endif
                @if(in_array("razorpay", $active_payment_gateway))
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='razorpay' value="razorpay">
                    <span class="sm-heading">Razorpay</span>
                </label>
                @endif
                @if(in_array("paystack", $active_payment_gateway))
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='paystack' value="paystack">
                    <span class="sm-heading">Paystack</span>
                </label>
                @endif
                @if(in_array("paydunya", $active_payment_gateway))
                <label class="custom-checkbox">
                    <input type="radio" required name="payment_type" id='paydunya' value="paydunya">
                    <span class="sm-heading">Paydunya</span>
                </label>
                @endif
            </div>
            @if($terms_and_condition_page)
                <p>
                    <input type="checkbox" required> I have read <a target="_blank" href="{{url('page/'.$terms_and_condition_page->slug)}}">Terms & Conditions</a>
                </p>
            @endif
            <div class="mar-top-30 mt-3">
                <button type="submit" class="btn btn-success text-center w-50">Payment Procced</button>
            </div>
        </form>

@endsection

