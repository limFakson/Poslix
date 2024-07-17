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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-
colorpicker/2.5.1/css/bootstrap-colorpicker.min.css"
            rel="stylesheet">
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
                padding-right: 10%;
                padding-left: 10%;
            }

            .main-content .content-area {
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
                                <a href="{{ url('/appearance/design') }}"
                                    class="{{ Route::currentRouteName() == 'appearance.design' ? 'btn btn-success' : 'btn btn-light' }}"
                                    id="design">Design</a>
                                <a href="{{ url('/appearance/menu') }}" class="btn btn-light" id="menu">Menu</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <form id="designForm" action="{{ url('/appearance/design') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-6">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3><b>logo</b></h3>
                                            </div>
                                            <div class="col-md-10">
                                                <label for="logoImage">Choose your main logo</label>
                                                <div class="form-group">
                                                    <input type="file" name="logoImage" accept="image/*"
                                                        onchange="previewFile()" class="form-control-file" id="logoImage">
                                                </div>
                                                <img id="file-preview"
                                                    src="{{ isset($appearance) ? url('logo/' . $appearance->logo) : '' }}"
                                                    class="preview" width="60">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <h3><b>Colors</b></h3>
                                            </div>
                                            <div class="col-md-10">
                                                <label class="custom-toggle">
                                                    <input type="checkbox" name="enable-color" checked id="enable-color">
                                                    <span class="slider"></span>
                                                </label>
                                                <span id="enable-color-label" class="ml-2">Enable Colors</span>
                                                <p>Activate color selection</p>
                                                <sl-color-picker label="Select a color" id="color_picker" inline opacity
                                                    value="{{ isset($appearance) ? "$appearance->color" : '' }}"
                                                    swatches="
                                                    #d0021b; #f5a623; #f8e71c; #8b572a; #7ed321; #417505; #bd10e0; #9013fe;
                                                    #4a90e2; #50e3c2; #b8e986; #000; #444; #888; #ccc; #fff;
                                                "></sl-color-picker>
                                                <input type="hidden" name="color" id="colorPicker" />
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn"
                                                style="background-color: #9b59b6; color: #fff">Save</button>
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
                                                <div class="content-area mt-2">
                                                    <!-- Content goes here -->
                                                </div>
                                                <div class="text-left mr-auto mt-3">
                                                    <button class="design_color btn"
                                                        style="background-color: {{ isset($appearance) ? $appearance->color : '' }}">Button</button>
                                                </div>
                                                {{-- <div class="button-area">
                                                    <button class="btn btn-purple"
                                                        style="background-color: #9b59b6; color: white;">Button</button>
                                                </div> --}}
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
        src="https://cdn.jsdelivr.net/npm/@shoelace-style/shoelace@2.15.1/cdn/components/color-picker/color-picker.js">
    </script>
    <script>
        $("ul#dmsetting").siblings('a').attr('aria-expanded','true');
        $("ul#dmsetting").addClass("show");
        $("ul#dmsetting #appearance-digital-menu").addClass("active");

        let color = $('#colorPicker').val();
        $('#color_picker').click(() => {
            color = $('#color_picker').val();
            $('#colorPicker').val(color);
            $('.design_color').css("background-color", color);
            console.log('color' + color);
        });

        $('#enable-color').click(() => {
            console.log($('#enable-color')[0].checked)
            if ($('#enable-color')[0].checked == false) {
                $('#color_picker').addClass('d-none');
            } else {
                $('#color_picker').removeClass('d-none')
            }
        })
    </script>
    <script>
        function previewFile() {
            const preview = document.getElementById('file-preview');
            const logo_preview = document.getElementById('logo_preview')
            const file = document.getElementById('logoImage').files[0];
            const reader = new FileReader();

            reader.addEventListener('load', function() {
                preview.src = reader.result;
                logo_preview.src = reader.result;
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
@endpush
