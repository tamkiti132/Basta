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

        //運営トップ
        Gate::define('admin-top', function ($user) {
            $group_role = $user->roles->first();

            if ($group_role) {
                $role = $group_role->role;
                return $role == 3;
            } else {
                return false;
            }
        });

        //運営ユーザー（運営トップを除く）
        Gate::define('admin', function ($user) {
            $group_role = $user->roles->first();

            if ($group_role) {
                $role = $group_role->role;
                return $role == 5;
            } else {
                return false;
            }
        });

        //運営ユーザー以上
        Gate::define('admin-higher', function ($user) {
            $group_role = $user->roles->first();

            if ($group_role) {
                $role = $group_role->role;
                return $role >= 3 && $role <= 5;
            } else {
                return false;
            }
        });

        //指定グループの 管理者
        Gate::define('manager', function ($user, $group_data) {
            // ユーザーのグループ内での権限が存在することを確認            
            // $user->groupRoles()->where('group_id', $group_data->id)->first()->pivot->role
            // と1行で書いてしまうと、
            // $user->groupRoles()->where('group_id', $group_data->id)->first()
            // がnullであった場合、そのnullのオブジェクトに対してpivotプロパティをアクセスしようとしてエラーが起きてしまう。
            $group_role = $user->groupRoles()->where('group_id', $group_data->id)->first();

            // group_roleがnullでないことを確認
            if ($group_role) {
                //グループに所属しているユーザーはここでさらにその権限が何かをチェックされる
                $role = $group_role->pivot->role;
                return $role === 10;
            } else {
                // グループに所属していないユーザー、運営権限のユーザーはこの条件に当たる
                return false;
            }
        });

        //指定グループの 管理者 ・ サブ管理者
        Gate::define('subManager-to-manager', function ($user, $group_data) {
            $group_role = $user->groupRoles()->where('group_id', $group_data->id)->first();

            if ($group_role) {
                $role = $group_role->pivot->role;
                return $role >= 10 && $role <= 50;
            } else {
                return false;
            }
        });

        //指定グループの 管理者 ・ サブ管理者 ・ メンバー
        Gate::define('member-to-manager', function ($user) {
            return $user->role >= 10 && $user->role <= 100;
        });

        //指定グループの メンバー 以上　
        Gate::define('member-higher', function ($user) {
            return $user->role >= 3  && $user->role <= 100;
        });

        //指定グループの管理者 以外
        Gate::define('notManager', function ($user, $group_data) {
            $group_role = $user->groupRoles()->where('group_id', $group_data->id)->first();

            if ($group_role) {
                $role = $group_role->pivot->role;
                return $role !== 10;
            } else {
                return false;
            }
        });

        //運営ユーザー以外 （どの権限ももたないユーザーを含む）
        Gate::define('admin-lower', function ($user) {
            $group_role = $user->roles->first();

            if ($group_role) {
                $role = $group_role->role;
                return $role > 5;
            } else {
                return false;
            }
        });
    }
}
