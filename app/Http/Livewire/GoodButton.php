<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GoodButton extends Component
{
    public $memo;

    public function mount($memo)
    {
        $this->memo = $memo;
    }

    public function toggleGood()
    {
        if (Auth::guest()) {
            return;
        }

        // 中間テーブルを１対多で繋げた場合
        // if ($this->memo->goods()->where('user_id', Auth::id())->exists()) {
        //     // User has already gooded this memo, so let's remove the good.
        //     $this->memo->goods()->where('user_id', Auth::id())->delete();
        // } else {
        //     // User has not yet gooded this memo, so let's add a new good.
        //     $this->memo->goods()->create(['user_id' => Auth::id()]);
        // }

        // 中間テーブルを多対多で繋げた場合
        $this->memo->goods()->toggle(Auth::id());


        // Refresh the memo model so the good count updates.
        $this->memo->refresh();
    }

    public function render()
    {
        return view('livewire.good-button');
    }
}
