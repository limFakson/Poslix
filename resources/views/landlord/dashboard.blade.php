@extends('landlord.layout.main')
@push('custom-css')
<style>
    .count-title .icon {
        font-size: 36px;
        margin-right: 20px
    }
    .count-title .count-number {
        font-size: 22px;
        font-weight: 400
    }
    .count-title .count-number span {
        color: #999;
        font-size: 16px;
    }
</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="row mt-5">
        <div class="col-sm-3">
            <div class="wrapper count-title">
                <div class="icon"><i class="dripicons-graph-bar" style="color: #733686"></i></div>
                <div>
                    <div class="count-number"><span>{{$general_setting->currency}}</span> {{number_format($subscription_value, 2)}}</div>
                    <div class="name"><strong style="color: #733686">{{ __('file.Subscription value') }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="wrapper count-title">
                <div class="icon"><i class="dripicons-card" style="color: #5fc64a"></i></div>
                <div>
                    <div class="count-number"><span>{{$general_setting->currency}}</span> {{number_format($received_amount, 2)}}</div>
                    <div class="name"><strong style="color: #5fc64a">{{ __('file.Received Amount') }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="wrapper count-title">
                <div class="icon"><i class="dripicons-user" style="color: #00c689"></i></div>
                <div>
                    <div class="count-number">{{count($tenants)}}({{count($active_tenants)}} <span>{{ __('file.Active') }}</span>)</div>
                    <div class="name"><strong style="color: #00c689">{{ __('file.Total Clients') }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="wrapper count-title">
                <div class="icon"><i class="dripicons-archive" style="color: #ff8952"></i></div>
                <div>
                    <div class="count-number">{{$package_count}}</div>
                    <div class="name"><strong style="color: #ff8952">{{ __('file.Packages') }}</strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>{{trans('file.Send message to tenants')}}</h4>
                </div>
                <div class="card-body">
                    <label>Select client</label>
                    <!-- <select name="tenants" multiple>
                        @foreach($tenants as $tenant)
                        <option value="{{$tenant->id}}">{{$tenant->id}}</option>
                        @endforeach
                    </select> -->
                    <p>This feature is coming soon...</p>
                </div>
            </div>      
        </div>
    </div>
</div>
@endsection