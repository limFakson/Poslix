<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Document Title -->
    <title>{{ $ecommerce_setting->site_title ?? '' }}</title>
    <meta name="description" content="@yield('description')" />
    <meta name="author" content="LionCoders" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta property="og:url" content="{{Request::url()}}" />
    <meta property="og:description" content="@yield('description')" />
    @if(request()->is('product/*'))
    <meta property="og:image" content="@yield('image')" />
    <meta property="product:image_link" content="@yield('image')">
    <meta property="product:brand" content="@yield('brand')">
    <meta property="product:availability" content="in stock">
    <meta property="product:condition" content="new">
    <meta property="product:price:amount" content="@yield('price')">
    <meta property="product:price:currency" content="{{$currency->code}}">
    <meta property="product:retailer_item_id" content="@yield('id')">
    <meta property="product:item_group_id" content="@yield('category_id')">
    @else
    <meta property="og:image" content="https://www.lion-coders.com/public/frontend/images/slider/slide-2.png" />
    @endif

    @if(!config('database.connections.saleprosaas_landlord'))
    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('public/frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    @else
    <!-- Links -->
    <link rel="icon" type="image/ico" href="{{ url('frontend/images') }}/{{$ecommerce_setting->favicon ?? ''}}" />
    <!-- Plugins CSS -->
    <link href="{{ asset('../../public/frontend/css/plugins.css') }}" rel="stylesheet" />
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css') }}">
    </noscript>
    @endif

    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet" />

      <!-- Font Awesome CSS-->
      <link rel="preload" href="<?php echo asset('../../public/vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
      <noscript>
        <link href="<?php echo asset('../../public/vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet">
      </noscript>
    <style>
        :root {
            --theme-color: {{ $color }};
        }
        
        .home-container-fluid { 
            width: 100%;
            padding-right: 105px;
            padding-left: 105px;
            margin-right: auto;
            margin-left: auto;
        }
        
        @media only screen and (max-width:768px) {
            .home-container-fluid { 
                width: 100%;
                padding-right: 15px;
                padding-left: 15px;
                margin-right: auto;
                margin-left: auto;
            }
        }
        
        .main-menu-btn button{
            width: 100%;
            padding: 16px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 30px;
            display: flex;
            justify-content: space-between;
            background-color: rgb(37, 37, 38);
            cursor: pointer;
            min-height: 60px;
            border: none;
            outline: none;
        }
        
        .main-menu-btn button:hover {
            box-shadow: 0 8px 15px rgba(0, 0, 0, .25);
            transform: translateY(-2px);
        }
        
        .main-menu-box {
            height: 100%;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        
        @media only screen and (max-width:768px) {
            .main-menu-box {
                height: 100%;
                width: 100%;
                max-width: 380px;
                margin: 0 auto;
            }
        }
        
        .bio-btn-icon i {
            color: white;
            width: 24px;
            height: 24px;
        }
        
        .bio-socials span {
            color: rgb(37, 37, 38);
        }
        
        .bio-socials span svg {
            font-size: 35px;
            width:35px;
            height: 35px;
        }
        
        .bio-btn-text{
            margin-left: -16px;
        }
        
        .main-menu-btn a:hover {
            text-decoration: none;
        }
        
    </style>

    @if(isset($ecommerce_setting->custom_css))
    <style>
    {{$ecommerce_setting->custom_css}}
    </style>
    @endif

    @if(env('USER_VERIFIED') == false)
    <style>

    </style>
    @endif
</head>

<body style="background: #e2e4e7">
    <div class="main-menu-box">
        <div class="logo text-center mb-4" style="margin-top: 150px;">
            <a href="{{url('/')}}">
                @if(!config('database.connections.saleprosaas_landlord'))
                    @if(isset($ecommerce_setting->logo))
                    <img src="{{ url('frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                    @else
                    <img src="{{ asset('public/logo') }}/{{$general_setting->site_logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                    @endif
                @else
                    @if(isset($logo))
                    <img src="{{ asset('../../public/logo') }}/{{$logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                    @else
                    <img src="{{ asset('../../public/frontend/images/') }}/{{$ecommerce_setting->logo}}" alt="{{$ecommerce_setting->site_title ?? ''}}">
                    @endif
                @endif
            </a>
        </div>
        <div class="main-menu-btns">
            <p class="text-center" style="font-size: 18px;">{{$general_setting->site_title}}</p>
            <div class="main-menu-btn mb-3">
                <a href="{{url('/')}}">
                    <button>
                            <span class="bio-btn-icon"><i class="material-symbols-outlined" title="menu">menu</i></span>
                            <span class="bio-btn-text" style="color: white;">View Menu</span>
                            <span class="bio-btn-action"></span>
                    </button>
                </a>
            </div>
            @foreach($action_buttons as $id => $action_button)
                @if($action_button->status == 1)
                   <div class="main-menu-btn mb-3">
                        <button style = "background-color: {{$action_button->color}}">
                            <span class="bio-btn-icon"><i class="{{$action_button->icon}}"></i></span>
                            <span class="bio-btn-text" style="color: white;">{{$action_button->name}}</span>
                            <span class="bio-btn-action"></span>
                        </button>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="bio-socials d-flex justify-content-center">
            <div class="mt-3">
                <!--<a href="https://demo.gettlb.com/social">-->
                <!--    <span><i class="fa fa-whatsapp"></i></span>-->
                <!--</a>-->
                <!--<a href="https://demo.gettlb.com/social" class="ml-3">-->
                <!--    <span><i class="fa fa-instagram"></i></span>-->
                <!--</a>-->
                @foreach($socials as $id => $social)
                    <a href="{{$social->link}}">
                        <span>{!!$social->icon!!}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</body>

@if(!config('database.connections.saleprosaas_landlord'))
<!--Plugin js -->
<script>
    {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
</script>
<script src="{{ asset('public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js') }}"></script>
<!-- Main js -->
<script>
    {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/main.js") !!}
</script>
@else
<!--Plugin js -->
<script>
    {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/plugin.js") !!}
</script>
<script src="{{ asset('../../public/vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js') }}"></script>
<!-- Main js -->
<script>
    {!! file_get_contents(Module::find('Ecommerce')->getPath(). "/assets/js/main.js") !!}
</script>
@endif
@if(isset($ecommerce_setting->custom_js))
<script type="text/javascript">
{{$ecommerce_setting->custom_js}}
</script>
@endif

<script>
    <script src="{{ asset('public/frontend/js/swiper.min.js') }}"></script>
</script>
