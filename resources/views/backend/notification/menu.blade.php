@extends('backend.layout.main')

@section('content')
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

    <style>
        .notification {
            padding: 10px;
        }

        .card {
            padding: 10px;
            flex-direction: row;
            width: 35%;
            position: relative;
        }

        .noti-box {
            width: 100%;
            display: flex;
            position: relative;
            cursor: pointer;
        }

        .info {
            padding-left: 10px;
            padding-right: 10px;
            width: 100%;
        }

        .icon {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 20%;
            font-size: 30px;
            cursor: pointer;
        }

        .icon .fa-ellipsis-h {
            font-size: 20px;
        }

        .noti-action-icon {
            background-color: red;
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 4rem;
            display: flex;
            justify-content: center;
            align-content: center;
        }

        .noti-action-icon i {
            font-size: 25px;
            color: white;
            vertical-align: middle;
            margin: auto;
        }

        .notification-msg h1 {
            padding-top: 8px;
            font-weight: 600;
        }

        .status {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .status p,
        .view {
            font-size: 12px;
            font-weight: 400;
            padding: 0;
            margin: 0;
        }

        .view-icon,
        .delete-icon {
            display: none;
        }
    </style>

    <section>
        <div class="cards">
            <div class="notification">
                <div class="card">
                    <div class="noti-box">
                        <span class="icon">
                            <i class="fa fa-bell"></i>
                        </span>
                        <div class="info flex flex-col">
                            <span class="notification-msg">
                                <h1>Call Waiter</h1>
                            </span>
                            <p class="table-no">
                                Table 1
                            </p>
                            <div class="status">
                                <div class="view">
                                    <i class="fa fa-eye_open"></i>
                                    <span>1</span>
                                </div>
                                <p>8 hours ago</p>
                            </div>
                        </div>
                        <span class="icon" id="noti-option"><i class="fa fa-ellipsis-h" aria-hidden="true"></i> </span>
                    </div>
                    <span class="delete-icon noti-action-icon"><i class="fa fa-trash-o" aria-hidden="true"></i> </span>
                    <span class="view-icon"><i class="fa fa-eye-slash" style="color: #000;" aria-hidden="true"></i> </span>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#menu-notification").addClass("active");
        $(document).ready(function() {})
        $(document).on('click', function(e) {
            if ($(e.target).is('#noti-option') || $(e.target).closest('#noti-option').length) {
                $('.view-icon').show().addClass('noti-action-icon').css({
                    display: 'flex',
                    backgroundColor: '#dcdcdc'
                });
            } else if (!$(e.target).closest('.view-icon').length) {
                $('.view-icon').hide();
            }
        });
    </script>
@endpush
