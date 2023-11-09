<?php

namespace App\Http\Livewire;

use App\Models\Comment_type_report_link;
use App\Models\Memo_type_report_link;
use App\Models\User;
use App\Models\User_type_report_link;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class UserTopAdmin extends Component
{
    use WithPagination;

    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_user = true;
    public $show_suspended_user = false;


    public function executeSearch()
    {
        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function deleteUser($user_id)
    {
        $user_data = User::find($user_id);
        $user_data->delete();

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
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        // 利用停止されていないユーザー情報一覧取得
        $all_not_suspended_users_data = User::where('suspension_state', 0)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereNull('group_id');
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();
            });



        // 利用停止中のユーザー情報一覧取得
        $all_suspended_users_data = User::where('suspension_state', 1)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereNull('group_id');
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();
            });


        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_not_suspended_users_page');
        $items = $all_not_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_not_suspended_users_data_paginated = new LengthAwarePaginator($items, count($all_not_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_not_suspended_users_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_suspended_users_page');
        $items = $all_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_suspended_users_data_paginated = new LengthAwarePaginator($items, count($all_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_suspended_users_page'
        ]);


        return view('livewire.user-top-admin', compact('all_not_suspended_users_data_paginated', 'all_suspended_users_data_paginated'));
    }
}
