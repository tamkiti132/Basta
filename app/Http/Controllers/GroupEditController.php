<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Requests\StoreGroupRequest;
use App\Mail\SendInviteMail;
use Illuminate\Support\Facades\Mail;

class GroupEditController extends Controller
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $group_id
     * @return \Illuminate\Http\Response
     */
    public function edit($group_id)
    {
        session()->put('group_id', $group_id);

        $group_data = Group::find($group_id);

        return view('group.group_edit', compact('group_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreGroupRequest $request, $id)
    {
        $group_data = Group::find($id);

        $group_data->name = $request->group_name;
        $group_data->introduction = $request->introduction;

        if ($request->group_image) {
            $group_image = $request->file('group_image');

            $group_image_data = $group_image->store('public/group-image/');

            $group_image_path = basename($group_image_data);

            $group_data->group_photo_path = $group_image_path;
        }

        $group_data->push();

        return to_route('group.group_edit.edit', ['group_id' => $id]);
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

    public function sendMail(Request $request)
    {

        // dd('aaa');
        // dd($request->email);
        // dd($request);
        $group_name = $request->group_name;
        $invite_url = "http://localhost/index";

        $to = [
            [
                'email' => $request->email,
            ]
        ];

        Mail::to($to)->send(new SendInviteMail($group_name, $invite_url));

        // return to_route('index');
        session()->flash('info', 'メールを送信しました');
        return redirect()->back();
    }
}
