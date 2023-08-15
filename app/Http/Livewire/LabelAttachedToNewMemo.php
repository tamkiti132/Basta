<?php

namespace App\Http\Livewire;

use Livewire\Component;

class LabelAttachedToNewMemo extends Component
{
    public $checked = [];

    protected $listeners = ['labelAdded' => 'loadLabels'];

    public function loadLabels($checked)
    {
        $this->checked = $checked;
    }

    public function render()
    {
        return view('livewire.label-attached-to-new-memo');
    }
}
