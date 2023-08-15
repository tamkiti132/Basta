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


    public function mount($memoId)
    {
        // dd($memoId);
        $this->memoId = $memoId;
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->get();
        $memo = Memo::find($this->memoId);

        foreach ($this->labels as $label) {
            $this->checked[$label->id] = $memo->labels->contains($label);
            // dump($this->checked[$label->id]);
        }


        // dd($this->labels);
    }

    //TODO:ここで、非同期でラベルをメモに紐付けしてしまっているので、『メモ更新ボタン』 を押した時にメモに紐付けするように変更する。
    public function updatedChecked($checked, $labelId)
    {
        $memo = Memo::find($this->memoId);
        $label = Label::find($labelId);

        if ($checked) {
            // チェックボックスがチェックされた場合、メモとラベルを紐付ける
            $memo->labels()->attach($label);
        } else {
            // チェックボックスがチェックされていない場合、メモとラベルの紐付けを解除する
            $memo->labels()->detach($label);
        }

        $this->emit('labelSelected');
    }

    public function render()
    {
        return view('livewire.label-selector');
    }
}
