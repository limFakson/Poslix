@extends('ecommerce::frontend.layout.main')

@php

    $all_categories = $categories_list->where('featured', 1);

    $parents = $all_categories->whereNull('parent_id')->pluck('id')->toArray();

@endphp

@section('title') {{ $ecommerce_setting->site_title ?? '' }} @endsection

@section('description') {{ '' }} @endsection

@push('css')
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
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
            
            #home_categories_list_responsive li {
                width: 110px;
            }
        }
    </style>
@endpush

@section('header')
<div class="mb-2" id="home_categories_list">
    <div class="home-container-fluid" id="home_categories_list_full">
        <div class="category-list">
            <ul>
                <li class="has-dropdown">
                    <!-- <a class="category-button" href="#"><i class="material-symbols-outlined">menu</i> Categories</a> -->
                    <ul class="row justify-content-center">
                        <li class="category-tab" id="category_0"><a
                            class="{{$categoryId == 0 ? "button style3 text-center" : ""}}"
                            href="javascript:void(0)">
                            All Categories</a></li>
                        @php $i = 0 @endphp
                        @foreach($all_categories as $category1)
                            @if(in_array($category1->id, $parents))
                                @php
                                    $categories1 = $all_categories->where('parent_id', $category1->id)->where('is_active', 1);
                                @endphp
                                @if(count($categories1) > 0)
                                    <li class="has-dropdown"><a href="javascript:void(0)">@if(isset($category1->icon))<img
                                                src="{{ url('images/category/icons/') }}/{{ $category1->icon }}"
                                                alt="{{ $category1->name }}">@endif <span>{{ $category1->name }}</span></a>
                                        <ul class="dropdown">
                                            @foreach($categories1 as $cat1)
                                                <li class="category-tab" id="category_{{$cat1->id}}"><a
                                                    class="{{$categoryId == $cat1->id ? "button style3 text-center" : ""}}"
                                                    href="javascript:void(0)">@if(isset($cat1->icon))<img
                                                        src="{{ url('images/category/icons/') }}/{{ $cat1->icon }}"
                                                        alt="{{ $cat1->name }}">@endif <span>{{ $cat1->name }}</span></a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                <li class="category-tab" id="category_{{$category1->id}}"><a
                                        class="{{$categoryId == $category1->id ? "button style3 text-center" : ""}}"
                                        href="javascript:void(0)">@if(isset($category1->icon))@endif <span>{{ $category1->name }}</span></a>
                                </li>
                                @endif
                            @endif
                            @php $i++ @endphp
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="home-container-fluid" id="home_categories_list_responsive">

                <div class="carousel--content" id="carouselContent">
                    <li class="category-tab box" id="category_0"><a
                        class="{{$categoryId == 0 ? "button style3 text-center" : ""}}"
                        href="javascript:void(0)">
                        All Categories</a></li>
                    @php $i = 0 @endphp
                    @foreach($all_categories as $category1)
                        @if(in_array($category1->id, $parents))
                            @php
                                $categories1 = $all_categories->where('parent_id', $category1->id)->where('is_active', 1);
                            @endphp
                            @if(count($categories1) > 0)
                                <li class="has-dropdown"><a href="javascript:void(0)">@if(isset($category1->icon))
                                    <img src="{{ url('images/category/icons/') }}/{{ $category1->icon }}"
                                        alt="{{ $category1->name }}">
                                        @endif <span>{{ $category1->name }}</span></a>
                                    <ul class="dropdown">
                                        @foreach($categories1 as $cat1)
                                            <li class="category-tab box" id="category_{{$cat1->id}}"><a
                                                class="{{$categoryId == $cat1->id ? "button style3 text-center" : "button text-center"}}"
                                                href="javascript:void(0)">@if(isset($cat1->icon))<img
                                                    src="{{ url('images/category/icons/') }}/{{ $cat1->icon }}"
                                                    alt="{{ $cat1->name }}">@endif <span>{{ $cat1->name }}</span></a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                            <li class="category-tab box" id="category_{{$category1->id}}"><a
                                    class="{{$categoryId == $category1->id ? "button style3 text-center" : "button text-center"}}"
                                    href="javascript:void(0)">@if(isset($category1->icon))@endif <span>{{ $category1->name }}</span></a>
                            </li>
                            @endif
                        @endif
                        @php $i++ @endphp
                    @endforeach
                </div>


            <div class="carousel--controls">
                <button class="btn left" id="carouselLeftBtn"><i class="material-symbols-outlined">arrow_left_alt</i></button>
                <button class="btn right show" id="carouselRightBtn" click="moveCarouselLeft" ><i class="material-symbols-outlined">arrow_right_alt</i></button>
            </div>
        </div>
