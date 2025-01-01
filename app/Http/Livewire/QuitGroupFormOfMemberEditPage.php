<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Gate;

class QuitGroupFormOfMemberEditPage extends Component
{
    public $user_id;
    public $password;
    public $group_data;
    public $showMemberQuitModal = false;


    protected $rules = [
        'password' => ['required', 'current_password'],
    ];


    public function mount()
    {
        $this->group_data = Group::with(['userRoles' => function ($query) {
            $query->wherePivot('role', 50);
        }])->find(session()->get('group_id'));
    }


    public function getListeners()
    {
        return ['showMemberQuitModal' => 'showMemberQuitModal'];
    }


    public function showMemberQuitModal($user_id)
    {
        $this->showMemberQuitModal = true;

        $this->user_id = $user_id;
    }


    public function closeModal()
    {
        $this->showMemberQuitModal = false;
        $this->password = '';
        $this->resetErrorBag();
    }


    public function quitGroup()
    {
        // パスワードのバリデーション
        $this->validate();

        $group_data = Group::find(session()->get('group_id'));
        $group_data->userRoles()->detach($this->user_id);

        $this->closeModal();

        $this->emit('quitGroupMember');
    }


    public function render()
    {
        return view('livewire.quit-group-form-of-member-edit-page');
    }
}
