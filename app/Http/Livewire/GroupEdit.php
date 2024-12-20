<?php

namespace App\Http\Livewire;

use App\Mail\InviteMail;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    public $storedImage;

    public $email;

    protected $rules = [
        'group_image_preview' => ['nullable', 'image', 'max:2048'],
        'group_data.name' => ['required', 'string', 'max:50'],
        'group_data.introduction' => ['required', 'string', 'max:200'],
    ];

    public function mount($group_id)
    {
        $group = Group::find($group_id);

        // グループが存在しない場合に 404 エラーを返す
        if (!$group) {
            abort(404);
        }

        //グループの管理者 and サブ管理者のIDを取得
        $manager_user_ids =
            $group->managerAndSubManagerUser($group_id)->pluck('user_id')->toArray();

        // グループの管理者のIDと　自分のIDが一致しない場合、直前のページにリダイレクト
        if (!in_array(Auth::id(), $manager_user_ids)) {
            session()->flash('error', '対象のグループの管理者 or サブ管理者ではないため、アクセスできません');
            $this->previous_route = url()->previous();
            return redirect($this->previous_route);
        }

        session()->put('group_id', $group_id);
        $this->group_id = $group_id;

        $this->group_data = Group::find($this->group_id);

        $this->checkSuspensionGroup();
    }

    public function checkSuspensionGroup()
    {
        $group = Group::find($this->group_id);

        // グループが存在し、suspension_stateが1の場合にエラーメッセージを出す
        if ($group && $group->suspension_state == 1) {
            session()->flash('error', 'このグループは現在利用停止中のため、この機能は利用できません');

            $this->previous_route = url()->previous();
            return redirect($this->previous_route);
        }
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
            $this->storedImage = $this->group_image_preview->store('group-image', 'public');
            $group_data->group_photo_path = basename($this->storedImage);
        }


        if ($this->group_image_delete_flag) {

            // ストレージから画像ファイルが存在するか確認して、あれば削除
            if ($group_data->group_photo_path && Storage::disk('public')->exists('group-image/' . $group_data->group_photo_path)) {
                Storage::disk('public')->delete('group-image/' . $group_data->group_photo_path);
            }

            // デーベース上のグループ画像パスをnullに更新
            $group_data->group_photo_path = null;
        }


        $group_data->save();

        $this->dispatchBrowserEvent('flash-message', ['message' => '更新しました']);
    }

    public function sendInviteToGroupMail()
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
        ]);

        // 指定のメールアドレスのユーザーがすでに$this->group_idにあたるグループに参加しているか確認
        $hasUser = Group::where('id', $this->group_id)
            ->whereHas('user', function ($query) {
                $query->where('email', $this->email);
            })->exists();


        if ($hasUser) {
            session()->flash('error', "指定のメールアドレスのユーザーは\nすでにグループに参加しています");
            return;
        } else {
            // $this->emailをもつユーザーを取得
            $target_user = User::where('email', $this->email)->first();

            // メール送信
            Mail::to($this->email)->send(new InviteMail($this->email, $this->group_data, $target_user));

            session()->flash('success', '招待メールを送信しました');

            // $this->emailを空にする
            $this->email = null;

            return redirect()->route('group.group_edit', ['group_id' => $this->group_id]);
        }
    }


    public function render()
    {
        return view('livewire.group-edit');
    }
}
