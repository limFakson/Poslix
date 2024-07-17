@extends('layout')
@section('title', 'Generate QR Code')
@section('css')
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
        width: 60px;
        height: 34px;
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
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #2196F3;
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
</style>
@endsection
@section('content')
    <div class="mt-6">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Main QR</h3>
                    </div>
                    <div class="card-body">
                        <h5 class="">Color</h3>
                            <form>
                                <div class="form-group row">
                                    <label for="backgroundColor" class="col-form-label">Background Color</label>
                                    <div class="">
                                        <input type="color" id="backgroundColor">
                                    </div>
                                    <label for="foregroundColor" class="col-form-label">Foreground Color</label>
                                    <div class="">
                                        <input type="color" id="foregroundColor">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="uploadImage">Upload Image</label>
                                    <input type="file" class="form-control-file" id="uploadImage">
                                </div>
                                <div class="form-group row">
                                    <label for="padding" class="col-sm-2 col-form-label">Padding</label>
                                    <div class="col-sm-4">
                                        <input type="range" class="form-control-range" id="padding" min="0"
                                            max="100">
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="switch1" name="example">
                                        <label class="custom-control-label" for="switch1">Toggle me</label>
                                    </div>
                                    <label for="shape" class="col-sm-2 col-form-label">Shape</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="shape">
                                            <option value="square">Square</option>
                                            <option value="circle">Circle</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary">Generate QR</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body m-auto">
                        {{ QrCode::size($size)->backgroundColor($backgroundColor[0], $backgroundColor[1], $backgroundColor[2])->color($foregroundColor[0], $foregroundColor[1], $foregroundColor[2])->margin($margin)->style($style)->generate('https://minhazulmin.github.io/') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        {{-- <label for="color">Color:</label>
        <input type="color" wire:model="color"><br><br>

        <label for="margin">Margin:</label>
        <input type="number" wire:model="margin"><br><br>

        <label for="size">Size:</label>
        <input type="number" wire:model="size"><br><br> --}}

        <!-- QR Code -->
    </div>
    <form id="qrCodeForm" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="data">Data:</label>
            <input type="text" name="data" id="data" required>
        </div>
        <div>
            <label for="background_color">Background Color:</label>
            <input type="color" name="background_color" id="background_color" required>
        </div>
        <div>
            <label for="foreground_color">Foreground Color:</label>
            <input type="color" name="foreground_color" id="foreground_color" required>
        </div>
        <div class="form-group">
            <label for="uploadImage">Upload Image</label>
            <input type="file" class="form-control-file" id="uploadImage">
        </div>
        <div class="form-group row">
            <label for="padding" class="col-sm-2 col-form-label">Padding</label>
            <div class="col-sm-4">
                <input type="range" class="form-control-range" id="padding" min="0" max="100">
            </div>
            <label for="shape" class="col-sm-2 col-form-label">Shape</label>
            <div class="col-sm-4">
                <label class="custom-toggle">
                    <input type="checkbox" id="shape" name="shape">
                    <span class="slider"></span>
                </label>
                <span id="shape-label" class="ml-2">square</span>
            </div>
        </div>
    </form>

    <div id="qrCodePreview"></div>
    {{-- <div class="col-md-2">
        <p class="mb-0">Simple</p>
        <a href="" id="container">{!! $simple !!}</a><br />
        <button id="download" class="mt-2 btn btn-info text-light" onclick="downloadSVG()">Download SVG</button>
    </div> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('shape').addEventListener('change', function() {
            document.getElementById('shape-label').innerText = this.checked ? 'circle' : 'square';
        });
    </script>
    <script>
        $(document).ready(function() {
            let formChanged = false;

            $('#qrCodeForm input').on('input change', function() {
                formChanged = true;
                submitForm();
            });

            // function submitForm() {
            //     if (!formChanged) return;

            //     formChanged = false;

            //     var formData = new FormData($('#qrCodeForm')[0]);

            //     $.ajax({
            //         url: "{{ route('generate.qrcode') }}",
            //         method: 'POST',
            //         data: formData,
            //         contentType: false,
            //         processData: false,
            //         success: function(response) {
            //             $('#qrCodePreview').html('<img src="' + response.url + '" alt="QR Code">');
            //         },
            //         error: function(response) {
            //             alert('An error occurred while generating the QR code.');
            //         }
            //     });
            // }
        });
    </script>
@endsection