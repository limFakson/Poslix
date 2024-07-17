<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <!-- Metas -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{$general_setting->developed_by}}" />
    <meta name="csrf-token" content="CmSeExxpkZmScDB9ArBZKMGKAyzPqnxEriplXWrS">

    <!-- Document Title -->
    <title>{{$general_setting->meta_title ?? 'SalePro SAAS'}}</title>
    <meta name="description" content="{{$general_setting->meta_description ?? 'SalePro SAAS is a multi-tenant SAAS POS & inventory management system allows you to sell subscriptions under your own brand. We handle support & customization, while you make money!'}}" />
    <!-- Links -->
    <link rel="icon" type="image/png" href="{{url('public/landlord/images/logo', $general_setting->site_logo)}}" />

    <!-- Bootstrap CSS -->
    <link href="landlord/css/bootstrap.min.css" rel="stylesheet">

    <!-- Plugins CSS -->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="landlord/css/plugins.css">
    <noscript>
        <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="landlord/css/plugins.css">
    </noscript>

    <!-- common style CSS -->
    <link href="landlord/css/common-style.css" rel="stylesheet">
    <!-- Font Awesome CSS-->
    <link rel="preload" href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="<?php echo asset('vendor/font-awesome/css/font-awesome.min.css') ?>" rel="stylesheet"></noscript>
    <!-- google fonts-->
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;600&display=swap">
    <noscript>
    <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;600&display=swap">
    </noscript>


    <style>
        :root {
            --theme-color: #f16232;
        }
    </style>


</head>

<body>
    @if(session()->has('not_permitted'))
      <div class="alert alert-danger alert-dismissible text-center">{{ session()->get('not_permitted') }}</div>
    @endif
    <!--Header-->
    <!--Header Area starts-->
    <header id="header-middle" class="header-middle">

        <div class="container">
            <div class="row">
                <!-- <div class="col-lg-2 col-7">
                    <div class="mobile-menu-icon d-lg-none"><i class="ti-menu"></i></div>
                    <div class="logo">
                        <a href="http://localhost/cartpro">
                            <img  src="images/logo/logo.png" alt="Brand logo">
                        </a>
                    </div>
                </div> -->
                <!-- <div class="col-lg-7 d-none d-lg-flex d-xl-flex middle-column justify-content-center">
                    <div id="main-menu" class="main-menu">
                        <ul class="pl-0">

                            <li class=""><a href="#features">Features</a></li>

                            <li class=""><a href="#addons">Addons</a></li>

                            <li><a href="saas.html">SAAS</a></li>

                            <li class="has-dropdown"><a href="https://lion-coders.com/support" target="_blank">Get Help</a>
                                <ul class="dropdown">
                                    <li class=""><a href="#faq">FAQ</a></li>
                                    <li><a href="https://salepropos.com/demo/public/read_me/" target="_blank">Documentation</a></li>
                                    <li><a href="salepro-video-tutorials.html" target="_blank">Videos</a></li>
                                    <li><a href="https://lion-coders.com/support" target="_blank">Support</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div> -->
                <!-- <div class="col-lg-3 col-5">
                    <ul class="offset-menu-wrapper p-0">
                        <li>
                            <a href="https://salepropos.com/demo" class="button style2 d-none d-lg-flex d-xl-flex">Demo</a>
                        </li>
                        <li>
                            <a href="https://codecanyon.net/checkout/90616619/create_account" class="button style1">Buy Now</a>
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>
        <!-- <nav id="mobile-nav"></nav> -->
    </header>

    <section class="hero mt-0 pt-0">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 hero-text mb-5">
                    <center><a href="{{url('/')}}"><img src="{{url('public/landlord/images/logo', $general_setting->site_logo)}}" alt="Brand logo" style="max-width: 150px; margin-bottom: 30px"></a></center>
                    @if($general_setting->active_payment_gateway == 'no')
                        <h1 class="heading h2 text-center">Your Subscription has expired! Please contact with superadmin</h1>
                    @else
                        <form action="{{route('renewSubscription')}}" method="POST" class="renew-subscription-form">
                            @csrf
                            <h1 class="heading h2 text-center">Your Subscription has expired!</h1>
                            <div class="row">
                                <div class="col-md-4 offset-md-2 form-group">
                                    <label>{{trans('file.Subscription Type')}} *</label>
                                    <div class="form-check">
                                      <input type="radio" name="subscription_type" value="monthly" class="form-check-input" id="subscription-type-1" checked>
                                      <label class="form-check-label" for="subscription-type-1">
                                        Monthly
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input type="radio" name="subscription_type" value="yearly" class="form-check-input" id="subscription-type-2">
                                      <label class="form-check-label" for="subscription-type-2">
                                        Yearly
                                      </label>
                                    </div>
                                </div>
                                <div class="col-md-4 offset-md-2 form-group">
                                    <label>{{trans('file.Package')}} *</label>
                                    @foreach($packages as $key => $package)
                                    <div class="form-check">
                                      <input type="radio" name="package_id" value="{{$package->id}}" class="form-check-input" id="{{'package-type-'.$package->id}}" @if(!$key){{'checked'}}@endif>
                                      <label class="form-check-label" for="{{'package-type-'.$package->id}}">
                                        {{$package->name}}
                                      </label>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="col-md-8 offset-md-2">
                                    <div class="input-group mt-3">
                                        <input class="form-control mt-0" type="text" name="id" value="{{$subdomain}}" required placeholder="Type your subdomain to renew..." aria-label="subdomain..." aria-describedby="basic-addon2">
                                      <span class="input-group-text" id="basic-addon2">{{'@'.env('CENTRAL_DOMAIN')}}</span>
                                    </div>
                                </div>
                                <div class="col-md-8 offset-md-2 mt-3">
                                    <input id="submit-btn" type="submit" class="button style1 d-block w-100" value="{{trans('file.submit')}}">
                                </div>
                            </div>
                        </form>
                    @endif
                    <br>
                    <h5 class="text-center">You can call at <a href="tel:{{$general_setting->phone}}"><span>{{$general_setting->phone}}</span></a></h5>
                    <h5 class="text-center">Contact for details at - <a class="button style1" href="mailto:{{$general_setting->email}}">{{$general_setting->email}}</a></h5>
                </div>
                <!-- <div class="col-md-8 offset-md-2">
                    <img class="hero-img" src="images/preview.png" alt=""/>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Footer section Starts-->
    <div class="footer-wrapper pt-0">
        <div class="container">
            <div class="footer-bottom">
                <ul class="footer-social p-0 pt-3 pb-3">
                    @foreach($socials as $social)
                    <li>
                        <a href="{{$social->link}}"><i class="{{$social->icon}}"></i></a>
                    </li>
                    @endforeach
                    <!-- <li><a href="https://www.facebook.com/lioncoders"><i class="ti-facebook"></i></a></li> -->
                </ul>
                <p class="copyright">&copy; {{$general_setting->meta_title.' '.date("Y")}}. All rights reserved</p>
            </div>
        </div>
    </div>
    <!-- Footer section Ends-->

    <!--Scroll to top starts-->
    <a href="#" id="scrolltotop"><i class="ti-arrow-up"></i></a>
    <!--Scroll to top ends-->

    <div class="body__overlay"></div>

    <!-- Cookie consent Starts-->


    <!-- Cookie consent Ends-->


    <!--Plugin js -->
    <script src="landlord/js/plugin.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>

    <!-- Main js -->
    <script src="landlord/js/main.js"></script>
    <script type="text/javascript" src="https://js.stripe.com/v3/"></script>

    <script>
        
    </script>
</body>
</html>