</div>

<!-- <div class="mb-2" id="home_categories_list">
    <div class="container-fluid">
        <div class="category-list">
            <ul>
                <li class="has-dropdown">
                    <ul class="row justify-content-center carousel slide" id="categories_carousel" data-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">

                            </div>
                            <div class="carousel-item">

                            </div>
                            <div class="carousel-item">

                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#categories_carousel" data-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </a>
                        <a class="carousel-control-next" href="#categories_carousel" data-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </a>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div> -->

@endsection

@section('content')

@if(isset($sliders))
    <!--Home Banner starts -->
    <section class="v3 pt-3" style="background-color: #ebebeb;">
        <div class="home-container-fluid">
            <div class="row">
                                @foreach($products as $key => $product)
                    @if ($categoryId == $product->category_id || $categoryId == 0)
                        @if($menu_option == 'vertical')
                            <div class="col-md-6 col-lg-3 text-center mt-3">
                                <div class="home-product-card">
                                    <a href="javascript:void(0)">
                                        <img loading="lazy" src="{{ url('images/product/' . $product->image) }}"
                                            alt="{{ $product->name }}" style="height: 250px;" width="100%">
                                        <h2 class="product-name">
                                            {{ $product->name }}
                                        </h2>
                                        <p class="home-product-description">
                                            @if(count($product->extra_category_names) != 0)
                                                <b>Price is on selection...</b>
                                            @else
                                                <b>{{ number_format((float)$product->price, $general_setting->decimal, '.', '')}} OMR</b>
                                            @endif
                                        </p>
                                        <p class="home-product-description">
                                            {{ $product->product_details }}
                                        </p>
                                            @if(count($product->extra_category_names) != 0)
                                                <button class="product-order-btn" style="background-color: {{$color}}" data-toggle="modal"
                                                    data-target="#productModal_{{$product->id}}">
                                                    Order
                                                </button>
                                            @else
                                                <button data-id="{{ $product->id }}" style="background-color: {{$color}}" data-price = "{{$product->price}}" type="submit" class="product-order-btn style1 direct-add-to-cart py-2">Order</button>
                                            @endif
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="col-md-6 col-lg-6 text-center mt-3">
                                <div class="home-product-card">
                                    <a href="javascript:void(0)" class="row">
                                        <div class="col-6">
                                            <img loading="lazy" src="{{ url('images/product/' . $product->image) }}"
                                                alt="{{ $product->name }}" style="height: 250px;" width="100%">
                                        </div>
                                        <div class="col-6 d-flex flex-column justify-content-between">
                                            <div class="mt-4">
                                                <h1 class="product-name" style="font-size: 24px;">
                                                    {{ $product->name }}
                                                </h1>
                                                <p class="home-product-description">
                                                    @if(count($product->extra_category_names) != 0)
                                                        <b>Price is on selection...</b>
                                                    @else
                                                        <b>{{ number_format((float)$product->price, $general_setting->decimal, '.', '')}} OMR</b>
                                                    @endif
                                                </p>
                                                <p class="home-product-description">
                                                    {{ $product->product_details }}
                                                </p>
                                            </div>
                                            <div>
                                                @if(count($product->extra_category_names) != 0)
                                                    <button class="product-order-btn" style="background-color: {{$color}}" data-toggle="modal"
                                                        data-target="#productModal_{{$product->id}}">
                                                        Order
                                                    </button>
                                                @else
                                                    <button data-id="{{ $product->id }}" style="background-color: {{$color}}" data-price = "{{$product->price}}" type="submit" class="product-order-btn style1 direct-add-to-cart py-2">Order</button>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                        <div class="modal fade" id="productModal_{{$product->id}}">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <img loading="lazy" src="{{ url('images/product/' . $product->image) }}"
                                                alt="{{ $product->name }}" width="100%" class="product-modal-image">
                                        </div>
                                        <div class="col-md-6 col-12 text-left d-flex flex-column justify-content-between">
                                            <div class="mt-4 pl-3">
                                                <h3>{{ $product->name }}</h3>
                                                <p>{{ $product->product_details }}</p>
                                                @if(count($product->extra_category_names) != 0)
                                                    <div class="mt-4">
                                                        @foreach($product->extra_category_names as $index => $extra_category_name)
                                                            <div class="mb-2">
                                                                <div class="product-modal-extra-category">{{$extra_category_name[0]}}</div>
                                                                @foreach($extras as $key => $extra)
                                                                    @if($extra_category_name[0] === $extra->extra_category_name)
                                                                        <div class="mt-2" style="font-size: 14px;">
                                                                            <div class="form-check">
                                                                                <label class="form-check-label d-flex justify-content-between">
                                                                                    <div class="ml-3">
                                                                                        <input type="{{$extra_category_name[1] == 1 ? "checkbox": "radio"}}" data-active = "0" data-price = "{{ $extra->price }}" class="form-check-input product-extra-category-option" 
                                                                                        value="{{ $extra->id }}"
                                                                                        name="extras[]">{{ $extra->name }}
                                                                                    </div>
                                                                                    <div class="mr-3">
                                                                                        {{ $extra->price }}
                                                                                    </div>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="product-details d-flex mb-4">
                                                @if($product->in_stock == 1)
                                                    @if(is_null($product->is_variant))
                                                    <form class="d-flex justify-content-between col-md-10" method="post" id="add_to_cart_{{ $product->id }}">
                                                        @csrf
                                                        <div class="d-flex align-items-center">
                                                            <div class="input-qty">
                                                                <button type="button" class="quantity-left-minus">
                                                                    <i class="material-symbols-outlined">remove</i>
                                                                </button>
                                                                <input type="number" name="qty" class="input-number" value="1" min="1" max="{{ $product->qty }}">
                                                                <button type="button" class="quantity-right-plus">
                                                                    <i class="material-symbols-outlined">add</i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <button data-id="{{ $product->id }}" data-decimal = "{{$general_setting->decimal}}" data-price = "{{$product->price}}" type="submit" class="button style1 add-to-cart py-2" data-dismiss="modal">Order(Total: &nbsp;<span class="order-total-price">{{ number_format((float)$product->price, $general_setting->decimal, '.', '')}}</span><span>&nbsp;{{$currency->code}}</span>)</button>
                                                        </div>
                                                    </form>
                                                    @else
                                                    <div class="text-center">
                                                        <a href="{{url('/')}}/product/{{$product->slug}}/{{$product->id}}" class="button style1">{{trans('file.Add to cart')}}</a>
                                                    </div>
                                                    @endif
                                                @else
                                                <span class="ml-2" style="color:red;">{{trans('file.Out of stock')}}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
    <!--Home Banner Area ends-->
