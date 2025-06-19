<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Comment_type_report_link;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Memo_type_report_link;
use App\Models\Report;
use App\Models\User;
use App\Models\User_type_report_link;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GroupShowAdmin extends Component
{
    use WithPagination;

    public $user_id;
    public $group_id;
    public $group_data;
    public $report_reason;
    public $user_block_state;
    public $show_web = true;
    public $show_book = true;
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_group_reports = true;
    public $show_members = false;
    public $show_users = false;
    public $show_users_pagination = false;
    public $show_suspension_users = false;
    public $show_suspension_users_pagination = false;

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

    public function mount($group_id)
    {
        $this->group_id = $group_id;
        $this->group_data = Group::find($this->group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (! $this->group_data) {
            abort(404);
        }

        $this->dispatchBrowserEvent('load');
    }

    public function showMember()
    {
        $this->show_group_reports = false;
        $this->show_members = true;

        if ($this->show_users) {
            $this->show_users_pagination = true;
        }

        if ($this->show_suspension_users) {
            $this->show_suspension_users_pagination = true;
        }
    }

    public function setReportReason($report_reason)
    {
        $this->report_reason = $report_reason;

        $this->resetPage('group_reports_page');
    }

    public function setUserBlockState($user_block_state)
    {
        $this->user_block_state = $user_block_state;

        $this->resetPage('users_data_page');
        $this->resetPage('suspension_users_data_page');
    }

    public function updatingSearch()
    {
        $this->resetPage('group_reports_page');
        $this->resetPage('users_data_page');
        $this->resetPage('suspension_users_data_page');
    }

    public function deleteGroup()
    {
        Group::find($this->group_id)->delete();

        return to_route('admin.group_top');
    }

    public function suspendGroup()
    {
        $group_data = Group::find($this->group_id);

        $group_data->suspension_state = 1;
        $group_data->save();

        $this->emit('suspendedGroup');
    }

    public function liftSuspendGroup()
    {
        $group_data = Group::find($this->group_id);

        $group_data->suspension_state = 0;
        $group_data->save();

        $this->emit('liftSuspendedGroup');
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
            $this->getManagedGroups($this->deleteTargetUserId);
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

        $hasUserRoles = Group::where('id', $this->group_id)
            ->whereHas('userRoles')
            ->exists();

        if ($hasUserRoles) {
            return to_route('admin.group_show', ['group_id' => $this->group_id]);
        } else {
            return to_route('admin.group_top');
        }
    }

    public function suspendUser($userId)
    {
        $user_data = User::find($userId);

        $user_data->suspension_state = 1;
        $user_data->save();

        $this->emit('suspendedGroup');

        $this->resetPage('users_data_page');
        $this->resetPage('suspension_users_data_page');
    }

    public function liftSuspendUser($userId)
    {
        $user_data = User::find($userId);

        $user_data->suspension_state = 0;
        $user_data->save();

        $this->emit('liftSuspendedGroup');

        $this->resetPage('users_data_page');
        $this->resetPage('suspension_users_data_page');
    }

    public function render()
    {
        $this->group_data = Group::where('id', $this->group_id)
            ->with(['userRoles' => function ($query) {
                $query->wherePivot('role', 10);
            }])
            ->first();

        $this->isSuspended = $this->group_data->suspension_state;

        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        // グループ通報情報
        $group_reports_data = Report::whereIn('id', function ($query) {
            $query->select('report_id')
                ->from('group_type_report_links')
                ->where('group_id', $this->group_id);
        })
            ->with('contribute_user')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->whereHas('contribute_user', function ($subQuery) use ($keyword) {
                            $subQuery->where('nickname', 'like', '%'.$keyword.'%')
                                ->orWhere('username', 'like', '%'.$keyword.'%');
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

        // ユーザー
        $users_data = User::where('suspension_state', 0)
            ->whereHas('groupRoles', function ($query) {
                $query->where('group_id', $this->group_id);
            })
            ->with(['groupRoles' => function ($query) {
                $query->where('group_id', $this->group_id)
                    ->select(['role']);
            }])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%'.$keyword.'%')
                            ->orWhere('users.username', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->when($this->user_block_state == 1, function ($query) {
                $query->whereDoesntHave('blockedGroup', function ($query) {
                    $query->where('groups.id', $this->group_id);
                });
            })
            ->when($this->user_block_state == 2, function ($query) {
                $query->whereHas('blockedGroup', function ($query) {
                    $query->where('groups.id', $this->group_id);
                });
            })
            ->leftJoin('roles', function ($join) {
                $join->on('users.id', '=', 'roles.user_id')
                    ->where('roles.group_id', '=', $this->group_id);
            })
            ->groupBy('users.id')
            ->orderByRaw('MIN(roles.role) ASC')
            ->orderBy('users.nickname', 'ASC')
            ->select('users.*')
            ->get();

        // N+1対策: ユーザーIDリストを取得
        $userIds = $users_data->pluck('id');
        $memoIdsByUser = Memo::whereIn('user_id', $userIds)->get()->groupBy('user_id');
        $commentIdsByUser = Comment::whereIn('user_id', $userIds)->get()->groupBy('user_id');
        $userReportCounts = User_type_report_link::whereIn('user_id', $userIds)
            ->selectRaw('user_id, count(*) as count')->groupBy('user_id')->pluck('count', 'user_id');
        $memoReportCounts = Memo_type_report_link::whereIn('memo_id', Memo::whereIn('user_id', $userIds)->pluck('id'))
            ->selectRaw('memo_id, count(*) as count')->groupBy('memo_id')->pluck('count', 'memo_id');
        $commentReportCounts = Comment_type_report_link::whereIn('comment_id', Comment::whereIn('user_id', $userIds)->pluck('id'))
            ->selectRaw('comment_id, count(*) as count')->groupBy('comment_id')->pluck('count', 'comment_id');

        $users_data->each(function ($user) use ($userReportCounts, $memoReportCounts, $commentReportCounts, $memoIdsByUser, $commentIdsByUser) {
            $user->userReportsCount = $userReportCounts[$user->id] ?? 0;
            $user->memoReportsCount = 0;
            $user->commentReportsCount = 0;
            if (isset($memoIdsByUser[$user->id])) {
                foreach ($memoIdsByUser[$user->id] as $memo) {
                    $user->memoReportsCount += $memoReportCounts[$memo->id] ?? 0;
                }
            }
            if (isset($commentIdsByUser[$user->id])) {
                foreach ($commentIdsByUser[$user->id] as $comment) {
                    $user->commentReportsCount += $commentReportCounts[$comment->id] ?? 0;
                }
            }
        });

        // 利用停止中ユーザー
        $suspension_users_data = User::where('suspension_state', 1)
            ->whereHas('groupRoles', function ($query) {
                $query->where('group_id', $this->group_id);
            })
            ->with(['groupRoles' => function ($query) {
                $query->where('group_id', $this->group_id)
                    ->select(['role']);
            }])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%'.$keyword.'%')
                            ->orWhere('users.username', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->when($this->user_block_state == 1, function ($query) {
                $query->whereDoesntHave('blockedGroup', function ($query) {
                    $query->where('groups.id', $this->group_id);
                });
            })
            ->when($this->user_block_state == 2, function ($query) {
                $query->whereHas('blockedGroup', function ($query) {
                    $query->where('groups.id', $this->group_id);
                });
            })
            ->leftJoin('roles', function ($join) {
                $join->on('users.id', '=', 'roles.user_id')
                    ->where('roles.group_id', '=', $this->group_id);
            })
            ->groupBy('users.id')
            ->orderByRaw('MIN(roles.role) ASC')
            ->orderBy('users.nickname', 'ASC')
            ->select('users.*')
            ->get();

        // N+1対策: 停止ユーザーIDリストを取得
        $suspUserIds = $suspension_users_data->pluck('id');
        $suspMemoIdsByUser = Memo::whereIn('user_id', $suspUserIds)->get()->groupBy('user_id');
        $suspCommentIdsByUser = Comment::whereIn('user_id', $suspUserIds)->get()->groupBy('user_id');
        $suspUserReportCounts = User_type_report_link::whereIn('user_id', $suspUserIds)
            ->selectRaw('user_id, count(*) as count')->groupBy('user_id')->pluck('count', 'user_id');
        $suspMemoReportCounts = Memo_type_report_link::whereIn('memo_id', Memo::whereIn('user_id', $suspUserIds)->pluck('id'))
            ->selectRaw('memo_id, count(*) as count')->groupBy('memo_id')->pluck('count', 'memo_id');
        $suspCommentReportCounts = Comment_type_report_link::whereIn('comment_id', Comment::whereIn('user_id', $suspUserIds)->pluck('id'))
            ->selectRaw('comment_id, count(*) as count')->groupBy('comment_id')->pluck('count', 'comment_id');

        $suspension_users_data->each(function ($user) use ($suspUserReportCounts, $suspMemoReportCounts, $suspCommentReportCounts, $suspMemoIdsByUser, $suspCommentIdsByUser) {
            $user->userReportsCount = $suspUserReportCounts[$user->id] ?? 0;
            $user->memoReportsCount = 0;
            $user->commentReportsCount = 0;
            if (isset($suspMemoIdsByUser[$user->id])) {
                foreach ($suspMemoIdsByUser[$user->id] as $memo) {
                    $user->memoReportsCount += $suspMemoReportCounts[$memo->id] ?? 0;
                }
            }
            if (isset($suspCommentIdsByUser[$user->id])) {
                foreach ($suspCommentIdsByUser[$user->id] as $comment) {
                    $user->commentReportsCount += $suspCommentReportCounts[$comment->id] ?? 0;
                }
            }
        });

        $user_groups = Group::whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
        })->get();

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('group_reports_page');
        $items = $group_reports_data->slice(($currentPage - 1) * $perPage, $perPage);
        $group_reports_data_paginated = new LengthAwarePaginator($items, count($group_reports_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'group_reports_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('users_data_page');
        $items = $users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $users_data_paginated = new LengthAwarePaginator($items, count($users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'users_data_page',
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('suspension_users_data_page');
        $items = $suspension_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $suspension_users_data_paginated = new LengthAwarePaginator($items, count($suspension_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'suspension_users_data_page',
        ]);

        return view('livewire.group-show-admin', compact(
            'users_data_paginated',
            'suspension_users_data_paginated',
            'user_groups',
            'group_reports_data_paginated',
        ));
    }
}
