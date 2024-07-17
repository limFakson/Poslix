@extends('landlord.layout.main') @section('content')

@if($errors->has('name'))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
@endif
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif

<section>
    <div class="container-fluid">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#createModal"><i class="dripicons-plus"></i> {{trans('file.Add Client')}}</button>
        <a href="{{route('superadmin.backupTenantDB')}}" class="btn btn-dark"><i class="dripicons-cloud-download"></i> {{__('file.Backup Client DB')}}</a>
        <a href="{{route('superadmin.updateTenantDB')}}" class="btn btn-primary"><i class="dripicons-stack"></i> {{__('file.Update Client DB')}}</a>
        <a href="{{route('superadmin.updateSuperadminDB')}}" class="btn btn-info"><i class="dripicons-stack"></i> {{__('file.Update SuperAdmin DB')}}</a>
    </div>
    <div class="table-responsive">
        <table id="client-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.name')}}</th>
                    <th>DB</th>
                    <th>{{trans('file.Domain')}}</th>
                    <th>{{trans('file.Package')}}</th>
                    <th>{{trans('file.Subscription Type')}}</th>
                    <th>{{trans('file.Company Name')}}</th>
                    <th>{{trans('file.Phone Number')}}</th>
                    <th>{{trans('file.Email')}}</th>
                    <th>{{trans('file.Created At')}}</th>
                    <th>{{trans('file.Expiry Date')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_client_all as $key => $client)
                <?php
                    if($client->package_id)
                        $package_name = \App\Models\landlord\Package::find($client->package_id)->name;
                    else
                        $package_name = 'N/A';
                ?>
                <tr data-id="{{$client->id}}">
                    <td>{{$key}}</td>
                    <td>{{$client->id}}</td>
                    <td>{{$client->database()->getName()}}</td>
                    <td>
                    @foreach ($client->domains as $index => $domain)
                        @if($index)<br>@endif
                        <a target="_blank" href="{!!'https://'.$domain->domain!!}">{{$domain->domain}}</a>
                    @endforeach
                    </td>
                    <td>{{$package_name}}</td>
                    <td>{{$client->subscription_type}}</td>
                    <td>{{$client->company_name}}</td>
                    <td>{{$client->phone_number}}</td>
                    <td>{{$client->email}}</td>
                    <td>{{date($general_setting->date_format, strtotime($client->created_at->toDateString()))}}</td>
                    @if($client->expiry_date >= date("Y-m-d"))
                      <td><div class="badge badge-success">{{date($general_setting->date_format, strtotime($client->expiry_date))}}</div></td>
                    @else
                      <td><div class="badge badge-danger">{{date($general_setting->date_format, strtotime($client->expiry_date))}}</div></td>
                    @endif
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="{{$client->id}}" data-subscription_type="{{$client->subscription_type}}" data-expiry_date="{{date('d-m-Y', strtotime($client->expiry_date))}}" class="renew-btn btn btn-link" data-toggle="modal" data-target="#renewModal" ><i class="dripicons-clockwise"></i>  {{trans('file.Renew Subscription')}}</button>
                                </li>
                                <li>
                                    <button type="button" data-id="{{$client->id}}" data-package_id="{{$client->package_id}}" class="switch-btn btn btn-link" data-toggle="modal" data-target="#switchModal" ><i class="dripicons-swap"></i>  {{trans('file.Change Package')}}</button>
                                </li>
                                {{ Form::open(['route' => ['clients.destroy', $client->id], 'method' => 'DELETE'] ) }}
                                <li>
                                  <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Create Modal -->
<div id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'clients.store', 'method' => 'post', 'id' => 'client-form']) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Add Client')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
          <form>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Package')}} *</label>
                    <select required class="form-control selectpicker" name="package_id">
                        @foreach($lims_package_all as $package)
                            <option value="{{$package->id}}">{{$package->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Subscription Type')}} *</label>
                    <div class="form-check">
                      <input type="radio" name="subscription_type" value="free" class="form-check-input" id="subscription-type-1" checked>
                      <label class="form-check-label" for="subscription-type-1">
                        Free
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="radio" name="subscription_type" value="monthly" class="form-check-input" id="subscription-type-1">
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
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Company Name')}} *</label>
                    <input class="form-control" type="text" name="company_name" required placeholder="company name...">
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Phone Number')}} *</label>
                    <input class="form-control" type="text" name="phone_number" required placeholder="contact number...">
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.UserName')}} *</label>
                    <input class="form-control" type="text" name="name" required placeholder="username...">
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Password')}} *</label>
                    <input class="form-control" type="password" name="password" required placeholder="password...">
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Email')}} *</label>
                    <input class="form-control" type="text" name="email" required placeholder="email...">
                </div>
                <div class="col-md-6 form-group">
                    <label>Subdomain</label>
                    <div class="input-group">
                        <input class="form-control mt-0" type="text" name="tenant" required placeholder="subdomain..." aria-label="subdomain..." aria-describedby="basic-addon2">
                      <span class="input-group-text" id="basic-addon2">{{'@'.env('CENTRAL_DOMAIN')}}</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
              <input type="submit" value="{{trans('file.submit')}}" id="submit-btn" class="btn btn-primary">
            </div>
          </form>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

<!-- Renew Modal -->
<div id="renewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'clients.renew', 'method' => 'post']) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Renew Subscription')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
          <form>
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Expiry Date')}} *</label>
                    <input type="text" name="expiry_date" required class="date form-control">
                    <input type="hidden" name="id">
                </div>
                <div class="col-md-6 form-group">
                    <label>{{trans('file.Subscription Type')}} *</label>
                    <div class="form-check">
                      <input type="radio" name="subscription_type" value="monthly" class="form-check-input" required id="subscription-type-1">
                      <label class="form-check-label" for="subscription-type-1">
                        Monthly
                      </label>
                    </div>
                    <div class="form-check">
                      <input type="radio" name="subscription_type" value="yearly" class="form-check-input" required id="subscription-type-2">
                      <label class="form-check-label" for="subscription-type-2">
                        Yearly
                      </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
              <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
          </form>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

<!-- Change Package Modal -->
<div id="switchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'clients.changePackage', 'method' => 'post']) !!}
        <div class="modal-header">
          <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Change Package')}}</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
          <form>
            <div class="form-group">
                <label>{{trans('file.Package')}} *</label>
                <select required class="form-control selectpicker" name="package_id">
                    @foreach($lims_package_all as $package)
                        <option value="{{$package->id}}">{{$package->name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="client_id">
                <input type="hidden" name="previous_package_id">
            </div>
            <div class="form-group">
              <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
            </div>
          </form>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#client").siblings('a').attr('aria-expanded','true');
    $("ul#client").addClass("show");
    $("ul#client #client-list-menu").addClass("active");

    var client_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    $('input[name=tenant]').on('input', function () {
        var tenant = $(this).val();
        var letters = /^[a-zA-Z0-9]+$/;
        if(!letters.test(tenant)) {
            alert('Tenant name must be alpha numeric(a-z and 0-9)!');
            tenant = tenant.substring(0, tenant.length-1);
            $('input[name=tenant]').val(tenant);
        }
    });

    $(document).on('click', '.renew-btn', function() {
        $("#renewModal input[name='id']").val($(this).data('id'));
        if($(this).data('subscription_type') == 'monthly')
            $("#subscription-type-1").prop("checked", true);
        else
            $("#subscription-type-2").prop("checked", true);
        $("#renewModal input[name='expiry_date']").val($(this).data('expiry_date'));
    });

    $(document).on('click', '.switch-btn', function() {
        $("#switchModal input[name='client_id']").val($(this).data('id'));
        $("#switchModal input[name='previous_package_id']").val($(this).data('package_id'));
        $("#switchModal select[name='package_id']").val($(this).data('package_id'));
        $('.selectpicker').selectpicker('refresh');
    });

    $(document).on('submit', '#client-form', function(e) {
        $("#submit-btn").prop('disabled', true);
    });

    $('#client-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 2, 3, 4, 5, 6, 8, 9]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'excel',
                text: '<i title="export to excel" class="dripicons-document-new"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<i title="print" class="fa fa-print"></i>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '<i title="column visibility" class="fa fa-eye"></i>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endpush
