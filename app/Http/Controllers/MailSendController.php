<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\SendInviteMail;
use Illuminate\Support\Facades\Mail;

class MailSendController extends Controller
{
    public function send()
    {

        // dd('aaa');

        $to = [
            [
                'email' => 'harada.a.0907@gmail.com',
                'name' => 'テスト ユーザー',
            ]
        ];

        Mail::to($to)->send(new SendInviteMail());

        // return to_route('index');
        session()->flash('info', 'メールを送信しました');
        return redirect()->back();
    }
}
