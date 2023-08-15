<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Web_type_feature;
use App\Models\Label;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class GroupTopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  int  $group_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $group_id)
    {
        // $search = $request->search;
        // $show_web = $request->query('web', 'true') === 'true';
        // $show_book = $request->query('book', 'true') === 'true';

        // $group_data = Group::find($group_id);
        session()->put('group_id', $group_id);

        // $web_memos_data = collect([]);
        // $book_memos_data = collect([]);

        // if ($show_web) {
        //     $web_memos_data = Memo::with('labels')
        //         ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
        //         ->join('users', 'memos.user_id', '=', 'users.id')
        //         ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
        //         ->where('group_id', session()->get('group_id'))
        //         ->search($search)
        //         ->get();
        // }

        // dd($web_memos_data);

        // if ($show_book) {
        //     $book_memos_data = Memo::with('labels')
        //         ->leftJoin('book_type_features', function ($join) {
        //             $join->on(
        //                 'memos.id',
        //                 '=',
        //                 'book_type_features.memo_id'
        //             );
        //         })
        //         ->join('users', 'memos.user_id', '=', 'users.id')
        //         ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
        //         ->where(function ($query) {
        //             $query->whereNotNull('book_type_features.memo_id')
        //                 ->orWhere(function ($query) {
        //                     $query->where('memos.type', 1)
        //                         ->whereNull('book_type_features.memo_id');
        //                 });
        //         })
        //         ->where('group_id', session()->get('group_id'))
        //         ->search($search)
        //         ->get();
        // }

        // $all_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();

        // $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // $perPage = 20;

        // $items = array_slice($all_memos_data, ($currentPage - 1) * $perPage, $perPage);

        // $all_memos_data_paginated = new LengthAwarePaginator($items, $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);


        // $labels_data = Label::all();

        // return view('group.top', compact('group_data'));
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $group_data = Group::find($id);
        $group_data->delete();

        return to_route('index');
    }

    /**
     * @param  int  $group_id
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function quit($group_id, $user_id)
    {
        // dd($id);

        // dd($request);

        // dd(Auth::user()->password);

        // $request->validate([
        //     'password' => ['required'],
        // ]);

        // if (!Hash::check($request->password, Auth::user()->password)) {
        //     return back()->withErrors([
        //         'password' => ['パスワードが一致しません。'],
        //     ]);
        // }

        $group_data = Group::find($group_id);
        $group_data->user()->detach($user_id);
        $group_data->userRoles()->detach($user_id);

        return to_route('index');
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeGroupTypeReport(Request $request)
    {
        // dd($request);

        $report_data = [
            'user_id' => Auth::id(),
            'type' => 3,
            'reason' => $request->reason,
            'detail' => $request->detail,
        ];

        $group_type_report_feature_data = [
            'group_id' => 1,
        ];

        $report = Report::create($report_data);
        $report->timestamp = false;
        $report->group_type_report_link()->create($group_type_report_feature_data);

        return to_route('group.index');
    }
}
