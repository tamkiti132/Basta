<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;

class LabelAdder extends Component
{
    public $labels;
    public $checked = [];
    public $labelsData = [];

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->get();
    }

    public function updateChecked($labelId, $labelName)
    {
        if (!isset($this->checked[$labelId])) {
            // このラベルが初めてチェックされた場合、IDと名前を保存する
            $this->checked[$labelId] = ['id' => $labelId, 'name' => $labelName];
        } else {
            // このラベルのチェックが外された場合、配列から削除する
            unset($this->checked[$labelId]);
        }

        $this->emit('labelAdded', $this->checked);
    }

    public function render()
    {
        return view('livewire.label-adder');
    }
}
