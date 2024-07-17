@extends('backend.layout.main') @section('content')
    @if ($errors->has('name'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('name') }}</div>
    @endif
    @if ($errors->has('image'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert"
                aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ $errors->first('image') }}</div>
    @endif
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif
    <section>
        <div class="container-fluid">
            <!-- Trigger the modal with a button -->
            @if (in_array('products-add', $all_permission))
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#extra-modal"><i
                        class="dripicons-plus"></i> {{ trans('Add Extra') }}</button>&nbsp;
            @endif
        </div>
        @livewire('extra.extras-livewire')
        {{-- <div class="table-responsive">
            <table id="category-table" class="table" style="width: 100%">
                <thead>
                    <tr>
                        <th class="not-exported"></th>
                        <th>{{ trans('file.name') }}</th>

                        <th class="not-exported">{{ trans('file.action') }}</th>
                    </tr>
                </thead>
            </table>
        </div> --}}
    </section>
@endsection
@push('scripts')
    <script type="text/javascript">
        Livewire.on('modal-close', function() {
            $('.modal').hide();
            window.location.reload();
        });

        var logoUrl = <?php echo json_encode(url('logo', $general_setting->site_logo)) ?>;

        document.addEventListener('livewire:init', function() {
            $('#extras-table').DataTable({
                responsive: true,
                fixedHeader: {
                    header: true,
                    footer: true
                },
                "processing": false,
                "serverSide": false,
                'select': {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                'lengthMenu': [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: '<"row"lfB>rtip',
                buttons: [{
                        extend: 'pdf',
                        text: '<i title="export to pdf" class="fa fa-file-pdf-o"></i>',
                        exportOptions: {
                            columns: ':visible:not(.not-exported)',
                            rows: ':visible',
                            stripHtml: false
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i title="export to excel" class="dripicons-document-new"></i>',
                        exportOptions: {
                            columns: ':visible:not(.not-exported)',
                            rows: ':visible',
                            format: {
                                body: function(data, row, column, node) {
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
                        extend: 'csv',
                        text: '<i title="export to csv" class="fa fa-file-text-o"></i>',
                        exportOptions: {
                            columns: ':visible:not(.not-exported)',
                            rows: ':visible',
                            format: {
                                body: function(data, row, column, node) {
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
                        title: '',
                        text: '<i title="print" class="fa fa-print"></i>',
                        exportOptions: {
                            columns: ':visible:not(.not-exported)',
                            rows: ':visible',
                            stripHtml: false
                        },
                        repeatingHead: {
                            logo: logoUrl,
                            logoPosition: 'left',
                            logoStyle: '',
                            title: '<h3>Product List</h3>'
                        }
                        /*customize: function ( win ) {
                            $(win.document.body)
                                .prepend(
                                    '<img src="http://datatables.net/media/images/logo-fade.png" style="margin:10px;" />'
                                );
                        }*/
                    },                    
                    {
                        extend: 'colvis',
                        text: '<i title="column visibility" class="fa fa-eye"></i>',
                        columns: ':gt(0)'
                    },
                ],
            });
        });

        $("ul#product").siblings('a').attr('aria-expanded', 'true');
        $("ul#product").addClass("show");
        $("ul#product #category-menu").addClass("active");

        function confirmDelete() {
            if (confirm(
                    "If you delete category all products under this category will also be deleted. Are you sure want to delete?"
                )) {
                return true;
            }
            return false;
        }

        var category_id = [];
        var user_verified = <?php echo json_encode(env('USER_VERIFIED')); ?>;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // $(document).on("click", ".open-EditCategoryDialog", function() {
        //     $("#editModal input[name='is_sync_disable']").prop("checked", false);
        //     $("#editModal input[name='featured']").prop("checked", false);
        //     var url = "category/";
        //     var id = $(this).data('id').toString();
        //     url = url.concat(id).concat("/edit");
        //     $.get(url, function(data) {
        //         $("#editModal input[name='name']").val(data['name']);
        //         $("#editModal select[name='parent_id']").val(data['parent_id']);
        //         $("#editModal input[name='category_id']").val(data['id']);
        //         if (data['is_sync_disable']) {
        //             $("#editModal input[name='is_sync_disable']").prop("checked", true);
        //         }
        //         if (data['featured']) {
        //             $("#editModal input[name='featured']").prop("checked", true);
        //         }
        //         $("#editModal input[name='page_title']").val(data['page_title']);
        //         $("#editModal input[name='short_description']").val(data['short_description']);
        //         $('.selectpicker').selectpicker('refresh');
        //     });
        // });

        // $(document).ready(function() {
        //     datatabletable();
        //     debugger

        //     function datatabletable() {
        //         $('#category-table').DataTable({


        //         });
        //     }

        // });
    </script>
@endpush
