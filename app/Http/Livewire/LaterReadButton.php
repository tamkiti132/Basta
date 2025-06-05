<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaterReadButton extends Component
{
    public $memo;
    public $isLaterRead = false;

    public function mount($memo, $isLaterRead = false)
    {
        $this->memo = $memo;
        $this->isLaterRead = $isLaterRead;
    }

    public function hydrate($memo, $isLaterRead = false)
    {
        $this->isLaterRead = $isLaterRead;
    }

    public function toggleLaterRead()
    {
        // なぜ、$this->memo->laterReads()->toggle(Auth::id());
        // ではなく、以下のような複雑なコードにしたかというと、
        // toggleメソッドだと、中間テーブルのcreated_atとupdated_atを更新してくれないため。

        $exists = $this->memo->laterReads()->where('user_id', Auth::id())->exists();

        if ($exists) {
            // 既に「あとで読む」に追加されている場合は削除
            $this->memo->laterReads()->detach(Auth::id());
            $this->isLaterRead = false;
        } else {
            // 「あとで読む」に追加する場合はタイムスタンプを明示的に指定
            $now = Carbon::now();
            $this->memo->laterReads()->attach(Auth::id(), [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->isLaterRead = true;
        }

        // laterReadsモデルを再度読み込んで、laterRead数の表示を更新する
        $this->memo->load('laterReads');
    }

    public function render()
    {
        return view('livewire.later-read-button');
    }
}
