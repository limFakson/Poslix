@extends('backend.layout.main') @section('content')
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
                        <h4>Whatsapp SMS Setting</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'setting.settingsmswhatsapp_setting_store', 'method' => 'post']) !!}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>WhatsApp *</label>
                                        <select class="form-control" name="whatsapp">
                                            <option {{$setting->whatsapp ?? "no" == 0 ? 'selected' : ''}} value="0">Disabled</option>
                                            <option {{$setting->whatsapp ?? "no" == 1 ? 'selected' : ''}} value="1">Enabled</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>SMS *</label>
                                        <select class="form-control" name="sms">
                                            <option {{$setting->sms ?? "no" == 0 ? 'selected' : ''}} value="0">Disabled</option>
                                            <option {{$setting->sms ?? "no" == 1 ? 'selected' : ''}} value="1">Enabled</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Template *</label>
                                        <textarea placeholder="Message Template" class="form-control" name="template" id="template" cols="30" rows="10">{{$setting->template ?? ''}}</textarea>
                                    </div>

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
    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #sms-setting-menu").addClass("active");

    if( $('input[name="gateway_hidden"]').val() == 'twilio' ){
        $('select[name="gateway"]').val('twilio');
        $('.clickatell').hide();
    }
    else if( $('input[name="gateway_hidden"]').val() == 'clickatell' ){
        $('select[name="gateway"]').val('clickatell');
        $('.twilio').hide();
    }
    else{
        $('.clickatell').hide();
        $('.twilio').hide();
    }

    $('select[name="gateway"]').on('change', function(){
        if( $(this).val() == 'twilio' ){
            $('.clickatell').hide();
            $('.twilio').show(500);
            $('.twilio-option').prop('required',true);
            $('.clickatell-option').prop('required',false);
        }
        else if( $(this).val() == 'clickatell' ){
            $('.twilio').hide();
            $('.clickatell').show(500);
            $('.twilio-option').prop('required',false);
            $('.clickatell-option').prop('required',true);
        }
    });

</script>
@endpush
