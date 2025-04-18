<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoListMember;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MemoListMemberTest extends TestCase
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
  public function test_deleteUser_without_manager_role()
  {
    // Arrange（準備）
    // 運営ユーザー（削除操作を行う側）
    $admin = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $admin->groupRoles()->attach($admin, [
      'role' => 5,
      'group_id' => null
    ]);

    $this->actingAs($admin);

    // 削除対象のユーザー（管理者権限なし）
    $userToDelete = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $group = Group::factory()->create([
      'suspension_state' => 0,
    ]);

    $group->userRoles()->attach($userToDelete, ['role' => 100]);

    // Act（実行）&Assert（検証）
    $component = Livewire::test(MemoListMember::class, [
      'group_id' => $group->id,
      'user_id' => $admin->id
    ]);

    $component->set('deleteTargetUserId', $userToDelete->id)
      ->call('deleteUser')
      ->assertRedirect(route('admin.user_top'));

    // ユーザーが削除されていることを確認
    $this->assertDatabaseMissing('users', [
      'id' => $userToDelete->id,
    ]);
  }

  /**
   * 管理者権限を持つユーザーを削除し、次の管理者を設定するテスト
   */
  public function test_deleteUser_with_manager_role_and_next_manager()
  {
    // Arrange（準備）
    // 運営ユーザー（削除操作を行う側）
    $admin = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $admin->groupRoles()->attach($admin, [
      'role' => 5,
      'group_id' => null
    ]);

    $this->actingAs($admin);

    // 削除対象のユーザー（管理者権限あり）
    $managerToDelete = User::factory()->create([
      'suspension_state' => 0,
    ]);

    // 次の管理者になるユーザー
    $nextManager = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $group = Group::factory()->create([
      'suspension_state' => 0,
    ]);

    $group->userRoles()->attach($managerToDelete, ['role' => 10]);

    $group->userRoles()->attach($nextManager, ['role' => 50]);

    // Act（実行）
    $component = Livewire::test(MemoListMember::class, [
      'group_id' => $group->id,
      'user_id' => $admin->id
    ]);

    // 必要な情報を設定
    $component->set('deleteTargetUserId', $managerToDelete->id)
      ->set('managedGroupIds', collect([$group->id]))
      ->set('selectedNextManagerIds', [$group->id => $nextManager->id])
      ->call('deleteUser')
      ->assertRedirect(route('admin.user_top'));

    // Assert（検証）
    // ユーザーが削除されていることを確認
    $this->assertDatabaseMissing('users', [
      'id' => $managerToDelete->id,
    ]);

    // 次の管理者が管理者権限を持っていることを確認
    $this->assertDatabaseHas('roles', [
      'user_id' => $nextManager->id,
      'group_id' => $group->id,
      'role' => 10,
    ]);

    // グループが削除されていないことを確認
    $this->assertDatabaseHas('groups', [
      'id' => $group->id,
    ]);
  }

  /**
   * 管理者権限を持つユーザーを削除し、グループも削除するテスト
   * （次の管理者を選択せず、グループを削除するケース）
   */
  public function test_deleteUser_with_manager_role_and_delete_group()
  {
    // Arrange（準備）
    // 運営ユーザー（削除操作を行う側）
    $admin = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $admin->groupRoles()->attach($admin, [
      'role' => 5,
      'group_id' => null
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
    $component = Livewire::test(MemoListMember::class, [
      'group_id' => $group->id,
      'user_id' => $admin->id
    ]);

    // 必要な情報を設定（次の管理者を選択せず、グループを削除するケース）
    $component->set('deleteTargetUserId', $managerToDelete->id)
      ->set('managedGroupIds', collect([$group->id]))
      ->set('selectedNextManagerIds', [$group->id => 0]) // 0は次の管理者を選択しないことを示す
      ->call('deleteUser')
      ->assertRedirect(route('admin.user_top'));

    // Assert（検証）
    // ユーザーが削除されていることを確認
    $this->assertDatabaseMissing('users', [
      'id' => $managerToDelete->id,
    ]);

    // グループが削除されていることを確認
    $this->assertDatabaseMissing('groups', [
      'id' => $group->id,
    ]);
  }
}
