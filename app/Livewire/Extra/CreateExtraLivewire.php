<?php

namespace App\Livewire\Extra;

use App\Models\ExtraCategory;
use App\Models\Extras;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateExtraLivewire extends Component
{
    public $category_name = '', $is_multi = 0;
    public $extra_categories = [0=>[]];
    public function render()
    {
        return view('livewire.extra.create-extra-livewire');
    }

    public function addLine()
    {
        $this->extra_categories[count($this->extra_categories)] = [];
    }

    public function removeLine($index)
    {        
        unset($this->extra_categories[$index]);
        $this->extra_categories = array_values($this->extra_categories);        
    }

    public function submit()
    {
        $this->validate([
            'category_name' => 'required',
            'is_multi' => 'required',
            'extra_categories.*.name' => 'required',
            'extra_categories.*.price' => 'required'
        ]);

        $extraCategory = new ExtraCategory();
        $extraCategory->fill($this->except('extra_categories','_token'));
        $extraCategory->save();

        $extracategories = collect($this->extra_categories)->map(function ($item) {
            return new Extras($item);
        });

        $extraCategory->extras()->saveMany($extracategories);

        $this->reset();

        $this->dispatch('modal-close');
    }
}
