<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;

class QuitGroupFormOfMemberEditPage extends Component
{
    public $user_id;
    public $password;
    public $group_data;
    public $showMemberQuitModal = false;


    protected $rules = [
        'password' => ['required', 'current_password'],
    ];


    public function mount($group_data)
    {
        $this->group_data = $group_data;
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

        $this->group_data->userRoles()->detach($this->user_id);

        $this->closeModal();

        $this->emit('quitGroupMember');
    }


    public function render()
    {
        return view('livewire.quit-group-form-of-member-edit-page');
    }
}
