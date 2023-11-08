<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupTopAdmin extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $report_reason;
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_groups = true;
    public $show_suspension_groups = false;


    public function mount()
    {
    }


    public function executeSearch()
    {
        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }

    public function deleteGroup($group_id)
    {
        $group_data = Group::find($group_id);

        $group_data->delete();

        return to_route('admin.group_top');
    }

    public function suspend($group_id)
    {
        $group_data = Group::find($group_id);

        $group_data->suspension_state = 1;
        $group_data->save();

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }

    public function liftSuspend($group_id)
    {
        $group_data = Group::find($group_id);

        $group_data->suspension_state = 0;
        $group_data->save();

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }



    public function render()
    {
        $user_data = User::find($this->user_id);


        $user_groups = Group::whereHas('user', function ($query) {
            $query->where('users.id', $this->user_id);
        })->get();


        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        //グループ
        $groups_data = Group::select('id', 'name', 'introduction', 'group_photo_path')
            ->where('suspension_state', 0)
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->withCount(['reports', 'user', 'memos', 'comments'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('groups.name', 'like', '%' . $keyword . '%')
                            ->orWhere('groups.introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get();


        //利用停止中グループ
        $suspension_groups_data = Group::select('id', 'name', 'introduction', 'group_photo_path')
            ->where('suspension_state', 1)
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->withCount(['reports', 'user', 'memos', 'comments'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('groups.name', 'like', '%' . $keyword . '%')
                            ->orWhere('groups.introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get();



        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('groups_page');
        $items = $groups_data->slice(($currentPage - 1) * $perPage, $perPage);
        $groups_data_paginated = new LengthAwarePaginator($items, count($groups_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'groups_page'
        ]);


        $currentPage = LengthAwarePaginator::resolveCurrentPage('suspension_groups_page');
        $items = $suspension_groups_data->slice(($currentPage - 1) * $perPage, $perPage);
        $suspension_groups_data_paginated = new LengthAwarePaginator($items, count($suspension_groups_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'suspension_groups_page'
        ]);



        return view('livewire.group-top-admin', compact(
            'user_groups',
            'groups_data_paginated',
            'suspension_groups_data_paginated',
        ));
    }
}
