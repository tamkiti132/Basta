<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Label;
use App\Models\Group;
use App\Models\Report;
use Illuminate\Pagination\LengthAwarePaginator;

class UserShow extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $report_reason;
    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_users = true;
    public $show_memos = false;
    public $show_comments = false;

    public $isSuspended;


    protected $listeners = [
        'setGroupId',
        'filterByWebBookLabels',
        'filterByLabels'
    ];


    public function mount($user_id)
    {
        $this->user_id = $user_id;

        $this->group_id = '';
    }


    public function setGroupId($group_id)
    {
        // dd($group_id);

        $this->group_id = $group_id;

        $this->emitTo('label-editor-mypage', 'setGroupId', $this->group_id);
        $this->emitTo('label-list-mypage', 'setGroupId', $this->group_id);

        $this->resetPage('all_user_reports_page');
        $this->resetPage('all_my_memos_page');
        $this->resetPage('comments_page');
    }


    public function setReportReason($report_reason)
    {
        $this->report_reason = $report_reason;

        $this->resetPage('all_user_reports_page');
    }


    public function filterByWebBookLabels($selected_web_book_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_web_book_labels = $selected_web_book_labels;

        $this->resetPage('all_my_memos_page');
    }

    public function filterByLabels($selected_labels)
    {
        // 選択されたラベルのON/OFF切り替え
        $this->selected_labels = $selected_labels;

        $this->resetPage('all_my_memos_page');
    }

    public function executeSearch()
    {
        $this->resetPage();

        $this->resetPage('all_user_reports_page');
        $this->resetPage('all_my_memos_page');
        $this->resetPage('comments_page');
    }

    public function deleteUser()
    {
        $user_data = User::find($this->user_id);

        $user_data->delete();

        return to_route('admin.user_top.index');
    }

    public function suspend()
    {

        $user_data = User::find($this->user_id);

        $user_data->suspension_state = 1;
        $user_data->save();

        $this->emit('userSuspended');
    }

    public function liftSuspend()
    {
        $user_data = User::find($this->user_id);

        $user_data->suspension_state = 0;
        $user_data->save();

        $this->emit('userLiftSuspended');
    }



    public function render()
    {
        $user_data = User::find($this->user_id);


        $user_groups = Group::whereHas('user', function ($query) {
            $query->where('users.id', $this->user_id);
        })->get();

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $this->isSuspended = $user_data->suspension_state;


        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);



        //ユーザー通報情報
        $all_user_reports_data = Report::whereIn('id', function ($query) {
            $query->select('report_id')
                ->from('user_type_report_links')
                ->where('user_id', $this->user_id);
        })
            ->with(['contribute_user'])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->whereHas('contribute_user', function ($subQuery) use ($keyword) {
                            $subQuery->where('nickname', 'like', '%' . $keyword . '%');
                        })
                            ->orWhere('reports.detail', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->when($this->report_reason, function ($query) {
                $query->where('reason', $this->report_reason);
            })
            ->get();




        //メモ
        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->withCount('reports')
                ->where('users.id', $this->user_id)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('memos.title', 'like', '%' . $keyword . '%')
                                ->orWhere('memos.shortMemo', 'like', '%' . $keyword . '%');
                        });
                    }
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
                ->when($this->selected_labels, function ($query) {
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
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
                ->withCount('reports')
                ->where('users.id', $this->user_id)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('memos.title', 'like', '%' . $keyword . '%')
                                ->orWhere('memos.shortMemo', 'like', '%' . $keyword . '%');
                        });
                    }
                })
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->when($this->group_id, function ($query) {
                    $query->where('group_id', $this->group_id);
                })
                ->when($this->selected_labels, function ($query) {
                    $query->whereHas('labels', function ($query) {
                        $query->whereIn('id', $this->selected_labels);
                    });
                })
                ->get();
        }

        $all_my_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();



        //コメント
        $comments_data = Comment::where('user_id', $this->user_id)
            ->with('memo:id,group_id')
            ->withCount('reports')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('comments.comment', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->when($this->group_id, function ($query) {
                $query->whereHas('memo', function ($query) {
                    $query->where('group_id', $this->group_id);
                });
            })
            ->get();



        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_user_reports_page');
        $items = $all_user_reports_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_user_reports_data_paginated = new LengthAwarePaginator($items, count($all_user_reports_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_user_reports_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_my_memos_page');
        $items = array_slice($all_my_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_my_memos_data_paginated = new LengthAwarePaginator($items, count($all_my_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_my_memos_page'
        ]);


        $currentPage = LengthAwarePaginator::resolveCurrentPage('comments_page');
        $items = $comments_data->slice(($currentPage - 1) * $perPage, $perPage);
        $comments_data_paginated = new LengthAwarePaginator($items, count($comments_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'comments_page'
        ]);



        return view('livewire.user-show', compact(
            'user_data',
            'user_groups',
            'all_user_reports_data_paginated',
            'all_my_memos_data_paginated',
            'comments_data_paginated',
        ));
    }
}
