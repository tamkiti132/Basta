<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Label;
use App\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;

class MemoListMember extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

    public $isSuspended;


    protected $listeners = [
        'filterByWebBookLabels',
        'filterByLabels',
        'labelUpdated',
    ];


    public function mount($id)
    {
        $this->user_id = $id;
        $this->group_id = session()->get('group_id');
    }

    public function filterByWebBookLabels($selected_web_book_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_web_book_labels = $selected_web_book_labels;

        $this->resetPage();
    }

    public function filterByLabels($selected_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_labels = $selected_labels;

        $this->resetPage();
    }

    public function labelUpdated($label_id = null)
    {
        if ($label_id) {
            // $label_id の値のキーを検索
            $key = array_search($label_id, $this->selected_labels);
            // dd($key);

            // 値が見つかった場合、そのキーを使用して値を削除
            if ($key !== false) {
                unset($this->selected_labels[$key]);
            }
        }

        $this->resetPage();
    }

    public function executeSearch()
    {
        $this->resetPage();
    }

    public function deleteUser()
    {
        $user_data = User::find($this->user_id);
        $user_data->delete();

        return to_route('admin.user_top.index');
    }

    public function suspendUser()
    {
        $user_data = User::find($this->user_id);

        $user_data->suspension_state = 1;
        $user_data->save();
    }

    public function liftSuspendUser()
    {
        $user_data = User::find($this->user_id);

        $user_data->suspension_state = 0;
        $user_data->save();
    }

    public function render()
    {
        $group_data = Group::find($this->group_id);
        $user_data = User::find($this->user_id);

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $this->isSuspended = $user_data->suspension_state;


        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('groups', 'memos.group_id', '=', 'groups.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $this->user_id)
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
                    $join->on(
                        'memos.id',
                        '=',
                        'book_type_features.memo_id'
                    );
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('groups', 'memos.group_id', '=', 'groups.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $this->user_id)
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
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = array_slice($all_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        $count_all_memos_data = count($all_memos_data);

        // 退会済みか確認
        $exists = Group::where('id', session()->get('group_id'))->whereHas('user', function ($query) {
            $query->where('id', $this->user_id);
        })->exists();

        if (!$exists) {
            session()->flash('quit', 'このユーザーはグループを退会済みです。');
        }

        return view('livewire.memo-list-member', compact('group_data', 'user_data', 'count_all_memos_data', 'all_memos_data_paginated'));
    }
}
