<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;

class ToggleTipEnableButton extends Component
{
    public $isTipEnabled;
    public $groupId;

    public function mount($groupId)
    {
        $this->groupId = $groupId;
        $group = Group::find($groupId);
        if ($group) {
            $this->isTipEnabled = $group->isTipEnabled;
        }
    }

    public function render()
    {
        return view('livewire.toggle-tip-enable-button');
    }

    public function updatedIsTipEnabled()
    {
        $group = Group::find($this->groupId);
        if ($group) {
            $group->isTipEnabled = $this->isTipEnabled;
            $group->save();
        }
    }
}
