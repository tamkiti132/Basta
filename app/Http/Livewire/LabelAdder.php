<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;
use App\Models\Memo;

class LabelAdder extends Component
{
    public $labels;
    public $checked = [];

    protected $listeners = [
        'saveLabels',
        'memoCreated' => 'saveLabels'
    ];


    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->orderBy('name')->get();
    }

    public function updatedChecked($checked, $labelId)
    {
        // 選択状態を一時保存
        $this->checked[$labelId] = $checked;

        // イベントを発火してラベルリストを更新
        $this->emit('labelSelected', $this->checked);
    }

    public function saveLabels($memoId)
    {
        // dd('ddd');
        if ($this->checked) {
            $memo = Memo::find($memoId);

            // チェックされているラベルだけを取得
            $checkedLabels = array_filter($this->checked);

            // メモにラベルを紐付ける
            $memo->labels()->sync(array_keys($checkedLabels));
        }

        $this->redirectRoute('group.index', ['group_id' => session()->get('group_id')]);
    }


    public function render()
    {
        return view('livewire.label-adder');
    }
}