@endif
@endsection

@section('script')
<script src="{{ asset('public/frontend/js/swiper.min.js') }}"></script>
<script type="text/javascript">
    "use strict";

    //category carousel
    if (('.category-slider-wrapper').length > 0) {
        $('.category-slider-wrapper').each(function () {
            var swiper = new Swiper('.category-slider-wrapper', {
                slidesPerView: 6,
                spaceBetween: 30,
                lazy: true,
                //centeredSlides: true,
                loop: $(this).data('loop'),
                navigation: {
                    nextEl: '.category-button-next',
                    prevEl: '.category-button-prev',
                },
                autoplay: {
                    delay: 4000,
                },
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 675
                    675: {
                        slidesPerView: 2,
                        spaceBetween: 30
                    },

                    // when window width is <= 991
                    991: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    // when window width is <= 1024px
                    1024: {
                        slidesPerView: 6,
                        spaceBetween: 15
                    }
                }
            });
        })

        $(document).ready(function () {
            $('.category-img').each(function () {
                var img = $(this).data('src');
                $(this).attr('src', img);
            })
        })
    }

    //product carousel
    if (('.product-slider-wrapper').length > 0) {
        $('.product-slider-wrapper').each(function () {
            var swiper = new Swiper('.product-slider-wrapper', {
                slidesPerView: 5,
                spaceBetween: 0,
                lazy: true,
                observer: true,
                observeParents: true,
                loop: false,
                navigation: {
                    nextEl: '.product-button-next',
                    prevEl: '.product-button-prev',
                },
                autoplay: {
                    delay: 4000,
                },
                // Responsive breakpoints
                breakpoints: {
                    // when window width is <= 675
                    675: {
                        slidesPerView: 2,
                        spaceBetween: 30
                    },

                    // when window width is <= 991
                    991: {
                        slidesPerView: 4,
                        spaceBetween: 30
                    },
                    // when window width is <= 1024px
                    1024: {
                        slidesPerView: 6,
                        spaceBetween: 15
                    }
                }
            });
        })
    }

    $(document).on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var parent = '#add_to_cart_' + id;
        var total_price = parseFloat($(this).find('.order-total-price').html());

        var qty = $(parent + " input[name=qty]").val();

        var route = "{{ route('addToCart') }}";

        var btn = $(this);

        var btn_text = $(this).html();

        // $(this).html('<span class="spinner-border spinner-border-sm" role="status"><span class="sr-only">...</span></span>');

        $.ajax({
            url: route,
            type: "POST",
            data: {
                product_id: id,
                qty: qty,
                total_price: total_price
            },
            success: function (response) {
                if (response) {
                    $('.alert').addClass('alert-custom show');
                    $('.alert-custom .message').html(response.success);
                    $('.cart__menu .cart_qty').html(response.total_qty);
                    $('.cart__menu .total').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    $('.total__price').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                     $('.shoping__total .total__price').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    
                    var cart_product = response.addCart;
                    
                    let variant, true_variant, id;
                    let currencySymbol = "{{ $currency->symbol ?? '' }}";
                    let currencyCode = "{{ $currency->code ?? '' }}";

                    if (cart_product.variant !== 0) {
                        variant = cart_product.variant.join(' | ');
                        true_variant = cart_product.variant.join(',');
                        id = cart_product.id + '-' + cart_product.variant.join('-');
                    } else {
                        true_variant = 0;
                        variant = 0;
                        id = cart_product.id;
                    }
                    
                    if(response.isExist) {
                        $('.single-cart-item-' + id).find('.amount').html(cart_product.total_price.toFixed(2));
                        $('.single-cart-item-' + id).find('.input-number').val(cart_product.qty);
                    } else {
                        let tr = document.createElement('tr');
                        tr.classList.add('single-cart-item-' + id);
                        
                        // Build inner HTML for the <tr> element
                        tr.innerHTML = `
                            <td class="cart-product-name">
                                <div class="d-flex align-self-center">
                                    <div class="remove">
                                        <a class="remove-from-cart cart" title="Remove from Cart" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <span class="material-symbols-outlined">delete</span>
                                        </a>
                                    </div>
                                    <div>
                                        ${cart_product.image !== null ? `<img src="images/product/small/${cart_product.image}" alt="${cart_product.name}">` : ''}
                                    </div>
                                    <span class="align-self-center">${cart_product.name} ${variant !== 0 ? `(${variant})` : ''}</span>
                                </div>
                            </td>
                            <td class="cart-product-quantity">
                                <div class="product-subtotal">
                                    <span>OMR</span><span class="amount">${cart_product.total_price}</span>
                                </div>
                                <div class="input-qty" style="width:100px;margin:0 auto">
                                    <span class="input-group-btn">
                                        <button type="button" class="cart-quantity-left-minus" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <i class="material-symbols-outlined">remove</i>
                                        </button>
                                    </span>
                                    <input type="text" class="input-number" value="${cart_product.qty}">
                                    <span class="input-group-btn">
                                        <button type="button" class="cart-quantity-right-plus" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                        `;

                        $("#cart_main_body").append(tr);
                    }
                    
                    $(btn).html(btn_text);
                    setTimeout(function () {
                        $('.alert').removeClass('show');
                    }, 4000);
                }
            },
        });
    });
    
      $(document).on('click', '.direct-add-to-cart', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var unit_price = $(this).data('price');
        var qty = 1;
        var total_price = unit_price * 1;

        var route = "{{ route('addToCart') }}";

        var btn = $(this);

        var btn_text = $(this).html();

        // $(this).html('<span class="spinner-border spinner-border-sm" role="status"><span class="sr-only">...</span></span>');

        $.ajax({
            url: route,
            type: "POST",
            data: {
                product_id: id,
                qty: qty,
                total_price: total_price
            },
            success: function (response) {
                if (response) {
                    $('.alert').addClass('alert-custom show');
                    $('.alert-custom .message').html(response.success);
                    $('.cart__menu .cart_qty').html(response.total_qty);
                    $('.cart__menu .total').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    $('.total__price').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    $('.shoping__total .total__price').html('{{$currency->symbol ?? $currency->code}}' + response.subTotal.toFixed(2));
                    
                    var cart_product = response.addCart;
                    
                    let variant, true_variant, id;
                    let currencySymbol = "{{ $currency->symbol ?? '' }}";
                    let currencyCode = "{{ $currency->code ?? '' }}";

                    if (cart_product.variant !== 0) {
                        variant = cart_product.variant.join(' | ');
                        true_variant = cart_product.variant.join(',');
                        id = cart_product.id + '-' + cart_product.variant.join('-');
                    } else {
                        true_variant = 0;
                        variant = 0;
                        id = cart_product.id;
                    }
                    
                    if(response.isExist) {
                        $('.single-cart-item-' + id).find('.amount').html(cart_product.total_price.toFixed(2));
                        $('.single-cart-item-' + id).find('.input-number').val(cart_product.qty);
                    } else {
                        let tr = document.createElement('tr');
                        tr.classList.add('single-cart-item-' + id);
                        
                        // Build inner HTML for the <tr> element
                        tr.innerHTML = `
                            <td class="cart-product-name">
                                <div class="d-flex align-self-center">
                                    <div class="remove">
                                        <a class="remove-from-cart cart" title="Remove from Cart" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <span class="material-symbols-outlined">delete</span>
                                        </a>
                                    </div>
                                    <div>
                                        ${cart_product.image !== null ? `<img src="images/product/small/${cart_product.image}" alt="${cart_product.name}">` : ''}
                                    </div>
                                    <span class="align-self-center">${cart_product.name} ${variant !== 0 ? `(${variant})` : ''}</span>
                                </div>
                            </td>
                            <td class="cart-product-quantity">
                                <div class="product-subtotal">
                                    <span>OMR</span><span class="amount">${cart_product.total_price}</span>
                                </div>
                                <div class="input-qty" style="width:100px;margin:0 auto">
                                    <span class="input-group-btn">
                                        <button type="button" class="cart-quantity-left-minus" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <i class="material-symbols-outlined">remove</i>
                                        </button>
                                    </span>
                                    <input type="text" class="input-number" value="${cart_product.qty}">
                                    <span class="input-group-btn">
                                        <button type="button" class="cart-quantity-right-plus" data-id="${cart_product.id}" data-variant="${true_variant}">
                                            <i class="material-symbols-outlined">add</i>
                                        </button>
                                    </span>
                                </div>
                            </td>
                        `;

                        $("#cart_main_body").append(tr);
                    }
                    
                    $(btn).html(btn_text);
                    setTimeout(function () {
                        $('.alert').removeClass('show');
                    }, 4000);
                }
            },
        });
    });
    
