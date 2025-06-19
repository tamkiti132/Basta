<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GroupJoin extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function joinGroup($group_id)
    {
        $group = Group::find($group_id);

        if ($group->isJoinFreeEnabled) {
            $group->userRoles()->syncWithoutDetaching([
                Auth::id() => ['role' => 100],
            ]);
        } else {
            session()->flash('isNotJoinFreeEnabled', 'このグループへの参加は現在許可されていません。');

            return redirect()->back();
        }

        $this->emit('joinedGroup');

        $this->resetPage();
    }

    public function render()
    {
        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        $all_groups_data = Group::whereDoesntHave('userRoles', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->withCount('userRoles')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('name', 'like', '%'.$keyword.'%')
                            ->orWhere('introduction', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->latest()
            ->get();

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $all_groups_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_groups_data_paginated = new LengthAwarePaginator($items, count($all_groups_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('livewire.group-join', compact('all_groups_data_paginated'));
    }
}
