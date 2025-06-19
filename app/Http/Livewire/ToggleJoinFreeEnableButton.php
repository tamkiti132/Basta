<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ToggleJoinFreeEnableButton extends Component
{
    public $isJoinFreeEnabled;
    public $group_data;

    public function mount($group_data)
    {
        $this->group_data = $group_data;
        $this->isJoinFreeEnabled = $group_data->isJoinFreeEnabled;
    }

    public function updatedIsJoinFreeEnabled()
    {
        $this->group_data->isJoinFreeEnabled = $this->isJoinFreeEnabled;
        $this->group_data->save();
    }

    public function render()
    {
        return view('livewire.toggle-join-free-enable-button');
    }
}
