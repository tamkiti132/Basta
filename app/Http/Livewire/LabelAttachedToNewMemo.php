<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;

class LabelAttachedToNewMemo extends Component
{
    public $labels;
    public $memoId;

    protected $listeners = [
        'labelSelected' => 'loadTempLabels'
    ];



    public function loadTempLabels($checked)
    {
        $checkedLabels = array_filter($checked);
        $this->labels = Label::whereIn('id', array_keys($checkedLabels))->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.label-attached-to-new-memo');
    }
}
