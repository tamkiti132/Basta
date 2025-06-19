<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MemoCreate extends Component
{
    use WithFileUploads;

    public $previous_route;

    public $group_id;

    public $type;
    public $web_title;
    public $web_shortMemo;
    public $web_additionalMemo;
    public $url;

    public $book_title;
    public $book_shortMemo;
    public $book_additionalMemo;
    public $book_image;

    public $storedBookImage;

    public $rules = [];

    public function mount($group_id)
    {
        $this->previous_route = url()->previous();

        // $group_idに一致するidのグループに自分が所属していなかった場合、直前のページにリダイレクト
        if (! (Auth::user()->groupRoles()->where('group_id', $group_id)->exists())) {
            session()->flash('error', '対象のグループに所属していないため、アクセスできません');
            redirect($this->previous_route);
        }

        session()->put('group_id', $group_id);

        // グループ内でのブロック状態を取得
        $isBlocked = User::where('id', Auth::id())
            ->whereHas('blockedGroup', function ($query) {
                $query->where(
                    'groups.id',
                    session()->get('group_id')
                );
            })->exists();

        if ($isBlocked) {
            session()->flash('blockedUser', 'ブロックされているため、この機能は利用できません。');
            $this->redirectRoute('group.index', ['group_id' => session()->get('group_id')]);
        }

        $this->group_id = session()->get('group_id');

        $this->checkSuspensionGroup();
    }

    public function checkSuspensionGroup()
    {
        $group = Group::find($this->group_id);

        // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
        if ($group && $group->suspension_state == 1) {
            session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません');

            $this->previous_route = url()->previous();

            return redirect($this->previous_route);
        }
    }

    public function store($type)
    {
        $this->type = $type;

        $this->validate([
            'type' => ['required', 'in:web,book'],
        ]);

        if ($this->type === 'web') {

            $this->rules = [
                'web_title' => ['required', 'string', 'max:50'],
                'web_shortMemo' => ['required', 'string', 'max:200'],
                'web_additionalMemo' => ['string', 'nullable'],
                'url' => ['required', 'url'],
            ];
        } elseif ($this->type === 'book') {
            $this->rules = [
                'book_title' => ['required', 'string', 'max:50'],
                'book_shortMemo' => ['required', 'string', 'max:200'],
                'book_additionalMemo' => ['string', 'nullable'],
                'book_image' => ['nullable', 'image', 'max:2048'],
            ];
        }

        $this->validate();

        if ($this->type === 'web') {
            // Webタイプのメモを保存する
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
            // 本タイプのメモを保存する
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

                $this->storedBookImage = $this->book_image->store('book-image', 'public');

                $memo->book_type_feature()->create([
                    'book_photo_path' => basename($this->storedBookImage),
                ]);
            }
        }

        $this->emitTo('label-adder', 'memoCreated', $memo->id);
    }

    public function render()
    {
        return view('livewire.memo-create');
    }
}
