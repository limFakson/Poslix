@extends('payment.master')
@section('payment')

<div class="row">
    <div class="col-12">
        <h1 class="page-title h2 text-center uppercase mt-1 mb-5">{{ $paymentMethodName }}
            <small>
                ({{ number_format((float)$totalAmount, 2, '.', '') }})
            </small>
        </h1>
    </div>
</div>


<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid" id="errorMessage"></div>

                <form class="mb-3 require-validation" id="stripePaymentForm" data-cc-on-file="false">
                    @csrf
                    <input type="hidden" name="requestData" value="{{ $requestData }}">
                    <input type="hidden" id="stripeKey" value="{{$stripe_public_key}}">
                    <input type="hidden" name="stripeToken">
                    <input type="hidden" name="central_domain" value="{{env('CENTRAL_DOMAIN')}}">

                    <div class='row'>
                        <div class='col-xs-12 form-group'>
                            <input type="number" autocomplete='off' name="card_number" class='form-control card-number' placeholder="Card Number" size='20' type='text'>
                        </div>
                    </div>

                    <div class='row mt-3'>
                        <div class='col-xs-12 col-md-4 form-group cvc'>
                            <input type="number" autocomplete='off' name="card-cvc" class='form-control card-cvc' size='4' type='text' placeholder="CVC">
                        </div>
                        <div class='col-xs-12 col-md-4 form-group expiration'>
                            <input type="number" min="1" max="12" class='form-control card-expiry-month' name="card-expiry-month" size='2' type='text' placeholder="Expiry Month">
                        </div>
                        <div class='col-xs-12 col-md-4 form-group expiration'>
                            <input  type="number" class='form-control card-expiry-year' name="card-expiry-year" placeholder='Expiry Year' size='4' type='text'>
                        </div>
                    </div>
                    <div class="mt-4 d-grid gap-2 mx-auto">
                        <button type="submit" id="payNowBtn" class="btn btn-outline-success">Pay Now</button>
                    </div>
                    <div class="mt-3 d-grid gap-2 mx-auto">
                        <button type="button" id="payCancelBtn" class="btn btn-outline-danger">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>
@endsection


@push('payment_scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">
    var requestData = JSON.parse($('input[name=requestData]').val());
    var modules = <?php echo json_encode($modules) ?>;

    if(requestData.tenant) {
        centralDomain = $('input[name=central_domain]').val();
        successUrl = 'https://'+requestData.tenant+'.'+centralDomain;
    }
    else {
        successUrl = 'https://'+JSON.parse(requestData.allDomains)+'/change-permission?key=0&length=1&package_id='+requestData.package_id+'&expiry_date='+requestData.expiry_date+'&subscription_type='+requestData.subscription_type+'&allDomains='+requestData.allDomains+'&abandoned_permission_ids='+requestData.abandoned_permission_ids+'&permission_ids='+requestData.permission_ids+'&modules='+modules+'&routeName='+JSON.parse(requestData.allDomains)+'/login';
    }

    let targetURL = "{{ url('/payment/stripe/pay/confirm')}}";
    let cancelURL = "{{ url('/payment/stripe/pay/cancel')}}";
    let redirectURL = successUrl;
    let redirectURLAfterCancel = "{{ url('/')}}";
</script>
<script type="text/javascript" src="{{ asset('js/payment/stripe.js') }}"></script>

@endpush