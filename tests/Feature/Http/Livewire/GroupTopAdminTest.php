<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupTopAdmin;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GroupTopAdminTest extends TestCase
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
        // 運営ユーザー（グループを削除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // Act（実行）
        $response = Livewire::test(GroupTopAdmin::class)
            ->call('deleteGroup', $group->id);

        // Assert（検証）
        // グループが削除されたことを確認
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);

        // リダイレクトされることを確認
        $response->assertRedirect(route('admin.group_top'));
    }

    public function test_suspend()
    {
        // Arrange（準備）
        // 運営ユーザー（グループを利用停止する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);


        // グループの利用停止状態が0になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'suspension_state' => 0
        ]);


        // Act（実行）
        Livewire::test(GroupTopAdmin::class)
            ->call('suspend', $group->id);

        // Assert（検証）
        // グループの利用停止状態が1になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'suspension_state' => 1
        ]);
    }

    public function test_liftSuspend()
    {
        // Arrange（準備）
        // 運営ユーザー（グループを利用停止解除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 1,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);


        // グループの利用停止状態が1になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'suspension_state' => 1
        ]);


        // Act（実行）
        Livewire::test(GroupTopAdmin::class)
            ->call('liftSuspend', $group->id);

        // Assert（検証）
        // グループの利用停止状態が0になっていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'suspension_state' => 0
        ]);
    }
}
