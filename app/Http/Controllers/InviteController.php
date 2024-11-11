<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function joinGroup($group_id, $target_user_id)
    {
        $group = Group::find($group_id);

        if (!$group) {
            return to_route('index')->with('error', '招待されたグループが見つかりません。');
        }

        // ログインユーザーのIDが$target_user_idと一致しているか確認
        if (Auth::id() != $target_user_id) {
            return to_route('index')->with('error', "グループ招待機能は、\n招待されたユーザーでログインした場合のみ実行できます。");
        }


        $group->user()->syncWithoutDetaching($target_user_id);
        $group->userRoles()->syncWithoutDetaching([
            $target_user_id => ['role' => 100]
        ]);
        return to_route('group.index', ['group_id' => $group_id])->with('success', '招待されたグループに参加しました。');
    }
}
