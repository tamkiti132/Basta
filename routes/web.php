<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemoEditController;
use App\Http\Controllers\GroupEditController;
use App\Http\Controllers\MemoShowController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\MailSendController;
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

// use App\Http\Controllers\MemoController;
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

Route::middleware(['auth', 'check_suspension'])

    ->group(function () {

        // Route::controller(IndexController::class)
        //     ->group(function () {
        //         Route::get('/', 'index')->name('index')
        //             ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
        //         Route::get('index', 'index')->name('index')
        //             ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
        //     });

        Route::get('/', Index::class)->name('index');
        Route::get('/index', Index::class);

        Route::get('group_create', GroupCreate::class)->name('group_create');

        // Route::prefix('group_join')
        //     ->controller(GroupJoinController::class)
        //     ->name('group_join.')
        //     ->group(function () {
        //         Route::get('/', 'index')->name('index')
        //             ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
        //         Route::get('/{group_id}', 'joinGroup')->name('joinGroup')
        //             ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
        //     });

        Route::prefix('group_join')
            ->name('group_join.')
            ->group(function () {
                Route::get('/', GroupJoin::class)->name('index');
            });

        Route::get('timeout', function () {
            return view('timeout');
        });

        Route::get('cannot_access', function () {
            return view('cannot_access');
        });

        Route::prefix('mypage')
            ->controller(MypageController::class)
            ->name('mypage.')
            ->group(function () {
                Route::get('/{user_id}/{group_id?}', MemoListMypage::class)->name('show');
            });

        Route::get('creditcard', function () {
            return view('creditcard');
        })->name('creditcard');

        Route::get('request', function () {
            return view('request');
        })->name('request');


        // Route::get('mail', [MailSendController::class, 'send']);



        Route::prefix('group')
            ->name('group.')
            ->group(function () {

                Route::get('/top/{group_id}', MemoList::class)->name('index');

                Route::prefix('memo_create')
                    ->name('memo_create.')
                    ->group(function () {
                        Route::get('/', MemoCreate::class)->name('create');
                    });


                Route::prefix('memo_edit')
                    ->controller(MemoEditController::class)
                    ->name('memo_edit.')
                    ->group(function () {
                        Route::get('/{id}/edit/{type}', MemoEdit::class)->name('edit');
                    });

                Route::prefix('memo_show')
                    ->controller(MemoShowController::class)
                    ->name('memo_show.')
                    ->group(function () {
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}/{group_id?}', 'show')->name('show')
                            ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                        Route::post('/{id}/destroyMemo', 'destroyMemo')->name('destroyMemo');
                        Route::post('/{id}/destroyComment', 'destroyComment')->name('destroyComment');
                        Route::post('/report/memo', 'storeMemoTypeReport')->name('storeMemoTypeReport');
                        Route::post('/report/comment', 'storeCommentTypeReport')->name('storeCommentTypeReport');
                    });

                Route::prefix('group_edit')
                    ->group(function () {
                        Route::get('/{group_id}', GroupEdit::class)->name('group_edit');
                        Route::get('mail', [GroupEditController::class, 'sendMail'])->name('group_edit.sendMail');
                    });

                Route::prefix('member')
                    ->controller(MemberController::class)
                    ->name('member.')
                    ->group(function () {
                        Route::get('/{id}', MemoListMember::class)->name('show')
                            ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                    });

                Route::prefix('member_edit')
                    ->group(function () {
                        Route::get('/', MemberEdit::class)->name('member_edit');
                    });
            });

        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {

                Route::prefix('user_top')
                    ->group(function () {
                        Route::get('/', UserTopAdmin::class)->name('user_top');
                    });

                Route::prefix('user_show')
                    ->group(function () {
                        Route::get('/{user_id}/{group_id?}', UserShow::class)->name('user_show')
                            ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                    });

                //TODO:このファイルのwithoutMiddlewareを外すのを忘れていたので、外して、かつそれが問題ないかテストして検討する
                Route::prefix('group_top')
                    ->group(function () {
                        Route::get('/', GroupTopAdmin::class)->name('group_top');
                    });


                Route::prefix('group_show')
                    ->group(function () {
                        Route::get('/{group_id}', GroupShowAdmin::class)->name('group_show');
                    });


                Route::prefix('admin_user_top')
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
