<div class="search-box form-group">
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <input type="text" name="product_code_name" id="lims_productcodeSearch" wire:model="searchTerm"
        id="lims_productcodeSearch" placeholder="Scan/Search product by name/code" class="form-control" />
</div>

@script
    <script>
        //console.log(@json($searchResults));
        $('#lims_productcodeSearch').autocomplete({            
            source: @json($searchResults),
            minLength: 2,
            select: function(event, ui) {
                //console.log(event);
                //console.log(ui.item);
                //console.log(ui.item.object);
                $wire.dispatch('addToCart', {
                    product: ui.item
                });
                ui.item.value = '';
                $(this).autocomplete("close");
            }
        }).autocomplete('instance')._renderItem = function(ul, item) {
            //console.log(item);            
            return $('<li>')
                .append('<div>' + item.label +' </div>')
                .appendTo(ul);
        };;
    </script>
@endscript