</script>

<script type="text/javascript">
    var carouselContent = document.querySelector('#carouselContent');
    var carouselLeftBtn = document.querySelector('#carouselLeftBtn');
    var carouselRightBtn = document.querySelector('#carouselRightBtn');

    carouselLeftBtn.addEventListener('click', moveCarouselRight);
    carouselRightBtn.addEventListener('click', moveCarouselLeft);
    
    window.addEventListener('resize', manageButtons);

    var carouselLeftValue = 0;
    var totalWidth = carouselContent.offsetWidth;
    var widthToMove = 120;
    var arrowBtnWidth = 50;
    console.log(totalWidth);
    var noOfSlids = parseInt(totalWidth / widthToMove);
    var currentSlide = 1;

    manageButtons();

    function moveCarouselLeft() {
        if(currentSlide == 1) carouselLeftValue -= widthToMove;
        else carouselLeftValue -= widthToMove;

        currentSlide++;
        manageButtons();
        carouselContent.style.left = `${carouselLeftValue}px`;
    }

    function moveCarouselRight() {
        if(currentSlide == 2) carouselLeftValue = 0;
        else carouselLeftValue += widthToMove;

        currentSlide--;
        manageButtons();
        carouselContent.style.left = `${carouselLeftValue}px`;
    }

    function manageButtons() {
        var totalWidth = carouselContent.offsetWidth;
        var widthToMove = 120;
        var noOfSlids = parseInt(totalWidth / widthToMove);
        if(currentSlide >= noOfSlids)
            carouselRightBtn.classList.remove('show');
        else
            carouselRightBtn.classList.add('show');

        if(currentSlide <= 1)
            carouselLeftBtn.classList.remove('show');
        else
            carouselLeftBtn.classList.add('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        //   let pressed_btn = false;
        //   let startx;
        //   let x;

        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;

          carouselContent.addEventListener("mousedown", (e) => {
            isDragging  = true;
            // startPosition = e.clientX;
            // startTranslate = carouselContent.scrollLeft;
              startPos = e.clientX;
              currentTranslate = carouselContent.getBoundingClientRect().left;
            carouselContent.style.cursor = "grabbing";
          });
    
          carouselContent.addEventListener("mouseup", () => {
            isDragging  = false;
            carouselContent.style.cursor = "grab";
          });
          
          carouselContent.addEventListener("mousemove", (e) => {
              console.log('123123');
            if (isDragging) {
               const newPos = e.clientX - carouselContent.offsetLeft;
                const diff = newPos - startPos;
                carouselContent.style.left  = `${currentTranslate + diff}px`;
            }
          });
          
          carouselContent.addEventListener('mouseleave', () => {
              isDragging = false;
        });
    });
    
</script>

@endsection