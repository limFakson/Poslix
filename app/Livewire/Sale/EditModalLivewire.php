<?php

namespace App\Livewire\Sale;

use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EditModalLivewire extends Component
{    
    public $item;
    public $qtyIndex;
    public $lims_tax_list;
    public $parentIndex;

    public function mount($item,$lims_tax_list, $parentIndex, $qtyIndex = 0)
    {
        //Log::debug($qtyIndex);        
        $this->item = $item;
        $this->lims_tax_list = $lims_tax_list;
        $this->parentIndex  = $parentIndex;
    }

    public function render()
    {
        return view('livewire.sale.edit-modal-livewire');
    }

    public function updateCartLocal()
    {        
        //$this->reset();
        //dd($this->items);
        Log::debug($this->item);
        //$this->dispatch('update-cart',['item' => $this->item, 'index' => $this->qtyIndex]) ;        
        $this->dispatch('submit-to-parent',$this->item);        
    }
}
