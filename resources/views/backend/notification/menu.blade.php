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
            padding: 5px 10px;
            padding-top: 13px;
            flex-direction: row;
            width: 35%;
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
        }
    </style>

    <section>
        <div class="cards">
            <div class="notification">
                <div class="card">
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
                    <span class="icon">…</span>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $("#menu-notification").addClass("active");
    </script>
@endpush
