@extends('backend.layout.main')

@push('css')
    <style>
        .color-picker {
            width: 100%;
            height: 34px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background-color: #fff;
            cursor: pointer;
        }

        .main-container {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

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
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .custom-range-slider {
            width: 80%;
            margin: 10px 0;
            position: relative;
        }

        .custom-range-slider input[type="range"] {
            -webkit-appearance: none;
            width: 80%;
            height: 8px;
            background: #ddd;
            border-radius: 5px;
            outline: none;
        }

        .custom-range-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #000;
            cursor: pointer;
            border-radius: 50%;
        }

        .custom-range-slider input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #000;
            cursor: pointer;
            border-radius: 50%;
        }

        .range-value {
            position: absolute;
            top: -25px;
            left: 40%;
            transform: translateX(-50%);
            background: #000;
            color: #fff;
            padding: 2px 5px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
        }

        .slider-container {
            width: 80%;
            max-width: 600px;
            text-align: center;
            position: relative;
        }

        input[type="range"] {
            width: 100%;
            margin: 20px 0;
            -webkit-appearance: none;
            appearance: none;
            background: #ddd;
            height: 5px;
            border-radius: 5px;
            outline: none;
            position: relative;
            z-index: 1;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            background: #000;
            border-radius: 50%;
            cursor: pointer;
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            background: #000;
            border-radius: 50%;
            cursor: pointer;
        }

        .tooltip {
            position: absolute;
            top: -30px;
            left: 0;
            background: #000;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            transform: translateX(-50%);
            white-space: nowrap;
            z-index: 2;
        }

        .labels {
            display: flex;
            justify-content: between;
            font-size: 14px;
        }

        .labels span {
            display: block;
            width: 33.33%;
        }
    </style>
@endpush

