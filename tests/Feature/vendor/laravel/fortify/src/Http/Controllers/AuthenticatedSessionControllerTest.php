<?php

namespace Tests\Feature\vendor\laravel\fortify\src\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Str;

class AuthenticatedSessionControllerTest extends TestCase
{
    public function test_ログイン画面が表示できる(): void
    {
        // Act（実行）
        $response = $this->get('/login');

        // Assert（検証）
        $response->assertStatus(200);
    }

    public function test_ログインできる_一般ユーザー(): void
    {
        // Arrange（準備）
        $user = User::factory()->create();

        // Act（実行）
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Assert（検証）
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_ログインできる_運営ユーザー(): void
    {
        // Arrange（準備）

        // 運営権限ユーザーを用意
        $adminUser = User::create([
            'nickname' => 'Admin User',
            'email' => 'test_admin@example.com',
            'password' => Hash::make('passwordAdmin'),
            'username' => '@' . (string) Str::ulid(),
        ]);

        Role::create([
            'user_id' => $adminUser->id,
            'role' => 5,
        ]);

        // Act（実行）
        $response = $this->post('/login', [
            'email' => $adminUser->email,
            'password' => 'passwordAdmin',
        ]);

        // Assert（検証）
        $this->assertAuthenticated();
        $response->assertRedirect('/admin/user_top');
    }

    public function test_間違えたパスワードを入力した場合ログインに失敗する(): void
    {
        // Arrange（準備）
        $user = User::factory()->create();

        // Act（実行）
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // Assert（検証）
        $this->assertGuest();
    }

    public function test_ログアウトできる(): void
    {
        // Arrange（準備）
        $user = User::factory()->create();

        // Act（実行）
        $this->post('/logout');

        // Assert（検証）
        $this->assertGuest();
    }
}
