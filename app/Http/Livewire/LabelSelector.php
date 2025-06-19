<?php

namespace App\Http\Livewire;

use App\Models\Label;
use App\Models\Memo;
use Livewire\Component;

class LabelSelector extends Component
{
    public $group_id;
    public $labels;
    public $checked = [];
    public $memoId;

    protected $listeners = [
        'saveLabels',
        'updated' => 'saveLabels',
    ];

    public function mount($memoId)
    {
        $this->group_id = session()->get('group_id');
        $this->memoId = $memoId;
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', $this->group_id)->orderBy('name')->get();
        $memo = Memo::find($this->memoId);

        foreach ($this->labels as $label) {
            $this->checked[$label->id] = $memo->labels->contains($label);
        }
    }

    public function updatedChecked($checked, $labelId)
    {
        // 選択状態を一時保存
        $this->checked[$labelId] = $checked;

        // イベントを発火してラベルリストを更新
        $this->emit('labelSelected', $this->checked);
    }

    public function saveLabels()
    {
        $memo = Memo::find($this->memoId);

        // チェックされているラベルだけを取得
        $checkedLabels = array_filter($this->checked, function ($value) {
            return $value === true; // trueの値のみを保持
        });

        $keysOfCheckedLabels = array_keys($checkedLabels);

        // ラベルの紐付けを更新
        $memo->labels()->sync($keysOfCheckedLabels);

        $this->dispatchBrowserEvent('flash-message', ['message' => '更新しました']);
    }

    public function render()
    {
        return view('livewire.label-selector');
    }
}
