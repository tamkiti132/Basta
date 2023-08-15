<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LaterReadButton extends Component
{
    public $memo;

    public function mount($memo)
    {
        $this->memo = $memo;
    }

    public function toggleLaterRead()
    {
        if (Auth::guest()) {
            return;
        }

        // 中間テーブルを１対多で繋げた場合
        // if ($this->memo->laterReads()->where('user_id', Auth::id())->exists()) {
        //     $this->memo->laterReads()->where('user_id', Auth::id())->delete();
        // } else {
        //     $this->memo->laterReads()->create(['user_id' => Auth::id()]);
        // }

        // 中間テーブルを多対多で繋げた場合
        $this->memo->laterReads()->toggle(Auth::id());

        $this->memo->refresh();
    }

    public function render()
    {
        return view('livewire.later-read-button');
    }
}
