<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LoginResponse implements LoginResponseContract
{
  public function toResponse($request)
  {
    $user = Auth::user();

    if (Gate::allows('admin-higher', $user)) {
      return redirect('/admin/user_top');
    } else {
      return redirect('/index');
    }
  }
}
