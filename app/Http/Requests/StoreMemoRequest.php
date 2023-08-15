<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if ($this->input('memo_type') === 'web') {
            return [
                'web_title' => ['required', 'string', 'max:50'],
                'web_shortMemo' => ['required', 'string', 'max:200'],
                'web_additionalMemo' => ['string', 'nullable'],
                'url' => ['required', 'url'],
            ];
        } elseif ($this->input('memo_type') === 'book') {
            return [
                'book_title' => ['required', 'string', 'max:50'],
                'book_shortMemo' => ['required', 'string', 'max:200'],
                'book_additionalMemo' => ['string', 'nullable'],
                'book_image' => ['image', 'max:2048'],

            ];
        }
    }
}
