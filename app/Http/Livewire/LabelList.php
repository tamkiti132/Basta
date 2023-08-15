<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;

class LabelList extends Component
{
    public $labels;
    public $selected_labels = [];

    protected $listeners = ['labelUpdated' => 'loadLabels'];

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->get();
    }

    public function toggleLabel($labelId)
    {
        if (in_array($labelId, $this->selected_labels)) {
            $this->selected_labels = array_diff($this->selected_labels, [$labelId]);
        } else {
            $this->selected_labels[] = $labelId;
        }

        $this->emit('filterByLabels', $this->selected_labels);
    }


    public function render()
    {
        return view('livewire.label-list');
    }
}
