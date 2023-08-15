<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_type_report_link;
use App\Models\Memo_type_report_link;
use App\Models\Comment_type_report_link;

use Illuminate\Database\Eloquent\Builder;


class UserTopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // 利用停止されていないユーザー情報一覧取得
        $all_not_suspended_users_data = User::where('suspension_state', 0)
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                // dump($memoIds);
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();

                // ここで、groupReportsCount も同様に計算できます。
            });



        // 利用停止中のユーザー情報一覧取得
        $all_suspended_users_data = User::where('suspension_state', 1)
            ->get()
            ->each(function ($user) {
                $user->userReportsCount = User_type_report_link::where('user_id', $user->id)->count();

                $memoIds = $user->memo()->pluck('id');
                $user->memoReportsCount = Memo_type_report_link::whereIn('memo_id', $memoIds)->count();

                $commentIds = $user->comment()->pluck('id');
                $user->commentReportsCount = Comment_type_report_link::whereIn('comment_id', $commentIds)->count();

                // ここで、groupReportsCount も同様に計算できます。
            });



        // foreach ($all_users_data as $user) {
        //     dump($user->type_0_reports_count);
        //     dump($user->type_1_reports_count);
        //     dump($user->type_2_reports_count);
        // }

        // dd($all_not_suspended_users_data, $all_suspended_users_data);

        return view('admin/user_top', compact('all_not_suspended_users_data', 'all_suspended_users_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        // dd($id);

        $user_data = User::find($id);
        $user_data->delete();

        // dd($user_data);

        return to_route('admin.user_top.index');
    }

    /**
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function liftSuspend($id)
    {
        // dd($id);

        $user_data = User::find($id);

        // dd($user_data);

        $user_data->suspension_state = 0;
        $user_data->save();

        return to_route('admin.user_top.index');
    }
}
