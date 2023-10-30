<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Label;
use Illuminate\Pagination\LengthAwarePaginator;

class MemoList extends Component
{
    use WithPagination;

    public $group_id;
    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

    public $isSuspended;


    protected $listeners = [
        'filterByWebBookLabels',
        'filterByLabels'
    ];

    public function mount($group_id)
    {
        $this->group_id = $group_id;
    }


    public function filterByWebBookLabels($selected_web_book_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_web_book_labels = $selected_web_book_labels;
    }

    public function filterByLabels($selected_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_labels = $selected_labels;
    }

    public function executeSearch()
    {
        $this->resetPage();
    }

    public function deleteGroup($group_id)
    {
        $group_data = Group::find($group_id);
        $group_data->delete();

        return to_route('index');
    }

    public function suspendGroup()
    {
        $user_data = Group::find($this->group_id);

        $user_data->suspension_state = 1;
        $user_data->save();
    }

    public function liftSuspendGroup()
    {
        $user_data = Group::find($this->group_id);

        $user_data->suspension_state = 0;
        $user_data->save();
    }

    public function render()
    {
        $group_data = Group::find($this->group_id);
        session()->put('group_id', $this->group_id);

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $this->isSuspended = $group_data->suspension_state;


        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);



        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('group_id', session()->get('group_id'))
                ->when($this->selected_labels, function ($query) { // 選択されたラベルがある場合のみフィルタリング
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
                })
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('memos.title', 'like', '%' . $keyword . '%')
                                ->orWhere('memos.shortMemo', 'like', '%' . $keyword . '%');
                        });
                    }
                })

                ->get();
        }

        if (in_array('book', $this->selected_web_book_labels)) {
            $book_memos_data = Memo::with('labels')
                ->leftJoin('book_type_features', function ($join) {
                    $join->on('memos.id', '=', 'book_type_features.memo_id');
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->where('group_id', session()->get('group_id'))
                ->when($this->selected_labels, function ($query) { // 選択されたラベルがある場合のみフィルタリング
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
                })
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('memos.title', 'like', '%' . $keyword . '%')
                                ->orWhere('memos.shortMemo', 'like', '%' . $keyword . '%');
                        });
                    }
                })
                ->get();
        }

        $all_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();
        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = array_slice($all_memos_data, ($page - 1) * $perPage, $perPage);
        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('livewire.memo-list', compact('group_data', 'all_memos_data_paginated'));
    }
}
