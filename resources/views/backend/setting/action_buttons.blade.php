@extends('backend.layout.main')
@section('content')
@push('css')
    <style>
        .action-btn-color button, .action-btn-icon button{
            margin-right: 10px;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            outline: none;
        }

        .action-btn-color button:hover, .action-btn-icon button:hover {
            transform: scale(1.1);
        }

        .action-btn-icon button.active {
            transform: scale(1.2);
            border: 1px solid red;
        }

        .action-btn-color:nth-child(1) button {
            background-color: #2fb5f3;
        }

        .action-btn-color:nth-child(2) button {
            background-color: #d82ff3;
        }

        .action-btn-color:nth-child(3) button {
            background-color: #f04f4f;
        }

        .action-btn-color:nth-child(4) button {
            background-color: #30d632;
        }

        .action-btn-color:nth-child(5) button {
            background-color: #dfdd1e;
        }

        .action-btn-color button.active {
            border: 1px solid red;
            transform: scale(1.1);
        }

        /* toggle button style */
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

        .action_buttons_table_body tr td {
            vertical-align: middle !important;
        }

        /* .action_buttons_table_body tr td:nth-child(5) {
            vertical-align: middle !important;
            padding-top: 8px;
        } */

    </style>
@endpush

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
    <div class="card">
        <div class="card-header mt-2">
            <h3 class="text-left">Manage your action buttons</h3>
        </div>
    </div>
    <div class="container-fluid">
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addActionModalLabel"><i class="dripicons-plus"></i> Add Action Button</button>
    </div>
    <div class="table-responsive">
        <table id="table-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Notification Title</th>
                    <th>Button Icon</th>
                    <th>Button Color</th>
                    <th>Status</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody id="action_buttons_table_body">
                @foreach($actionbuttons as $key => $actionbutton)
                    <tr data-id="{{$actionbutton->id}}">
                        <td>{{$key}}</td>
                        <td>{{ $actionbutton->name }}</td>
                        <td><i style="color: black; font-size:18px;" class="{{$actionbutton->icon}}"></i></td>
                        <td><button style="width: 25%; height: 18px; border: none; background-color: {{$actionbutton->color}}"></button></td>
                        <td><label class='switch'><input type='checkbox' {{$actionbutton->status == 1 ? 'checked' : ''}} class='enable-order-check action_button_status' name='status' data-id="{{$actionbutton->id}}"><span class='slider'></span></label></td>
                        <td style="padding-top: 8px;">
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    <li>
                                        <button type="button" data-id="{{$actionbutton->id}}" data-name="{{$actionbutton->name}}" data-buttonIcon="{{$actionbutton->icon}}" data-color="{{$actionbutton->color}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editActionModal" ><i class="dripicons-document-edit"></i>  {{trans('file.edit')}}</button>
                                    </li>
                                    <li class="divider"></li>
                                    {{ Form::open(['route' => ['setting.deleteActionButton', $actionbutton->id], 'method' => 'DELETE']) }}
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
<div id="addActionModalLabel" tabindex="-1" role="dialog" aria-labelledby="addActionModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        {!! Form::open(['route' => 'tables.store', 'method' => 'post']) !!}
        <div class="modal-header">
          <h5  class="modal-title">Add Action Button</h5>
          <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
          <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
          <form>
          	<div class="row">
          		<div class="col-md-12 form-group">
	                <label>Notifiction Title *</label>
	                {{Form::text('name', null, array('required' => 'required', 'id' => 'add_notification_title', 'class' => 'form-control', 'placeholder' => ''))}}
	            </div>
	            <div class="col-md-6 form-group">
	                <label>Icon</label>
                    <div class="row action-btn-icons-list">
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-envelope"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-heart"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-phone"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-music"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-paperclip"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-bell"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-star"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-home"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-plane"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-gift"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-fire"></i></button></div>
                        <div class="action-btn-icon col-md-2"><button type="button"><i class="fa fa-globe"></i></button></div>
                    </div>
	            </div>
                <div class="col-md-6 form-group">
	                <label class="ml-3">Color</label>
	                <div class="d-flex action-btn-colors-list">
                        <div class="action-btn-color col-md-2"><button type="button"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button"></button></div>
                    </div>
	            </div>
	            <input type="hidden" name="_token" id="csrf_token" value="<?php echo csrf_token(); ?>">
          	</div>
            <div class="form-group">
              <input type="button" id="add_action_button" value="{{trans('file.submit')}}" class="btn btn-primary float-right mb-4">
            </div>
          </form>
        </div>
        {{ Form::close() }}
      </div>
    </div>
