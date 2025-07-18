<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if (Gate::allows('admin-higher', $user)) {
            return redirect()->route('admin.user_top');
        } else {
            return redirect()->intended('/index');
        }
    }
}
