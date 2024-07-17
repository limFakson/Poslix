@extends('landlord.layout.main') @section('content')

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Package')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['packages.update', $packageData->id], 'method' => 'put', 'id' => 'package-form']) !!}
                            <div class="row">
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.name')}} *</label>
                                    <input type="text" name="name" required class="form-control" value="{{$packageData->name}}">
                                </div>
                                <div class="col-md-3 mt-4">
                                    <input type="checkbox" name="is_free_trial" value="1" @if($packageData->is_free_trial){{'checked'}}@endif>
                                    <label>{{trans('file.Free Trial')}}</label>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Monthly Fee')}} *</label>
                                    <input type="number" name="monthly_fee" required class="form-control" value="{{$packageData->monthly_fee}}">
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Yearly Fee')}} *</label>
                                    <input type="number" name="yearly_fee" required class="form-control" value="{{$packageData->yearly_fee}}">
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Number of Warehouses')}}</label>
                                    <input type="number" name="number_of_warehouse" class="form-control" value="{{$packageData->number_of_warehouse}}" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Products')}}</label>
                                    <input type="number" name="number_of_product" class="form-control" value="{{$packageData->number_of_product}}" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Invoices')}}</label>
                                    <input type="number" name="number_of_invoice" class="form-control" value="{{$packageData->number_of_invoice}}" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-3 form-group">
                                	<label>{{trans('file.Number of User Account')}}</label>
                                    <input type="number" name="number_of_user_account" class="form-control" value="{{$packageData->number_of_user_account}}" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-2 form-group">
                                	<label>{{trans('file.Number of Employees')}}</label>
                                    <input type="number" name="number_of_employee" class="form-control" value="{{$packageData->number_of_employee}}" required>
                                    <p>0 = {{trans('file.Infinity')}}</p>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>{{trans('file.Features')}}</label>
                                    <ul style="list-style-type: none; margin-left: -30px;">
                                        <li><input type="checkbox" class="features" name="features[]" value="product_and_categories" checked disabled>&nbsp; Product and Categories</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="purchase_and_sale" checked disabled>&nbsp; Purchase and Sale</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="sale_return" @if(in_array("sale_return", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Sale Return</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="purchase_return" @if(in_array("purchase_return", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Purchase Return</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="expense" @if(in_array("expense", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Expense</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="extras" @if(in_array("extras", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Extras/Add-ons</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="transfer" @if(in_array("transfer", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Stock Transfer</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="quotation" @if(in_array("quotation", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Quotation</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="delivery" @if(in_array("delivery", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Product Delivery</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="stock_count_and_adjustment" @if(in_array("stock_count_and_adjustment", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Stock Count and Adjustment</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="report" @if(in_array("report", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Report</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="hrm" @if(in_array("hrm", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; HRM</li>
                                        <li><input type="checkbox" class="features" name="features[]" value="accounting" @if(in_array("accounting", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Accounting</li>
                                        @if(file_exists(base_path('Modules/Ecommerce')))
                                        <li><input type="checkbox" class="features" name="features[]" value="ecommerce" @if(in_array("ecommerce", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Ecommerce</li>
                                        @endif
                                        @if(file_exists(base_path('Modules/Woocommerce')))
                                        <li><input type="checkbox" class="features" name="features[]" value="woocommerce" @if(in_array("woocommerce", json_decode($packageData->features))){{'checked'}}@endif>&nbsp; Woocommerce</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-12 form-group">
                                    <input type="checkbox" name="is_update_existing" value="1">&nbsp; <strong>{{trans('file.Update existing clients who are using this package')}}</strong>
                                </div>
                                <input type="hidden" name="permission_id">
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                                </div>
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
    $("ul#package").siblings('a').attr('aria-expanded','true');
    $("ul#package").addClass("show");
    $("ul#package #package-create-menu").addClass("active");

    setPermission();

    $(".features").on("change", function() {
        setPermission();
    });

    function setPermission() {
        permission_ids = '';
        $(".features").each(function(index) {
            if ($(this).is(':checked')) {
                if($(this).val() == 'sale_return') {
                    permission_ids += '24,25,26,27,';
                }
                else if($(this).val() == 'purchase_return') {
                    permission_ids += '63,64,65,66,';
                }
                else if($(this).val() == 'expense') {
                    permission_ids += '55,56,57,58,';
                }
                else if($(this).val() == 'transfer') {
                    permission_ids += '20,21,22,23,';
                }
                else if($(this).val() == 'quotation') {
                    permission_ids += '16,17,18,19,';
                }
                else if($(this).val() == 'delivery') {
                    permission_ids += '99,';
                }
                else if($(this).val() == 'stock_count_and_adjustment') {
                    permission_ids += '78,79,';
                }
                else if($(this).val() == 'report') {
                    permission_ids += '36,37,38,39,40,45,46,47,48,49,50,51,52,53,54,77,90,112,122,123,125,';
                }
                else if($(this).val() == 'hrm') {
                    permission_ids += '62,70,71,72,73,74,75,76,89,';
                }
                else if($(this).val() == 'accounting') {
                    permission_ids += '67,68,69,97,';
                }
            }
        });
        if(permission_ids)
            permission_ids = permission_ids.substring(0, permission_ids.length - 1);
        $("input[name=permission_id]").val(permission_ids);
    }

    $(document).on('submit', '#package-form', function(e) {
	    $(".features").prop("disabled", false);
	});
</script>
@endpush
