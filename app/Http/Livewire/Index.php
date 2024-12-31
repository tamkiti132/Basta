<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination;

    public $search = '';


    public function executeSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        $my_user_id = Auth::id();

        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        $my_groups_data = Group::whereHas('userRoles', function ($query) use ($my_user_id) {
            $query->where('user_id', $my_user_id);
        })
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->withCount('userRoles')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->latest()
            ->get();

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $my_groups_data->slice(($currentPage - 1) * $perPage, $perPage);
        $my_groups_data_paginated = new LengthAwarePaginator($items, count($my_groups_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('livewire.index', compact('my_groups_data_paginated'));
    }
}
