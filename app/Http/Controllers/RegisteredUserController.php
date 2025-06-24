<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\RegisterViewResponse;

class RegisteredUserController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Show the registration view.
     */
    public function create(Request $request): RegisterViewResponse
    {
        return app(RegisterViewResponse::class);
    }

    /**
     * Create a new registered user.
     */
    public function store(Request $request, CreatesNewUsers $creator): RegisterResponse
    {
        event(new Registered($user = $creator->create($request->all())));

        $this->guard->login($user);

        return app(RegisterResponse::class);
    }

    /**
     * Create a new registered user（運営権限のユーザーとして登録）.
     */
    public function storeAdmin(Request $request, CreatesNewUsers $creator): RedirectResponse
    {
        // 運営トップ権限ユーザーであることを確認（Gateで）
        if (! Gate::allows('admin-top', Auth::user())) {
            return redirect()->route('admin.user_top');
        }

        $user = $creator->create($request->all());
        event(new Registered($user));

        Role::create([
            'group_id' => null,
            'user_id' => $user->id,
            'role' => 5,
        ]);

        return to_route('admin.admin_user_top');
    }
}
