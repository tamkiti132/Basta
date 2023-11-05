<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Label;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LabelEditorMypage extends Component
{
    public $group_id;
    public $showLabelEditModal = false;
    public $labelName;
    public $labelNames = [];
    public $labels;

    protected $listeners = [
        'deleteLabel',
        'setGroupId',
    ];


    public function mount()
    {
        $this->loadLabels();
    }

    public function getListeners()
    {
        return [
            'showLabelEditModal' => 'showLabelEditModal',
            'setGroupId' => 'setGroupId',
            'deleteLabel' => 'deleteLabel',
        ];
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', $this->group_id)->get();
        foreach ($this->labels as $label) {
            $this->labelNames[$label->id] = $label->name;
        }
    }

    public function createLabel()
    {
        $this->validate([
            'labelName' => [
                'required',
                'string',
                'max:30',
                Rule::unique('labels', 'name')->where(function ($query) {
                    return $query->where('group_id', $this->group_id);
                }),
            ],
        ]);

        $label_data = [
            'group_id' => $this->group_id,
            'name' => $this->labelName,
        ];

        Label::create($label_data);

        $this->loadLabels();
        $this->reset('labelName');

        $this->emit('labelUpdated');
    }

    // TODO:バリデーションを加える
    public function updateLabel($labelId, $newName)
    {
        $label = Label::find($labelId);

        $rules = [
            'newName' => [
                'required',
                'string',
                'max:30',
                Rule::unique('labels', 'name')->ignore($labelId)->where(function ($query) {
                    return $query->where('group_id', $this->group_id);
                }),
            ],
        ];

        $validatedData = Validator::make(['newName' => $newName], $rules)->validate();

        $label->name = $validatedData['newName'];
        $label->save();

        $this->loadLabels();

        $this->emit('labelUpdated');
    }

    public function showLabelEditModal()
    {
        $this->showLabelEditModal = true;
    }

    public function closeLabelEditModal()
    {
        $this->showLabelEditModal = false;
        $this->labelName = '';
        $this->resetErrorBag();
    }

    public function deleteLabel($labelId)
    {
        Label::find($labelId)->delete();

        $this->loadLabels();

        $this->emit('labelUpdated', $labelId);
    }

    public function setGroupId($group_id = null)
    {
        // dd($group_id);

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
