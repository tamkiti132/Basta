<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('adminTop', function ($user) {
            return $user->role === 3;
        });
        Gate::define('admin-higher', function ($user) {
            return $user->role >= 3 && $user->role <= 5;
        });
        Gate::define('manager', function ($user, $group_data) {
            $role = $user->groupRoles()->where('group_id', $group_data->id)->where('group_id', $group_data->id)->first()->pivot->role;
            return $role === 10;
        });
        Gate::define('subManager-to-manager', function ($user) {
            return $user->role >= 10 && $user->role <= 50;
        });
        Gate::define('member-to-manager', function ($user) {
            return $user->role >= 10 && $user->role <= 100;
        });
        Gate::define('member-higher', function ($user) {
            return $user->role >= 3  && $user->role <= 100;
        });
        Gate::define('notManager', function ($user, $group_data) {
            $role = $user->groupRoles()->where('group_id', $group_data->id)->first()->pivot->role;
            return $role !== 10;
        });
    }
}
