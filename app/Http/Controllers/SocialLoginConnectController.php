<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginConnectController extends Controller
{
    public function index()
    {
        return view('social-login-connect');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirectUrl(route('social_login_connect.google.callback'))
            ->redirect();
    }

    public function disconnectGoogle()
    {
        try {
            $user = Auth::user();
            $user->google_id = null;
            $user->save();

            return redirect()->intended('/social-login-connect');
        } catch (\Exception $e) {
            Log::error('Google disconnect error: '.$e->getMessage());

            return redirect()->route('social_login_connect')->with('error', 'Googleアカウントの連携解除に失敗しました。');
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->redirectUrl(route('social_login_connect.google.callback'))->user();

            $authUser = Auth::user();
            $authUser->google_id = $user->getId();
            $authUser->save();

            return redirect()->intended('/social-login-connect');
        } catch (\Exception $e) {
            Log::error('Google login error: '.$e->getMessage());

            return redirect()->route('social_login_connect')->with('error', 'Googleアカウントの連携がキャンセルされました。');
        }
    }
}
