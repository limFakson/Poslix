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
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
        <style>
            /* Switch container */
            .switch {
                position: relative;
                display: inline-block;
                width: 34px;
                height: 20px;
            }

            /* Hide default HTML checkbox */
            .switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            /* Custom slider */
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
                height: 16px;
                width: 16px;
                left: 2px;
                bottom: 2px;
                background-color: black;
                transition: .4s;
                border-radius: 50%;
            }

            input:disabled+.slider {
                background-color: #f0f0f0;
            }

            /* When the checkbox is checked, change background color */
            input:checked+.slider {
                background-color: #d496d4;
            }

            /* Move the slider to the right when the checkbox is checked */
            input:checked+.slider:before {
                transform: translateX(14px);
            }

            .card-alert.card-alert-warning {
                transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
                border-radius: 4px;
                box-shadow: none;
                font-family: Roboto, Helvetica, Arial, sans-serif;
                font-weight: 400;
                font-size: 0.875rem;
                line-height: 1.43;
                letter-spacing: 0.01071em;
                background-color: transparent;
                padding: 6px 16px;
                color: rgb(102, 60, 0);
                border: 1px solid rgb(255, 152, 0);
            }

            .card-body p {
                font-size: .97em;
                line-height: 2rem;
                margin-bottom: 10px;
                color: #000 !important;
            }

            .navbar h3 {
                color: #f5f5f5;
                margin-top: 14px;
            }

            .hljs-pre {
                background: #f8f8f8;
                padding: 3px;
            }

            .footer {
                border-top: 1px solid #eee;
                margin-top: 40px;
                padding: 40px 0;
            }

            .input-group {
                width: 110px;
                margin-bottom: 10px;
            }

            .pull-center {
                margin-left: auto;
                margin-right: auto;
            }

            @media (min-width: 768px) {
                .container {
                    max-width: 730px;
                }
            }

            @media (max-width: 767px) {
                .pull-center {
                    float: right;
                }
            }

            .input-group-addon {
                padding: 6px 12px;
                font-size: 14px;
                font-weight: 400;
                line-height: 1;
                color: #555;
                text-align: center;
                background-color: #eee;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            .input-group .form-control:last-child,
            .input-group-addon:last-child,
            .input-group-btn:last-child>.btn,
            .input-group-btn:last-child>.btn-group>.btn,
            .input-group-btn:last-child>.dropdown-toggle,
            .input-group-btn:first-child>.btn:not(:first-child),
            .input-group-btn:first-child>.btn-group:not(:first-child)>.btn {
                border-bottom-left-radius: 0;
                border-top-left-radius: 0;
            }

            .input-group-addon .fa {
                position: relative;
                top: 5px;
                display: inline-block;
                font-style: normal;
                font-weight: 400;
                line-height: 1;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .page-container {
                margin-top: 100px;
            }
        </style>
    @endpush

    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>{{ trans('file.Working Hours') }}</h4>
                        </div>
                        <div class="card-body">
                            <h1>Set your working hours</h1>
                            <hr>
                            <p>Enable or disable working hours</p>
                            <div class="card-alert card-alert-warning w-50">
                                <strong class="fa fa-warning text-warning"></strong> Customer will be able to place orders
                                all the time if Working Hours control is diabled
                            </div>
                            <form action="{{ url('setting/working_hours_setting') }}" method="post">
                                @csrf
                                <div class="row col-md-8 mt-5">
                                    <div class="col-md-6"><b>Working Hours</b></div>
                                    <div class="col-md-6">
                                        <div class="enable-dine-order row">
                                            <div class="first">
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        {{ isset($workinghours) && $workinghours->enable_workinghours ? 'checked' : '' }}
                                                        class="enable-order-check" name="workinghours_check"
                                                        id="workinghours_check">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>
                                            <div class="ml-5">
                                                <label for="workinghours_check"><b>Activate dine-in orders</b></label>
                                                <p class="text-black">Enable dine-in orders</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div id="time_settings">
                                    <div class="col-md-8 mt-5">
                                        <h3><b>Time Settings</b></h3>
                                        <p>Specify your working hours and days</p>
                                        <div class="card-alert card-alert-warning w-50">
                                            <strong class="fa fa-warning text-warning"></strong> Customer can only place
                                            orders in the selected days and times
                                        </div>
                                    </div>
                                    <div class="col-md-12 sunday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Sunday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="sunday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->sunday_first_time_start ? substr($workinghours->sunday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2"><b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="sunday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->sunday_first_time_end ? substr($workinghours->sunday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="sunday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('sunday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;" data-week="sunday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="sunday_second_time_enable"
                                            id="sunday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->sunday_second_time_enable ? true : false }}">
                                        <div class="sunday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span onclick="secondremove('sunday')"
                                                    style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="sunday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->sunday_second_time_start ? substr($workinghours->sunday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="sunday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->sunday_second_time_end ? substr($workinghours->sunday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox"
                                                    {{ isset($workinghours) && $workinghours->sunday ? 'checked' : '' }}
                                                    name="sunday_week_check" id="sunday_week_check">
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 monday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Monday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="monday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->monday_first_time_start ? substr($workinghours->monday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="monday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->monday_first_time_end ? substr($workinghours->monday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="monday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('monday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="monday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="monday_second_time_enable"
                                            id="monday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->monday_second_time_enable ? true : false }}">
                                        <div class="monday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span onclick="secondremove('monday')"
                                                    style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="monday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->monday_second_time_start ? substr($workinghours->monday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="monday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->monday_second_time_end ? substr($workinghours->monday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox"
                                                    {{ isset($workinghours) && $workinghours->monday ? 'checked' : '' }}
                                                    name="monday_week_check" id="monday_week_check">
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 tuesday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Tuesday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control"
                                                    name="tuesday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->tuesday_first_time_start ? substr($workinghours->tuesday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="tuesday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->tuesday_first_time_end ? substr($workinghours->tuesday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="tuesday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('tuesday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="tuesday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="tuesday_second_time_enable"
                                            id="tuesday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->tuesday_second_time_enable ? true : false }}">
                                        <div class="tuesday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span onclick="secondremove('tuesday')"
                                                    style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="tuesday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->tuesday_second_time_start ? substr($workinghours->tuesday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="tuesday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->tuesday_second_time_end ? substr($workinghours->tuesday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox"
                                                    {{ isset($workinghours) && $workinghours->tuesday ? 'checked' : '' }}
                                                    name="tuesday_week_check" id="tuesday_week_check">
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 wednesday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Wednesday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control"
                                                    name="wednesday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->wednesday_first_time_start ? substr($workinghours->wednesday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control"
                                                    name="wednesday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->wednesday_first_time_end ? substr($workinghours->wednesday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="wednesday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('wednesday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="wednesday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="wednesday_second_time_enable"
                                            id="wednesday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->wednesday_second_time_enable ? true : false }}">
                                        <div class="wednesday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span
                                                    onclick="secondremove('wednesday')" style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="wednesday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->wednesday_second_time_start ? substr($workinghours->wednesday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="wednesday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->wednesday_second_time_end ? substr($workinghours->wednesday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox" name="wednesday_week_check"
                                                    id="wednesday_week_check"
                                                    {{ isset($workinghours) && $workinghours->wednesday ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 thursday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Thursday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control"
                                                    name="thursday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->thursday_first_time_start ? substr($workinghours->thursday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="thursday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->thursday_first_time_end ? substr($workinghours->thursday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="thursday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('thursday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="thursday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="thursday_second_time_enable"
                                            id="thursday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->thursday_second_time_enable ? true : false }}">
                                        <div class="thursday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span
                                                    onclick="secondremove('thursday')" style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="thursday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->thursday_second_time_start ? substr($workinghours->thursday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="thursday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->thursday_second_time_end ? substr($workinghours->thursday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox" name="thursday_week_check"
                                                    id="thursday_week_check"
                                                    {{ isset($workinghours) && $workinghours->thursday ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 friday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Friday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="friday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->friday_first_time_start ? substr($workinghours->friday_first_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="friday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->friday_first_time_end ? substr($workinghours->friday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="friday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('friday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="friday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="friday_second_time_enable"
                                            id="friday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->friday_second_time_enable ? true : false }}">
                                        <div class="friday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span onclick="secondremove('friday')"
                                                    style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="friday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->friday_second_time_start ? substr($workinghours->friday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="friday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->friday_second_time_end ? substr($workinghours->friday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox"
                                                    {{ isset($workinghours) && $workinghours->friday ? 'checked' : '' }}
                                                    name="friday_week_check" id="friday_week_check">
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 saturday-week row align-item-center mt-5">
                                        <div class="col-md-2 py-2">
                                            <h3><b>Saturday</b></h3>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control"
                                                    name="saturday_first_time_start"
                                                    value="{{ isset($workinghours) && $workinghours->friday_second_time_start ? substr($workinghours->friday_second_time_start, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-item-center justify-content-center py-2">
                                            <b>TO</b>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group clockpicker" data-placement="bottom" data-align="top"
                                                data-autoclose="true">
                                                <input type="text" class="form-control" name="saturday_first_time_end"
                                                    value="{{ isset($workinghours) && $workinghours->saturday_first_time_end ? substr($workinghours->saturday_first_time_end, 0, 5) : '16:00' }}">
                                                <span class="input-group-addon">
                                                    <span class="fa fa-clock-o"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="saturday_second_shift" class="col-md-1 text-center py-2">
                                            <span onclick="secondshift('saturday')"
                                                style="font-size: 12px; cursor: pointer; color: purple;"
                                                data-week="saturday">
                                                + 2nd shift</span>
                                        </div>
                                        <input type="hidden" name="saturday_second_time_enable"
                                            id="saturday_second_time_enable"
                                            value="{{ isset($workinghours) && $workinghours->saturday_second_time_enable ? true : false }}">
                                        <div class="saturday-secondtime d-none col-md-5 row">
                                            <div class="col-md-3 text-center py-2"><span
                                                    onclick="secondremove('saturday')" style="cursor: pointer">--</span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="saturday_second_time_start"
                                                        value="{{ isset($workinghours) && $workinghours->saturday_second_time_start ? substr($workinghours->saturday_second_time_start, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-1 text-center py-2">
                                                <b>TO</b>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group clockpicker" data-placement="bottom"
                                                    data-align="top" data-autoclose="true">
                                                    <input type="text" class="form-control"
                                                        name="saturday_second_time_end"
                                                        value="{{ isset($workinghours) && $workinghours->saturday_second_time_end ? substr($workinghours->saturday_second_time_end, 0, 5) : '16:00' }}">
                                                    <span class="input-group-addon">
                                                        <span class="fa fa-clock-o"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 justify-content-center py-2 row">
                                            <label class="switch">
                                                <input type="checkbox" name="saturday_week_check"
                                                    id="saturday_week_check"
                                                    {{ isset($workinghours) && $workinghours->saturday ? 'checked' : '' }}>
                                                <span class="slider"></span>
                                            </label>
                                            <span><b> &nbsp;Available</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-auto text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
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
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>
    <script>
        $("ul#dmsetting").siblings('a').attr('aria-expanded','true');
        $("ul#dmsetting").addClass("show");
        $("ul#dmsetting #whours-digital-menu").addClass("active");

        $(document).ready(function() {
            $('.clockpicker').clockpicker()
                .find('input').change(function() {
                    console.log(this.value);
                });
            var input = $('#single-input').clockpicker({
                placement: 'bottom',
                align: 'center',
                autoclose: true,
                'default': 'now'
            });
            if ($('#workinghours_check')[0].checked == false) {
                $('#time_settings').addClass('d-none');
            } else {
                $('#time_settings').removeClass('d-none');
            }
            $('#workinghours_check').click(() => {
                if ($('#workinghours_check')[0].checked == false) {
                    $('#time_settings').addClass('d-none');
                } else {
                    $('#time_settings').removeClass('d-none');
                }
            })
        });

        function secondremove(week) {
            $("#" + week + '_second_time_enable').val(false);
            $('.' + week + '-secondtime').addClass('d-none');
            $('#' + week + '_second_shift').removeClass('d-none');
        }

        function secondshift(week) {
            console.log(week);
            $("#" + week + '_second_time_enable').val(true);
            $('#' + week + '_second_shift').addClass('d-none');
            $('.' + week + '-secondtime').removeClass('d-none');
        }

        let weeks = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        weeks.forEach(week => {
            console.log(week);
            if ($('#' + week + '_second_time_enable').val() == true) {
                console.log(true);
                $('.' + week + '-secondtime').removeClass('d-none');
                $('#' + week + '_second_shift').addClass('d-none');
            } else {
                $('.' + week + '-secondtime').addClass('d-none');
                $('#' + week + '_second_shift').removeClass('d-none');
            }
        });

        // function weekcheck(week) {
        //     console.log(week);
        //     if ($('#' + week + "_week_check")[0].checked == false) {
        //         $('.' + week + "-week").addClass('d-none');
        //     } else {
        //         $('.' + week + "-week").removeClass('d-none');
        //     }
        // }
    </script>
    <script></script>
@endpush
