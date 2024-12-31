<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Group_type_report_link;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GroupTopAdmin extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $sortCriteria = 'report';
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_groups = true;
    public $show_suspension_groups = false;


    public function checkSuspension($skip = false)
    {
        // 指定のメソッドの最初でこのメソッドを呼び出すと、利用停止中ユーザーはそのメソッドを利用できない
        if (!$skip && Auth::check() && Auth::user()->suspension_state == 1) {
            abort(403, '利用停止中のため、この機能は利用できません。');
        }
    }


    public function setSortCriteria($sortCriteria)
    {
        $this->sortCriteria = $sortCriteria;

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }


    public function executeSearch()
    {
        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }

    public function deleteGroup($group_id)
    {
        $this->checkSuspension();

        // グループに関連する通報リンクを取得
        $reportLinks = Group_type_report_link::where('group_id', $group_id)->get();

        // 各通報リンクに対して
        foreach ($reportLinks as $link) {
            // 通報レコードを削除
            Report::find($link->report_id)->delete();
            // 通報リンクを削除
            $link->delete();
        }

        // グループを削除
        $group_data = Group::find($group_id);

        $group_data->delete();

        return to_route('admin.group_top');
    }

    public function suspend($group_id)
    {
        $this->checkSuspension();

        $group_data = Group::find($group_id);

        $group_data->suspension_state = 1;
        $group_data->save();

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }

    public function liftSuspend($group_id)
    {
        $this->checkSuspension();

        $group_data = Group::find($group_id);

        $group_data->suspension_state = 0;
        $group_data->save();

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
    }



    public function render()
    {
        $user_data = User::find($this->user_id);


        $user_groups = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
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
            ->withCount(['reports', 'userRoles', 'memos', 'comments'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('groups.name', 'like', '%' . $keyword . '%')
                            ->orWhere('groups.introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->when($this->sortCriteria === 'report', function ($query) {
                $query->orderBy('reports_count', 'desc');
            })
            ->orderBy('name')
            ->get();


        //利用停止中グループ
        $suspension_groups_data = Group::select('id', 'name', 'introduction', 'group_photo_path')
            ->where('suspension_state', 1)
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->withCount(['reports', 'userRoles', 'memos', 'comments'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('groups.name', 'like', '%' . $keyword . '%')
                            ->orWhere('groups.introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->when($this->sortCriteria === 'report', function ($query) {
                $query->orderBy('reports_count', 'desc');
            })
            ->orderBy('name')
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
