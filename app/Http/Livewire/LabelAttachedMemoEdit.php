<?php

namespace App\Http\Livewire;

use App\Models\Memo;
use App\Http\Livewire\LabelAttached;

class LabelAttachedMemoEdit extends LabelAttached
{
    public $memoId;

    public function getListeners()
    {
        return $this->listeners + [
            'labelSelected' => 'loadLabels',
        ];
    }


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

    public function render()
    {
        return view('livewire.label-attached-memo-edit');
    }
}
