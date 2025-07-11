<?php

namespace App\Http\Livewire;

use App\Models\Label;
use Livewire\Component;

class LabelList extends Component
{
    public $labels;
    public $selected_labels = [];

    protected $listeners = ['labelUpdated' => 'loadLabels'];

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels($labelId = null)
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->orderBy('name')->get();

        if ($labelId) {
            $this->deleteLabel($labelId);
        }
    }

    public function deleteLabel($labelId)
    {
        // $label_id の値のキーを検索
        $key = array_search($labelId, $this->selected_labels);

        // 値が見つかった場合、そのキーを使用して値を削除
        if ($key !== false) {
            unset($this->selected_labels[$key]);
        }

        $this->emit('filterByLabels', $this->selected_labels);
    }

    public function toggleLabel($labelId)
    {
        if (in_array($labelId, $this->selected_labels)) {
            $this->selected_labels = array_diff($this->selected_labels, [$labelId]);
        } else {
            $this->selected_labels[] = $labelId;
        }

        $this->emit('filterByLabels', $this->selected_labels);
    }

    public function render()
    {
        return view('livewire.label-list');
    }
}
