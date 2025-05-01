<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GoodButton extends Component
{
    public $memo;

    public function mount($memo)
    {
        $this->memo = $memo;
    }

    public function toggleGood()
    {
        // なぜ、$this->memo->goods()->toggle(Auth::id());
        // ではなく、以下のような複雑なコードにしたかというと、
        // toggleメソッドだと、中間テーブルのcreated_atとupdated_atを更新してくれないため。

        $exists = $this->memo->goods()->where('user_id', Auth::id())->exists();

        if ($exists) {
            // 既に「いいね」がある場合は削除
            $this->memo->goods()->detach(Auth::id());
        } else {
            // 「いいね」を追加する場合はタイムスタンプを明示的に指定
            $now = Carbon::now();
            $this->memo->goods()->attach(Auth::id(), [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Memoモデルをリフレッシュして、goodの数を更新する
        $this->memo->refresh();
    }

    public function render()
    {
        return view('livewire.good-button');
    }
}
