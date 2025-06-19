<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GoodButton extends Component
{
    public $memo;
    public $isGood = false;

    public function mount($memo, $isGood = false)
    {
        $this->memo = $memo;
        $this->isGood = $isGood;
    }

    public function hydrate($memo, $isGood = false)
    {
        $this->isGood = $isGood;
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
            $this->isGood = false;
        } else {
            // 「いいね」を追加する場合はタイムスタンプを明示的に指定
            $now = Carbon::now();
            $this->memo->goods()->attach(Auth::id(), [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->isGood = true;
        }

        // goodsモデルを再度読み込んで、good数の表示を更新する
        $this->memo->load('goods');
    }

    public function render()
    {
        return view('livewire.good-button');
    }
}
