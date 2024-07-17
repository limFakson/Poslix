<div class="row">
    <div class="col-sm-12">
        <form wire:submit.prevent="submit">
            {{-- Stop trying to control. --}}
            @csrf
            <div class="form-group">
                <label>{{ trans('Extra category name') }} *</label>
                <input id="category_name" wire:model="category_name" name="category_name" type="text" class="form-control"
                    value="">
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="is_multi"
                     wire:model="is_multi">
                <label class="form-check-label" for="is_multi">Multi-Select</label>                
            </div>

            @foreach ($extra_categories as $category)
                <div class="row" wire:key="div-{{$loop->index}}">
                    <div class="col-sm-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" placeholder="Extra Name"
                            wire:model="extra_categories.{{$loop->index}}.name">
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" placeholder="Price"
                            wire:model="extra_categories.{{$loop->index}}.price">
                        </div>
                    </div>
                    <div class="col-sm-2">                        
                        @if($loop->first)
                        <button class="btn btn-success" wire:click="addLine"> + </button>
                        @else                                     
                        <button class="btn btn-danger" wire:click="removeLine({{$loop->index}})"> - </button>                             
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="form-group">
                <input type="submit" wire:loading.attr="disabled" value="{{ trans('file.submit') }}" class="btn btn-primary">
            </div>
        </form>
    </div>
</div>
