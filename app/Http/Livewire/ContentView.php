<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ContentView extends Component
{
    public $isVisible = true;

    protected $listeners = ['toggleView' => 'toggle'];

    public function toggle()
    {
        $this->isVisible = !$this->isVisible;
    }

    public function render()
    {
        return view('livewire.content-view');
    }
}
