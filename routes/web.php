<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\GroupEditController;
use App\Http\Controllers\InviteController;
use App\Http\Livewire\MemoCreate;
use App\Http\Livewire\MemoEdit;
use App\Http\Livewire\MemoList;
use App\Http\Livewire\MemoListMember;
use App\Http\Livewire\MemoListMypage;
use App\Http\Livewire\UserShow;
use App\Http\Livewire\GroupTopAdmin;
use App\Http\Livewire\GroupShowAdmin;
use App\Http\Livewire\AdminUserTop;
use App\Http\Livewire\Index;
use App\Http\Livewire\GroupJoin;
use App\Http\Livewire\GroupEdit;
use App\Http\Livewire\GroupCreate;
use App\Http\Livewire\MemberEdit;
use App\Http\Livewire\UserTopAdmin;
use App\Http\Livewire\MemoShow;
use App\Http\Livewire\Request;
use App\Http\Middleware\CheckSuspensionState;
use App\Http\Livewire\SocialLoginConnect;
use App\Http\Controllers\SocialLoginConnectController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

Route::middleware(['auth', CheckSuspensionState::class])

    ->group(function () {

        Route::get('/', Index::class)->name('index')
            ->withoutMiddleware([CheckSuspensionState::class]);
        Route::get('/index', Index::class)
            ->withoutMiddleware([CheckSuspensionState::class]);

        Route::get('group_create', GroupCreate::class)->name('group_create');

        Route::prefix('group_join')
            ->name('group_join.')
            ->group(function () {
                Route::get('/', GroupJoin::class)->name('index');
            });

        Route::get('/invite/join-group/{group_id}/{target_user_id}', [InviteController::class, 'joinGroup'])
            ->name('invite.joinGroup')
            ->middleware('signed');

        Route::get('timeout', function () {
            return view('timeout');
        });

        Route::get('cannot_access', function () {
            return view('cannot_access');
        });

        Route::prefix('mypage')
            ->name('mypage.')
            ->group(function () {
                Route::get('/{user_id}/{group_id?}', MemoListMypage::class)->name('show')
                    ->withoutMiddleware([CheckSuspensionState::class]);
            });

        //TODO: クレジットカード関連のルーティング（あとでやる）
        // Route::get('creditcard', function () {
        //     return view('creditcard');
        // })->name('creditcard')
        //     ->withoutMiddleware([CheckSuspensionState::class]);


        Route::prefix('request')
            ->group(function () {
                Route::get('/', Request::class)->name('request')
                    ->withoutMiddleware([CheckSuspensionState::class]);
            });

        Route::get('/social-login-connect', [SocialLoginConnectController::class, 'index'])->name('social_login_connect');
        Route::get('/social-login-connect/google', [SocialLoginConnectController::class, 'redirectToGoogle'])->name('social_login_connect.google');
        Route::get('/social-login-connect/google/disconnect', [SocialLoginConnectController::class, 'disconnectGoogle'])->name('social_login_connect.google.disconnect');
        Route::get('/social-login-connect/google/callback', [SocialLoginConnectController::class, 'handleGoogleCallback'])->name('social_login_connect.google.callback');



        Route::prefix('group')
            ->name('group.')
            ->group(function () {

                Route::get('/top/{group_id}', MemoList::class)->name('index');

                Route::prefix('memo_create')
                    ->name('memo_create.')
                    ->group(function () {
                        Route::get('/{group_id}', MemoCreate::class)->name('create');
                    });


                Route::prefix('memo_edit')
                    ->name('memo_edit.')
                    ->group(function () {
                        Route::get('/{memo_id}/edit/{type}', MemoEdit::class)->name('edit');
                    });


                Route::prefix('memo_show')
                    ->group(function () {
                        Route::get('/{memo_id}/show/{group_id?}', MemoShow::class)->name('memo_show');
                    });


                Route::prefix('group_edit')
                    ->group(function () {
                        Route::get('/{group_id}', GroupEdit::class)->name('group_edit');
                        Route::get('mail', [GroupEditController::class, 'sendMail'])->name('group_edit.sendMail');
                    });

                Route::prefix('member_show')
                    ->group(function () {
                        Route::get('/{group_id}/{user_id}', MemoListMember::class)->name('member_show');
                    });

                Route::prefix('member_edit')
                    ->group(function () {
                        Route::get('/{group_id}', MemberEdit::class)->name('member_edit');
                    });
            });

        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {

                Route::prefix('user_top')
                    ->middleware('can:admin-higher')
                    ->group(function () {
                        Route::get('/', UserTopAdmin::class)->name('user_top')
                            ->withoutMiddleware([CheckSuspensionState::class]);
                    });

                Route::prefix('user_show')
                    ->middleware('can:admin-higher')
                    ->group(function () {
                        Route::get('/{user_id}/{group_id?}', UserShow::class)->name('user_show');
                    });

                //TODO:このファイルのwithoutMiddlewareを外すのを忘れていたので、外して、かつそれが問題ないかテストして検討する
                Route::prefix('group_top')
                    ->middleware('can:admin-higher')
                    ->group(function () {
                        Route::get('/', GroupTopAdmin::class)->name('group_top')
                            ->withoutMiddleware([CheckSuspensionState::class]);
                    });


                Route::prefix('group_show')
                    ->middleware('can:admin-higher')
                    ->group(function () {
                        Route::get('/{group_id}', GroupShowAdmin::class)->name('group_show');
                    });


                Route::prefix('admin_user_top')
                    ->middleware('can:admin-top')
                    ->group(function () {
                        Route::get('/', AdminUserTop::class)->name('admin_user_top');
                    });
            });
    });



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

require_once __DIR__ . '/fortify.php';
