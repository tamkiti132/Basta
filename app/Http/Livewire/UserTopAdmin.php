<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Comment_type_report_link;
use App\Models\Memo;
use App\Models\Memo_type_report_link;
use App\Models\Report;
use App\Models\User;
use App\Models\User_type_report_link;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class UserTopAdmin extends Component
{
    use WithPagination;

    public $sortCriteria = 'report_all';
    public $search = '';

    // 各タブの表示状態を管理するプロパティ
    public $show_user = true;
    public $show_suspended_user = false;


    public function setSortCriteria($sortCriteria)
    {
        $this->sortCriteria = $sortCriteria;

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function executeSearch()
    {
        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function deleteUser($user_id)
    {
        // 以下、削除対象ユーザーに対するレポートを削除する処理
        // ユーザーに関連する通報リンクを取得
        $userReportLinks = User_type_report_link::where('user_id', $user_id)->get();

        // 各通報リンクに対して
        foreach ($userReportLinks as $link) {
            // 通報レコードを削除
            Report::find($link->report_id)->delete();
            // 通報リンクを削除
            $link->delete();
        }


        // 以下、ユーザーが投稿したメモに対するレポートを削除する処理
        // ユーザーが投稿したメモを取得
        $user_memos = Memo::where('user_id', $user_id)->get();

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
        Report::where('contribute_user_id', $user_id)->delete();


        // 以下、ユーザーが投稿したコメントに対するレポートを削除する処理
        // ユーザーが投稿したメモを取得
        $user_comments = Comment::where('user_id', $user_id)->get();

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
        Report::where('contribute_user_id', $user_id)->delete();

        $user_data = User::find($user_id);
        $user_data->delete();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function suspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 1;
        $user_data->save();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function liftSuspendUser($user_id)
    {
        $user_data = User::find($user_id);

        $user_data->suspension_state = 0;
        $user_data->save();

        $this->resetPage('all_not_suspended_users_page');
        $this->resetPage('all_suspended_users_page');
    }


    public function render()
    {
        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $this->search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);


        // 利用停止されていないユーザー情報一覧取得
        $all_not_suspended_users_data = User::where('suspension_state', 0)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereNull('group_id');
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();

                $user->allReportsCount = $user->userReportsCount + $user->memoReportsCount + $user->commentReportsCount;
            });

        // ソート基準に応じて並び替え
        switch ($this->sortCriteria) {
            case 'report_all':
                $all_not_suspended_users_data = $all_not_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->allReportsCount <=> $a->allReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_user':
                $all_not_suspended_users_data = $all_not_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->userReportsCount <=> $a->userReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_memo':
                $all_not_suspended_users_data = $all_not_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->memoReportsCount <=> $a->memoReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_comment':
                $all_not_suspended_users_data = $all_not_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->commentReportsCount <=> $a->commentReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'nickname':
                $all_not_suspended_users_data = $all_not_suspended_users_data->sort(function ($a, $b) {
                    return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                });
                break;
        }




        // 利用停止中のユーザー情報一覧取得
        $all_suspended_users_data = User::where('suspension_state', 1)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereNull('group_id');
            })
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($query) use ($keyword) {
                        $query->where('users.nickname', 'like', '%' . $keyword . '%')
                            ->orWhere('users.username', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();

                $user->allReportsCount = $user->userReportsCount + $user->memoReportsCount + $user->commentReportsCount;
            });

        // ソート基準に応じて並び替え
        switch ($this->sortCriteria) {
            case 'report_all':
                $all_suspended_users_data = $all_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->allReportsCount <=> $a->allReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_user':
                $all_suspended_users_data = $all_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->userReportsCount <=> $a->userReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_memo':
                $all_suspended_users_data = $all_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->memoReportsCount <=> $a->memoReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'report_comment':
                $all_suspended_users_data = $all_suspended_users_data->sort(function ($a, $b) {
                    $result = $b->commentReportsCount <=> $a->commentReportsCount;
                    if ($result === 0) {
                        return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                    }
                    return $result;
                });
                break;
            case 'nickname':
                $all_suspended_users_data = $all_suspended_users_data->sort(function ($a, $b) {
                    return mb_convert_kana($a->nickname, 'C') <=> mb_convert_kana($b->nickname, 'C');
                });
                break;
        }


        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_not_suspended_users_page');
        $items = $all_not_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_not_suspended_users_data_paginated = new LengthAwarePaginator($items, count($all_not_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_not_suspended_users_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_suspended_users_page');
        $items = $all_suspended_users_data->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $all_suspended_users_data_paginated = new LengthAwarePaginator($items, count($all_suspended_users_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_suspended_users_page'
        ]);


        return view('livewire.user-top-admin', compact('all_not_suspended_users_data_paginated', 'all_suspended_users_data_paginated'));
    }
}
