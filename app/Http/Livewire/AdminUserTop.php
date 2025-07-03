<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUserTop extends Component
{
    use WithPagination;

    public $all_not_suspended_users_data;
    public $all_suspended_users_data;
    public $search = '';

    // 各タブのページネーションを管理するプロパティ
    public $show_users_pagination = false;
    public $show_suspension_users_pagination = false;

    public function updatingSearch()
    {
        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }

    public function deleteUser($user_id)
    {
        User::find($user_id)->delete();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }

    public function suspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 1;
        $user_data->save();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }

    public function liftSuspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 0;
        $user_data->save();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }

    public function render()
    {
        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        // ユーザー
        $this->all_not_suspended_users_data = User::where('suspension_state', 0)
            ->whereHas('roles', function ($query) {
                $query->where('role', '=', 5);
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%'.$keyword.'%')
                            ->orWhere('users.username', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->orderBy('nickname')
            ->get();

        // 利用停止中ユーザー
        $this->all_suspended_users_data = User::where('suspension_state', 1)
            ->whereHas('roles', function ($query) {
                $query->where('role', '=', 5);
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%'.$keyword.'%')
                            ->orWhere('users.username', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->orderBy('nickname')
            ->get();

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_not_suspended_users_page');
        $items = $this->all_not_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_not_suspended_users_data_paginated = new LengthAwarePaginator($items, count($this->all_not_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_not_suspended_users_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_suspended_users_page');
        $items = $this->all_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_suspended_users_data_paginated = new LengthAwarePaginator($items, count($this->all_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_suspended_users_page',
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
