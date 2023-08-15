<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('navigation-menu', function ($view) {
            if (Auth::check()) {
                $my_user_id = Auth::id();
                $user_groups = Group::whereHas('user', function ($query) use ($my_user_id) {
                    $query->where('group_user.user_id', $my_user_id);
                })->get();

                // dd($user_groups);

                $view->with('user_groups', $user_groups);
            }
        });
    }
}
