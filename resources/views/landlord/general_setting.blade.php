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
            {!! Form::open(['route' => 'superadminGeneralSetting.store', 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#gs_collapse" aria-expanded="true" aria-controls="gs_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{trans('file.General Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.System Title')}} *</label>
                                    <input type="text" name="site_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->site_title}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.System Logo')}} *</label>
                                    <input type="file" name="site_logo" class="form-control" value=""/>
                                </div>
                                @if($errors->has('site_logo'))
                                <span>
                                    <strong>{{ $errors->first('site_logo') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->phone}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}} *</label>
                                    <input type="text" name="email" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->email}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{trans('file.Free Trial Limit')}} *</label>
                                    <input type="number" name="free_trial_limit" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->free_trial_limit}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{trans('file.Currency Code')}} *</label>
                                    <input type="text" name="currency" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->currency}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Frontend Layout</label>
                                    <div class="form-check">
                                        <input name="frontend_layout" id="regular" class="form-check-input" type="radio" value="regular" @if($lims_general_setting_data->frontend_layout == 'regular') checked @endif required>
                                        <label class="form-check-label" for="regular">
                                            Regular
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input name="frontend_layout" id="custom" class="form-check-input" type="radio" value="custom" @if($lims_general_setting_data->frontend_layout == 'custom') checked @endif  required>
                                        <label class="form-check-label" for="custom">
                                            Custom
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Date Format')}} *</label>
                                    @if($lims_general_setting_data)
                                    <input type="hidden" name="date_format_hidden" value="{{$lims_general_setting_data->date_format}}">
                                    @endif
                                    <select name="date_format" class="selectpicker form-control">
                                        <option value="d-m-Y"> dd-mm-yyy</option>
                                        <option value="d/m/Y"> dd/mm/yyy</option>
                                        <option value="d.m.Y"> dd.mm.yyy</option>
                                        <option value="m-d-Y"> mm-dd-yyy</option>
                                        <option value="m/d/Y"> mm/dd/yyy</option>
                                        <option value="m.d.Y"> mm.dd.yyy</option>
                                        <option value="Y-m-d"> yyy-mm-dd</option>
                                        <option value="Y/m/d"> yyy/mm/dd</option>
                                        <option value="Y.m.d"> yyy.mm.dd</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Dedicated IP')}}</label> <i class="dripicons-question" data-toggle="tooltip" title="{{trans('file.If you purchase dedicated IP from your hosting service provider put it down here. By doing this your client will be able to set custom domain.')}}"></i>
                                    <input type="text" name="dedicated_ip" class="form-control" value="{{$lims_general_setting_data->dedicated_ip}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Developed By')}}</label>
                                    <input type="text" name="developed_by" class="form-control" value="{{$lims_general_setting_data->developed_by}}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#ps_collapse" aria-expanded="true" aria-controls="ps_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{trans('file.Payment Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="ps_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Active Payment Gateway')}} *</label>
                                    <select name="active_payment_gateway[]" class="form-control active-payment-gateway" multiple>
                                        <option value="no">{{trans('file.No Payment Gateway')}}</option>
                                        <option value="stripe">Stripe</option>
                                        <option value="paypal">Paypal</option>
                                        <option value="razorpay">Razorpay</option>
                                        <option value="paystack">Paystack</option>
                                        <option value="paydunya">Paydunya</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <!-- stripe setion -->
                            <div class="col-md-12">
                                <h5>Stripe Credentials</h5>
                            </div>
                            <div class="col-md-6 stripe-section">
                                <div class="form-group">
                                    <label>Stripe Publishable key</label>
                                    <input type="text" name="stripe_public_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->stripe_public_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-6 stripe-section">
                                <div class="form-group">
                                    <label>Stripe Secret key</label>
                                    <input type="text" name="stripe_secret_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->stripe_secret_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <!-- paypal setion -->
                            <div class="col-md-12">
                                <h5>Paypal Credentials</h5>
                            </div>
                            <div class="col-md-6 paypal-section">
                                <div class="form-group">
                                    <label>Paypal Client ID</label>
                                    <input type="text" name="paypal_client_id" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paypal_client_id}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-6 paypal-section">
                                <div class="form-group">
                                    <label>Paypal Client Secret</label>
                                    <input type="text" name="paypal_client_secret" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paypal_client_secret}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <!-- razorpay setion -->
                            <div class="col-md-12">
                                <h5>Razorpay Credentials</h5>
                            </div>
                            <div class="col-md-4 razorpay-section">
                                <div class="form-group">
                                    <label>Razorpay Number</label>
                                    <input type="text" name="razorpay_number" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->razorpay_number}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-4 razorpay-section">
                                <div class="form-group">
                                    <label>Razorpay Key</label>
                                    <input type="text" name="razorpay_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->razorpay_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-4 razorpay-section">
                                <div class="form-group">
                                    <label>Razorpay Secret</label>
                                    <input type="text" name="razorpay_secret" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->razorpay_secret}}@endif" />
                                </div>
                            </div>
                            <!-- paystack setion -->
                            <div class="col-md-12">
                                <h5>Paystack Credentials</h5>
                            </div>
                            <div class="col-md-6 paystack-section">
                                <div class="form-group">
                                    <label>Paystack Publishable key</label>
                                    <input type="text" name="paystack_public_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paystack_public_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-6 paystack-section">
                                <div class="form-group">
                                    <label>Paystack Secret key</label>
                                    <input type="text" name="paystack_secret_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paystack_secret_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <!-- paydunya setion -->
                            <div class="col-md-12">
                                <h5>Paydunya Credentials</h5>
                            </div>
                            <div class="col-md-3 paydunya-section">
                                <div class="form-group">
                                    <label>Paydunya Master key</label>
                                    <input type="text" name="paydunya_master_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paydunya_master_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-3 paydunya-section">
                                <div class="form-group">
                                    <label>Paydunya Public key</label>
                                    <input type="text" name="paydunya_public_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paydunya_public_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-3 paydunya-section">
                                <div class="form-group">
                                    <label>Paydunya Secret key</label>
                                    <input type="text" name="paydunya_secret_key" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paydunya_secret_key}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-3 paydunya-section">
                                <div class="form-group">
                                    <label>Paydunya Token</label>
                                    <input type="text" name="paydunya_token" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->paydunya_token}}@endif" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#seos_collapse" aria-expanded="true" aria-controls="seos_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{trans('file.SEO Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="seos_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">  
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Meta Title')}} * ({{trans('file.50-60 characters')}})</label>
                                    <input type="text" name="meta_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->meta_title}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Meta Description')}} * {{trans('file.150-160 characters')}}</label>
                                    <textarea name="meta_description" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->meta_description}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.og Title')}} *</label>
                                    <input type="text" name="og_title" class="form-control" value="@if($lims_general_setting_data){{$lims_general_setting_data->og_title}}@endif" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.og Description')}} *</label>
                                    <textarea name="og_description" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->og_description}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.og Image')}} *</label>
                                    <input type="file" name="og_image" class="form-control" />
                                </div>
                            </div>
                        </div> 
                        <div class="form-group mt-3">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center" data-toggle="collapse" href="#as_collapse" aria-expanded="true" aria-controls="as_collapse">
                        <h4 class="d-flex justify-content-between w-100">{{trans('file.Analytics Setting')}} <i class="icon dripicons-chevron-up"></i></h4>
                    </div>
                    <div class="card-body collapse show" id="as_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Google Analytics Script')}} *</label>
                                    <textarea name="ga_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->ga_script}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Facebook Pixel Script')}} *</label>
                                    <textarea name="fb_pixel_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->fb_pixel_script}}@endif</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Chat Script')}} *</label>
                                    <textarea name="chat_script" class="form-control">@if($lims_general_setting_data){{$lims_general_setting_data->chat_script}}@endif</textarea>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group mt-3">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #general-setting-menu").addClass("active");
    active_payment_gateway = <?php echo json_encode(explode(",", $lims_general_setting_data->active_payment_gateway)) ?>;
    $(".active-payment-gateway").val(active_payment_gateway);
    $('.selectpicker').selectpicker('refresh');
    $('[data-toggle="tooltip"]').tooltip();
    /*$("select[name=active_payment_gateway]").on("change", function() {
        hidePaymentGateway();
        if($(this).val() == 'stripe') {
            $(".stripe-section").removeClass('d-none');
        }
        else if($(this).val() == 'paypal') {
            $(".paypal-section").removeClass('d-none');
        }
        else if($(this).val() == 'razorpay') {
            $(".razorpay-section").removeClass('d-none');
        }
    });*/

    $('.card-header').on('click', function() {
        $(this).find('.icon').toggleClass('dripicons-chevron-down dripicons-chevron-up');
    });

    /*function hidePaymentGateway() {
        $(".stripe-section").addClass('d-none');
        $(".paypal-section").addClass('d-none');
        $(".razorpay-section").addClass('d-none');
    }*/

</script>
@endpush