</div>
<!-- Edit Modal -->
<div id="editActionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
  <div role="document" class="modal-dialog">
    <div class="modal-content">
        {{ Form::open(['route' => ['tables.update', 1], 'method' => 'PUT']) }}
      <div class="modal-header">
        <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Table')}}</h5>
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
      </div>
      <div class="modal-body">
        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
        <form>
          	<div class="row">
          		<div class="col-md-12 form-group">
	                <label>Notifiction Title *</label>
	                {{Form::text('name', null, array('required' => 'required', 'id' => 'edit_notification_title', 'class' => 'form-control', 'placeholder' => ''))}}
	            </div>
	            <div class="col-md-6 form-group">
	                <label>Icon</label>
                    <div class="row action-btn-icons-list">
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-envelope"><i class="fa fa-envelope"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-heart"><i class="fa fa-heart"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-phone"><i class="fa fa-phone"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-music"><i class="fa fa-music"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-paperclip"><i class="fa fa-paperclip"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-bell"><i class="fa fa-bell"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-star"><i class="fa fa-star"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-home"><i class="fa fa-home"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-plane"><i class="fa fa-plane"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-gift"><i class="fa fa-gift"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-fire"><i class="fa fa-fire"></i></button></div>
                        <div class="action-btn-icon col-md-2" ><button type="button" data-buttonIcon="fa fa-globe"><i class="fa fa-globe"></i></button></div>
                    </div>
	            </div>
                <div class="col-md-6 form-group">
	                <label class="ml-3">Color</label>
	                <div class="d-flex action-btn-colors-list">
                        <div class="action-btn-color col-md-2"><button type="button" data-buttonColor="rgb(47, 181, 243)"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button" data-buttonColor="rgb(216, 47, 243)"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button" data-buttonColor="rgb(240, 79, 79)"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button" data-buttonColor="rgb(48, 214, 50)"></button></div>
                        <div class="action-btn-color col-md-2"><button type="button" data-buttonColor="rgb(223, 221, 30)"></button></div>
                    </div>
	            </div>
          	</div>
            <div class="form-group">
              <input type="button" id="edit_action_button" value="{{trans('file.submit')}}" class="btn btn-primary float-right mb-4">
            </div>
          </form>
      {{ Form::close() }}
    </div>
  </div>
</div>


@endsection

