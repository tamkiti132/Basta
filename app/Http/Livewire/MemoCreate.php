<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

class MemoCreate extends Component
{
    use WithFileUploads;

    public $type;
    public $web_title;
    public $web_shortMemo;
    public $web_additionalMemo;
    public $url;

    public $book_title;
    public $book_shortMemo;
    public $book_additionalMemo;
    public $book_image;


    public $rules = [];


    public function mount()
    {
        // グループ内でのブロック状態を取得
        $isBlocked = User::where('id', Auth::id())
            ->whereHas('blockedGroup', function ($query) {
                $query->where(
                    'groups.id',
                    session()->get('group_id')
                );
            })->exists();

        // if ($isBlocked) {
        //     session()->flash('error', 'ブロックされているため、この機能は利用できません。');
        //     $this->redirectRoute('group.index', ['group_id' => session()->get('group_id')]);
        // } else {
        //     return view('group/memo_create');
        // }
    }


    public function store($type)
    {
        // dd($this->memo_data);

        $this->type = $type;

        if ($this->type === "web") {

            $this->rules = [
                'web_title' => ['required', 'string', 'max:50'],
                'web_shortMemo' => ['required', 'string', 'max:200'],
                'web_additionalMemo' => ['string', 'nullable'],
                'url' => ['required', 'url'],
            ];
        } elseif ($this->type === "book") {
            $this->rules = [
                'book_title' => ['required', 'string', 'max:50'],
                'book_shortMemo' => ['required', 'string', 'max:200'],
                'book_additionalMemo' => ['string', 'nullable'],
                'book_image' => ['nullable', 'image', 'max:2048'],
            ];
        }


        // dd($this->type, $this->rules);
        $this->validate();


        //TODO:group_id　と　type　を正しく登録できるようにする（今は仮のデータ）

        if ($this->type === "web") {
            //Webタイプのメモを保存する
            $memo_data = [
                'user_id' => Auth::id(),
                'group_id' => session()->get('group_id'),
                'title' => $this->web_title,
                'shortMemo' => $this->web_shortMemo,
                'additionalMemo' => $this->web_additionalMemo,
                'type' => 0,
            ];

            $web_type_feature_data = [
                'url' => $this->url,
            ];

            $memo = Memo::create($memo_data);
            $memo->web_type_feature()->create($web_type_feature_data);
        } else {
            //本タイプのメモを保存する
            $memo_data = [
                'user_id' => Auth::id(),
                'group_id' => session()->get('group_id'),
                'title' => $this->book_title,
                'shortMemo' => $this->book_shortMemo,
                'additionalMemo' => $this->book_additionalMemo,
                'type' => 1,
            ];

            $memo = Memo::create($memo_data);

            if ($this->book_image) {

                $book_image_path = $this->book_image->store('public/book-image/');

                $book_type_feature_data = [
                    'book_photo_path' => $book_image_path
                ];

                $memo->book_type_feature()->create($book_type_feature_data);
            }
        }
        $this->emitTo('label-adder', 'memoCreated', $memo->id);
    }


    public function render()
    {
        return view('livewire.memo-create');
    }
}
