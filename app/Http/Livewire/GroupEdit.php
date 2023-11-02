<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Livewire\Component;
use Livewire\WithFileUploads;

class GroupEdit extends Component
{
    use WithFileUploads;

    public $group_id;
    public $group_data;
    public $group_image;


    protected $rules = [
        'group_image' => ['nullable', 'image', 'max:2048'],
        'group_data.group_name' => ['required', 'string', 'max:50'],
        'group_data.introduction' => ['required', 'string', 'max:200'],
    ];

    public function mount($group_id)
    {
        session()->put('group_id', $group_id);
        $this->group_id = $group_id;

        $this->group_data = Group::find($this->group_id);
    }

    public function updateGroupInfo()
    {
        $this->validate();

        $group_data = Group::find($this->group_id);

        $group_data->name = $this->group_data['group_name'];
        $group_data->introduction = $this->group_data['introduction'];

        if ($this->group_image) {
            $group_data->group_photo_path = basename($this->group_image->store('public/group-image/'));
        }

        $group_data->save();

        $this->dispatchBrowserEvent('flash-message', ['message' => '更新しました']);
    }


    public function render()
    {
        return view('livewire.group-edit');
    }
}
