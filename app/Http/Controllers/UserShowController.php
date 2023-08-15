<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserShowController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // dd($id);

        // $user = User::with([
        //     'report' => function ($query) {
        //         $query->where('type', 1);
        //     },
        //     'report as type_2_reports' => function ($query) {
        //         $query->where('type', 2);
        //     },
        //     'report as type_3_reports' => function ($query) {
        //         $query->where('type', 3);
        //     },
        // ])->find($id);

        // $reports_data = User::with('type_0_report', 'type_1_report', 'type_2_report')->find($id);

        // $reports_data = User::with([
        //     'type_0_report.contributor', 'type_0_report.target',
        //     'type_1_report.contributor', 'type_1_report.target',
        //     'type_2_report.contributor', 'type_2_report.target'
        // ])
        //     ->find($id);



        // $user = User::find($id);

        // // ユーザーが存在しない場合、nullを返す
        // if (!$user) {
        //     return null;
        // }

        // // ユーザーが対象となっているレポート一覧
        // $userReports = $user->userReports()->get();

        // // ユーザーが作成したメモが対象となっているレポートに紐づくメモ情報一覧
        // $memoReports = $user->memoReports()->get();

        // // ユーザーが作成したコメントが対象となっているレポートに紐づくコメント情報一覧
        // $commentReports = $user->commentReports()->get();



        // dd($userReports, $memoReports, $commentReports);

        // return view('admin.user_show', compact('reports_data'));

        // $userData = User::where('id', $id)
        //     ->with([
        //         'userReports.report',
        //         'memoReports.report.memo',
        //         'commentReports.report.comment',
        //     ])->first();

        // $targetMemoReports = $user->memoReports()->with(['report', 'memo'])->get()->groupBy('report.type');

        // dd($userData);

        // $comments = $user->comment;
        // $commentReports = $comments->flatMap(function ($comment) {
        //     return $comment->reports;
        // });

        // $groupedTargetReports = $targetReports->groupBy('type');
        // $groupedMemoReports = $memoReports->groupBy('type');
        // $groupedCommentReports = $commentReports->groupBy('type');





        // 3
        // ユーザIDが一致するusersテーブルの全てのデータを取得
        $user = User::find($id);
        dd($user);

        $userReports = $user->reports()->get();

        dd($userReports);



        dd($groupedTargetReports, $groupedMemoReports, $groupedCommentReports);

        // $type1Reports = $userTargetReports->target_report->filter(function ($report) {
        //     return $report->type == 1;
        // });




        // dd($userTargetReports);
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
