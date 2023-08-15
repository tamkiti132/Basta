<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class MemberController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // dd($id);

        $search = $request->search;

        $user_data = User::find($id);

        // $memo_data = Memo::with('web_type_feature')->find($id);
        $web_memos_data = Memo::with('labels')
            ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
            ->join('users', 'memos.user_id', '=', 'users.id')
            ->join('groups', 'memos.group_id', '=', 'groups.id')
            ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
            ->where('users.id', $id)
            ->where('group_id', session()->get('group_id'))
            ->search($search)
            ->get();

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
            ->where('users.id', $id)
            ->where(function ($query) {
                $query->whereNotNull('book_type_features.memo_id')
                    ->orWhere(function ($query) {
                        $query->where('memos.type', 1)
                            ->whereNull('book_type_features.memo_id');
                    });
            })
            ->where('group_id', session()->get('group_id'))
            ->search($search)
            ->get();

        // dd($web_memos_data, $book_memos_data);

        $all_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;

        $items = array_slice($all_memos_data, ($currentPage - 1) * $perPage, $perPage);

        $all_memos_data_paginated = new LengthAwarePaginator($items, count($all_memos_data), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        $count_all_memos_data = count($all_memos_data);

        $labels_data = Label::all();

        // 退会済みか確認
        $exists = Group::where('id', session()->get('group_id'))->whereHas('user', function ($query) use ($id) {
            $query->where('id', $id);
        })->exists();

        // dd($exists);

        if (!$exists) {
            session()->flash('quit', 'このユーザーはグループを退会済みです。');
        }

        // dd($user_data, $all_memos_data, $count_all_memos_data, $labels_data);

        return view('group/member', compact('user_data', 'count_all_memos_data', 'all_memos_data_paginated', 'labels_data'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);

        $user_data = User::find($id);
        $user_data->delete();

        // dd($user_data);

        return to_route('admin.user_top.index');
    }

    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function suspend($id)
    {
        // dd($id);

        $user_data = User::find($id);

        // dd($user_data);

        $user_data->suspension_state = 1;
        $user_data->save();

        return to_route('admin.user_top.index');
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeUserTypeReport(Request $request)
    {
        // dd($request);

        $report_data = [
            'user_id' => Auth::id(),
            'type' => 0,
            'reason' => $request->reason,
            'detail' => $request->detail,
        ];

        $user_type_report_feature_data = [
            'user_id' => $request->user_id,
        ];

        $report = Report::create($report_data);
        $report->timestamp = false;
        $report->user_type_report_link()->create($user_type_report_feature_data);

        return to_route('group.member.show', ['id' => $request->user_id]);
    }
}
