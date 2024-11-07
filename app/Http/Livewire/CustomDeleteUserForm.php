<?php

namespace App\Http\Livewire;

use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\DeletesUsers;
use Livewire\Component;
use App\Models\User;

class CustomDeleteUserForm extends Component
{
    // このファイルは、
    // vendor/laravel/jetstream/src/Http/Livewire/DeleteUserForm.php
    // をコピーしてカスタマイズしたものです。
    // （なぜコピーして編集したかというと、当時調べた限りでは、JetstreamのLivewireコンポーネント自体を直接パブリッシュする機能は、
    // Laravelの標準的なパッケージでは提供されていなかったためです。）

    /**
     * Indicates if user deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingUserDeletion = false;

    /**
     * The user's current password.
     *
     * @var string
     */
    public $password = '';

    /**
     * Confirm that the user would like to delete their account.
     *
     * @return void
     */
    public function confirmUserDeletion()
    {
        $this->resetErrorBag();

        $this->password = '';

        $this->dispatchBrowserEvent('confirming-delete-user');

        $this->confirmingUserDeletion = true;
    }


    /**
     * Check if the current user is any group manager.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\Contracts\DeletesUsers  $deleter
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $auth
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function isManager(Request $request, DeletesUsers $deleter, StatefulGuard $auth)
    {
        $this->resetErrorBag();

        if (! Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => [__('This password does not match our records.')],
            ]);
        }

        $userId = Auth::id();

        if (User::find($userId)->groupRoles()->where('role', 10)->exists()) {
            // ユーザーがいずれかのグループの管理者である場合は、削除処理を実行せず、警告文を表示する
            session()->flash('error', "このユーザーは管理者です。\nグループごとの時期管理者を設定してください");
        } else {
            // ユーザーがいずれかのグループの管理者でない場合は、削除処理を実行する
            $this->deleteUser($request, $deleter, $auth);
        }
    }


    /**
     * Delete the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\Contracts\DeletesUsers  $deleter
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $auth
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function deleteUser(Request $request, DeletesUsers $deleter, StatefulGuard $auth)
    {
        $deleter->delete(Auth::user()->fresh());

        $auth->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect(config('fortify.redirects.logout') ?? '/');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.custom-delete-user-form');
    }
}
