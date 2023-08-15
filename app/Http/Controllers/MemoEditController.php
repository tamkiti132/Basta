<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Label;
use App\Models\Web_type_feature;
use App\Http\Requests\StoreMemoRequest;
use App\Models\Book_type_feature;

class MemoEditController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // dd($request->type, $id);

        $memo_data = Memo::find($id);
        if ($request->type === "web") {
            $memo_data = Memo::with('web_type_feature')->find($id);
        } else {
            $memo_data = Memo::with('book_type_feature')->find($id);
        }


        // $memo = $memo_data;

        // $memo_data = Memo::with('web_type_feature')->where('memo_id', '=', '' $id)

        // $memo_data = Memo::with('web_type_feature')
        //     ->where('id', $id)
        //     ->first();

        // $memo_data = Memo::with('web_type_feature')->where('id', $id)->first();


        // $memo_data = Memo::find($id)->web_type_feature;

        // $memo_data = Memo::with('web_type_feature')
        //     ->whereHas('web_type_feature') // web_type_featuresテーブルとの関連が存在するデータのみ取得
        //     ->find($id);



        // dd($memo_data->);

        $labels_data = Label::all();

        return view('group.memo_edit', compact('memo_data', 'labels_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreMemoRequest $request, $id)
    {
        $memo_data = Memo::find($id);
        // $memo_data = Memo::with('web_type_feature')->find($id);

        // dd($memo_data, $request, $id);
        // dd($request->shortMemo);

        if ($request->memo_type === "web") {
            // dd($memo_data);
            $memo_data->title = $request->web_title;
            $memo_data->shortMemo = $request->web_shortMemo;
            $memo_data->additionalMemo = $request->web_additionalMemo;

            $memo_data->web_type_feature->url = $request->url;

            $memo_data->push();
        } else {
            // dd($memo_data, $request);
            // dd($memo_data->book_type_feature->book_photo_path);
            $memo_data->title = $request->book_title;
            $memo_data->shortMemo = $request->book_shortMemo;
            $memo_data->additionalMemo = $request->book_additionalMemo;

            if ($request->book_image) {
                // dd($request->book_image);
                $book_image = $request->file('book_image');

                $book_image_data = $book_image->store('public/book-image/');

                $book_image_path = basename($book_image_data);

                $book_type_feature = $memo_data->book_type_feature;
                if (!$book_type_feature) {
                    $book_type_feature = new Book_type_feature;
                    $book_type_feature->memo_id = $memo_data->id;
                }

                $book_type_feature->book_photo_path = $book_image_path;

                // dd($book_type_feature);
                // dd($memo_data->book_type_feature());

                $book_type_feature->save();

                $memo_data->book_type_feature()->save($book_type_feature);
            }
            $memo_data->save();
        }


        return to_route('group.index', ['group_id' => session()->get('group_id')]);
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
