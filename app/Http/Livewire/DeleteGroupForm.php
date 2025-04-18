<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;

class DeleteGroupForm extends Component
{
    public $password;
    public $group_data;
    public $showModal = false;

    protected $rules = [
        'password' => ['required', 'current_password'],
    ];

    public function mount()
    {
        $this->group_data = Group::find(session()->get('group_id'));
    }

    public function deleteGroup()
    {
        $this->validate();

        // グループを削除
        $group_data = Group::find(session()->get('group_id'));
        $group_data->delete();

        return to_route('index');
    }

    public function render()
    {
        return view('livewire.delete-group-form');
    }

    public function getListeners()
    {
        return ['showModal' => 'showModal'];
    }

    public function showModal()
    {
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->password = '';
        $this->resetErrorBag();
    }
}
