<?php

namespace App\Livewire\Extra;

use App\Models\ExtraCategory;
use Livewire\Component;

class ExtrasLivewire extends Component
{
    public $items = [];
    public function render()
    {
        $this->items = ExtraCategory::all();
        return view('livewire.extra.extras-livewire');
    }

    public function delete($id)
    {        
        $extraCategory = ExtraCategory::find($id);

        $extraCategory->extras()->delete();
        $extraCategory->delete();

        $this->items = ExtraCategory::all();
    }
}
