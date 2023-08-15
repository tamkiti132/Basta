<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Memo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MemberEditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_data = Group::find(session()->get('group_id'));

        // $all_users_data = User::withCount('memo')
        //     ->whereHas('group_user', 'group_id', 1)
        //     ->get();

        // $all_users_data = User::whereHas('group', function ($query) {
        //     $query->where('group_id', '=', 1);
        // })->get();

        // $all_users_data = User::join('group_user', 'users.id', '=', 'group_user.user_id')
        //     ->where('group_user.group_id', session()->get('group_id'))
        //     ->get();

        // これでも動く
        // $all_users_data = User::join('group_user', 'users.id', '=', 'group_user.user_id')
        //     ->where('group_user.group_id', session()->get('group_id'))
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('block_states')
        //             ->whereRaw('block_states.user_id = users.id')
        //             ->where('block_states.group_id', 2);
        //     })
        //     ->get();

        // ブロックされていないユーザー一覧取得
        // $all_not_blocked_users_data = User::withCount(['memo' => function ($query) {
        //     $query->where('group_id', session()->get('group_id'));
        // }])->whereHas('group', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->whereDoesntHave('blockedGroup', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->paginate(20, ['*'], 'not_blocked_page');

        // $all_not_blocked_users_data = User::with(['memo' => function ($query) {
        //     $query->where('group_id', session()->get('group_id'));
        // }, 'groupRoles' => function ($query) {
        //     $query->where('group_id', session()->get('group_id'));
        // }])->withCount(['memo' => function ($query) {
        //     $query->where('group_id', session()->get('group_id'));
        // }])->whereHas('group', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->whereDoesntHave('blockedGroup', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->paginate(20, ['*'], 'not_blocked_page');

        $all_not_blocked_users_data = User::with(['memo' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }, 'groupRoles' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }])->whereHas('group', function ($query) {
            $query->where('groups.id', session()->get('group_id'));
        })->whereDoesntHave('blockedGroup', function ($query) {
            $query->where('groups.id', session()->get('group_id'));
        })->paginate(20, ['*'], 'not_blocked_page');

        // ブロック中のユーザー一覧取得
        // $all_blocked_users_data = User::withCount('memo')->whereHas('group', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->whereHas('group', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->whereHas('blockedGroup', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->paginate(20, ['*'], 'blocked_page');

        // $all_blocked_users_data = User::withCount(['memo' => function ($query) {
        //     $query->where('group_id', session()->get('group_id'));
        // }])->whereHas('group', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->whereHas('blockedGroup', function ($query) {
        //     $query->where('groups.id', session()->get('group_id'));
        // })->paginate(20, ['*'], 'blocked_page');

        $all_blocked_users_data = User::with(['memo' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }, 'groupRoles' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }])->withCount(['memo' => function ($query) {
            $query->where('group_id', session()->get('group_id'));
        }])->whereHas('group', function ($query) {
            $query->where('groups.id', session()->get('group_id'));
        })->whereHas('blockedGroup', function ($query) {
            $query->where('groups.id', session()->get('group_id'));
        })->paginate(20, ['*'], 'blocked_page');

        // dd($all_not_blocked_users_data, $all_blocked_users_data);

        return view('group/member_edit', compact('group_data', 'all_not_blocked_users_data', 'all_blocked_users_data'));
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
    public function edit(/*$id*/)
    {
        return view('group.member_edit'/*,  compact('memo_data', 'labels_data') */);
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

    /**
     * @param  int  $group_id
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function quit($group_id, $user_id)
    {
        // dd($group_id, $user_id);

        $group_data = Group::find(session()->get('group_id'));
        $group_data->user()->detach($user_id);

        return to_route('group.member_edit.index');
    }

    public function updateRole(Request $request, User $user)
    {
        $group = Group::find(session()->get('group_id'));


        // dd($user);
        if ($group) {
            $user->groupRoles()->updateExistingPivot($group->id, ['role' => $request->role]);
        }

        return back();
    }


    /**
     * @param  int  $id
     */
    public function blockMember($id)
    {
        $user = User::find($id);

        // dd(session()->get('group_id'));

        $user->blockedGroup()->syncWithoutDetaching(session()->get('group_id'));

        return to_route('group.member_edit.index', ['group_id' => $id]);
    }


    /**
     * @param  int  $id
     */
    public function liftBlockMember($id)
    {
        $user = User::find($id);

        // dd(session()->get('group_id'));

        $user->blockedGroup()->detach(session()->get('group_id'));

        return to_route('group.member_edit.index', ['group_id' => $id]);
    }
}
