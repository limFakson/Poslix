<br />
<div class="row">
    <div class="col-md-12">
        @foreach ($extraCategories as $extraCategory)
            @if ($extraCategory->is_multi == 1)
                <strong>{{ $extraCategory->category_name }}</strong><br />
                {{-- <div class="col-md-12 form-group">
            <label>{{$extraCategory->category_name}}</label>
            <select name="extra_categories[]" class="form-control" multiple>
                @foreach ($extraCategory->extras as $key => $extra)
                <option value="{{$extra->price}}">{{$extra->name}}</option>
                @endforeach
            </select>
        </div> --}}
                @foreach ($extraCategory->extras as $key => $extra)
                    <div class="form-check-inline">
                        <label class="form-check-label extras">
                            <input type="checkbox" class="form-check-input" value="{{ $extra->id }}"
                                id="items.{{ $qtyIndex }}.extras.{{ $extra->id }}"
                                name="extras[]"
                                wire:model="items.{{$index}}.extras">{{ $extra->name }} -
                            {{ $extra->price }}
                        </label>
                    </div>
                @endforeach
            @else
                <br />
                <strong>{{ $extraCategory->category_name }}</strong><br />
                {{-- <label>{{$extraCategory->category_name}}</label>
            <select name="extra_categories[]" class="form-control">
                @foreach ($extraCategory->extras as $key => $extra)
                <option value="{{$extra->price}}">{{$extra->name}}</option>
                @endforeach
            </select> --}}
                @foreach ($extraCategory->extras as $key => $extra)
                    <div class="form-check">
                        <label class="form-check-label extras">
                            <input type="radio" class="form-check-input"
                                id="items.{{ $qtyIndex }}.extras.{{ $extra->id }}" value="{{ $extra->id }}"
                                name="single_extras[]"
                                wire:model="items.{{$index}}.single_extras">{{ $extra->name }} -
                            {{ $extra->price }}
                        </label>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>
</div>
<br />
