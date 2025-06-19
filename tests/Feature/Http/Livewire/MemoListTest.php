<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoList;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MemoListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_delete_group()
    {
        // Arrange（準備）
        // グループを作る側
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'name' => 'テストグループ',
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        // グループを削除する側
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($admin);

        // 運営ユーザー権限を設定
        $admin->groupRoles()->attach($admin, [
            'role' => 5, // 運営ユーザー権限
            'group_id' => null,
        ]);

        // グループが存在することを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);

        // Act（実行）
        // グループを削除
        $response = Livewire::test(MemoList::class, ['group_id' => $group->id])
            ->call('deleteGroup', $group->id);

        // Assert（検証）
        // グループが削除されたことを確認
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);

        // リダイレクトされることを確認
        $response->assertRedirect(route('admin.group_top'));
    }

    public function test_suspend_group()
    {
        // Arrange（準備）
        // 通常のグループを作成
        $group = Group::factory()->create([
            'name' => 'テスト利用停止グループ',
            'suspension_state' => 0, // 初期状態は利用可能
        ]);

        // 運営ユーザーを作成
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($admin);

        // 運営ユーザー権限を設定
        $admin->groupRoles()->attach($admin, [
            'role' => 5, // 運営ユーザー権限
            'group_id' => null,
        ]);

        // 利用可能状態であることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'テスト利用停止グループ',
            'suspension_state' => 0,
        ]);

        // Act（実行）
        // グループを利用停止にする
        Livewire::test(MemoList::class, ['group_id' => $group->id])
            ->call('suspendGroup');

        // Assert（検証）
        // グループが利用停止状態になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'テスト利用停止グループ',
            'suspension_state' => 1, // 利用停止状態になっている
        ]);
    }

    public function test_lift_suspend_group()
    {
        // Arrange（準備）
        // 利用停止されたグループを作成
        $group = Group::factory()->create([
            'name' => 'テスト利用停止解除グループ',
            'suspension_state' => 1, // 初期状態は利用停止中
        ]);

        // 運営ユーザーを作成
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($admin);

        // 運営ユーザー権限を設定
        $admin->groupRoles()->attach($admin, [
            'role' => 5, // 運営ユーザー権限
            'group_id' => null,
        ]);

        // 利用停止状態であることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'テスト利用停止解除グループ',
            'suspension_state' => 1,
        ]);

        // Act（実行）
        // グループの利用停止を解除
        Livewire::test(MemoList::class, ['group_id' => $group->id])
            ->call('liftSuspendGroup');

        // Assert（検証）
        // グループが利用可能状態になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'テスト利用停止解除グループ',
            'suspension_state' => 0, // 利用可能状態になっている
        ]);
    }
}
