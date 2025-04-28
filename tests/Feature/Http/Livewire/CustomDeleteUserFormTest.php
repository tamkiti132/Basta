<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\CustomDeleteUserForm;
use App\Models\User;
use App\Models\Group;
use App\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class CustomDeleteUserFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_validation_成功_isManager()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // パスワードのバリデーション
        Livewire::test(CustomDeleteUserForm::class)
            ->set('password', $password)
            ->call('isManager')
            ->assertHasNoErrors(['password']);
    }

    public function test_validation_失敗_isManager()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        // データベースにユーザーが存在することを確認
        $this->assertDatabaseHas('users', [
            'nickname' => $user->nickname,
        ]);

        // Act（実行） & Assert（検証）
        // パスワードのバリデーション
        Livewire::test(CustomDeleteUserForm::class)
            ->set('password', 'wrong-password')
            ->call('isManager')
            ->assertHasErrors(['password']);
    }


    public function test_deleteUser()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        // データベースにユーザーが存在することを確認
        $this->assertDatabaseHas('users', [
            'nickname' => $user->nickname,
        ]);

        // ユーザーがログイン状態であることを確認
        $this->assertAuthenticated();

        // Act（実行） & Assert（検証）
        Livewire::test(CustomDeleteUserForm::class)
            ->set('password', $password)
            ->call('deleteUser')
            ->assertRedirect(route('index'));

        // ユーザーがデータベースから削除されたことを確認
        $this->assertDatabaseMissing('users', [
            'nickname' => $user->nickname,
        ]);

        // ユーザーがログアウトしていることを確認
        $this->assertGuest();
    }
}
