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
    public $selectedUserId;
    public $password;
    public $group_data;
    public $showModal = false;
    public $showNextManagerModal = false;
    public $showModalNobodySubManager = false;
    public $showModalFinalConfirmation = false;


    protected $rules = [
        'password' => ['required'],
    ];


    public function mount()
    {
        $this->group_data = Group::with(['userRoles' => function ($query) {
            $query
                ->wherePivot('role', 50)
                ->orderBy('nickname');
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
        $this->selectedUserId = '';
        $this->showModal = false;
        $this->showNextManagerModal = false;
        $this->showModalNobodySubManager = false;
        $this->showModalFinalConfirmation = false;
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

        $this->password = "";

        // パスワードが一致したときの処理
        if (Gate::allows('manager', $this->group_data)) {
            //管理者権限の場合

            // グループのデータ（サブ管理者のデータも併せて取得）
            $this->group_data = Group::with(['userRoles' => function ($query) {
                $query->wherePivot('role', 50);
            }])->find(session()->get('group_id'));


            if ($this->group_data->userRoles->isNotEmpty()) {
                //サブ管理者がいる場合                

                // →「サブ管理者から次の管理者を選択するためのモーダル」を表示する
                $this->showModal = false;
                $this->showNextManagerModal = true;
            } else {
                //サブ管理者がいない場合

                // →「サブ管理者いませんよモーダル」を表示する
                $this->showModal = false;
                $this->showModalNobodySubManager = true;
            }
        } else {
            // 管理者以外の権限の場合
            $group_data = Group::find(session()->get('group_id'));
            $group_data->user()->detach(Auth::user());
            $group_data->userRoles()->detach(Auth::user());

            return to_route('index');
        }
    }


    public function quitGroupForManager()
    {
        if ($this->selectedUserId) {
            // サブ管理者を選択する場合

            // →選択したサブ管理者を管理者にして自分はグループを退会する
            $this->group_data->userRoles()->updateExistingPivot($this->selectedUserId, ['role' => 10]);

            $this->group_data->user()->detach(Auth::user());
            $this->group_data->userRoles()->detach(Auth::user());

            return to_route('index');
        } else {
            // サブ管理者を選択しない場合

            $this->showNextManagerModal = false;
            $this->showModalFinalConfirmation = true;
        }
    }


    public function deleteGroup()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'パスワードが一致しません。');
            return;
        }

        // →グループを削除する
        $group_data = Group::find(session()->get('group_id'));
        $group_data->delete();

        return to_route('index');
    }


    public function quitGroupWhenNobodySubManager()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'パスワードが一致しません。');
            return;
        }


        // →グループを削除する
        $group_data = Group::find(session()->get('group_id'));
        $group_data->delete();

        return to_route('index');
    }


    public function render()
    {
        // foreach ($this->group_data->userRoles as $user_data) {
        //     dd($user_data->nickname);
        // }
        return view('livewire.quit-group-form');
    }
}
