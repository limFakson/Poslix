@extends('backend.layout.top-head')
@section('content')
    @php
        $custom_methods = \DB::table('custom_methods')->where('active', 1)->get();
    @endphp
    <style>
        .transaction-list {
            height: 50vh;
        }

        .category-img img {
            height: 100px;
        }

        .category-img {
            border: 1px solid #35a5a379;
            border-radius: 12px;
        }

        #product-table td p {
            margin: 0;
        }

        table.dataTable img {
            max-height: 100px;
            max-width: 100px;
            height: 100px;
            width: 100px;
        }

        .modal-header {
            display: flex;
            background-color: #339189;
        }

        .cat-class {
            background-color: #339189;
            color: white
        }

        .payment-amount h2 {
            background-color: #339189;
            color: white;
        }

        .modal-header h5 {
            margin-top: auto;
            margin-bottom: auto;
            color: #FFFFFF;
        }

        table.dataTable img {
            border-radius: 1px;
        }
    </style>
    @if ($errors->has('phone_number'))
        <div class="alert alert-danger alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ $errors->first('phone_number') }}
        </div>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    <!-- Side Navbar -->
    <nav class="side-navbar shrink">
        <span class="brand-big">
            @if ($general_setting->site_logo)
                <a href="{{ url('/') }}"><img src="{{ url('logo', $general_setting->site_logo) }}" width="115"></a>
            @else
                <a href="{{ url('/') }}">
                    <h1 class="d-inline">{{ $general_setting->site_title }}</h1>
                </a>
            @endif
        </span>

        @include('backend.layout.sidebar')
    </nav>
    <section class="forms pos-section">
        @php
            $data['lims_pos_setting_data'] = $lims_pos_setting_data;
            $data['lims_warehouse_list'] = $lims_warehouse_list;
            $data['lims_table_list'] = $lims_table_list;
            $data['lims_biller_list'] = $lims_biller_list;
            $data['lims_customer_list'] = $lims_customer_list;
            $data['custom_fields'] = $custom_fields;
            $data['general_setting'] = $general_setting;
            $data['options'] = $options;
            $data['lims_reward_point_setting_data'] = $lims_reward_point_setting_data;
            $data['role_has_permissions_list'] = $role_has_permissions_list;
            $data['permission_list'] = $permission_list;
            $data['lims_category_list'] = $lims_category_list;
            $data['lims_brand_list'] = $lims_brand_list;
            $data['product_number'] = $product_number;
            $data['alert_product'] = $alert_product;
            $data['lims_tax_list'] = $lims_tax_list;
            $data['lims_coupon_list'] = $lims_coupon_list;            
            $data['options'] = $options;
            $data['recent_sale'] = $recent_sale;
            $data['recent_draft'] = $recent_draft;
            $data['currency_list'] = $currency_list;
            $data['numberOfInvoice'] = $numberOfInvoice;
            $data['all_permission'] = $all_permission;
            $data['lims_customer_group_all'] = $lims_customer_group_all;            
            $data['lims_product_list'] = $lims_product_list;
            
            
            
        @endphp
        @livewire('sale.pos-livewire', compact('data'))
    </section>

    
@endsection
