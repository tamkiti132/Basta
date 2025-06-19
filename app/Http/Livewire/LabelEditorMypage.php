<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Label;

class LabelEditorMypage extends LabelEditor
{
    public $group_id;

    // 親クラスのリスナーを継承するためには、
    // $listenersプロパティではなく、getListenersメソッドを使う必要がある
    // （親クラスでは、$listenersプロパティでリスナーを設定する）
    public function getListeners()
    {
        // 親クラスのlistenersを継承するための記述
        return $this->listeners + [
            'setGroupId' => 'setGroupId',
        ];
    }

    public function mount()
    {
        $this->loadLabels();
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', $this->group_id)->orderBy('name')->get();
        foreach ($this->labels as $label) {
            $this->labelNames[$label->id] = $label->name;
        }
    }

    public function showLabelEditModal()
    {
        if ($this->group_id !== null) {
            $group = Group::find($this->group_id);

            // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
            if ($group && $group->suspension_state == 1) {
                session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません');

                $this->previous_route = url()->previous();

                return redirect($this->previous_route);
            }
        }

        $this->showLabelEditModal = true;
    }

    public function setGroupId($group_id = null)
    {
        $this->group_id = $group_id;

        if ($group_id !== null) {
            $this->loadLabels(); // グループIDが指定されている場合にのみラベル情報をロード
        }
    }

    public function render()
    {
        return view('livewire.label-editor-mypage');
    }
}
