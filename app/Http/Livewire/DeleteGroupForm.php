<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;
use App\Models\Group_type_report_link;
use App\Models\Report;
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

        // 以下、パスワードが一致したときの処理
        // グループに関連する通報リンクを取得
        $reportLinks = Group_type_report_link::where('group_id', session()->get('group_id'))->get();

        // 各通報リンクに対して
        foreach ($reportLinks as $link) {
            // 通報レコードを削除
            Report::find($link->report_id)->delete();
            // 通報リンクを削除
            $link->delete();
        }

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
