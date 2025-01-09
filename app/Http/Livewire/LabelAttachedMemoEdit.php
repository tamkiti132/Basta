<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;
use App\Models\Memo;

class LabelAttachedMemoEdit extends Component
{
    public $labels;
    public $memoId;

    protected $listeners = ['labelSelected' => 'loadLabels', 'labelSelected' => 'loadTempLabels'];


    public function mount($memoId)
    {
        $this->memoId = $memoId;
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $memo = Memo::find($this->memoId);  // $memoIdは取得したいメモのID
        $this->labels = $memo->labels;
    }

    public function loadTempLabels($checked)
    {
        $checkedLabels = array_filter($checked);
        $this->labels = Label::whereIn('id', array_keys($checkedLabels))->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.label-attached-memo-edit');
    }
}
