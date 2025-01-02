<?php

namespace Tests\Feature\vendor\laravel\fortify\src\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Str;

class AuthenticatedSessionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

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

    public function test_ログイン画面でバリデーションで失敗させる(): void
    {
        // Arrange（準備）
        User::create([
            'nickname' => 'TestUser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'username' => '@' . (string) Str::ulid(),
        ]);

        $url = '/login';


        // Act（実行）  &  Assert（検証）

        // ログイン時のバリデーションルールは、
        // vendor/laravel/fortify/src/Http/Requests/LoginRequest.php
        // のrules()メソッドで定義されている。
        // （データベース上のデータと一致しているかを確認するバリデーションはまた別の場所）
        // なお、そこに記載されている、
        // Fortify::username()にあたるカラムは
        // config/fortify.phpで設定することができる。
        // 今回は'email'となっている。

        // emailのバリデーション
        $this->post($url, ['email' => ''])
            ->assertInvalid(['email' => 'required']);

        // パスワードのバリデーション
        $this->post($url, ['password' => ''])
            ->assertInvalid(['password' => 'required']);
    }
}
