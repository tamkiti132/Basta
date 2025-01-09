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
        $this->memo->goods()->toggle(Auth::id());


        // Memoモデルをリフレッシュして、goodの数を更新する
        $this->memo->refresh();
    }

    public function render()
    {
        return view('livewire.good-button');
    }
}
