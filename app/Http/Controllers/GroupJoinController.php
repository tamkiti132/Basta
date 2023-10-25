<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupJoinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;

        // 全角スペースを半角スペースに変換
        $search = str_replace("　", " ", $search);

        // 半角スペースで検索ワードを分解
        $keywords = explode(' ', $search);

        // $all_groups_data = Group::all();
        $all_groups_data = Group::whereDoesntHave('user', function ($query) {
            $query->where('group_user.user_id', Auth::id());
        })->with(['userRoles' => function ($query) {
            $query->where('roles.role', 10);
        }])
            ->withCount('user')
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('introduction', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->paginate(20);

        // dd($all_groups_data);

        return view('group_join', compact('all_groups_data'));
    }

    public function joinGroup($group_id)
    {
        $group = Group::find($group_id);

        if ($group->isJoinFreeEnabled) {
            $group->user()->syncWithoutDetaching(Auth::id());
            $group->userRoles()->syncWithoutDetaching([
                Auth::id() => ['role' => 100]
            ]);
        } else {
            session()->flash('isNotJoinFreeEnabled', 'このグループへの参加は現在許可されていません。');
            return redirect()->back();
        }

        return to_route('group.index', ['group_id' => $group_id]);
    }
}
