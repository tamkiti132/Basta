<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MemberEdit extends Component
{
    use WithPagination;

    public $previous_route;

    public $group_id;
    public $group_data;

    public $is_manager = false;

    public function getListeners()
    {
        return [
            'quitGroupMember' => 'resetAllModal',
            'updateRole' => 'updateRole',
        ];
    }

    public function mount($group_id)
    {
        $group = Group::find($group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (! $group) {
            abort(404);
        }

        // グループの管理者 and サブ管理者のIDを取得
        $manager_user_ids =
            $group->managerAndSubManagerUser($group_id)->pluck('user_id')->toArray();

        // グループの管理者のIDと　自分のIDが一致しない場合、直前のページにリダイレクト
        if (! in_array(Auth::id(), $manager_user_ids)) {
            session()->flash('error', '対象のグループの管理者 or サブ管理者ではないため、アクセスできません');
            $this->previous_route = url()->previous();

            return redirect($this->previous_route);
        }

        $this->group_id = $group_id;
        $this->group_data = $group;

        // 自分が管理者かどうかをチェック
        $this->is_manager = auth()->user()->can('manager', $this->group_data);

        $this->checkSuspensionGroup();
    }

    public function checkSuspensionGroup()
    {
        // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
        if ($this->group_data && $this->group_data->suspension_state == 1) {
            session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません。');

            $this->previous_route = url()->previous();

            return redirect($this->previous_route);
        }
    }

    public function resetAllModal()
    {
        $this->resetPage('all_not_blocked_users_page');
        $this->resetPage('all_blocked_users_page');
    }

    public function checkUpdateRole($user_id, $role)
    {
        // 管理者を変更しようとした場合に確認メッセージを出す（イベントを発行して、ビュー側で確認メッセージを出す）
        if ($role == 10) {
            $this->emit('checkUpdateRole', $user_id, $role);
        } else {
            $this->updateRole($user_id, $role);
        }
    }

    public function updateRole($user_id, $role)
    {
        $user_data = User::find($user_id);

        if ($this->group_data) {
            $user_data->groupRoles()->updateExistingPivot($this->group_id, ['role' => $role]);
        }

        if ($role == 10) {
            // 自分自身のユーザーデータを取得
            $self_user_data = User::find(Auth::id());
            // 自分自身の権限をサブ管理者に変更する
            $self_user_data->groupRoles()->updateExistingPivot($this->group_id, ['role' => 50]);

            // グループトップページにリダイレクト
            return redirect()->route('group.index', ['group_id' => $this->group_id]);
        }
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
    }

    public function render()
    {
        $all_not_blocked_users_data = User::with(['groupRoles' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->whereDoesntHave('blockedGroup', function ($query) {
            $query->where('groups.id', $this->group_id);
        })->join('roles', function ($join) {
            $join->on('users.id', '=', 'roles.user_id')
                ->where('roles.group_id', '=', $this->group_id);
        })->orderBy('roles.role', 'asc') // ここで権限に基づいてソート
            ->orderBy('nickname')
            ->get();

        $all_blocked_users_data = User::with(['groupRoles' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', $this->group_id);
        }])->whereHas('blockedGroup', function ($query) {
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
            'pageName' => 'all_not_blocked_users_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_blocked_users_page');
        $items = $all_blocked_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_blocked_users_data_paginated = new LengthAwarePaginator($items, count($all_blocked_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_blocked_users_page',
        ]);

        return view('livewire.member-edit', compact(
            'all_not_blocked_users_data_paginated',
            'all_blocked_users_data_paginated',
        ));
    }
}
