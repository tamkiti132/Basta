<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MemberEdit extends Component
{
    use WithPagination;

    public $previous_route;

    public $group_id;
    public $group_data;

    // 各タブの表示状態を管理するプロパティ
    public $show_members = true;
    public $show_block_members = false;


    public function getListeners()
    {
        return [
            'quitGroupMember' => 'resetAllModal',
        ];
    }


    public function mount($group_id)
    {
        $group = Group::find($group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (!$group) {
            abort(404);
        }

        //グループの管理者 and サブ管理者のIDを取得
        $manager_user_ids =
            $group->managerAndSubManagerUser($group_id)->pluck('user_id')->toArray();

        // グループの管理者のIDと　自分のIDが一致しない場合、直前のページにリダイレクト
        if (!in_array(Auth::id(), $manager_user_ids)) {
            session()->flash('error', '対象のグループの管理者 or サブ管理者ではないため、アクセスできません');
            $this->previous_route = url()->previous();
            return redirect($this->previous_route);
        }

        $this->group_id = $group_id;
    }


    public function resetAllModal()
    {
        $this->resetPage('all_not_blocked_users_page');
        $this->resetPage('all_blocked_users_page');
    }


    public function updateRole($user_id, $role)
    {
        $user_data = User::find($user_id);

        if ($this->group_data) {
            $user_data->groupRoles()->updateExistingPivot($this->group_id, ['role' => $role]);
        }
    }


    public function quitUser($selected_user_id)
    {
        $group_data = Group::find($this->group_id);
        $group_data->user()->detach($selected_user_id);

        $this->resetPage('all_not_blocked_users_page');
        $this->resetPage('all_blocked_users_page');
    }


    public function blockMember($user_id)
    {
        $user = User::find($user_id);

        $user->blockedGroup()->syncWithoutDetaching($this->group_id);

        $this->resetPage('all_not_blocked_users_page');
        $this->resetPage('all_blocked_users_page');
    }


    public function liftBlockMember($user_id)
    {
        $user = User::find($user_id);

        $user->blockedGroup()->detach($this->group_id);

        $this->resetPage('all_not_blocked_users_page');
        $this->resetPage('all_blocked_users_page');
    }


    public function render()
    {
        $this->group_data = Group::find($this->group_id);


        $all_not_blocked_users_data = User::with(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }, 'groupRoles' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->whereHas('group', function ($query) {
            $query->where('groups.id', $this->group_id);
        })->whereDoesntHave('blockedGroup', function ($query) {
            $query->where('groups.id', $this->group_id);
        })->join('roles', function ($join) {
            $join->on('users.id', '=', 'roles.user_id')
                ->where('roles.group_id', '=', $this->group_id);
        })->orderBy('roles.role', 'asc') // ここで権限に基づいてソート
            ->orderBy('nickname')
            ->get();


        // dd($all_not_blocked_users_data);


        $all_blocked_users_data = User::with(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }, 'groupRoles' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->whereHas('group', function ($query) {
            $query->where('groups.id', $this->group_id);
        })->whereHas('blockedGroup', function ($query) {
            $query->where('groups.id', $this->group_id);
        })->join('roles', function ($join) {
            $join->on('users.id', '=', 'roles.user_id')
                ->where('roles.group_id', '=', $this->group_id);
        })->orderBy('roles.role', 'asc') // ここで権限に基づいてソート
            ->orderBy('nickname')
            ->get();


        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_not_blocked_users_page');
        $items = $all_not_blocked_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_not_blocked_users_data_paginated = new LengthAwarePaginator($items, count($all_not_blocked_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_not_blocked_users_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_blocked_users_page');
        $items = $all_blocked_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_blocked_users_data_paginated = new LengthAwarePaginator($items, count($all_blocked_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_blocked_users_page'
        ]);

        return view('livewire.member-edit', compact(
            'all_not_blocked_users_data_paginated',
            'all_blocked_users_data_paginated',
        ));
    }
}
