<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $googleUser->id)->first();

            if ($finduser) {
                Auth::login($finduser);

                return redirect('/index');
            } else {
                // セッションをクリアしてリダイレクト
                session()->flush();

                return redirect()->route('login')
                    ->with('error', "このGoogleアカウントでの連携情報がありません。\nプロフィール画面からソーシャルログイン設定をしてください。");
            }
        } catch (\Exception $e) {
            Log::error('Google login error: '.$e->getMessage());
            session()->flush();

            return redirect()->route('login')->with('error', 'Googleログインがキャンセルされました。');
        }
    }
}
