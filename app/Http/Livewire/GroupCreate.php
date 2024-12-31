<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class GroupCreate extends Component
{
    use WithFileUploads;

    public $group_id;
    public $group_image;
    public $group_name;
    public $introduction;

    public $storedImage;


    protected $rules = [
        'group_image' => ['nullable', 'image', 'max:2048'],
        'group_name' => ['required', 'string', 'max:50'],
        'introduction' => ['required', 'string', 'max:200'],
    ];


    public function mount() {}

    public function storeGroup()
    {
        $this->validate();

        if ($this->group_image) {

            $this->storedImage = $this->group_image->store('group-image', 'public');

            $group = Group::create([
                'group_photo_path' => basename($this->storedImage),
                'name' => $this->group_name,
                'introduction' => $this->introduction,
            ]);
        } else {
            $group = Group::create([
                'name' => $this->group_name,
                'introduction' => $this->introduction,
            ]);
        }

        $group->userRoles()->syncWithoutDetaching([
            Auth::id() => ['role' => 10]
        ]);


        return to_route('index');
    }


    public function deleteGroupImagePreview()
    {
        $this->group_image = null;
    }


    public function render()
    {
        return view('livewire.group-create');
    }
}
