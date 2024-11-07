<?php

namespace App\Actions\Jetstream;

use App\Models\Comment;
use App\Models\Comment_type_report_link;
use App\Models\Memo;
use App\Models\Memo_type_report_link;
use App\Models\Report;
use App\Models\User;
use App\Models\User_type_report_link;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        $user_id = $user->id;
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

        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
