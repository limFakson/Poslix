@extends('backend.layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<div id="limits-content" class="alert alert-danger limit-content text-center" style="display: none;">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    You can select a maximum of 3 payment options.
</div>

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Digital Menu Payment Setting')}}</h4>
                    </div>
                    <div class="card-body">
                        {{-- <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p> --}}
                        {!! Form::open(['route' => 'menu.payment.create', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h4><strong>Payment Options</strong></h4>
                                </div>
                                <div id="checkbox-container" class="col-md-12 d-flex justify-content-between" style="padding-right: 8rem">
                                    <div class="form-group d-inline">
                                        @if(in_array("cash",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="cash" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="cash">
                                        @endif
                                        <label class="mt-2"><strong>Cash</strong></label>
                                    </div>

                                    <div class="form-group d-inline">
                                        @if(in_array("card",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="card" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="card">
                                        @endif
                                        <label class="mt-2"><strong>Card</strong></label>
                                    </div>

                                    <div class="form-group d-inline">
                                        @if(in_array("cheque",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="cheque" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="cheque">
                                        @endif
                                        <label class="mt-2"><strong>Cheque</strong></label>
                                    </div>

                                    <div class="form-group d-inline">
                                        @if(in_array("gift_card",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="gift_card" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="gift_card">
                                        @endif
                                        <label class="mt-2"><strong>Gift Card</strong></label>
                                    </div>

                                    <div class="form-group d-inline">
                                        @if(in_array("deposit",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="deposit" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="deposit">
                                        @endif
                                        <label class="mt-2"><strong>Deposit</strong></label>
                                    </div>

                                    <div class="form-group d-inline">
                                        @if(in_array("paypal",$options))
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="paypal" checked>
                                        @else
                                        <input class="mt-2 payment-option" type="checkbox" name="options[]" value="paypal">
                                        @endif
                                        <label class="mt-2"><strong>Paypal</strong></label>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-md-4 mb-3 d-flex">
                                    <h4 class="d-flex"><strong class="my-auto">Custom Payment Options</strong></h4>
                                </div>
                                <div class="col-md-12 d-flex ">
                                    @foreach ($methods as $method)
                                        <div id="checkbox-container" class="form-group d-inline col-md-2 d-flex">
                                            <div>
                                                @if($method->is_online == 1)
                                                <input class="mt-2 payment-option" type="checkbox" name="coptions[]" value="{{$method->id}}" checked>
                                                @else
                                                <input class="mt-2 payment-option" type="checkbox" name="coptions[]" value="{{$method->id}}">
                                                @endif
                                                <label class="mt-2"><strong>{{$method->name}}</strong></label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@push('scripts')
<script type="text/javascript">

    $("ul#dmsetting").siblings('a').attr('aria-expanded','true');
    $("ul#dmsetting").addClass("show");
    $("ul#dmsetting #payment-digital-menu").addClass("active");

    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('#checkbox-container .payment-option');
        const alertBox = document.getElementById('limits-content');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const checkedCheckboxes = document.querySelectorAll('#checkbox-container .payment-option:checked');
                if (checkedCheckboxes.length > 3) {
                    this.checked = false;
                    showAlert();
                }
            });
        });

        function showAlert() {
            alertBox.style.display = 'block';
            setTimeout(function () {
                alertBox.style.opacity = '1';
                alertBox.style.transition = 'opacity 1s linear';
                alertBox.style.opacity = '0';
                setTimeout(function () {
                    alertBox.style.display = 'none';
                    alertBox.style.opacity = '1';
                }, 1000);
            }, 2000);
        }
    });

    $('select[name="customer_id"]').val($("input[name='customer_id_hidden']").val());
    $('select[name="biller_id"]').val($("input[name='biller_id_hidden']").val());
    $('select[name="warehouse_id"]').val($("input[name='warehouse_id_hidden']").val());
    $('.selectpicker').selectpicker('refresh');

</script>
@endpush
