<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminUserTop extends Component
{
    use WithPagination;

    public $all_not_suspended_users_data;
    public $all_suspended_users_data;
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_users = true;
    public $show_suspension_users = false;



    public function mount()
    {
    }


    public function showMember()
    {
        $this->show_group_reports = false;
        $this->show_members = true;

        if ($this->show_users) {
            $this->show_users_pagination = true;
        }

        if ($this->show_suspension_users) {
            $this->show_suspension_users_pagination = true;
        }
    }


    public function executeSearch()
    {
        $this->resetPage();
    }


    public function deleteUser($user_id)
    {
        User::find($user_id)->delete();
    }

    public function suspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 1;
        $user_data->save();
    }

    public function liftSuspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 0;
        $user_data->save();
    }



    public function render()
    {

        //ユーザー
        $this->all_not_suspended_users_data = User::where('suspension_state', 0)
            ->whereHas('roles', function ($query) {
                $query->where('role', '=', 5);
            })
            ->where(function ($query) {
                $query->where('users.nickname', 'like', '%' . $this->search . '%')
                    ->orWhere('users.username', 'like', '%' . $this->search . '%');
            })
            ->get();


        //利用停止中ユーザー
        $this->all_suspended_users_data = User::where('suspension_state', 1)
            ->whereHas('roles', function ($query) {
                $query->where('role', '=', 5);
            })
            ->where(function ($query) {
                $query->where('users.nickname', 'like', '%' . $this->search . '%')
                    ->orWhere('users.username', 'like', '%' . $this->search . '%');
            })
            ->get();


        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_not_suspended_users_page');
        $items = $this->all_not_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_not_suspended_users_data_paginated = new LengthAwarePaginator($items, count($this->all_not_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_not_suspended_users_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_suspended_users_page');
        $items = $this->all_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_suspended_users_data_paginated = new LengthAwarePaginator($items, count($this->all_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_suspended_users_page'
        ]);



        return view(
            'livewire.admin-user-top',
            compact(
                'all_not_suspended_users_data_paginated',
                'all_suspended_users_data_paginated',
            )
        );
    }
}
