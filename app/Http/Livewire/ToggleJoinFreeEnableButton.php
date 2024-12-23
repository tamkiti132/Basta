<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;

class ToggleJoinFreeEnableButton extends Component
{
    public $isJoinFreeEnabled;
    public $groupId;

    public function mount($groupId)
    {
        $this->groupId = $groupId;
        $group = Group::find($groupId);
        if ($group) {
            $this->isJoinFreeEnabled = $group->isJoinFreeEnabled;
        }
    }

    public function updatedIsJoinFreeEnabled()
    {
        $group = Group::find($this->groupId);
        if ($group) {
            $group->isJoinFreeEnabled = $this->isJoinFreeEnabled;
            $group->save();
        }
    }

    public function render()
    {
        return view('livewire.toggle-join-free-enable-button');
    }
}
