<?php

namespace Tests\Feature\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RegisteredUserControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_ユーザー登録画面が表示できる(): void
    {
        // Arrange（準備）
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        // Act（実行）
        $response = $this->get('/register');

        // Assert（検証）
        $response->assertStatus(200);
    }

    // 現在、ユーザー登録機能を有効にしているため、このテストは不要なので、コメントアウトしておきます。
    // public function test_ユーザー登録機能が無効の場合、ユーザー登録画面にアクセスすると404が返る(): void
    // {
    //     // Arrange（準備）
    //     if (Features::enabled(Features::registration())) {
    //         $this->markTestSkipped('Registration support is enabled.');

    //         return;
    //     }

    //     // Act（実行）
    //     $response = $this->get('/register');

    //     // Assert（検証）
    //     $response->assertStatus(404);
    // }

    public function test_store(): void
    {
        // Arrange（準備）
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }

        // Act（実行）
        $response = $this->post('/register', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123456789',
            'password_confirmation' => 'password123456789',
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

    public function test_storeAdmin(): void
    {
        // Arrange（準備）
        if (! Features::enabled(Features::registration())) {
            $this->markTestSkipped('Registration support is not enabled.');

            return;
        }


        // 運営トップ権限ユーザーを用意
        $adminTopUser = User::factory()->create([
            'nickname' => 'AdminTopUser',
            'email' => 'test_admin_top@example.com',
            'password' => Hash::make('passwordAdminTop123'),
            'username' => '@' . (string) Str::ulid(),
        ]);

        $adminTopUser->groupRoles()->attach($adminTopUser, [
            'role' => 3,
            'group_id' => null,
        ]);

        // 運営トップ権限ユーザーでログイン
        $this->actingAs($adminTopUser);

        // 運営トップ権限ユーザーがログインしていることを確認
        $this->assertAuthenticatedAs($adminTopUser);

        // Act（実行）
        $response = $this->post('/registerAdmin', [
            'nickname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123456789',
            'password_confirmation' => 'password123456789',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
        ]);

        // Assert（検証）
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
