<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\CustomDeleteUserForm;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_validation_失敗_isManager()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        $this->assertDatabaseHas('users', [
            'nickname' => $user->nickname,
        ]);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(CustomDeleteUserForm::class)
            ->set('password', 'wrong-password') // 間違ったパスワードをセット
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

        $this->assertDatabaseHas('users', [
            'nickname' => $user->nickname,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(CustomDeleteUserForm::class)
            ->set('password', $password)
            ->assertSet('password', $password)
            ->call('deleteUser')
            ->assertRedirect(route('index'));

        $this->assertDatabaseMissing('users', [
            'nickname' => $user->nickname,
        ]);
    }
}
