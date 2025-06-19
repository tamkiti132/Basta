<?php

namespace App\Http\Livewire;

use Livewire\Component;

class WebBookLabel extends Component
{
    public $selected_web_book_labels = ['web', 'book'];

    public function mount() {}

    public function toggleLabel($labelType)
    {
        if (in_array($labelType, $this->selected_web_book_labels)) {
            $this->selected_web_book_labels = array_diff($this->selected_web_book_labels, [$labelType]);
        } else {
            $this->selected_web_book_labels[] = $labelType;
        }

        $this->emit('filterByWebBookLabels', $this->selected_web_book_labels);
    }

    public function render()
    {
        return view('livewire.web-book-label');
    }
}
