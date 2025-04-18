<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupShowAdmin;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GroupShowAdminTest extends TestCase
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
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->call('deleteGroup');

        // Assert（検証）
        // グループと関連データが削除されたことを確認
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    public function test_suspendGroup()
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


        // Act（実行）
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->call('suspendGroup');

        // Assert（検証）
        // グループの利用停止状態が1になっていることを確認
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'suspension_state' => 1]);
    }

    public function test_liftSuspendGroup()
    {
        // Arrange（準備）
        // 運営ユーザー（グループの利用停止を解除する側）
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


        // Act（実行）
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->call('liftSuspendGroup');

        // Assert（検証）
        // グループの利用停止状態が0になっていることを確認
        $this->assertDatabaseHas('groups', ['id' => $group->id, 'suspension_state' => 0]);
    }

    public function test_deleteUser()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを削除する側）
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

        // 一般ユーザー（削除されるユーザー）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // Act（実行）
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->set('deleteTargetUserId', $user->id)
            ->call('deleteUser');

        // Assert（検証）
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_suspendUser()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを利用停止する側）
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

        // 一般ユーザー（利用停止されるユーザー）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // Act（実行）
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->call('suspendUser', $user->id);

        // Assert（検証）
        // ユーザーの利用停止状態が1になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 1]);
    }

    public function test_liftSuspendUser()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーの利用停止を解除する側）
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

        // 一般ユーザー（利用停止を解除される側）
        $user = User::factory()->create([
            'suspension_state' => 1,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // ユーザーの利用停止状態が1になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 1]);

        // Act（実行）
        Livewire::test(GroupShowAdmin::class, ['group_id' => $group->id])
            ->call('liftSuspendUser', $user->id);

        // Assert（検証）
        // ユーザーの利用停止状態が0になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 0]);
    }
}