@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ session()->get('message') }}
        </div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}
        </div>
    @endif
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <div class="col-md-4">
                                <h4>QR {{ trans('file.code') }}</h4>
                            </div>
                            <div class="col-md-6" id="qr_tabs">
                                <button class="btn btn-success" id="main_qr">Main QR</button>
                                <button class="btn btn-light" id="table_qr">Table QR</button>
                                <button class="btn btn-light" id="action_qr">Action QR</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mt-6">
                                <form id="qrCodeForm" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="text-center"><b>Main QR</b></h3>
                                                    <h5 class=""><b>Color</b></h3>
                                                        <div class="form-group row">
                                                            <div class="col-md-6">
                                                                <label for="backgroundColor"
                                                                    class="col-form-label">Background
                                                                    Color</label>
                                                                <input type="color" value="#FFFFFF" name="backgroundColor"
                                                                    id="backgroundColor">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="foregroundColor"
                                                                    class="col-form-label">Foreground
                                                                    Color</label>
                                                                <input type="color" name="foregroundColor"
                                                                    id="foregroundColor">
                                                            </div>
                                                        </div>
                                                        <label for="uploadImage"><b>Upload Image</b></label>
                                                        <div class="form-group">
                                                            <input type="file" name="uploadImage"
                                                                class="form-control-file" id="uploadImage">
                                                        </div>
                                                        <label for="othersettings" class="mt-3"><b>Other
                                                                Settings</b></label>
                                                        <div class="form-group row">
                                                            <div class="col-md-6 d-flex">
                                                                <div>
                                                                    <label for="padding"
                                                                        class="col-form-label mr-5">Padding</label>
                                                                </div>
                                                                <div class="custom-range-slider">
                                                                    <input type="range" class="form-control-range"
                                                                        id="padding"
                                                                        oninput="paddingValue.innerText = this.value*10"
                                                                        name="padding" min="1" max="10"
                                                                        value="2">
                                                                    <span class="range-value" id="paddingValue">20</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <span><b>Shape: &nbsp;</b></span>
                                                                <label class="custom-toggle">
                                                                    <input type="checkbox" name="shape" id="shape">
                                                                    <span class="slider"></span>
                                                                </label>
                                                                <span id="shape-label" class="ml-2">Square</span>
                                                            </div>

                                                        </div>
                                                        <?php
                                                        $general_settings = \App\Models\GeneralSetting::select('without_stock')->first();
                                                        ?>
                                                        @if ($general_settings && $general_settings->without_stock != 'no')
                                                            <div class="form-group" id="warehouse_form">
                                                                <label for="select_table"><b>Select Warehouse *</b></label>
                                                                <select class="form-control" name="warehouse"
                                                                    id="select_warehouse" required>
                                                                    @foreach ($warehouse as $key)
                                                                        <option value="{{ $key->id }}"
                                                                            {{ $user_warehouse == $key->id ? 'selected' : '' }}>
                                                                            {{ $key->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif
                                                        <div class="form-group" id="table_form">
                                                            <label for="select_table"><b>Select Table</b></label>
                                                            <select class="form-control" name="table" id="select_table">
                                                                <option value="0">Select Table..</option>
                                                                @foreach ($tables as $key => $table)
                                                                    <option value={{ $table->id }}>{{ $table->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group" id="action_form">
                                                            <label for="select_action"><b>Select Action</b></label>
                                                            <select class="form-control" name="action"
                                                                id="select_action">
                                                                <option value="0">Select Table..</option>
                                                                @foreach ($tables as $key => $table)
                                                                    <option value="{{ $table->id }}">{{ $table->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="error"></div>
                                                    <div id="qr-code-container" class="d-flex justify-content-center">
                                                        {{ QrCode::size($size)->backgroundColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2])->color($foregroundColor[0], $foregroundColor[1], $foregroundColor[2])->margin($margin)->style($style)->generate('http://demo.gettlb.com/main') }}
                                                    </div>
                                                    <div class="slider-container m-auto">
                                                        <input type="range" name="size" min="1"
                                                            max="9" value="1" id="range-slider">
                                                        <div class="tooltip" id="tooltip">250px</div>
                                                        <div class="d-flex justify-content-between">
                                                            <div><span>250px</span></div>
                                                            <div><span>1000px</span></div>
                                                            <div><span>2000px</span></div>
                                                        </div>
                                                        <sl-range></sl-range>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <a type="button" id="downloadqrButton" href=""
                                                        download="" class="btn btn-primary w-100">Download</a>
                                                </div>
                                                <div class="bg-gradient-to-r from-gray-900 to-red-500"></div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
    <script>
        $("ul#dmsetting").siblings('a').attr('aria-expanded', 'true');
        $("ul#dmsetting").addClass("show");
        $("ul#dmsetting #qr-digital-menu").addClass("active");

        // Optional: To display the data URL in an image element
        // const imgElement = document.createElement('img');
        // imgElement.src = dataUrl;
        // document.body.appendChild(imgElement);
        document.getElementById('shape').addEventListener('change', function() {
            document.getElementById('shape-label').innerText = this.checked ? 'Dots' : 'Square';
        });
        $(document).ready(function() {
            let formChanged = true;
            if ($('#qr_tabs button.btn-success').attr('id') == 'main_qr') {
                $('#table_form').addClass('d-none');
                $('#action_form').addClass('d-none');
            } else if ($('#qr_tabs button.btn-success').attr('id') == 'table_qr') {
                $('#action_form').addClass('d-none');
            }
            $('#qr_tabs button').click(function() {
                $('#qr_tabs button').removeClass('btn-success');
                $('#qr_tabs button').addClass('btn-light');
                $('#table_form').removeClass('d-none');
                $('#action_form').removeClass('d-none');
                $(this).removeClass('btn-light');
                $(this).addClass('btn-success');
                if ($(this).attr('id') == 'main_qr') {
                    $('#table_form').addClass('d-none');
                    $('#action_form').addClass('d-none');
                } else if ($(this).attr('id') == 'table_qr') {
                    $('#action_form').addClass('d-none');
                }
                displayErrorOrQr()
            })

            displayErrorOrQr()

            function displayErrorOrQr() {
                let formChanged = false;
                var isTableFormHidden = $('#table_form').hasClass('d-none');
                var isActionFormHidden = $('#action_form').hasClass('d-none');
                var tableValue = $('#select_table').val();
                var actionValue = $('#select_action').val()
                $('.error').text('').css('height', '0');

                if (!isTableFormHidden && tableValue <= "0") {
                    $('.error').text('You need to select table').css('height', '250px');
                    $('#qr-code-container svg').hide();
                } else if (!isActionFormHidden && actionValue <= "0") {
                    $('.error').text('You need to select an action').css('height', '250px');
                    $('#qr-code-container svg').hide();
                } else {
                    $('#qr-code-container svg').show();
                    let formChanged = true;
                    submitForm();
                }

            }

            function svgToDataUrl(svgElement) {
                const serializer = new XMLSerializer();
                const svgString = serializer.serializeToString(svgElement);
                const base64Data = btoa(unescape(encodeURIComponent(svgString)));
                const dataUrl = `data:image/svg+xml;base64,${base64Data}`;
                return dataUrl;
            }

            function svgToImageUrl(svgDataUrl, callback) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    const context = canvas.getContext('2d');
                    context.drawImage(img, 0, 0);
                    const imageUrl = canvas.toDataURL(
                        'image/png'); // Change 'image/png' to 'image/jpeg' for JPEG
                    callback(imageUrl);
                };
                img.src = svgDataUrl;
            }

            const svgElement = $('#qr-code-container svg')[0]; // Select the SVG element with jQuery
            const svgDataUrl = svgToDataUrl(svgElement);

            svgToImageUrl(svgDataUrl, function(imageUrl) {
                // Optional: To display the image data URL in an image element
                $('#downloadqrButton').attr('href', imageUrl);
            });



            $('#qrCodeForm input').on('input change', function() {
                formChanged = true;
                displayErrorOrQr()
            });

            $('#qrCodeForm select').on('change', function() {
                console.log($('input[name="table"]').text())
                formChanged = true;
                displayErrorOrQr()
            });

            submitForm();


            function submitForm() {
                if (!formChanged) return;
                console.log('trigger')
                formChanged = false;

                // var formData = $('#qrCodeForm').serialize();
                var form = $('#qrCodeForm')[0];
                var formData = new FormData(form);
                console.log($('#qrCodeForm').serialize())
                // formData.append('size', $("input[name=size]").val();
                // formData.append('backgroundColor', $("input[name=backgroundColor]").val());
                // formData.append('foregroundColor', $("input[name=foregroundColor]").val());
                // formData.append('padding', $("input[name=padding]").val());
                // formData.append('shape', $("input[name=shape]").val());
                // formData.append('uploadImage', $('#uploadImage').prop('files')[0]);
                // var form = $('#qrCodeForm')[0];
                // var formData = new FormData(form);
                // formData.append('uploadImage', $('#uploadImage').prop('files')[0]);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{ route('generate.qr') }}",
                    method: 'POST',
                    headers: {
                        "Accept": "application/json",
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if ($('#uploadImage').prop('files')[0]) {
                            $('#qr-code-container').html(response);
                            const svgElement = $('#qr-code-container svg')[0];
                            const svgDataUrl = svgToDataUrl(svgElement);

                            svgToImageUrl(svgDataUrl, function(imageUrl) {
                                // Optional: To display the image data URL in an image element
                                $('#downloadqrButton').attr('href', imageUrl);
                            });
                        } else {
                            $('#qr-code-container').html(response);
                            const svgElement = $('#qr-code-container svg')[0];
                            const svgDataUrl = svgToDataUrl(svgElement);

                            svgToImageUrl(svgDataUrl, function(imageUrl) {
                                // Optional: To display the image data URL in an image element
                                $('#downloadqrButton').attr('href', imageUrl);
                            });
                        }
                    },
                    error: function(response) {
                        alert(response);
                    }
                });
            }
        });
    </script>
@endpush
