<?php

namespace App\Http\Controllers;

use App\Models\Book_type_feature;
use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\User;
use App\Models\Web_type_feature;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreMemoRequest;

class MemoCreateController extends Controller
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
        // グループ内でのブロック状態を取得
        $isBlocked = User::where('id', Auth::id())
            ->whereHas('blockedGroup', function ($query) {
                $query->where(
                    'groups.id',
                    session()->get('group_id')
                );
            })->exists();

        if ($isBlocked) {
            session()->flash('error', 'ブロックされているため、この機能は利用できません。');
            return redirect()->back();
        } else {
            return view('group/memo_create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMemoRequest $request)
    {
        // dd($request);

        //TODO:group_id　と　type　を正しく登録できるようにする（今は仮のデータ）

        if ($request->memo_type === "web") {
            //Webタイプのメモを保存する
            $memo_data = [
                'user_id' => Auth::id(),
                'group_id' => session()->get('group_id'),
                'title' => $request->web_title,
                'shortMemo' => $request->web_shortMemo,
                'additionalMemo' => $request->web_additionalMemo,
                'type' => 0,
            ];

            $web_type_feature_data = [
                'url' => $request->url,
            ];

            $memo = Memo::create($memo_data);
            $memo->web_type_feature()->create($web_type_feature_data);
        } else {
            //本タイプのメモを保存する
            $memo_data = [
                'user_id' => Auth::id(),
                'group_id' => session()->get('group_id'),
                'title' => $request->book_title,
                'shortMemo' => $request->book_shortMemo,
                'additionalMemo' => $request->book_additionalMemo,
                'type' => 1,
            ];

            $memo = Memo::create($memo_data);

            if ($request->book_image) {
                $book_image = $request->file('book_image');

                $book_image_path = $book_image->store('public/book-image/');

                // dd($book_image_path);

                $book_type_feature_data = [
                    'book_photo_path' => basename($book_image_path)
                ];

                $book_type_feature = new Book_type_feature($book_type_feature_data);
                $memo->book_type_feature()->save($book_type_feature);
            }
        }

        return to_route('group.index', ['group_id' => session()->get('group_id')]);
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
