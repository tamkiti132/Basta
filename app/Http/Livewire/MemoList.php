<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Memo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MemoList extends Component
{
    use WithPagination;

    public $previous_route;

    public $group_id;
    public $group_data;
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
        'labelDeleted',
    ];

    public function mount($group_id)
    {
        $this->previous_route = url()->previous();

        $this->group_data = Group::find($group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (! $this->group_data) {
            abort(404);
        }

        // 運営ユーザー以上の権限を持つユーザーは常にアクセス可能
        if (! Auth::user()->can('admin-higher')) {
            // 指定のグループに自分が所属していない場合、直前のページにリダイレクト
            if (! (Auth::user()->groupRoles()->where('group_id', $group_id)->exists())) {
                session()->flash('error', '対象のグループに所属していないため、アクセスできません');

                return redirect($this->previous_route);
            }
        }

        $this->group_id = $group_id;
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
            dd($label_id);
            $key = array_search($label_id, $this->selected_labels);

            // 値が見つかった場合、そのキーを使用して値を削除
            if ($key !== false) {
                unset($this->selected_labels[$key]);
            }
        }
    }

    public function labelDeleted($label_id = null)
    {
        if ($label_id) {
            // $label_id の値のキーを検索
            $key = array_search($label_id, $this->selected_labels);

            // 値が見つかった場合、そのキーを使用して値を削除
            if ($key !== false) {
                unset($this->selected_labels[$key]);
            }
        }

        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteGroup($group_id)
    {
        // グループを削除
        $group_data = Group::find($group_id);
        $group_data->delete();

        return to_route('admin.group_top');
    }

    public function suspendGroup()
    {
        $this->group_data->suspension_state = 1;
        $this->group_data->save();
    }

    public function liftSuspendGroup()
    {
        $this->group_data->suspension_state = 0;
        $this->group_data->save();
    }

    public function render()
    {
        $group_data = $this->group_data;
        session()->put('group_id', $this->group_id);

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $this->isSuspended = $group_data->suspension_state;

        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'web_type_feature'])
                ->where('group_id', session()->get('group_id'))
                ->when($this->selected_labels, function ($query) { // 選択されたラベルがある場合のみフィルタリング
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
                })
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('title', 'like', '%'.$keyword.'%')
                                ->orWhere('shortMemo', 'like', '%'.$keyword.'%');
                        });
                    }
                })
                ->where('type', 0)
                ->get();
        }

        if (in_array('book', $this->selected_web_book_labels)) {
            $book_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'book_type_feature'])
                ->where('group_id', session()->get('group_id'))
                ->when($this->selected_labels, function ($query) { // 選択されたラベルがある場合のみフィルタリング
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
                })
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('title', 'like', '%'.$keyword.'%')
                                ->orWhere('shortMemo', 'like', '%'.$keyword.'%');
                        });
                    }
                })
                ->where('type', 1)
                ->get();
        }

        $all_memos_data = $web_memos_data->concat($book_memos_data)->sortByDesc('created_at')->values();

        // N+1対策: いいね・あとで読むIDを一括取得
        $all_memo_ids = $all_memos_data->pluck('id');
        $goodMemoIds = DB::table('goods')
            ->where('user_id', Auth::id())
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');
        $laterReadMemoIds = DB::table('later_reads')
            ->where('user_id', Auth::id())
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');

        $perPage = 20;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $items = $all_memos_data->slice(($page - 1) * $perPage, $perPage);
        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return view('livewire.memo-list', compact(
            'group_data',
            'all_memos_data_paginated',
            'goodMemoIds',
            'laterReadMemoIds',
        ));
    }
}
