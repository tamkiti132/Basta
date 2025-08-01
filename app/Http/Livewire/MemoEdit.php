<?php

namespace App\Http\Livewire;

use App\Models\Book_type_feature;
use App\Models\Group;
use App\Models\Memo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class MemoEdit extends Component
{
    use WithFileUploads;

    public $previous_route;

    public $group_id;

    public $memo_data;
    public $memo_id;
    public $type;
    public $book_image_delete_flag = false;
    public $book_image_preview;

    public $storedBookImage;

    // TODO: protectedにすると、Missing [$rules/rules()] property/method on Livewire component: [memo-edit].のエラーが発生したため、publicにしたが、セキュリティ的な問題が懸念される。

    public $rules = [];

    public function mount($memo_id, $type)
    {
        $this->previous_route = url()->previous();

        $memo_posted_user_id = Memo::where('id', $memo_id)->value('user_id');

        $this->group_id = Memo::where('id', $memo_id)->value('group_id');

        // 自分が作成したメモかどうかを確認
        if (Auth::id() !== $memo_posted_user_id) {
            session()->flash('error', '他のユーザーのメモは編集できません');
            redirect($this->previous_route);
        }

        $this->memo_id = $memo_id;
        $this->type = $type;

        if ($type === 'web') {

            $this->rules = [
                'memo_data.title' => ['required', 'string', 'max:50'],
                'memo_data.shortMemo' => ['required', 'string', 'max:200'],
                'memo_data.additionalMemo' => ['string', 'nullable'],
                'memo_data.web_type_feature.url' => ['required', 'url'],
            ];
        } elseif ($type === 'book') {
            $this->rules = [
                'memo_data.title' => ['required', 'string', 'max:50'],
                'memo_data.shortMemo' => ['required', 'string', 'max:200'],
                'memo_data.additionalMemo' => ['string', 'nullable'],
                'book_image_preview' => ['nullable', 'image', 'max:2048'],
            ];
        }

        if ($this->type === 'web') {
            $this->memo_data = Memo::with('web_type_feature')->find($this->memo_id);
        } else {
            $this->memo_data = Memo::with('book_type_feature')->find($this->memo_id);
        }

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

    public function deleteBookImage()
    {
        $this->book_image_preview = null;

        $this->book_image_delete_flag = true;
    }

    public function updatedBookImagePreview($value)
    {
        // $book_image_preview に新しい値がセットされたときに呼ばれる
        if (! is_null($value)) {
            $this->book_image_delete_flag = false;
        }
    }

    public function update()
    {
        $this->validate([
            'type' => ['required', 'in:web,book'],
        ]);

        $this->validate();

        if ($this->type === 'web') {
            $memo_data = Memo::with('web_type_feature')->find($this->memo_id);

            $memo_data->title = $this->memo_data['title'];
            $memo_data->shortMemo = $this->memo_data['shortMemo'];
            $memo_data->additionalMemo = $this->memo_data['additionalMemo'];

            $memo_data->web_type_feature->url = $this->memo_data['web_type_feature']['url'];
        } else {
            $memo_data = Memo::with('book_type_feature')->find($this->memo_id);

            $memo_data->title = $this->memo_data['title'];
            $memo_data->shortMemo = $this->memo_data['shortMemo'];
            $memo_data->additionalMemo = $this->memo_data['additionalMemo'];

            if ($this->book_image_preview) {
                $feature = $this->memo_data->book_type_feature;

                if (! $feature) {
                    $feature = new Book_type_feature;
                    $feature->memo_id = $this->memo_data->id;
                }

                $this->storedBookImage = $this->book_image_preview->store('book-image', 'public');
                $feature->book_photo_path = basename($this->storedBookImage);
                $feature->save();
            }

            if ($this->book_image_delete_flag) {

                // 関連するBookTypeFeatureモデルが存在するか確認
                if ($memo_data && $memo_data->book_type_feature) {
                    // 関連する画像ファイルがストレージに存在する場合は削除
                    if (Storage::disk('public')->exists('book-image/'.$memo_data->book_type_feature->book_photo_path)) {
                        Storage::disk('public')->delete('book-image/'.$memo_data->book_type_feature->book_photo_path);
                    }

                    // book_type_featuresテーブルの関連レコードを削除
                    $memo_data->book_type_feature()->delete();
                }
            }
        }

        $this->emitTo('label-selector', 'updated');

        $memo_data->save();

        if ($this->type === 'web') {
            $memo_data->web_type_feature->save();
        }
    }

    public function render()
    {
        return view('livewire.memo-edit');
    }
}