@push('scripts')
    <script type="text/javascript">
        $("ul#dmsetting").siblings('a').attr('aria-expanded','true');
        $("ul#dmsetting").addClass("show");
        $("ul#dmsetting #button-digital-menu").addClass("active");

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
    $(document).ready(function() {
        $('.edit-btn').on('click', function(){
            $("#editActionModal input[name='name']").val($(this).data('name'));
            $("#edit_action_button").attr('data-id', $(this).data('id'));
            var icon = $(this).attr('data-buttonIcon');
            var color = $(this).attr('data-color');

            $('#editActionModal .action-btn-icon button').each(function(index, element) {
                // Get the data-buttonIcon attribute value
                var eachIcon = $(element).attr('data-buttonIcon');
                if (eachIcon == icon) {
                    $(element).addClass('active');
                }
            });

            $('#editActionModal .action-btn-color button').each(function(index, element) {
                // Get the data-buttonIcon attribute value
                var eachColor = $(element).attr('data-buttonColor');

                if (eachColor == color) {
                    console.log('123123');
                    $(element).addClass('active');
                }
            });
        });

        $('.action-btn-icon button').click(function(){
            $('.action-btn-icon button').removeClass('active');
            $(this).addClass('active');
        });

        $('.action-btn-color button').click(function(){
            $('.action-btn-color button').removeClass('active');
            $(this).addClass('active');
        })

        $('.action_button_status').click(function(){
            var dataId = $(this).attr('data-id');
            var status = $(this).prop('checked');

            var formData = {
                status: status
            }

            $.ajax({
                url: "status_action_button/" + dataId,
                method: 'post',
                data:formData,
                dataType: 'json',
                success:function(response){
                    console.log(response);
                }
            })
        })

        $("#add_action_button").click(function(){
            var token = $("#csrf_token").val();
            console.log($('meta[name="csrf-token"]').attr('content'));
            var name = $('#add_notification_title').val();
            var icon = $('.action-btn-icon button.active i').attr('class');
            var color = $('.action-btn-color button.active').css('background-color');
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            let actionlink = '';
            for (let i = 0; i < 6; i++) {
                actionlink += characters.charAt(Math.floor(Math.random() * characters.length));
            }

            var formData = {
                name:name,
                icon:icon,
                color:color,
                actionlink:actionlink
            }

            $.ajax({
                url: "{{ route('setting.addActionButton') }}",
                method: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data:formData,
                dataType: 'json',
                success:function(response){
                    $("#addActionModalLabel").modal("hide");
                    location.reload();
                    // var add_action_button_row = $("<tr>");
                    // add_action_button_row.append("<td>"+ response.add_action_buton.id +"</td>");
                    // add_action_button_row.append("<td>"+ response.add_action_buton.name +"</td>");
                    // add_action_button_row.append("<td><i style='color: black; font-size:18px;' class='"+ response.add_action_buton.icon +"'></i></td>");
                    // add_action_button_row.append("<td><button style='width: 25%; height: 18px; border: none; background-color: "+ response.add_action_buton.color +"'</button></td>");
                    // add_action_button_row.append("<label class='switch' style='margin-top: 12px;'><input type='checkbox' checked class='enable-order-check' name='status' id='action_button_status'><span class='slider'></span></label>");
                    // add_action_button_row.append("<td><div class='btn-group'><button type='button' class='btn btn-default btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>action<span class='caret'></span><span class='sr-only'>Toggle Dropdown</span></button><ul class='dropdown-menu edit-options dropdown-menu-right dropdown-default' user='menu'><li><button type='button' data-id="+ response.add_action_buton.id +" data-name="+ response.add_action_buton.name +" class='edit-btn btn btn-link' data-toggle='modal' data-target='#editActionModal' ><i class='dripicons-document-edit'></i> Edit</button></li><li class='divider'></li><li><button type='submit' class='btn btn-link' onclick='return confirmDelete()'><i class='dripicons-trash'></i> Delete</button></li></ul></div></td>");

                    // $('#action_buttons_table_body').append(add_action_button_row);
                }
            })
        })


        $("#edit_action_button").click(function(){
            var dataId = $('#edit_action_button').attr('data-id');;
            var name = $('#edit_notification_title').val();
            var icon = $('#editActionModal .action-btn-icon button.active i').attr('class');
            var color = $('#editActionModal .action-btn-color button.active').css('background-color');

            var formData = {
                name:name,
                icon:icon,
                color:color
            }

            $.ajax({
                url: "update_action_button/" + dataId,
                method: 'post',
                data:formData,
                dataType: 'json',
                success:function(response){
                    $("#editActionModal").modal("hide");
                    location.reload();
                }
            })
        })
    });

        $('#table-table').DataTable( {
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
                    'targets': [0, 3, 4]
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
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]]
        } );
    </script>
@endpush
