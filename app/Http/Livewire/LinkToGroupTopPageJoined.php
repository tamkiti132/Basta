<?php

namespace App\Http\Livewire;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LinkToGroupTopPageJoined extends Component
{
    public $user_groups;

    protected $listeners = [
        'joinedGroup' => 'refreshUserGroups',
    ];

    public function mount()
    {
        $this->fetchUserGroups();
    }

    public function refreshUserGroups()
    {
        $this->fetchUserGroups();
    }

    public function fetchUserGroups()
    {
        if (Auth::check()) {
            $my_user_id = Auth::id();
            $this->user_groups = Group::whereHas('userRoles', function ($query) use ($my_user_id) {
                $query->where('user_id', $my_user_id);
            })->orderBy('name')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.link-to-group-top-page-joined');
    }
}
