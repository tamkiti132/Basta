<?php

namespace App\Http\Livewire;

use App\Http\Livewire\LabelList;
use App\Models\Label;

class LabelListMypage extends LabelList
{
    public $group_id;

    protected $listeners = [
        'labelUpdated' => 'loadLabels',
        'setGroupId',
    ];

    public function setGroupId($group_id = null)
    {
        $this->group_id = $group_id;

        $this->selected_labels = [];

        $this->loadLabels();
    }

    public function loadLabels($labelId = null)
    {
        $this->labels = Label::where('group_id', $this->group_id)->orderBy('name')->get();

        if ($labelId) {
            $this->deleteLabel($labelId);
        }
    }


    public function render()
    {
        return view('livewire.label-list-mypage');
    }
}
