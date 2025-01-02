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

        // まず、管理者としてグループ編集画面にアクセスできるかテスト
        $this->get('group/group_edit/' . $group->id)
            ->assertSeeLivewire(DeleteGroupForm::class);

        // Act（実行） & Assert（検証）
        Livewire::test(DeleteGroupForm::class)
            ->assertSet('group_data.name', $group->name)
            ->set('password', $password)
            ->assertSet('password', $password)
            ->call('deleteGroup')
            ->assertRedirect(route('index'));

        // Assert（検証）
        $this->assertDatabaseMissing('groups', [
            'name' => $group->name,
        ]);
    }

    public function test_validation_deleteGroup()
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

        // まず、管理者としてグループ編集画面にアクセスできるかテスト
        $this->get('group/group_edit/' . $group->id)
            ->assertSeeLivewire(DeleteGroupForm::class);

        // Act（実行） & Assert（検証）
        Livewire::test(DeleteGroupForm::class)
            ->set('password', '')
            ->call('deleteGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(DeleteGroupForm::class)
            ->set('password', 'aaaa')
            ->call('deleteGroup')
            ->assertHasErrors(['password' => 'current_password']);

        // Assert（検証）
        $this->assertDatabaseHas('groups', [
            'name' => $group->name,
        ]);
    }
}
