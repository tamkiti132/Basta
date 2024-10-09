<?php

namespace App\Http\Livewire;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Report;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MemoShow extends Component
{
    use WithPagination;

    public $previous_route;

    public $group_id;

    public $memo_id;
    public $type;
    public $comment;

    // 各タブの表示状態を管理するプロパティ
    public $show_reports_memo = false;
    public $show_reports_comments = [];
    public $commentsReportsPage = [];


    protected $rules = [
        'comment' => ['required', 'string']
    ];


    public function mount($memo_id, $type, $group_id = null)
    {
        $this->previous_route = url()->previous();

        // メモに紐づくグループのidを取得
        $memo_posted_group_id = Memo::where('id', $memo_id)->value('group_id');

        $this->group_id = $memo_posted_group_id;

        // 運営ユーザー以上の権限を持つユーザーは常にアクセス可能
        if (!Auth::user()->can('admin-higher')) {
            // メモが投稿されたグループに自分が所属していない場合、直前のページにリダイレクト
            if (!(Auth::user()->group()->where('id', $memo_posted_group_id)->exists())) {
                session()->flash('error', '対象のグループに所属していないため、アクセスできません');
                redirect($this->previous_route);
            }
        }

        if ($group_id) {
            session()->put('group_id', $group_id);
        }

        $this->memo_id = $memo_id;
        $this->type = $type;

        // 各コメントのページネーション状態を初期化
        $comments = Comment::where('memo_id', $this->memo_id)->get();
        foreach ($comments as $comment) {
            $this->commentsReportsPage[$comment->id] = 1;
        }
    }

    public function checkSuspensionGroup()
    {
        $group = Group::find($this->group_id);

        // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
        if ($group && $group->suspension_state == 1) {
            session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません');

            $this->previous_route = url()->previous();
            return redirect($this->previous_route);
        }
    }


    public function toggleCommentReport($comment_id)
    {
        $this->show_reports_comments[$comment_id] = !$this->show_reports_comments[$comment_id];
    }



    public function deleteMemo($memo_id)
    {
        if ($this->checkSuspensionGroup()) {
            return;
        }

        $memo_data = Memo::find($memo_id);
        $memo_data->delete();

        return to_route('group.index', ['group_id' => session()->get('group_id')]);
    }


    public function storeComment()
    {
        if ($this->checkSuspensionGroup()) {
            return;
        }

        // グループ内でのブロック状態を取得
        $isBlocked = User::where('id', Auth::id())
            ->whereHas('blockedGroup', function ($query) {
                $query->where(
                    'groups.id',
                    session()->get('group_id')
                );
            })->exists();


        if ($isBlocked) {
            session()->flash('blockedUser', 'ブロックされているため、この機能は利用できません。');
            return redirect()->back();
        } else {

            $this->validate();

            $comments_data = [
                'user_id' => Auth::id(),
                'memo_id' => $this->memo_id,
                'comment' => $this->comment,
            ];

            Comment::create($comments_data);

            $this->reset(['comment']);
        }
    }


    public function deleteComment($comment_id)
    {
        if ($this->checkSuspensionGroup()) {
            return;
        }

        $comment_data = Comment::find($comment_id);
        $comment_data->delete();
    }


    public function render()
    {
        // メモ
        if ($this->type === "web") {
            $memo_data = Memo::with(['web_type_feature', 'user' => function ($query) {
                $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
            }, 'labels'])
                ->withCount('reports')
                ->find($this->memo_id);
        } else {
            $memo_data = Memo::with(['book_type_feature', 'user' => function ($query) {
                $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
            }, 'labels'])
                ->withCount('reports')
                ->find($this->memo_id);
        }



        //メモ通報情報
        $all_memo_reports_data = Report::whereIn('id', function ($query) {
            $query->select('report_id')
                ->from('memo_type_report_links')
                ->where('memo_id', $this->memo_id);
        })
            ->with(['contribute_user'])
            ->latest()
            ->get();



        // コメント, コメント通報情報
        $comments_data = Comment::with([
            'user' => function ($query) {
                $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
            },
            'memo' => function ($query) {
                $query->select('id', 'group_id');
            },
            'reports'
        ])
            ->where('memo_id', $this->memo_id)
            ->withCount('reports')
            ->get();

        // dd($comments_data);

        // 各コメントに対する表示状態を初期化
        foreach ($comments_data as $comment) {
            if (!array_key_exists($comment->id, $this->show_reports_comments)) {
                $this->show_reports_comments[$comment->id] = false;
            }
        }



        $perPage = 10;
        $perPage_for_report = 5;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('comments_data_page');
        $items = $comments_data->slice(($currentPage - 1) * $perPage, $perPage);
        $comments_data_paginated = new LengthAwarePaginator($items, count($comments_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'comments_data_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_memo_reports_page');
        $items = $all_memo_reports_data->slice(($currentPage - 1) * $perPage_for_report, $perPage_for_report);
        $all_memo_reports_data_paginated = new LengthAwarePaginator($items, count($all_memo_reports_data), $perPage_for_report, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_memo_reports_page'
        ]);



        return view('livewire.memo-show', compact('memo_data', 'comments_data_paginated', 'all_memo_reports_data_paginated'));
    }
}
