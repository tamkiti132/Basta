<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Livewire\Component;
use App\Models\Label;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;


class LabelEditor extends Component
{
    public $group_id;

    public $previous_route;

    public $showLabelEditModal = false;
    public $labelName;
    public $labelNames = [];
    public $labels;
    protected $listeners = ['deleteLabel' => 'deleteLabel'];




    public function mount()
    {
        $this->loadLabels();

        $this->group_id = session()->get('group_id');
    }

    public function getListeners()
    {
        return [
            'showLabelEditModal' => 'showLabelEditModal',
            'deleteLabel' => 'deleteLabel'
        ];
    }

    public function loadLabels()
    {
        $this->labels = Label::where('group_id', session()->get('group_id'))->orderBy('name')->get();
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

        $group = Group::find($this->group_id);

        // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
        if ($group && $group->suspension_state == 1) {
            session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません');

            $this->previous_route = url()->previous();
            return redirect($this->previous_route);
        }

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

        $this->emit('labelDeleted', $labelId);
        $this->emit('labelUpdated');
    }



    public function render()
    {
        return view('livewire.label-editor');
    }
}
