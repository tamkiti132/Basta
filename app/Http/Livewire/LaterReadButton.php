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

        $this->memo->laterReads()->toggle(Auth::id());

        // Memoモデルをリフレッシュして、laterReadの数を更新する
        $this->memo->refresh();
    }

    public function render()
    {
        return view('livewire.later-read-button');
    }
}
