<?php

namespace App\Http\Livewire;

use App\Mail\SendRequestMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class Request extends Component
{
    use WithFileUploads;

    public $request_type;

    // サービスの不具合の報告
    public $email_1;
    public $title_1;
    public $detail_1;
    public $environment_1;
    public $additional_information;
    public $reference_url_1;
    public $uploaded_photo_1;

    // サービス機能の追加・改善リクエスト
    public $email_2;
    public $function_request_type;
    public $title_2;
    public $detail_2;
    public $environment_2;
    public $reference_url_2;
    public $uploaded_photo_2;

    // セキュリティ脆弱性の報告
    public $email_3;
    public $title_3;
    public $detail_3;
    public $explanation;
    public $steps_to_reproduce;
    public $abuse_method;
    public $workaround;
    public $environment_3;
    public $reference_url_3;
    public $uploaded_photo_3;

    // その他お問い合わせ
    public $email_4;
    public $title_4;
    public $detail_4;
    public $environment_4;
    public $reference_url_4;
    public $uploaded_photo_4;

    public $rules = [];



    public function sendRequest($request_type)
    {
        $this->request_type = $request_type;

        if ($this->request_type === "type_1") {


            $this->rules = [
                'email_1' => ['required', 'string', 'email', 'max:255'],
                'title_1' => ['required', 'string', 'max:100'],
                'detail_1' => ['required', 'string'],
                'environment_1' => ['required'],
                'additional_information' => ['nullable', 'string'],
                'reference_url_1' => ['nullable', 'url'],
                'uploaded_photo_1' => ['nullable', 'image', 'max:2048'],
            ];
        } elseif ($this->request_type === "type_2") {

            $this->rules = [
                'email_2' => ['required', 'string', 'email', 'max:255'],
                'function_request_type' => ['required'],
                'title_2' => ['required', 'string', 'max:100'],
                'detail_2' => ['required', 'string'],
                'environment_2' => ['required'],
                'reference_url_2' => ['nullable', 'url'],
                'uploaded_photo_2' => ['nullable', 'image', 'max:2048'],
            ];
        } elseif ($this->request_type === "type_3") {

            $this->rules = [
                'email_3' => ['required', 'string', 'email', 'max:255'],
                'title_3' => ['required', 'string', 'max:100'],
                'detail_3' => ['required', 'string'],
                'explanation' => ['nullable', 'string'],
                'steps_to_reproduce' => ['required', 'string'],
                'abuse_method' => ['nullable', 'string'],
                'workaround' => ['nullable', 'string'],
                'environment_3' => ['required'],
                'reference_url_3' => ['nullable', 'url'],
                'uploaded_photo_3' => ['nullable', 'image', 'max:2048'],
            ];
        } elseif ($this->request_type === "type_4") {

            $this->rules = [
                'email_4' => ['required', 'string', 'email', 'max:255'],
                'title_4' => ['required', 'string', 'max:100'],
                'detail_4' => ['required', 'string'],
                'environment_4' => ['required'],
                'reference_url_4' => ['nullable', 'url'],
                'uploaded_photo_4' => ['nullable', 'image', 'max:2048'],
            ];
        }

        $this->validate();

        if ($this->request_type === "type_1") {

            $report_data = [
                'email_1' => $this->email_1,
                'title_1' => $this->title_1,
                'detail_1' => $this->detail_1,
                'environment_1' => $this->environment_1,
                'additional_information' => $this->additional_information,
                'reference_url_1' => $this->reference_url_1,
                'uploaded_photo_1' => $this->uploaded_photo_1,
            ];
        } elseif ($this->request_type === "type_2") {

            $report_data = [
                'email_2' => $this->email_2,
                'function_request_type' => $this->function_request_type,
                'title_2' => $this->title_2,
                'detail_2' => $this->detail_2,
                'environment_2' => $this->environment_2,
                'reference_url_2' => $this->reference_url_2,
                'uploaded_photo_2' => $this->uploaded_photo_2,
            ];
        } elseif ($this->request_type === "type_3") {

            $report_data = [
                'email_3' => $this->email_3,
                'title_3' => $this->title_3,
                'detail_3' => $this->detail_3,
                'explanation' => $this->explanation,
                'steps_to_reproduce' => $this->steps_to_reproduce,
                'abuse_method' => $this->abuse_method,
                'workaround' => $this->workaround,
                'environment_3' => $this->environment_3,
                'reference_url_3' => $this->reference_url_3,
                'uploaded_photo_3' => $this->uploaded_photo_3,
            ];
        } elseif ($this->request_type === "type_4") {

            $report_data = [
                'email_4' => $this->email_4,
                'title_4' => $this->title_4,
                'detail_4' => $this->detail_4,
                'environment_4' => $this->environment_4,
                'reference_url_4' => $this->reference_url_4,
                'uploaded_photo_4' => $this->uploaded_photo_4,
            ];
        }


        Mail::to('basta.h.a.132@gmail.com')->send(new SendRequestMail($this->request_type, $report_data));

        session()->flash('success', 'リクエストを送信しました。');

        //TODO: これを設定しないと、メール送信後のフラッシュメッセージが表示されたりされなかったりするので、応急処置でつけた
        return redirect()->route('request');
    }

    public function render()
    {
        return view('livewire.request');
    }
}
