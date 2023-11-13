<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;
use App\Models\Memo;
use Illuminate\Support\Facades\Auth;

class LabelSelector extends Component
{
    public $labels;
    public $checked = [];
    public $memoId;

    protected $listeners = [
        'saveLabels',
        'updated' => 'saveLabels'
    ];


    public function mount($memoId)
    {
        // dd($memoId);
        $this->memoId = $memoId;
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->orderBy('name')->get();
        $memo = Memo::find($this->memoId);

        foreach ($this->labels as $label) {
            $this->checked[$label->id] = $memo->labels->contains($label);
            // dump($this->checked[$label->id]);
        }


        // dd($this->labels);
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

        // dd('ddd');
        $memo = Memo::find($this->memoId);

        // チェックされているラベルだけを取得
        $checkedLabels = array_filter($this->checked);

        // ラベルの紐付けを更新
        $memo->labels()->sync(array_keys($checkedLabels));

        $this->dispatchBrowserEvent('flash-message', ['message' => '更新しました']);
    }


    public function render()
    {
        return view('livewire.label-selector');
    }
}
