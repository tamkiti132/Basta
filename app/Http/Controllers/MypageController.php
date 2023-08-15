<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Memo;
use App\Models\Group;
use App\Models\Comment;
use App\Models\Label;
use Illuminate\Pagination\LengthAwarePaginator;

class MypageController extends Controller
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
     * @param  int  $user_id
     * @param  int  $group_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $user_id, $group_id = null)
    {
        // dd($group_id);

        $user_groups = Group::whereHas('user', function ($query) use ($user_id) {
            $query->where('users.id', $user_id);
        })->get();

        $search = $request->search;


        if ($group_id) {
            //自分が作成したメモ
            $web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $user_id)
                ->where('group_id', $group_id)
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
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $user_id)
                ->where('group_id', $group_id)
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();


            //いいねしたメモ
            $good_web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('goods', 'goods.memo_id', '=', 'memos.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where(
                    'goods.user_id',
                    $user_id
                )
                ->where('group_id', $group_id)
                ->search($search)
                ->get();

            $good_book_memos_data = Memo::with('labels')
                ->leftJoin('book_type_features', function ($join) {
                    $join->on(
                        'memos.id',
                        '=',
                        'book_type_features.memo_id'
                    );
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('goods', 'goods.memo_id', '=', 'memos.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where(
                    'goods.user_id',
                    $user_id
                )
                ->where('group_id', $group_id)
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();


            //あとでよむしたメモ
            $later_read_web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('later_reads.user_id', $user_id)
                ->where('group_id', $group_id)
                ->search($search)
                ->get();

            $later_read_book_memos_data = Memo::with('labels')
                ->leftJoin('book_type_features', function ($join) {
                    $join->on(
                        'memos.id',
                        '=',
                        'book_type_features.memo_id'
                    );
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('later_reads.user_id', $user_id)
                ->where('group_id', $group_id)
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();
        } else {
            //自分が作成したメモ
            $web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $user_id)
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
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('users.id', $user_id)
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();


            //いいねしたメモ
            $good_web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('goods', 'goods.memo_id', '=', 'memos.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where(
                    'goods.user_id',
                    $user_id
                )
                ->search($search)
                ->get();

            $good_book_memos_data = Memo::with('labels')
                ->leftJoin('book_type_features', function ($join) {
                    $join->on(
                        'memos.id',
                        '=',
                        'book_type_features.memo_id'
                    );
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('goods', 'goods.memo_id', '=', 'memos.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where(
                    'goods.user_id',
                    $user_id
                )
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();


            //あとでよむしたメモ
            $later_read_web_memos_data = Memo::with('labels')
                ->join('web_type_features', 'memos.id', '=', 'web_type_features.memo_id')
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                ->select('memos.*', 'web_type_features.url', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('later_reads.user_id', $user_id)
                ->search($search)
                ->get();

            $later_read_book_memos_data = Memo::with('labels')
                ->leftJoin('book_type_features', function ($join) {
                    $join->on(
                        'memos.id',
                        '=',
                        'book_type_features.memo_id'
                    );
                })
                ->join('users', 'memos.user_id', '=', 'users.id')
                ->join('later_reads', 'later_reads.memo_id', '=', 'memos.id')
                ->select('memos.*', 'book_type_features.book_photo_path', 'users.id as memo_user_id', 'users.email', 'users.nickname', 'users.username', 'users.profile_photo_path')
                ->where('later_reads.user_id', $user_id)
                ->where(function ($query) {
                    $query->whereNotNull('book_type_features.memo_id')
                        ->orWhere(function ($query) {
                            $query->where('memos.type', 1)
                                ->whereNull('book_type_features.memo_id');
                        });
                })
                ->search($search)
                ->get();
        }

        $all_my_memos_data = $web_memos_data->concat($book_memos_data)->sortBy('updated_at')->values()->all();

        $all_good_memos_data = $good_web_memos_data->concat($good_book_memos_data)->sortBy('updated_at')->values()->all();

        $all_later_read_memos_data = $later_read_web_memos_data->concat($later_read_book_memos_data)->sortBy('updated_at')->values()->all();

        // dd($all_memos_data, $all_good_memos_data, $all_later_read_memos_data);

        $perPage = 20;

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_my_memos_page');
        $items = array_slice($all_my_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_my_memos_data_paginated = new LengthAwarePaginator($items, count($all_my_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_my_memos_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_good_memos_page');
        $items = array_slice($all_good_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_good_memos_data_paginated = new LengthAwarePaginator($items, count($all_good_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_good_memos_page'
        ]);

        $currentPage = LengthAwarePaginator::resolveCurrentPage('all_later_read_memos_page');
        $items = array_slice($all_later_read_memos_data, ($currentPage - 1) * $perPage, $perPage);
        $all_later_read_memos_data_paginated = new LengthAwarePaginator($items, count($all_later_read_memos_data), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'all_later_read_memos_page'
        ]);

        $count_all_my_memos_data = count($all_my_memos_data);

        $labels_data = Label::all();

        // dd($all_memos_data, $count_all_memos_data);

        return view('mypage', compact(
            'user_groups',
            'group_id',
            'all_my_memos_data_paginated',
            'all_good_memos_data_paginated',
            'all_later_read_memos_data_paginated',
            'count_all_my_memos_data',
            'labels_data'
        ));
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
        //
    }
}
