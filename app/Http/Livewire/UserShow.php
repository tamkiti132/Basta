<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Report;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UserShow extends Component
{
    use WithPagination;

    public $user_id;
    public $user_data;
    public $group_id;
    public $report_reason;
    public $sortCriteria = 'report';
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

    public $deleteTargetUserId = 0;
    public $targetGroup;
    public $fragSubManagerOrMember = '';
    public $showNextManagerModal = false;
    public $showModalNobodyMember = false;
    public $selectedNextManagerIds = [];
    public $managedGroupIds;
    public $selectedNextManagerCount = 0;
    public $totalManagedGroupCount;
    public $nextManagerId = '';

    protected $listeners = [
        'setGroupId',
        'filterByWebBookLabels',
        'filterByLabels',
        'deleteUser' => 'deleteUser',
        'closeModal' => 'closeModal',
    ];

    public function checkSuspension($skip = false)
    {
        // 指定のメソッドの最初でこのメソッドを呼び出すと、利用停止中ユーザーはそのメソッドを利用できない
        if (! $skip && Auth::check() && Auth::user()->suspension_state == 1) {
            abort(403, '利用停止中のため、この機能は利用できません。');
        }
    }

    public function mount($user_id)
    {
        $this->user_id = $user_id;
        $this->user_data = User::find($this->user_id);

        // ユーザーが存在しない場合に 404 エラーを返す
        if (! $this->user_data) {
            abort(404);
        }

        $this->group_id = '';

        $this->dispatchBrowserEvent('load');
    }

    public function setGroupId($group_id)
    {
        $this->group_id = $group_id;

        $this->selected_labels = [];

        $this->emitTo('label-editor-mypage', 'setGroupId', $this->group_id);
        $this->emitTo('label-list-mypage', 'setGroupId', $this->group_id);

        $this->resetPage('all_user_reports_page');
        $this->resetPage('all_my_memos_page');
        $this->resetPage('comments_page');
    }

    public function setSortCriteria($sortCriteria)
    {
        $this->sortCriteria = $sortCriteria;

        $this->resetPage('groups_page');
        $this->resetPage('suspension_groups_page');
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

    public function updatingSearch()
    {
        $this->resetPage('all_user_reports_page');
        $this->resetPage('all_my_memos_page');
        $this->resetPage('comments_page');
    }

    public function closeModal()
    {
        $this->deleteTargetUserId = 0;
        $this->targetGroup = null;
        $this->fragSubManagerOrMember = '';
        $this->showNextManagerModal = false;
        $this->showModalNobodyMember = false;
        $this->selectedNextManagerIds = [];
        $this->managedGroupIds = [];
        $this->selectedNextManagerCount = 0;
        $this->totalManagedGroupCount = 0;
        $this->nextManagerId = '';
    }

    public function isManager($user_id)
    {
        $this->checkSuspension();

        $this->deleteTargetUserId = $user_id;

        // ユーザーが管理者であるグループがあるかどうかの確認
        $hasManagedGroup = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->deleteTargetUserId)
                ->where('role', 10);
        })->exists();

        // 管理者であるグループがあるかどうかによる分岐
        if ($hasManagedGroup) {
            // 管理者権限のグループがある場合
            $this->getManagedGroups();
        } else {
            // 管理者の権限のグループがない場合
            $this->deleteUser();
        }
    }

    public function getManagedGroups()
    {
        // ユーザーが管理者であるグループを全て取得
        $managedGroups = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->deleteTargetUserId)
                ->where('role', 10);
        })->get();

        // 取得したグループのIDを取得
        $this->managedGroupIds = $managedGroups->pluck('id');
        $this->totalManagedGroupCount = $this->managedGroupIds->count();

        $this->setTargetGroupWithSubManagers($this->managedGroupIds[0]);
    }

    public function setTargetGroupWithSubManagers($group_id)
    {
        // グループのデータ（サブ管理者のデータも併せて取得）
        $this->targetGroup = Group::with(['userRoles' => function ($query) {
            $query->wherePivot('role', 50)
                ->orderBy('nickname');
        }])->find($group_id);

        $this->hasSubManager();
    }

    public function hasSubManager()
    {
        if ($this->targetGroup->userRoles->isNotEmpty()) {
            // サブ管理者がいる場合
            $this->fragSubManagerOrMember = 'subManager';
            // モーダルフラグをリセットしてから新しいフラグをセット
            $this->showModalNobodyMember = false;
            $this->showNextManagerModal = true;
        } else {
            // サブ管理者がいない場合
            $this->setTargetGroupWithMembers($this->targetGroup->id);
        }
    }

    public function setTargetGroupWithMembers($group_id)
    {
        // グループのデータ（メンバーのデータも併せて取得）
        $this->targetGroup = Group::with(['userRoles' => function ($query) {
            $query->wherePivot('role', 100)
                ->orderBy('nickname');
        }])->find($group_id);

        $this->hasMember();
    }

    public function hasMember()
    {
        if ($this->targetGroup->userRoles->isNotEmpty()) {
            // メンバーがいる場合
            $this->fragSubManagerOrMember = 'member';
            // モーダルフラグをリセットしてから新しいフラグをセット
            $this->showModalNobodyMember = false;
            $this->showNextManagerModal = true;
        } else {
            // メンバーがいない場合
            // モーダルフラグをリセットしてから新しいフラグをセット
            $this->showNextManagerModal = false;
            $this->showModalNobodyMember = true;
        }
    }

    public function selectNextManager()
    {
        $this->selectedNextManagerIds[$this->targetGroup->id] = $this->nextManagerId;

        $this->selectedNextManagerCount++;
        $this->nextManagerId = '';
        $this->fragSubManagerOrMember = '';

        // モーダルフラグをリセット
        $this->showNextManagerModal = false;
        $this->showModalNobodyMember = false;

        if ($this->selectedNextManagerCount != $this->totalManagedGroupCount) {
            $this->setTargetGroupWithSubManagers($this->managedGroupIds[$this->selectedNextManagerCount]);
        } else {
            // 最後のグループだった場合
            $this->emit('confirmDeletion');
        }
    }

    public function addDeleteGroupFlag()
    {
        $this->selectedNextManagerIds[$this->targetGroup->id] = 0;

        $this->selectedNextManagerCount++;
        $this->nextManagerId = '';
        $this->fragSubManagerOrMember = '';

        // モーダルフラグをリセット
        $this->showNextManagerModal = false;
        $this->showModalNobodyMember = false;

        if ($this->selectedNextManagerCount != $this->totalManagedGroupCount) {
            $this->setTargetGroupWithSubManagers($this->managedGroupIds[$this->selectedNextManagerCount]);
        } else {
            // 最後のグループだった場合
            $this->emit('confirmDeletion');
        }
    }

    public function deleteUser()
    {
        $this->checkSuspension();

        if ($this->managedGroupIds) {
            // 管理者権限のグループがある場合
            foreach ($this->selectedNextManagerIds as $groupId => $nextManagerId) {
                // 各グループの管理者を更新 or グループ自体を削除する処理

                // グループを取得
                $group = Group::find($groupId);

                if ($group) {
                    if ($nextManagerId) {
                        // 次の管理者が選択されている場合 （selectNextManagerが実行されたグループ）
                        // 現在の管理者の権限を更新
                        $group->userRoles()->updateExistingPivot($this->deleteTargetUserId, ['role' => 100]);

                        // 次の管理者を設定
                        $group->userRoles()->updateExistingPivot($nextManagerId, ['role' => 10]);
                    } else {
                        // 次の管理者が選択されていない場合 （addDeleteGroupFlagが実行されたグループ）
                        $group->delete();
                    }
                }
            }
        }

        // ユーザーを削除
        $user_data = User::find($this->deleteTargetUserId);
        $user_data->delete();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');

        $this->closeModal();

        return to_route('admin.user_top');
    }

    public function suspend()
    {

        $user_data = $this->user_data;

        $user_data->suspension_state = 1;
        $user_data->save();

        $this->emit('userSuspended');
    }

    public function liftSuspend()
    {
        $user_data = $this->user_data;

        $user_data->suspension_state = 0;
        $user_data->save();

        $this->emit('userLiftSuspended');
    }

    public function render()
    {
        $user_data = $this->user_data;

        $user_groups = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
        })->orderBy('name')
            ->get();

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        $this->isSuspended = $user_data->suspension_state;

        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        // ユーザー通報情報
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
                            $subQuery->where('nickname', 'like', '%'.$keyword.'%');
                        })
                            ->orWhere('reports.detail', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->when($this->report_reason, function ($query) {
                $query->where('reason', $this->report_reason);
            })
            ->latest()
            ->get();

        // メモ
        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with(['labels', 'goods', 'laterReads'])
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->withCount('reports')
                ->where('users.id', $this->user_id)
                ->where(function ($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->where(function ($query) use ($keyword) {
                            $query->where('memos.title', 'like', '%'.$keyword.'%')
                                ->orWhere('memos.shortMemo', 'like', '%'.$keyword.'%');
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
            $book_memos_data = Memo::with(['labels', 'goods', 'laterReads'])
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
                            $query->where('memos.title', 'like', '%'.$keyword.'%')
                                ->orWhere('memos.shortMemo', 'like', '%'.$keyword.'%');
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

        $all_my_memos_data = $web_memos_data->concat($book_memos_data);

        // いいね・あとで読むIDを一括取得
        $all_memo_ids = $all_my_memos_data->pluck('id');
        $goodMemoIds = DB::table('goods')
            ->where('user_id', Auth::id())
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');
        $laterReadMemoIds = DB::table('later_reads')
            ->where('user_id', Auth::id())
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');

        if ($this->sortCriteria === 'report') {
            $all_my_memos_data =
                $all_my_memos_data->sortByDesc(function ($memo) {
                    return [$memo->reports_count, $memo->created_at];
                });
        } elseif ($this->sortCriteria === 'time') {
            $all_my_memos_data = $all_my_memos_data->sortByDesc('created_at');
        }

        $all_my_memos_data = $all_my_memos_data->values()->all();

        // コメント
        $comments_data = Comment::where('user_id', $this->user_id)
            ->with('memo:id,group_id')
            ->withCount('reports')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('comments.comment', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->when($this->group_id, function ($query) {
                $query->whereHas('memo', function ($query) {
                    $query->where('group_id', $this->group_id);
                });
            })
            ->when($this->sortCriteria === 'report', function ($query) {
                $query->orderByDesc('reports_count');
            })
            ->orderByDesc('created_at')
            ->get();

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_user_reports_page');
        $items = $all_user_reports_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_user_reports_data_paginated = new LengthAwarePaginator($items, count($all_user_reports_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_user_reports_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_my_memos_page');
        $items = array_slice($all_my_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_my_memos_data_paginated = new LengthAwarePaginator($items, count($all_my_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_my_memos_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('comments_page');
        $items = $comments_data->slice(($currentPage - 1) * $perPage, $perPage);
        $comments_data_paginated = new LengthAwarePaginator($items, count($comments_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'comments_page',
        ]);

        return view('livewire.user-show', compact(
            'user_data',
            'user_groups',
            'all_user_reports_data_paginated',
            'all_my_memos_data_paginated',
            'comments_data_paginated',
            'goodMemoIds',
            'laterReadMemoIds',
        ));
    }
}
