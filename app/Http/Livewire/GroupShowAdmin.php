<?php

namespace App\Http\Livewire;

use App\Models\Comment_type_report_link;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Group;
use App\Models\Group_type_report_link;
use App\Models\Memo_type_report_link;
use App\Models\Report;
use App\Models\User_type_report_link;
use Illuminate\Pagination\LengthAwarePaginator;

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


    public function mount($group_id)
    {
        $this->group_id = $group_id;
        $this->group_data = Group::find($this->group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (!$this->group_data) {
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


    public function executeSearch()
    {
        $this->resetPage('group_reports_page');
        $this->resetPage('users_data_page');
        $this->resetPage('suspension_users_data_page');
    }


    public function deleteGroup()
    {
        // グループに関連する通報リンクを取得
        $reportLinks = Group_type_report_link::where('group_id', $this->group_id)->get();

        // 各通報リンクに対して
        foreach ($reportLinks as $link) {
            // 通報レコードを削除
            Report::find($link->report_id)->delete();
            // 通報リンクを削除
            $link->delete();
        }

        // グループを削除
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


    public function deleteUser($userId)
    {
        User::find($userId)->delete();
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
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        //グループ通報情報
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
                            $subQuery->where('nickname', 'like', '%' . $keyword . '%')
                                ->orWhere('username', 'like', '%' . $keyword . '%');
                        })
                            ->orWhere('reports.detail', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->when($this->report_reason, function ($query) {
                $query->where('reason', $this->report_reason);
            })
            ->latest()
            ->get();



        //ユーザー
        $users_data = User::where('suspension_state', 0)
            ->whereHas('group', function ($query) {
                $query->where('id', $this->group_id);
            })
            ->with(['groupRoles' => function ($query) {
                $query->where('group_id', $this->group_id)
                    ->select(['role']);
            }])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
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
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();
            });





        //利用停止中ユーザー
        $suspension_users_data = User::where('suspension_state', 1)
            ->whereHas('group', function ($query) {
                $query->where('id', $this->group_id);
            })
            ->with(['groupRoles' => function ($query) {
                $query->where('group_id', $this->group_id)
                    ->select(['role']);
            }])
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
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
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();
            });




        $user_groups = Group::whereHas('user', function ($query) {
            $query->where('users.id', $this->user_id);
        })->get();




        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('group_reports_page');
        $items = $group_reports_data->slice(($currentPage - 1) * $perPage, $perPage);
        $group_reports_data_paginated = new LengthAwarePaginator($items, count($group_reports_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'group_reports_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('users_data_page');
        $items = $users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $users_data_paginated = new LengthAwarePaginator($items, count($users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'users_data_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('suspension_users_data_page');
        $items = $suspension_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $suspension_users_data_paginated = new LengthAwarePaginator($items, count($suspension_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'suspension_users_data_page'
        ]);



        return view('livewire.group-show-admin', compact(
            'users_data_paginated',
            'suspension_users_data_paginated',
            'user_groups',
            'group_reports_data_paginated',
        ));
    }
}
