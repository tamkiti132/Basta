<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ToggleButton extends Component
{
    public function toggleView()
    {
        // "toggleView"イベントを発行
        $this->emit('toggleView');
    }

    public function render()
    {
        return view('livewire.toggle-button');
    }
}
