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
            {!! Form::open(['route' => 'heroSection.store', 'files' => true, 'method' => 'post']) !!}
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Hero Section')}}</h4>
                    </div>
                    <div class="card-body collapse show" id="gs_collapse">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
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
                                    <input type="text" name="heading" class="form-control" value="@if($hero){{$hero->heading}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Sub Heading')}} *</label>
                                    <input type="text" name="sub_heading" class="form-control" value="@if($hero){{$hero->sub_heading}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Button Text')}} *</label>
                                    <input type="text" name="button_text" class="form-control" value="@if($hero){{$hero->button_text}}@endif" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Hero Image')}} *</label>
                                    <input type="file" name="image" class="form-control" required />
                                </div>
                                @if($errors->has('image'))
                                <span>
                                    <strong>{{ $errors->first('image') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" value="{{trans('file.Save')}}" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')

<script>
    $("ul#cms").siblings('a').attr('aria-expanded','true');
    $("ul#cms").addClass("show");
    $("ul#cms #cms-hero-menu").addClass("active");

    var lang_id = <?php echo json_encode($lang_id); ?>;
    $("select[name=language_id]").val(lang_id);
    $('.selectpicker').selectpicker('refresh');   
</script>

@endpush
