@extends('backend.layout.main') @section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
    @endif

    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}
        </div>
    @endif

    @push('css')
        <style>
            /* Switch container */
            .switch {
                position: relative;
                display: inline-block;
                width: 34px;
                height: 20px;
            }

            /* Hide default HTML checkbox */
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            /* Custom slider */
            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 34px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                left: 2px;
                bottom: 2px;
                background-color: black;
                transition: .4s;
                border-radius: 50%;
            }

            input:disabled+.slider {
                background-color: #f0f0f0;
            }

            /* When the checkbox is checked, change background color */
            input:checked+.slider {
                background-color: #d496d4;
            }

            /* Move the slider to the right when the checkbox is checked */
            input:checked+.slider:before {
                transform: translateX(14px);
            }
        </style>
    @endpush
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{ trans('file.Order') }}</h4>
                        </div>
                        <div class="card-body">
                            <h1>Manage your business order types and payments</h1>
                            <hr>
                            <h3>Type Settings</h3>
                            <h6>Manage your order type configuration</h6>
                            <form action="{{ url('setting/order') }}" method="post">
                                @csrf
                                <div class="row mt-5">
                                    <div class="col-md-6"><b>Dine-in Orders</b></div>
                                    <div class="col-md-6">
                                        <div class="enable-dine-order row">
                                            <div class="first">
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($dine) && $dine->enable_order == 1 ? 'checked' : '' }}
                                                        class="enable-order-check" name="enable_dine_in_order"
                                                        id="enable_dine_in_order">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_dine_in_order"><b>Activate dine-in orders</b></label>
                                                <p class="text-black">Enable dine-in orders</p>
                                            </div>
                                        </div>
                                        <div class="enable-dine-online-payments row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($dine) && $dine->enable_payments == 1 ? 'checked' : '' }}
                                                        class="enable-online-payments-check"
                                                        name="enable_dine_online_payments_check"
                                                        id="enable_dine_online_payments_check">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_dine_online_payments_check"><b>Enable dine-in online
                                                        payments</b></label>
                                                <p class="text-black">Allow customer to pay via credit card for 'dine-in'
                                                    orders.</p>
                                            </div>
                                        </div>
                                        <div class="dine-payment-required row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox" class="enable-dine-in-order"
                                                        {{ isset($dine) && $dine->payment_required == 1 ? 'checked' : '' }}
                                                        name="enable_dine_payment_required"
                                                        id="enable_dine_payment_required">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_dine_payment_required"><b>Payment is required</b></label>
                                                <p class="text-black">Force customers to make payment before a dine-in order
                                                    is placed.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-6"><b>Pickup Orders</b></div>
                                    <div class="col-md-6">
                                        <div class="enable-pickup-order row">
                                            <div class="first">
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($pickup) && $pickup->enable_order == 1 ? 'checked' : '' }}
                                                        class="enable-order-check" name="enable_pickup_order"
                                                        id="enable_pickup_order">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_pickup_order"><b>Enable pickup orders</b></label>
                                                <p class="text-black">Activate pickup orders</p>
                                            </div>
                                        </div>
                                        <div class="enable-pickup-online-payments row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($pickup) && $pickup->enable_payments == 1 ? 'checked' : '' }}
                                                        class="enable-online-payments-check"
                                                        name="enable_pickup_online_payments_check"
                                                        id="enable_pickup_online_payments_check">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_pickup_online_payments_check"><b>Enable pickup online
                                                        payments</b></label>
                                                <p class="text-black">Allow customer to pay via credit card for 'pickup'
                                                    orders.</p>
                                            </div>
                                        </div>
                                        <div class="pickup-payment-required row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($pickup) && $pickup->payment_required == 1 ? 'checked' : '' }}
                                                        class="enable-pickup-order" name="enable_pickup_payment_required"
                                                        id="enable_pickup_payment_required">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_pickup_payment_required"><b>Payment is
                                                        required</b></label>
                                                <p class="text-black">Force customers to make payment before a pickup order
                                                    is placed.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md-6"><b>Delivery Orders</b></div>
                                    <div class="col-md-6">
                                        <div class="enable-delivery-order row">
                                            <div class="first">
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($delivery) && $delivery->enable_order == 1 ? 'checked' : '' }}
                                                        class="enable-order-check" name="enable_delivery_order"
                                                        id="enable_delivery_order">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_delivery_order"><b>Enable delivery orders</b></label>
                                                <p class="text-black">Activate delivery orders</p>
                                            </div>
                                        </div>
                                        <div class="enable-delivery-online-payments row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($delivery) && $delivery->enable_payments == 1 ? 'checked' : '' }}
                                                        class="enable-online-payments-check"
                                                        name="enable_delivery_online_payments_check"
                                                        id="enable_delivery_online_payments_check">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_delivery_online_payments_check"><b>Enable delivery
                                                        online
                                                        payments</b></label>
                                                <p class="text-black">Allow customer to pay via credit card for 'delivery'
                                                    orders.</p>
                                            </div>
                                        </div>
                                        <div class="delivery-payment-required row">
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($delivery) && $delivery->payment_required == 1 ? 'checked' : '' }}
                                                        class="enable-delivery-order"
                                                        name="enable_delivery_payment_required"
                                                        id="enable_delivery_payment_required">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="enable_delivery_payment_required"><b>Payment is
                                                        required</b></label>
                                                <p class="text-black">Force customers to make payment before a delivery
                                                    order is placed.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-auto text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>

        $("ul#dmsetting").siblings('a').attr('aria-expanded','true');
        $("ul#dmsetting").addClass("show");
        $("ul#dmsetting #order-digital-menu").addClass("active");

        $(document).ready(function() {
            if ($('#enable_dine_in_order')[0].checked == false) {
                $('.enable-dine-online-payments').addClass('d-none');
                $('.dine-payment-required').addClass('d-none');
            } else {
                $('.enable-dine-online-payments').removeClass('d-none');
                $('.dine-payment-required').removeClass('d-none');
            }
            if ($('#enable_dine_online_payments_check')[0].checked == false) {
                $('#enable_dine_payment_required').attr('disabled', true);
            } else {
                $('#enable_dine_payment_required').attr('disabled', false);
            }
            if ($('#enable_pickup_order')[0].checked == false) {
                $('.enable-pickup-online-payments').addClass('d-none');
                $('.pickup-payment-required').addClass('d-none');
            } else {
                $('.enable-pickup-online-payments').removeClass('d-none');
                $('.pickup-payment-required').removeClass('d-none');
            }
            if ($('#enable_pickup_online_payments_check')[0].checked == false) {
                $('#enable_pickup_payment_required').attr('disabled', true);
            } else {
                $('#enable_pickup_payment_required').attr('disabled', false);
            }
            if ($('#enable_delivery_order')[0].checked == false) {
                $('.enable-delivery-online-payments').addClass('d-none');
                $('.delivery-payment-required').addClass('d-none');
            } else {
                $('.enable-delivery-online-payments').removeClass('d-none');
                $('.delivery-payment-required').removeClass('d-none');
            }
            if ($('#enable_delivery_online_payments_check')[0].checked == false) {
                $('#enable_delivery_payment_required').attr('disabled', true);
            } else {
                $('#enable_delivery_payment_required').attr('disabled', false);
            }
            $('#enable_dine_in_order').click(() => {
                if ($('#enable_dine_in_order')[0].checked == false) {
                    $('.enable-dine-online-payments').addClass('d-none');
                    $('.dine-payment-required').addClass('d-none');
                } else {
                    $('.enable-dine-online-payments').removeClass('d-none');
                    $('.dine-payment-required').removeClass('d-none');
                }
            });
            $('#enable_dine_online_payments_check').click(() => {
                if ($('#enable_dine_online_payments_check')[0].checked == false) {
                    $('#enable_dine_payment_required').attr('disabled', true);
                } else {
                    $('#enable_dine_payment_required').attr('disabled', false);
                }
            });
            $('#enable_pickup_order').click(() => {
                if ($('#enable_pickup_order')[0].checked == false) {
                    $('.enable-pickup-online-payments').addClass('d-none');
                    $('.pickup-payment-required').addClass('d-none');
                } else {
                    $('.enable-pickup-online-payments').removeClass('d-none');
                    $('.pickup-payment-required').removeClass('d-none');
                }
            });
            $('#enable_pickup_online_payments_check').click(() => {
                if ($('#enable_pickup_online_payments_check')[0].checked == false) {
                    $('#enable_pickup_payment_required').attr('disabled', true);
                } else {
                    $('#enable_pickup_payment_required').attr('disabled', false);
                }
            });
            $('#enable_delivery_order').click(() => {
                if ($('#enable_delivery_order')[0].checked == false) {
                    $('.enable-delivery-online-payments').addClass('d-none');
                    $('.delivery-payment-required').addClass('d-none');
                } else {
                    $('.enable-delivery-online-payments').removeClass('d-none');
                    $('.delivery-payment-required').removeClass('d-none');
                }
            });
            $('#enable_delivery_online_payments_check').click(() => {
                if ($('#enable_delivery_online_payments_check')[0].checked == false) {
                    $('#enable_delivery_payment_required').attr('disabled', true);
                } else {
                    $('#enable_delivery_payment_required').attr('disabled', false);
                }
            });
        });
    </script>
@endpush
