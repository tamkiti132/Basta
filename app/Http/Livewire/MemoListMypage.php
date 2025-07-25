<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Memo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MemoListMypage extends Component
{
    use WithPagination;

    public $previous_route;

    public $user_id;
    public $group_id;
    public $group_data;

    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

    protected $listeners = [
        'setGroupId',
        'filterByWebBookLabels',
        'filterByLabels',
        'labelUpdated',
        'labelDeleted',
    ];

    public function mount($user_id)
    {
        $this->user_id = $user_id;
        $authUserId = auth()->id();

        // リクエストのユーザーIDと　自分のIDが一致しない場合、直前のページにリダイレクト
        if ($authUserId != $this->user_id) {
            session()->flash('error', '他のユーザーのマイページにはアクセスできません');
            $this->previous_route = url()->previous();
            redirect($this->previous_route);
        }

        $this->group_id = '';
    }

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;
        $this->group_data = Group::find($this->group_id);
        $this->selected_labels = [];

        $this->emitTo('label-editor-mypage', 'setGroupId', $this->group_id);
        $this->emitTo('label-list-mypage', 'setGroupId', $this->group_id);

        $this->resetPage('all_my_memos_page');
        $this->resetPage('all_good_memos_page');
        $this->resetPage('all_later_read_memos_page');
    }

    public function filterByWebBookLabels($selected_web_book_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_web_book_labels = $selected_web_book_labels;

        $this->resetPage('all_my_memos_page');
        $this->resetPage('all_good_memos_page');
        $this->resetPage('all_later_read_memos_page');
    }

    public function filterByLabels($selected_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_labels = $selected_labels;

        $this->resetPage('all_my_memos_page');
        $this->resetPage('all_good_memos_page');
        $this->resetPage('all_later_read_memos_page');
    }

    public function labelUpdated($label_id = null)
    {
        if ($label_id) {
            // $label_id の値のキーを検索
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

        $this->resetPage('all_my_memos_page');
        $this->resetPage('all_good_memos_page');
        $this->resetPage('all_later_read_memos_page');
    }

    public function updatingSearch()
    {
        $this->resetPage('all_my_memos_page');
        $this->resetPage('all_good_memos_page');
        $this->resetPage('all_later_read_memos_page');
    }

    public function render()
    {
        $user_groups = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
        })->orderBy('name')
            ->get();

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $good_web_memos_data = collect([]);
        $good_book_memos_data = collect([]);

        $later_read_web_memos_data = collect([]);
        $later_read_book_memos_data = collect([]);

        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        // 自分が作成したメモ
        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'web_type_feature'])
                ->where('user_id', $this->user_id)
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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
                ->where('user_id', $this->user_id)
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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

        // いいねしたメモ
        if (in_array('web', $this->selected_web_book_labels)) {
            $good_web_memos_data = Memo::with([
                'labels',
                'user',
                'goods',
                'laterReads',
                'web_type_feature',
            ])
                ->withCount([
                    'goods as is_good' => function ($q) {
                        $q->where('user_id', $this->user_id);
                    },
                    'laterReads as is_later_read' => function ($q) {
                        $q->where('user_id', $this->user_id);
                    },
                ])
                ->whereHas('goods', function ($query) {
                    $query->where('user_id', $this->user_id);
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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
            $good_book_memos_data = Memo::with([
                'labels',
                'user',
                'goods',
                'laterReads',
                'book_type_feature',
            ])
                ->withCount([
                    'goods as is_good' => function ($q) {
                        $q->where('user_id', $this->user_id);
                    },
                    'laterReads as is_later_read' => function ($q) {
                        $q->where('user_id', $this->user_id);
                    },
                ])
                ->whereHas('goods', function ($query) {
                    $query->where('user_id', $this->user_id);
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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

        // あとでよむしたメモ
        if (in_array('web', $this->selected_web_book_labels)) {
            $later_read_web_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'web_type_feature'])
                ->whereHas('laterReads', function ($query) {
                    $query->where('user_id', $this->user_id);
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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
            $later_read_book_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'book_type_feature'])
                ->whereHas('laterReads', function ($query) {
                    $query->where('user_id', $this->user_id);
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
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

        $all_my_memos_data = $web_memos_data->concat($book_memos_data)->sortByDesc('created_at')->values();
        $all_good_memos_data = $good_web_memos_data->concat($good_book_memos_data)->sortByDesc('created_at')->values();
        $all_later_read_memos_data = $later_read_web_memos_data->concat($later_read_book_memos_data)->sortByDesc('created_at')->values();

        // N+1対策: いいね・あとで読むIDを一括取得
        $all_memo_ids = collect()
            ->concat($all_my_memos_data)
            ->concat($all_good_memos_data)
            ->concat($all_later_read_memos_data)
            ->pluck('id')
            ->unique()
            ->toArray();

        $goodMemoIds = DB::table('goods')
            ->where('user_id', $this->user_id)
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');

        $laterReadMemoIds = DB::table('later_reads')
            ->where('user_id', $this->user_id)
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_my_memos_page');
        $items = $all_my_memos_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_my_memos_data_paginated = new LengthAwarePaginator($items, count($all_my_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_my_memos_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_good_memos_page');
        $items = $all_good_memos_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_good_memos_data_paginated = new LengthAwarePaginator($items, count($all_good_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_good_memos_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_later_read_memos_page');
        $items = $all_later_read_memos_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_later_read_memos_data_paginated = new LengthAwarePaginator($items, count($all_later_read_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_later_read_memos_page',
        ]);

        $count_all_my_memos_data = count($all_my_memos_data);

        return view('livewire.memo-list-mypage', compact(
            'user_groups',
            'all_my_memos_data_paginated',
            'all_good_memos_data_paginated',
            'all_later_read_memos_data_paginated',
            'count_all_my_memos_data',
            'goodMemoIds',
            'laterReadMemoIds',
        ));
    }
}
