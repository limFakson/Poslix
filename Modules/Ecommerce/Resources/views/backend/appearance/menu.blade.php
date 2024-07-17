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
            .custom-toggle {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 24px;
            }

            .custom-toggle input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 24px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 13px;
                width: 13px;
                left: 4px;
                bottom: 4px;
                background-color: #000;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: #AF84D5;
            }

            input:checked+.slider:before {
                transform: translateX(24px);
            }

            .container-custom {
                border: 5px solid #f8f9fa;
                border-radius: 15px;
                max-width: 258px;
                margin: auto;
                padding: 0;
            }

            .top-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px;
                background-color: #ffffff;
                border-top-left-radius: 15px;
                border-top-right-radius: 15px;
            }

            .main-content {
                height: 400px;
                /* Adjust height as needed */
                background-color: #d3d3d3;
                display: flex;
                flex-direction: column;
                align-items: center;
                /* justify-content: center; */
                padding-right: 2%;
                padding-left: 2%;
            }

            .content-area {
                background-color: #ffffff;
                width: 100%;
                height: 40%;
            }


            .button-area {
                margin-top: 10px;
            }

            .bottom-bar {
                background-color: #F0F0F0;
                color: white;
                padding: 10px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-radius: 15px;
            }

            .bottom-bar .cart-icon,
            .bottom-bar .menu-icon {
                font-size: 24px;
            }

            #color_picker {
                border: 10px solid #d3d3d3;
                border-radius: 15px;
            }
        </style>
    @endpush
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="col-md-4">
                                <h4>{{ trans('file.Appearance') }}</h4>
                            </div>
                            <div class="col-md-6" id="qr_tabs">
                                <a href="{{ url('/appearance/design') }}" class="btn btn-light" id="design">Design</a>
                                <a href="{{ url('/appearance/menu') }}"
                                    class="{{ Route::currentRouteName() == 'appearance.menu' ? 'btn btn-success' : 'btn btn-light' }}"
                                    id="menu">Menu</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <form id="menuForm" action="{{ url('/appearance/menu') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-6">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <h3><b>Menu Display</b></h3>
                                                <sl-switch>Switch</sl-switch>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="d-flex align-item-center">
                                                    <label class="custom-toggle">
                                                        <input type="checkbox" name="enable_horizontal"
                                                            {{ isset($appearance) && $appearance->menu_option == 'horizontal' ? 'checked' : '' }}
                                                            id="enable_horizontal">
                                                        <span class="slider"></span>
                                                    </label>
                                                    <span id="enable_horizontal-label" class="ml-2">Horizontal Menu</span>
                                                </div>
                                                <p>Activate Horizontal menu display</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="container-custom">
                                            <div class="top-bar">
                                                <img id="logo_preview"
                                                    src="{{ isset($appearance) ? url('logo/' . $appearance->logo) : '' }}"
                                                    class="preview" width="60">
                                                <div class="cart-icon">
                                                    <i class="fa fa-cog"></i>
                                                </div>
                                            </div>
                                            <div class="main-content">
                                                <div
                                                    class="w-100 {{ isset($appearance) && $appearance->menu_option == 'horizontal' ? '' : 'd-flex' }}">
                                                    <div class="content-area mt-1 mr-1 p-1 " style="height: 164px">
                                                        <div class="content-area-body"
                                                            style="height: 100px; background-color:#ccc;"></div>
                                                        <div class="content-area-bottom pl-3">
                                                            <div class="content-area-line my-1"
                                                                style="width: 90%; height:13px; background-color: #ccc;">
                                                            </div>
                                                            <div class="content-area-line my-1"
                                                                style="width: 50px; height:13px; background-color: {{ isset($appearance) ? "$appearance->color" : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="content-area mt-1 p-1" style="height: 164px">
                                                        <div class="content-area-body"
                                                            style="height: 100px; background-color:#ccc;"></div>
                                                        <div class="content-area-bottom pl-3">
                                                            <div class="content-area-line my-1"
                                                                style="width: 90%; height:13px; background-color: #ccc;">
                                                            </div>
                                                            <div class="content-area-line my-1"
                                                                style="width: 50px; height:13px; background-color: {{ isset($appearance) ? "$appearance->color" : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="design_color bottom-bar d-flex justify-content-between"
                                                style="background-color: {{ isset($appearance) ? "$appearance->color" : '' }}">
                                                <div class="cart-icon">
                                                    <i class="fa fa-shopping-cart"></i>
                                                </div>
                                                <div class="menu-icon">
                                                    <i class="fa fa-bars"></i>
                                                </div>
                                                <div class="price">
                                                    24.000 OMR
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <script type="module"
        src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.15.1/cdn/components/switch/switch.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.15.1/cdn/components/range/range.js">
    </script>
    <script type="module"
        src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.15.1/cdn/components/spinner/spinner.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#enable_horizontal')[0].checked == false) {
                $('.main-content>div').addClass('d-flex');
                $('.content-area').removeClass('d-flex');
                $('.content-area').css('height', '164px');
                $('.content-area-body').removeClass('w-25');
                $('.content-area-body').css('margin', '0');
                $('.content-area-body').css('height', '100px');
                $('.content-area-bottom').removeClass('w-75');
            } else {
                $('.main-content>div').removeClass('d-flex');
                $('.content-area').addClass('d-flex');
                $('.content-area').css('height', 'auto');
                $('.content-area-body').addClass('w-25');
                $('.content-area-body').css('margin', '10px 5px');
                $('.content-area-body').css('height', 'auto');
                $('.content-area-bottom').addClass('w-75');
            }
            $('#enable_horizontal').click(() => {
                console.log($('#enable_horizontal')[0].checked)
                if ($('#enable_horizontal')[0].checked == false) {
                    $('.main-content>div').addClass('d-flex');
                    $('.content-area').removeClass('d-flex');
                    $('.content-area').css('height', '164px');
                    $('.content-area-body').removeClass('w-25');
                    $('.content-area-body').css('margin', '0');
                    $('.content-area-body').css('height', '100px');
                    $('.content-area-bottom').removeClass('w-75');
                } else {
                    $('.main-content>div').removeClass('d-flex');
                    $('.content-area').addClass('d-flex');
                    $('.content-area').css('height', 'auto');
                    $('.content-area-body').addClass('w-25');
                    $('.content-area-body').css('margin', '10px 5px');
                    $('.content-area-body').css('height', 'auto');
                    $('.content-area-bottom').addClass('w-75');
                }
                $('#menuForm').submit();
            })
        });
    </script>
@endpush



</div>
