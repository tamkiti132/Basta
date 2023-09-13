<?php

namespace App\Http\Livewire;

use App\Models\Book_type_feature;
use App\Models\Memo;
use Livewire\Component;
use Livewire\WithFileUploads;

class MemoEdit extends Component
{
    use WithFileUploads;

    public $memo_data;
    public $memo_id;
    public $type;
    public $book_image;

    //TODO: protectedにすると、Missing [$rules/rules()] property/method on Livewire component: [memo-edit].のエラーが発生したため、publicにしたが、セキュリティ的な問題が懸念される。

    public $rules = [];


    public function mount($id, $type)
    {
        $this->memo_id = $id;
        $this->type = $type;

        if ($type === 'web') {

            $this->rules = [
                // 'memo_data.type' => ['string'],
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
                'book_image' => ['nullable', 'image', 'max:2048'],
            ];
        }

        // dd($this->rules);

        if ($this->type === "web") {
            $this->memo_data = Memo::with('web_type_feature')->find($this->memo_id);
        } else {
            $this->memo_data = Memo::with('book_type_feature')->find($this->memo_id);
        }

        // dd($this->memo_data);
        // dd(Memo::with('web_type_feature')->find($this->memo_id));
    }





    public function update()
    {
        $this->validate();

        $memo_data = Memo::find($this->memo_id);

        if ($this->type === "web") {
            // dd($memo_data);
            $memo_data->title = $this->memo_data['title'];
            $memo_data->shortMemo = $this->memo_data['shortMemo'];
            $memo_data->additionalMemo = $this->memo_data['additionalMemo'];
            $memo_data->web_type_feature->url = $this->memo_data['url'];
        } else {
            // dd($memo_data);
            $memo_data->title = $this->memo_data['title'];
            $memo_data->shortMemo = $this->memo_data['shortMemo'];
            $memo_data->additionalMemo = $this->memo_data['additionalMemo'];


            if ($this->book_image) {

                if ($this->book_image) {
                    $this->memo_data->book_type_feature->book_photo_path = $this->book_image->store('public/book-image/');

                    // dd($this->memo_data);
                    $this->memo_data->push();
                }
            }
        }

        // dd($data);
        $this->emitTo('label-selector', 'updated');

        $memo_data->save();
    }

    public function render()
    {


        return view('livewire.memo-edit');
    }
}
