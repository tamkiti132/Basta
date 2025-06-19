<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\UserTopAdmin;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UserTopAdminTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    /**
     * 管理者権限のないユーザーを削除するテスト
     */
    public function test_delete_user_without_manager_role()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを削除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー（削除されるユーザー - 管理者権限なし）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);

        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(UserTopAdmin::class, ['user_id' => $user->id])
            ->set('deleteTargetUserId', $user->id)
            ->call('deleteUser');

        // Assert（検証）
        // ユーザーが削除されていることを確認
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // ユーザーに関連するロールも削除されていることを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $user->id,
        ]);

        // グループは削除されていないことを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }

    /**
     * 管理者権限を持つユーザーを削除し、次の管理者を設定するテスト
     */
    public function test_delete_user_with_manager_role_and_next_manager()
    {
        // Arrange（準備）
        // 運営ユーザー（削除操作を行う側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);

        $this->actingAs($admin);

        // 削除対象のユーザー（管理者権限あり）
        $managerToDelete = User::factory()->create([
            'suspension_state' => 0,
        ]);

        // 次の管理者になるユーザー（サブ管理者）
        $nextManager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($managerToDelete, ['role' => 10]);

        $group->userRoles()->attach($nextManager, ['role' => 50]);

        // Act（実行）
        $component = Livewire::test(UserTopAdmin::class, [
            'user_id' => $managerToDelete->id,
        ]);

        // 必要な情報を設定
        $component->set('deleteTargetUserId', $managerToDelete->id)
            ->set('managedGroupIds', collect([$group->id]))
            ->set('selectedNextManagerIds', [$group->id => $nextManager->id])
            ->call('deleteUser');

        // Assert（検証）
        // 対象ユーザーが削除されていることを確認
        $this->assertDatabaseMissing('users', [
            'id' => $managerToDelete->id,
        ]);

        // 次の管理者が管理者権限に昇格していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $nextManager->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);

        // グループが残っていることを確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }

    /**
     * 管理者権限を持つユーザーを削除し、グループも削除するテスト
     * （次の管理者を選択しない場合）
     */
    public function test_delete_user_with_manager_role_and_delete_group()
    {
        // Arrange（準備）
        // 運営ユーザー（削除操作を行う側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);

        $this->actingAs($admin);

        // 削除対象のユーザー（管理者権限あり）
        $managerToDelete = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($managerToDelete, ['role' => 10]);

        // メンバー
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // Act（実行）
        $component = Livewire::test(UserTopAdmin::class, [
            'user_id' => $managerToDelete->id,
        ]);

        // 必要な情報を設定（グループを削除するケース - 次の管理者IDを0に設定）
        $component->set('deleteTargetUserId', $managerToDelete->id)
            ->set('managedGroupIds', collect([$group->id]))
            ->set('selectedNextManagerIds', [$group->id => 0]) // 0は次の管理者を選択しない（グループを削除する）
            ->call('deleteUser');

        // Assert（検証）
        // 対象ユーザーが削除されていることを確認
        $this->assertDatabaseMissing('users', [
            'id' => $managerToDelete->id,
        ]);

        // グループが削除されていることを確認
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);

        // グループに関連するロールも削除されていることを確認
        $this->assertDatabaseMissing('roles', [
            'group_id' => $group->id,
        ]);
    }

    /**
     * ユーザーを利用停止状態にするテスト
     */
    public function test_suspend_user()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを利用停止する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
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
        // 一般ユーザーをグループ管理者として設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // 初期状態の確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'suspension_state' => 0,
        ]);

        // Act（実行）
        Livewire::test(UserTopAdmin::class)
            ->call('suspendUser', $user->id);

        // Assert（検証）
        // ユーザーが利用停止状態になっていることを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'suspension_state' => 1,
        ]);
    }

    /**
     * 利用停止中のユーザーの利用停止を解除するテスト
     */
    public function test_lift_suspend_user()
    {
        // Arrange（準備）
        // 運営ユーザー（利用停止を解除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        $admin->groupRoles()->attach($admin, [
            'role' => 5, // 運営ユーザー権限
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 利用停止中のユーザー
        $user = User::factory()->create([
            'suspension_state' => 1, // 初期状態は利用停止中
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // ユーザーをグループ管理者として設定
        $group->userRoles()->attach($user, ['role' => 10]);

        // 初期状態の確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'suspension_state' => 1,
        ]);

        // Act（実行）
        Livewire::test(UserTopAdmin::class)
            ->call('liftSuspendUser', $user->id);

        // Assert（検証）
        // ユーザーが利用可能状態になっていることを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'suspension_state' => 0,
        ]);
    }
}
