<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use Illuminate\Support\Facades\Gate;


class QuitGroupForm extends Component
{
    public $user_id;
    public $password;
    public $group_data;
    public $showModal = false;
    public $showNextManagerModal = false;


    protected $rules = [
        'password' => ['required'],
    ];

    public function mount()
    {
        $this->group_data = Group::with(['userRoles' => function ($query) {
            $query->wherePivot('role', 50);
        }])->find(session()->get('group_id'));

        // dd($this->group_data);
    }

    public function getListeners()
    {
        return ['showModal' => 'showModal'];
    }

    public function updatedShowNextManagerModal($value)
    {
        if ($value) {
            $this->dispatchBrowserEvent('showNextManagerModal');
        }
    }

    public function showModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showNextManagerModal = false;
        $this->password = '';
        $this->resetErrorBag();
    }

    public function quitGroup()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'パスワードが一致しません。');
            return;
        }

        // パスワードが一致したときの処理
        //管理者権限の場合
        if (Gate::allows('manager', $this->group_data)) {
            $this->showModal = false;
            $this->showNextManagerModal = true;


            $this->group_data = Group::with(['userRoles' => function ($query) {
                $query->wherePivot('role', 50);
            }])->find(session()->get('group_id'));

            // dd($this->group_data);


            // dd($this->showNextManagerModal);
        } else {
            $group_data = Group::find(session()->get('group_id'));
            $group_data->user()->detach(Auth::user());
            $group_data->userRoles()->detach(Auth::user());

            return to_route('index');
        }
    }


    public function quitGroupForManager()
    {
        $this->group_data->userRoles()->updateExistingPivot($this->user_id, ['role' => 10]);


        $this->group_data->user()->detach(Auth::user());
        $this->group_data->userRoles()->detach(Auth::user());

        return to_route('index');
    }

    public function render()
    {
        return view('livewire.quit-group-form');
    }
}
