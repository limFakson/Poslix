@if($item)
<div>
<form wire:submit.prevent="updateCartLocal">
    <div class="row modal-element">
        <div class="col-md-4 form-group">
            <label>{{ trans('file.Quantity') }}</label>
            <input type="text" name="edit_qty" class="form-control numkey" wire:model="item.qty">
        </div>
        <div class="col-md-4 form-group">
            <label>{{ trans('file.Unit Discount') }}</label>
            <input type="text" name="edit_discount" class="form-control numkey"
                wire:model="item.discount">
        </div>
        <div class="col-md-4 form-group">
            <label>{{ trans('file.Unit Price') }}</label>
            <input type="text" name="edit_unit_price" class="form-control numkey"
                wire:model="item.price" step="any">
        </div>
        <?php
        $tax_name_all[] = 'No Tax';
        $tax_rate_all[] = 0;
        foreach ($lims_tax_list as $tax) {
            $tax_name_all[] = $tax->name;
            $tax_rate_all[] = $tax->rate;
        }
        ?>
        <div class="col-md-4 form-group">
            <label>{{ trans('file.Tax Rate') }}</label>
            <select name="edit_tax_rate" class="form-control selectpicker"
                wire:model="item.tax_rate">
                @foreach ($tax_name_all as $key => $name)
                    <option value="{{ $key }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div id="edit_unit" class="col-md-4 form-group">
            <label>{{ trans('file.Product Unit') }}</label>
            <select name="edit_unit" class="form-control selectpicker">
            </select>
        </div>

        <div class="col-md-4 form-group" id="dvExtras">
        </div>
    </div>
    @if (isset($item['extra_categories']))
        @include('backend.sale.extra-categories', [
            'extraCategories' => $item['extra_categories'],
        ])
    @endif
    <button type="submit"     
    name="update_btn" class="btn btn-primary">{{ trans('file.update') }}</button>
</form>
</div>

@if($parentIndex == $qtyIndex)
@script
<script>    
        $wire.dispatch('open-qty-modal',{{$qtyIndex}});    

        Livewire.on('submit-to-parent',function(itemUpdated){
            alert(itemUpdated);
            $wire.dispatch('update-cart',{ item: itemUpdated })
        });
</script>
@endscript

@endif
@endif