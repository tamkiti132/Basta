<?php

namespace App\Http\Livewire;

use App\Models\Label;
use Livewire\Component;

class LabelAttached extends Component
{
    public $labels;

    protected $listeners = [
        'labelSelected' => 'loadTempLabels',
    ];

    public function loadTempLabels($checked)
    {
        $checkedLabels = array_filter($checked);
        $this->labels = Label::whereIn('id', array_keys($checkedLabels))->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.label-attached');
    }
}
