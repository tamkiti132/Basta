<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreGroupRequest;

class GroupCreateController extends Controller
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
        return view('group_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupRequest $request)
    {
        // dd($request);

        if ($request->group_image) {
            $group_image = $request->file('group_image');
            $group_photo_path = $group_image->store('public/group-image/');

            $group = Group::create([
                'group_photo_path' => basename($group_photo_path),
                'name' => $request->group_name,
                'introduction' => $request->introduction,
            ]);
        } else {
            $group = Group::create([
                'name' => $request->group_name,
                'introduction' => $request->introduction,
            ]);
        }

        // dd($group);

        $group->user()->sync(Auth::id());
        $group->userRoles()->syncWithoutDetaching([
            Auth::id() => ['role' => 10]
        ]);


        return to_route('index');
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
        //
    }
}
