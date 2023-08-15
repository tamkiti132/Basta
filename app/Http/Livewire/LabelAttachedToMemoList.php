<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;
use App\Models\Memo;

class LabelAttachedToMemoList extends Component
{
    public $labels;
    public $memoId;

    protected $listeners = ['labelSelected' => 'loadLabels'];


    public function mount($memoId)
    {
        $this->memoId = $memoId;
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $memo = Memo::find($this->memoId);  // $memoIdは取得したいメモのID
        $this->labels = $memo->labels;

        // dd($this->labels);
    }

    public function render()
    {
        return view('livewire.label-attached-to-memo-list');
    }
}
