<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Memo;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCommentRequest;

class MemoShowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request)
    {
        // グループ内でのブロック状態を取得
        $isBlocked = User::where('id', Auth::id())
            ->whereHas('blockedGroup', function ($query) {
                $query->where(
                    'groups.id',
                    session()->get('group_id')
                );
            })->exists();

        // dd($request);

        if ($isBlocked) {
            session()->flash('error', 'ブロックされているため、この機能は利用できません。');
            return redirect()->back();
        } else {

            $comments_data = [
                'user_id' => Auth::id(),
                'memo_id' => $request->memo_id,
                'comment' => $request->comment,
            ];

            Comment::create($comments_data);

            return to_route('group.memo_show.show', ['id' => $request->memo_id]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id, $group_id = null)
    {
        if ($group_id) {
            session()->put('group_id', $group_id);
        }


        //メモ情報取得
        if ($request->type === "web") {
            $memo_data = Memo::with(['web_type_feature', 'user' => function ($query) {
                $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
            }, 'labels'])  // Add this to load labels related to the memo
                ->find($id);
        } else {
            $memo_data = Memo::with(['book_type_feature', 'user' => function ($query) {
                $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
            }, 'labels'])
                ->find($id);
        }

        // dd($memo_data);

        // コメント情報取得
        $comments_data = Comment::with(['user' => function ($query) {
            $query->select('id', 'email', 'username', 'nickname', 'username', 'profile_photo_path');
        }])
            ->where('memo_id', $id)
            ->get();


        // dd($memo_data, $comments_data);

        return view('group/memo_show', compact('memo_data', 'comments_data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyMemo(Request $request, $id)
    {
        // dd($request);

        $memo_data = Memo::find($id);
        $memo_data->delete();

        return to_route('group.index', ['group_id' => session()->get('group_id')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyComment(Request $request, $id)
    {
        // dd($request);

        $comment_data = Comment::find($id);
        $comment_data->delete();

        return to_route('group.memo_show.show', ['id' => $request->memo_id]);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMemoTypeReport(Request $request)
    {
        // dd($request);

        $report_data = [
            'user_id' => Auth::id(),
            'type' => 1,
            'reason' => $request->reason,
            'detail' => $request->detail,
        ];

        $memo_type_report_feature_data = [
            'memo_id' => $request->memo_id,
        ];

        $report = Report::create($report_data);
        $report->timestamp = false;
        $report->memo_type_report_link()->create($memo_type_report_feature_data);

        return to_route('group.memo_show.show', ['id' => $request->memo_id]);
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCommentTypeReport(Request $request)
    {
        // dd($request);

        $report_data = [
            'user_id' => Auth::id(),
            'type' => 2,
            'reason' => $request->reason,
            'detail' => $request->detail,
        ];

        $comment_type_report_feature_data = [
            'comment_id' => $request->comment_id,
        ];

        $report = Report::create($report_data);
        $report->timestamp = false;
        $report->comment_type_report_link()->create($comment_type_report_feature_data);

        return to_route('group.memo_show.show', ['id' => $request->memo_id]);
    }
}
