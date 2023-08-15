<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DeleteGroupForm extends Component
{
    public $password;
    public $group_data;
    public $showModal = false;

    protected $rules = [
        'password' => ['required'],
    ];

    public function mount()
    {
        $this->group_data = Group::find(session()->get('group_id'));
    }

    public function deleteGroup()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'パスワードが一致しません。');
            return;
        }

        // パスワードが一致したときの処理
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
