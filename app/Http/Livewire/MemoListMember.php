<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Comment_type_report_link;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Group_type_report_link;
use App\Models\Memo_type_report_link;
use App\Models\Report;
use App\Models\User_type_report_link;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MemoListMember extends Component
{
    use WithPagination;

    public $previous_route;

    public $user_id;
    public $group_id;
    public $show_web = true;
    public $show_book = true;
    public $selected_web_book_labels = ['web', 'book'];
    public $selected_labels = [];
    public $search = '';

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
        'filterByWebBookLabels',
        'filterByLabels',
        'labelUpdated',
        'labelDeleted',
        'deleteUser' => 'deleteUser',
        'closeModal' => 'closeModal'
    ];


    public function checkSuspension($skip = false)
    {
        // 指定のメソッドの最初でこのメソッドを呼び出すと、利用停止中ユーザーはそのメソッドを利用できない
        if (!$skip && Auth::check() && Auth::user()->suspension_state == 1) {
            abort(403, '利用停止中のため、この機能は利用できません。');
        }
    }


    public function mount($group_id, $user_id)
    {
        $group = Group::find($group_id);

        $this->previous_route = url()->previous();

        // 運営ユーザー以上の権限を持つユーザーは常にアクセス可能
        if (!Auth::user()->can('admin-higher')) {
            // 指定のグループに自分が所属していない場合、直前のページにリダイレクト
            if (!$group->userRoles()->where('user_id', Auth::id())->exists()) {
                session()->flash('error', '対象のグループに所属していないため、アクセスできません');
                redirect($this->previous_route);
            }
        }

        $this->group_id = $group_id;
        $this->user_id = $user_id;
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

    public function executeSearch()
    {
        $this->resetPage();
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
            $this->showNextManagerModal = true;
        } else {
            // メンバーがいない場合
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
                        // グループに関連する通報リンクを取得
                        $groupReportLinks = Group_type_report_link::where('group_id', $groupId)->get();

                        // 各通報リンクに対して
                        foreach ($groupReportLinks as $link) {
                            // 通報レコードを削除
                            Report::find($link->report_id)->delete();
                            // 通報リンクを削除
                            $link->delete();
                        }
                        // グループ自体を削除
                        $group->delete();
                    }
                }
            }
        }





        // 以下、削除対象ユーザーに対するレポートを削除する処理
        // ユーザーに関連する通報リンクを取得
        $userReportLinks = User_type_report_link::where('user_id', $this->deleteTargetUserId)->get();

        // 各通報リンクに対して
        foreach ($userReportLinks as $link) {
            // 通報レコードを削除
            Report::find($link->report_id)->delete();
            // 通報リンクを削除
            $link->delete();
        }

        // 以下、ユーザーが投稿したメモに対するレポートを削除する処理
        // ユーザーが投稿したメモを取得
        $user_memos = Memo::where('user_id', $this->deleteTargetUserId)->get();

        // 各メモに対して
        foreach ($user_memos as $memo) {
            // メモに関連する通報リンクを取得
            $memoReportLinks = Memo_type_report_link::where('memo_id', $memo->id)->get();

            // 各通報リンクに対して
            foreach ($memoReportLinks as $link) {
                // 通報レコードを削除
                Report::find($link->report_id)->delete();
                // 通報リンクを削除
                $link->delete();
            }

            // メモを削除
            $memo->delete();
        }

        // ユーザーが投稿者である通報を削除
        Report::where('contribute_user_id', $this->deleteTargetUserId)->delete();

        // 以下、ユーザーが投稿したコメントに対するレポートを削除する処理
        // ユーザーが投稿したメモを取得
        $user_comments = Comment::where('user_id', $this->deleteTargetUserId)->get();

        // 各コメントに対して
        foreach ($user_comments as $comment) {
            // コメントに関連する通報リンクを取得
            $commentReportLinks = Comment_type_report_link::where('comment_id', $comment->id)->get();

            // 各通報リンクに対して
            foreach ($commentReportLinks as $link) {
                // 通報レコードを削除
                Report::find($link->report_id)->delete();
                // 通報リンクを削除
                $link->delete();
            }

            // コメントを削除
            $comment->delete();
        }

        // ユーザーが投稿者である通報を削除
        Report::where('contribute_user_id', $this->deleteTargetUserId)->delete();

        // ユーザーを削除
        $user_data = User::find($this->deleteTargetUserId);
        $user_data->delete();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');

        $this->closeModal();

        return to_route('admin.user_top');
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

        // ユーザーのサスペンション状態を取得
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

        $all_memos_data = $web_memos_data->concat($book_memos_data)->sortByDesc('created_at')->values()->all();
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = array_slice($all_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        $count_all_memos_data = count($all_memos_data);

        // 退会済みか確認
        $exists = Group::where('id', $this->group_id)->whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
        })->exists();


        if (!$exists) {
            session()->flash('not_member', 'このユーザーはグループに所属していません。');
            redirect($this->previous_route);
        }

        return view('livewire.memo-list-member', compact('group_data', 'user_data', 'count_all_memos_data', 'all_memos_data_paginated'));
    }
}
