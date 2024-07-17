@extends('landlord.layout.main') @section('content')

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
            {!! Form::open(['route' => 'faq.store', 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.FAQ Section')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        <div id="custom-field">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{trans('file.language')}} *</label>
                                        <select name="language_id" class="form-control">
                                            @foreach($language_all as $language)
                                            <option value="{{$language->id}}">{{$language->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{trans('file.Heading')}} *</label>
                                        <input type="text" name="heading" class="form-control" value="@if($faq_description){{$faq_description->heading}}@endif" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{trans('file.Sub Heading')}} *</label>
                                        <input type="text" name="sub_heading" class="form-control" value="@if($faq_description){{$faq_description->sub_heading}}@endif" required />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h4>FAQ</h4>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-info mar-bot-30" id="custom-btn" type="button">+ {{trans('file.Add Faq')}}</button>
                                <button type="submit" class="btn btn-primary mar-bot-30">{{trans('file.submit')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="faq-table" class="table table-striped">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Question')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody id="tablecontents">
                @if($faqs)
                @foreach($faqs as $key=>$faq)
                <tr class="row1" data-id="{{$faq->id}}">
                    <td>{{$key}}</td>
                    <td>{{ $faq->question }}</td>
                    <td>
                        <button type="button" data-id="{{$faq->id}}" data-question="{{$faq->question}}" data-answer="{{$faq->answer}}" class="edit-btn btn btn-link" data-toggle="modal" data-target="#editModal" ><i class="dripicons-document-edit"></i></button>
                        <form class="d-inline" method="post" action="{{ route('faq.delete', $faq->id) }}">
                        @csrf
                            <button type="submit" class="btn btn-link" onclick="return confirm('{{trans('file.Are you sure you want to delete?')}}')"><i class="dripicons-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- edit Modal -->
    <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('faq.update')}}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
            <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update FAQ')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body">
                <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}</small></p>
                    <form>
                        <div class="form-group">
                            <label><strong>{{trans('file.Question')}} *</strong></label>
                            <input type="text" class="form-control" name="question" required />
                        </div>
                        <div class="form-group">
                            <label><strong>{{trans('file.Answer')}} *</strong></label>
                            <textarea class="form-control" name="answer"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="faq_id">
                            <input type="submit" value="{{trans('file.Submit')}}" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </form>
        </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-faq-menu").addClass("active");

    var lang_id = <?php echo json_encode($lang_id); ?>;
    $("select[name=language_id]").val(lang_id);
    $('.selectpicker').selectpicker('refresh');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-btn').on('click', function(){
        $("#editModal input[name='question']").val($(this).data('question'));
        $("#editModal textarea[name='answer']").html($(this).data('answer'));
        $("#editModal input[name='faq_id']").val($(this).data('id'));
    });
    $('#custom-btn').on('click', function(){
        htmlText = '<div class="row"><div class="col-sm-5"><input type="text" name="question[]" class="form-control" placeholder="{{trans('file.Question')}}"/></div><div class="col-sm-6"><textarea name="answer[]" class="form-control" placeholder="{{trans('file.Answer')}}"></textarea></div><div class="col-sm-1"><span class="input-group-btn delete-fields"><button class="btn btn-danger btn-lg delete-btn" type="button">X</button></span></div></div><hr>';
        $('#custom-field').append(htmlText);
    });

    $(document).on("click", ".delete-btn", function() {
        $(this).parent().parent().parent().next().remove();
        $(this).closest(".row").remove();
    });

    $('#faq-table').DataTable( {
        "order": [],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 2]
            },
            {
                'checkboxes': {
                   'selectRow': true
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi', selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: 'PDF',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                // customize: function(doc) {
                //     for (var i = 1; i < doc.content[1].table.body.length; i++) {
                //         if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                //             var imagehtml = doc.content[1].table.body[i][0].text;
                //             var regex = /<img.*?src=['"](.*?)['"]/;
                //             var src = regex.exec(imagehtml)[1];
                //             var tempImage = new Image();
                //             tempImage.src = src;
                //             var canvas = document.createElement("canvas");
                //             canvas.width = tempImage.width;
                //             canvas.height = tempImage.height;
                //             var ctx = canvas.getContext("2d");
                //             ctx.drawImage(tempImage, 0, 0);
                //             var imagedata = canvas.toDataURL("image/png");
                //             delete doc.content[1].table.body[i][0].text;
                //             doc.content[1].table.body[i][0].image = imagedata;
                //             doc.content[1].table.body[i][0].fit = [30, 30];
                //         }
                //     }
                // },
            },
            {
                extend: 'csv',
                text: 'CSV',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') !== -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'print',
                text: 'Print',
                exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                }
            },
            {
                extend: 'colvis',
                text: 'Column visibility',
                columns: ':gt(0)'
            },
        ],
    } );

    $( "#tablecontents" ).sortable({
      items: "tr",
      cursor: 'move',
      opacity: 0.6,
      update: function() {
          sendOrderToServer();
      }
    });

    function sendOrderToServer() {
        var order = [];
        $('tr.row1').each(function(index,element) {
          order.push({
            id: $(this).attr('data-id'),
            position: index+1
          });
        });

        $.ajax({
          type: "POST",
          url: "{{ url('superadmin/faq-section/sort') }}",
          data: {
            order:order
          },
          success: function(response) {
              alert(response);
          }
        });

    }
</script>
@endpush
