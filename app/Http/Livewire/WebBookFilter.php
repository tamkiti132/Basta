<?php

namespace App\Http\Livewire;

use Livewire\Component;

class WebBookFilter extends Component
{
    public $type = null;

    protected $listeners = ['filterWebBook' => 'filter'];

    public function filter($type)
    {
        $this->type = $type; // フィルタリングタイプをセットします
        $this->emit('filteredMemos', $type); // イベントを発火して、新しいフィルタリングタイプを送信します
    }

    public function render() {}
}
