<?php

use App\Http\Controllers\GroupCreateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupJoinController;
use App\Http\Controllers\GroupTopController;
use App\Http\Controllers\MemoCreateController;
use App\Http\Controllers\MemoEditController;
use App\Http\Controllers\GroupEditController;
use App\Http\Controllers\MemberEditController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MemoShowController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\UserTopController;
use App\Http\Controllers\UserShowController;
use App\Http\Controllers\MailSendController;
use App\Http\Livewire\MemoList;

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

        Route::get('/a', function () {
            return view('a');
        });


        Route::controller(IndexController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index')
                    ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                Route::get('index', 'index')->name('index')
                    ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                // Route::get('/', 'create')->name('create');
                // Route::post('/', 'store')->name('store');
            });

        // Route::get('/', IndexController::class, 'index');

        Route::prefix('group_create')
            ->controller(GroupCreateController::class)
            ->name('group_create.')
            ->group(function () {
                Route::get('/', 'create')->name('create');
                Route::post('/', 'store')->name('store');
            });

        Route::prefix('group_join')
            ->controller(GroupJoinController::class)
            ->name('group_join.')
            ->group(function () {
                Route::get('/', 'index')->name('index')
                    ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                Route::get('/{group_id}', 'joinGroup')->name('joinGroup')
                    ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
            });

        // Route::get('group_join', [GroupJoinController::class, 'index'])->name('group_join');

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
                // Route::get('/{id}', 'show')->name('show');
                Route::get('/{user_id}/{group_id?}', 'show')->name('show');
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

                // Route::get('/top/{group_id}', function ($group_id) {
                //     return view('group.top', ['group_id' => $group_id]);
                // })->name('index')
                //     ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);

                Route::controller(GroupTopController::class)
                    ->group(function () {
                        // Route::get('/top/{group_id}', 'index')->name('index')
                        //     ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                        Route::get('/top/{group_id}', MemoList::class)->name('index');

                        // Route::get('/', 'create')->name('create');
                        // Route::post('/', 'store')->name('store');
                        Route::post('/{id}/destroy', 'destroy')->name('destroy');
                        Route::post('/{group_id}/{user_id}/quit', 'quit')->name('quit')
                            ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                        Route::post('/report/group', 'storeGroupTypeReport')->name('storeGroupTypeReport');
                    });



                // Route::get('/memo_create', function () {
                //     return view('group/memo_create');
                // })->name('memo_create');


                Route::prefix('memo_create')
                    ->controller(MemoCreateController::class)
                    ->name('memo_create.')
                    ->group(function () {
                        Route::get('/', 'create')->name('create');
                        Route::post('/', 'store')->name('store');
                        // Route::get('/{id}', 'edit')->name('edit');
                    });


                Route::prefix('memo_edit')
                    ->controller(MemoEditController::class)
                    ->name('memo_edit.')
                    ->group(function () {
                        // Route::get('/', 'create')->name('create');
                        // Route::post('/', 'store')->name('store');
                        Route::get('/{id}/edit', 'edit')->name('edit');
                        Route::post('/{id}', 'update')->name('update');
                    });

                Route::prefix('memo_show')
                    ->controller(MemoShowController::class)
                    ->name('memo_show.')
                    ->group(function () {
                        // Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}/{group_id?}', 'show')->name('show')
                            ->withoutMiddleware([\App\Http\Middleware\CheckSuspensionState::class]);
                        Route::post('/{id}/destroyMemo', 'destroyMemo')->name('destroyMemo');
                        Route::post('/{id}/destroyComment', 'destroyComment')->name('destroyComment');
                        Route::post('/report/memo', 'storeMemoTypeReport')->name('storeMemoTypeReport');
                        Route::post('/report/comment', 'storeCommentTypeReport')->name('storeCommentTypeReport');
                        // Route::get('/{id}/edit', 'edit')->name('edit');
                        // Route::post('/{id}', 'update')->name('update');
                    });

                // Route::get('/memo_show', function () {
                //     return view('group/memo_show');
                // })->name('memo_show');

                Route::prefix('group_edit')
                    ->controller(GroupEditController::class)
                    ->name('group_edit.')
                    ->group(function () {
                        // Route::get('/', 'create')->name('create');
                        // Route::post('/', 'store')->name('store');
                        Route::get('/{group_id}/edit', 'edit')->name('edit');
                        Route::post('/{id}', 'update')->name('update');
                        Route::get('mail', 'sendMail')->name('sendMail');
                        // Route::post('/{id}/destroy', 'destroy')->name('destroy');
                    });
                // Route::get('/group_edit', function () {
                //     return view('group/group_edit');
                // })->name('group_edit');

                Route::prefix('member')
                    ->controller(MemberController::class)
                    ->name('member.')
                    ->group(function () {
                        // Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::get('/{id}', 'show')->name('show');
                        // Route::get('/{id}/edit', 'edit')->name('edit');
                        // Route::post('/{id}', 'update')->name('update');
                        Route::post('/{id}/destroy', 'destroy')->name('destroy');
                        Route::post('/{id}/suspend', 'suspend')->name('suspend');
                        Route::post('/report/user', 'storeUserTypeReport')->name('storeUserTypeReport');
                    });

                // Route::get('/member', function () {
                //     return view('group/member');
                // })->name('member');

                Route::prefix('member_edit')
                    ->controller(MemberEditController::class)
                    ->name('member_edit.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        // Route::get('/', 'create')->name('create');
                        // Route::post('/', 'store')->name('store');
                        // Route::get('/{id}/edit', 'edit')->name('edit');
                        // Route::get('/', 'edit')->name('edit');
                        Route::post('/{group_id}/{user_id}/quit', 'quit')->name('quit');
                        Route::post('/updateRole/{user}', 'updateRole')->name('updateRole');
                        Route::get('/{id}/blockMember', 'blockMember')->name('blockMember');
                        Route::get('/{id}/liftBlockMember', 'liftBlockMember')->name('liftBlockMember');
                    });
            });

        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {

                Route::prefix('user_top')
                    ->controller(UserTopController::class)
                    ->name('user_top.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        // Route::post('/', 'store')->name('store');
                        // Route::get('/{id}', 'show')->name('show');
                        // Route::get('/{id}/edit', 'edit')->name('edit');
                        // Route::post('/{id}', 'update')->name('update');
                        Route::post('/{id}/destroy', 'destroy')->name('destroy');
                        Route::post('/{id}/suspend', 'suspend')->name('suspend');
                        Route::post('/{id}/liftSuspend', 'liftSuspend')->name('liftSuspend');
                    });

                Route::prefix('user_show')
                    ->controller(UserShowController::class)
                    ->name('user_show.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        // Route::post('/', 'store')->name('store');
                        Route::get('/{id}', 'show')->name('show');
                        // Route::get('/{id}/edit', 'edit')->name('edit');
                        // Route::post('/{id}', 'update')->name('update');
                        Route::post('/{id}/destroy', 'destroy')->name('destroy');
                    });

                Route::get('/group', function () {
                    return view('admin/group_top');
                })->name('admin/group');
                Route::get('/group/show', function () {
                    return view('admin/group_show');
                })->name('admin/group_show');
                Route::get('/admin/user/show', function () {
                    return view('admin/admin_user_show');
                })->name('admin/admin/user/show');
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

// Route::prefix('manager')
//     ->middleware('can:manager-higher')->group(function () {
//         Route::get('index', function () {
//             dd('manager');
//         });
//     });
// Route::middleware('can:user-higher')->group(function () {
//     Route::get('index', function () {
//         dd('user');
//     });
// });
