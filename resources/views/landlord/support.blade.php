@extends('landlord.layout.main') @section('content')

@push('custom-css')
    <style>
        #icons {
            opacity: 0;
            visibility: hidden;
        }
        .icon_collapse {
            height: 50vh;
            margin-top: 15px;
            overflow-y: scroll;
        }
        .icon_collapse section {
            padding: 30px 0 0;
        }
        .icon_collapse .card {
            background: #e5e6e7;
            margin: 0 15px;
        }
        .page-header {
            border-bottom: 1px solid #555;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .fa-hover {
            text-align: center;
            margin-bottom: 15px
        }
        .fa-hover a {
            cursor: pointer;
            font-size: 0px;
        }
        .fa-hover i:hover {
            opacity: 0.8;
        }
        .fa-hover i {
            color: #7c5cc4;
            display: block;
            font-size: 20px;
        }
        .ticket{
            border:1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 30px;
            padding: 20px;
        }
        .ticket:hover{
            border: 1px solid #999
        }
        .ticket div span {
            font-size: 12px;
        }
    </style>
@endpush

@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>All Tickets</h3>
                    </div>
                    <div class="card-body all-tickets">
                        <div class="col-8 mb-5">
                            <form class="" method="post" action="{{url('superadmin/support/ticket/tenant/')}}">
                            @csrf
                                <label>Filter by tenants</label>
                                <select class="form-control w-50" name="tenant">
                                    <option value="all">All Tenants</option>
                                    @foreach ($tenants as $tenant)
                                    <option value="{{$tenant->id}}">{{$tenant->id}}</option>
                                    @endforeach
                                </select>
                                <input type="submit" class="btn btn-primary" value="Filter">
                            </form>
                        </div>
                        @if(count($tickets) > 0)
                            @foreach ($tickets as $ticket)
                            <div class="ticket mb-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <small>Ticket No. #{{ $ticket->id }}</small> | <small><i class="dripicons-calendar"></i> {{ date('d M Y', strtotime($ticket->created_at)) }}</small>
                                    </div>
                                    <div>
                                        <a class="btn" href="{{ url('superadmin/support/ticket/') }}/{{ $ticket->id }}"><i class="dripicons-preview"></i></a>
                                    </div>
                                </div>
                                <a href="{{ url('superadmin/support/ticket/') }}/{{ $ticket->id }}">
                                <h3 class="mb-2">
                                {{ $ticket->subject }}
                                </h3>
                                </a>
                                <p class="short mb-0">{{ $ticket->description }}</p>
                            </div>
                            @endforeach
                        @else
                        <h3>No tickets found.</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection

@push('scripts')
<script type="text/javascript">
    // Load more
    var page_num = 1;
    var total_page = <?php echo json_encode($tickets->total()) ?>;
    $(window).scroll( function() {
        if( ( $(window).scrollTop() + $(window).height() > ( $(document).height() * (2/3) ) ) && (total_page>=page_num) ) {
            loadMoreData(++page_num);
        }
        
    });
    
    function loadMoreData(page_num) {
        $.ajax({
            url: '?page=' + page_num,
            type: "get",
        }).done(function(data) {
            $(".all-tickets").append(data.html);
        }).fail(function(jqXHR, ajaxOptions, thrownError)
        {
                console.log('server not responding...');
        });
    }
</script>
@endpush
