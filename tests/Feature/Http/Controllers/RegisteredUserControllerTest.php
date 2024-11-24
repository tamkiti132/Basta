<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisteredUserControllerTest extends TestCase
{
    public function test_ユーザー登録画面が表示できる(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_ユーザー登録機能が無効の場合、ユーザー登録画面にアクセスすると404が返る(): void
    {
        if (Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is enabled.');

            return;
        }

        $response = $this->get('/register');

        $response->assertStatus(404);
    }

    public function test_新規ユーザーが登録できる_一般ユーザー(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        // Act（実行）
        $response = $this->post('/register', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        // Assert（検証）
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_新規ユーザーが登録できる_運営ユーザー(): void
    {
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        // Arrange（準備）

        // 運営トップ権限ユーザーを用意
        $adminTopUser = User::create([
            'nickname' => 'AdminTopUser',
            'email' => 'test_admin_top@example.com',
            'password' => Hash::make('passwordAdminTop'),
            'username' => '@' . (string) Str::ulid(),
        ]);

        Role::create([
            'user_id' => $adminTopUser->id,
            'role' => 3,
        ]);

        // 運営トップ権限ユーザーでログイン
        $this->post('/login', [
            'email' => 'test_admin_top@example.com',
            'password' => 'passwordAdminTop',
        ]);

        // Act（実行）
        $response = $this->post('/registerAdmin', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        // Assert（検証）
        // 運営トップ権限ユーザーがログインしていることを確認
        $this->assertAuthenticatedAs($adminTopUser);
        // 運営トップ権限ユーザーがデータベースに登録されていることを確認
        $this->assertDatabaseHas('users', [
            'email' => 'test_admin_top@example.com',
        ]);
        $this->assertDatabaseHas('roles', [
            'user_id' => $adminTopUser->id,
            'role' => 3,
        ]);


        // テストユーザーが運営ユーザーとしてデータベースに登録されていることを確認
        $this->assertDatabaseHas('users', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->assertDatabaseHas('roles', [
            'role' => 5,
        ]);

        $response->assertRedirect('/admin/admin_user_top');
    }
}
