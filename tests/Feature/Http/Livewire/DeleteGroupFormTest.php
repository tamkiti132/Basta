<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\DeleteGroupForm;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DeleteGroupFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_deleteGroup()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => 'テストグループ',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(DeleteGroupForm::class, ['group_data' => $group])
            ->assertSet('group_data.name', $group->name)
            ->set('password', $password)
            ->call('deleteGroup')
            ->assertRedirect(route('index'));

        // データベース検証
        $this->assertDatabaseMissing('groups', [
            'name' => $group->name,
        ]);
    }

    public function test_validation_成功_deleteGroup()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => 'テストグループ',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // パスワードのバリデーション
        Livewire::test(DeleteGroupForm::class, ['group_data' => $group])
            ->set('password', $password)
            ->call('deleteGroup')
            ->assertHasNoErrors(['password' => 'current_password']);
    }

    public function test_validation_失敗_deleteGroup()
    {
        // Arrange（準備）
        $password = 'secure-password';
        $user = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => 'テストグループ',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // パスワードのバリデーション
        Livewire::test(DeleteGroupForm::class, ['group_data' => $group])
            ->set('password', '')
            ->call('deleteGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(DeleteGroupForm::class, ['group_data' => $group])
            ->set('password', 'wrong-password')
            ->call('deleteGroup')
            ->assertHasErrors(['password' => 'current_password']);

        // データベース検証
        $this->assertDatabaseHas('groups', [
            'name' => $group->name,
        ]);
    }
}
