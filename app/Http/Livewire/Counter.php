<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;
    public $count1 = 0;
    public $count2 = 0;
    public $text1 = false;
    public $text2 = false;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.counter');
    }

    public function textSwitch1()
    {
        if (!$this->text1) {
            $this->text1 = true;
        } else {
            $this->text1 = false;
        }
    }

    public function textSwitch2()
    {
        if (!$this->text2) {
            $this->text2 = true;
        } else {
            $this->text2 = false;
        }
    }
}
