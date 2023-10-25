<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Label;
use App\Models\Group;
use Illuminate\Pagination\LengthAwarePaginator;

class MemoListMypage extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_my_memos = true;
    public $show_good_memos = false;
    public $show_later_read_memos = false;


    protected $listeners = [
        'setGroupId',
        'filterByWebBookLabels',
        'filterByLabels'
    ];


    public function setGroupId($group_id)
    {
        // dd($group_id);

        $this->group_id = $group_id;

        $this->emitTo('label-editor-mypage', 'setGroupId', $this->group_id);
        $this->emitTo('label-list-mypage', 'setGroupId', $this->group_id);
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
        // dd($this->search);
    }


    public function mount($user_id)
    {
        $this->user_id = $user_id;

        $this->group_id = '';


        // dd($this->user_id);
    }

    public function render()
    {
        // dd($this->group_id);

        $user_groups = Group::whereHas('user', function ($query) {
            $query->where('users.id', $this->user_id);
        })->get();

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $good_web_memos_data = collect([]);
        $good_book_memos_data = collect([]);

        $later_read_web_memos_data = collect([]);
        $later_read_book_memos_data = collect([]);


        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        if ($this->group_id) {
            //自分が作成したメモ
            if (in_array('web', $this->selected_web_book_labels)) {
                $web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('users.id', $this->user_id)
                    ->where('group_id', $this->group_id)
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
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('users.id', $this->user_id)
                    ->where('group_id', $this->group_id)
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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


            //いいねしたメモ
            if (in_array('web', $this->selected_web_book_labels)) {
                $good_web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('goods', 'goods.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where(
                        'goods.user_id',
                        $this->user_id
                    )
                    ->where('group_id', $this->group_id)
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
                $good_book_memos_data = Memo::with('labels')
                    ->leftJoin('book_type_features', function ($join) {
                        $join->on(
                            'memos.id',
                            '=',
                            'book_type_features.memo_id'
                        );
                    })
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('goods', 'goods.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where(
                        'goods.user_id',
                        $this->user_id
                    )
                    ->where('group_id', $this->group_id)
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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


            //あとでよむしたメモ
            if (in_array('web', $this->selected_web_book_labels)) {
                $later_read_web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('later_reads.user_id', $this->user_id)
                    ->where('group_id', $this->group_id)
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
                $later_read_book_memos_data = Memo::with('labels')
                    ->leftJoin('book_type_features', function ($join) {
                        $join->on(
                            'memos.id',
                            '=',
                            'book_type_features.memo_id'
                        );
                    })
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('later_reads.user_id', $this->user_id)
                    ->where('group_id', $this->group_id)
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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
        } else {
            //自分が作成したメモ
            // dd($this->user_id);
            if (in_array('web', $this->selected_web_book_labels)) {
                $web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('users.id', $this->user_id)
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
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('users.id', $this->user_id)
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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


            //いいねしたメモ
            if (in_array('web', $this->selected_web_book_labels)) {
                $good_web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('goods', 'goods.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where(
                        'goods.user_id',
                        $this->user_id
                    )
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
                $good_book_memos_data = Memo::with('labels')
                    ->leftJoin('book_type_features', function ($join) {
                        $join->on(
                            'memos.id',
                            '=',
                            'book_type_features.memo_id'
                        );
                    })
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('goods', 'goods.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where(
                        'goods.user_id',
                        $this->user_id
                    )
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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


            //あとでよむしたメモ
            if (in_array('web', $this->selected_web_book_labels)) {
                $later_read_web_memos_data = Memo::with('labels')
                    ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('later_reads.user_id', $this->user_id)
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
                $later_read_book_memos_data = Memo::with('labels')
                    ->leftJoin('book_type_features', function ($join) {
                        $join->on(
                            'memos.id',
                            '=',
                            'book_type_features.memo_id'
                        );
                    })
                    ->join('users', 'memos.user_id', '=', 'users.id')
                    ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                    ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                    ->where('later_reads.user_id', $this->user_id)
                    ->where(function ($query) {
                        $query->whereNotNull('book_type_features.memo_id')
                            ->orWhere(function ($query) {
                                $query->where('memos.type', 1)
                                    ->whereNull('book_type_features.memo_id');
                            });
                    })
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
        }

        $all_my_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();

        $all_good_memos_data = $good_web_memos_data->concat($good_book_memos_data)->sortBy('updated_at')->values()->all();

        $all_later_read_memos_data = $later_read_web_memos_data->concat($later_read_book_memos_data)->sortBy('updated_at')->values()->all();

        // dd($all_memos_data, $all_good_memos_data, $all_later_read_memos_data);

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_my_memos_page');
        $items = array_slice($all_my_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_my_memos_data_paginated = new LengthAwarePaginator($items, count($all_my_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_my_memos_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_good_memos_page');
        $items = array_slice($all_good_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_good_memos_data_paginated = new LengthAwarePaginator($items, count($all_good_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_good_memos_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_later_read_memos_page');
        $items = array_slice($all_later_read_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_later_read_memos_data_paginated = new LengthAwarePaginator($items, count($all_later_read_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_later_read_memos_page'
        ]);

        $count_all_my_memos_data = count($all_my_memos_data);

        // $labels_data = Label::all();

        // dd($all_memos_data, $count_all_memos_data);

        return view('livewire.memo-list-mypage', compact(
            'user_groups',
            'all_my_memos_data_paginated',
            'all_good_memos_data_paginated',
            'all_later_read_memos_data_paginated',
            'count_all_my_memos_data',
        ));
    }
}
