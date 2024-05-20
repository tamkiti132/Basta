<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GroupEdit extends Component
{
    use WithFileUploads;

    public $previous_route;

    public $group_id;
    public $group_data;
    public $group_image_preview;
    public $group_image_delete_flag = false;


    protected $rules = [
        'group_image_preview' => ['nullable', 'image', 'max:2048'],
        'group_data.name' => ['required', 'string', 'max:50'],
        'group_data.introduction' => ['required', 'string', 'max:200'],
    ];

    public function mount($group_id)
    {
        $group = Group::find($group_id);
        $manager_user_id = $group->managerUser()->value('user_id');

        // dd($manager_user_id);

        // グループの管理者のIDと　自分のIDが一致しない場合、直前のページにリダイレクト
        if (Auth::id() !== $manager_user_id) {
            session()->flash('error', '対象のグループの管理者ではないため、アクセスできません');
            $this->previous_route = url()->previous();
            redirect($this->previous_route);
        }

        session()->put('group_id', $group_id);
        $this->group_id = $group_id;

        $this->group_data = Group::find($this->group_id);
    }


    public function deleteGroupImage()
    {
        $this->group_image_preview = null;

        $this->group_image_delete_flag = true;
    }

    public function updatedGroupImagePreview($value)
    {
        // $book_image_preview に新しい値がセットされたときに呼ばれる
        if (!is_null($value)) {
            $this->group_image_delete_flag = false;
        }
    }


    public function updateGroupInfo()
    {
        $this->validate();

        $group_data = Group::find($this->group_id);

        $group_data->name = $this->group_data['name'];
        $group_data->introduction = $this->group_data['introduction'];


        if ($this->group_image_preview) {
            $group_data->group_photo_path = basename($this->group_image_preview->store('public/group-image/'));
        }


        if ($this->group_image_delete_flag) {

            // ストレージから画像ファイルが存在するか確認して、あれば削除
            if ($group_data->group_photo_path && Storage::disk('public')->exists('group-image/' . $group_data->group_photo_path)) {
                Storage::disk('public')->delete('group-image/' . $group_data->group_photo_path);
            }

            // データベース上のグループ画像パスをnullに更新
            $group_data->group_photo_path = null;
        }


        $group_data->save();

        $this->dispatchBrowserEvent('flash-message', ['message' => '更新しました']);
    }


    public function render()
    {
        return view('livewire.group-edit');
    }
}
