<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MemoListMember extends Component
{
    use WithPagination;

    public $previous_route;

    public $user_id;
    public $user_data;
    public $group_id;
    public $group_data;
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
        'closeModal' => 'closeModal',
    ];

    public function checkSuspension($skip = false)
    {
        // 指定のメソッドの最初でこのメソッドを呼び出すと、利用停止中ユーザーはそのメソッドを利用できない
        if (! $skip && Auth::check() && Auth::user()->suspension_state == 1) {
            abort(403, '利用停止中のため、この機能は利用できません。');
        }
    }

    public function mount($group_id, $user_id)
    {
        $this->group_data = Group::find($group_id);
        $this->user_data = User::find($user_id);

        $this->previous_route = url()->previous();

        // 運営ユーザー以上の権限を持つユーザーは常にアクセス可能
        if (! Auth::user()->can('admin-higher')) {
            // 指定のグループに自分が所属していない場合、直前のページにリダイレクト
            if (! $this->group_data->userRoles()->where('user_id', Auth::id())->exists()) {
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

    public function updatingSearch()
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

    public function suspendUser()
    {
        $this->user_data->suspension_state = 1;
        $this->user_data->save();
    }

    public function liftSuspendUser()
    {
        $this->user_data->suspension_state = 0;
        $this->user_data->save();
    }

    public function render()
    {
        $group_data = $this->group_data;
        $user_data = $this->user_data;

        $web_memos_data = collect([]);
        $book_memos_data = collect([]);

        // ユーザーのサスペンション状態を取得
        $this->isSuspended = $user_data->suspension_state;

        // 全角スペースを半角スペースに変換
        $search = str_replace('　', ' ', $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        if (in_array('web', $this->selected_web_book_labels)) {
            $web_memos_data = Memo::with(['labels', 'user', 'goods', 'laterReads', 'web_type_feature'])
                ->where('user_id', $this->user_id)
                ->where('group_id', $this->group_id)
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
                ->where('group_id', $this->group_id)
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
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = $all_memos_data->slice(($currentPage - 1) * $perPage, $perPage);
        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $count_all_memos_data = count($all_memos_data);

        // N+1対策: いいね・あとで読むIDを一括取得
        $all_memo_ids = $all_memos_data->pluck('id');
        $goodMemoIds = DB::table('goods')
            ->where('user_id', $this->user_id)
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');
        $laterReadMemoIds = DB::table('later_reads')
            ->where('user_id', $this->user_id)
            ->whereIn('memo_id', $all_memo_ids)
            ->pluck('memo_id');

        // 退会済みか確認
        $exists = Group::where('id', $this->group_id)->whereHas('userRoles', function ($query) {
            $query->where('user_id', $this->user_id);
        })->exists();

        if (! $exists) {
            session()->flash('not_member', 'このユーザーはグループに所属していません。');
            redirect($this->previous_route);
        }

        return view('livewire.memo-list-member', compact(
            'group_data',
            'user_data',
            'count_all_memos_data',
            'all_memos_data_paginated',
            'goodMemoIds',
            'laterReadMemoIds',
        ));
    }
}
