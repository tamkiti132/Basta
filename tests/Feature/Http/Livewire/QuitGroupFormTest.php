<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Http\Livewire\QuitGroupForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class QuitGroupFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_quitGroup_メンバー権限が退会()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // メンバーを追加
        $password = 'member-password';
        $member = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // メンバーとしてログイン
        $this->actingAs($member);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->assertSet('password', $password)
            ->call('quitGroup')
            ->assertRedirect('/');

        // メンバーが退会したか確認（rolesテーブルから削除されているか）
        $this->assertDatabaseMissing('roles', [
            'user_id' => $member->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_quitGroup_サブ管理者権限が退会()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // サブ管理者を追加
        $password = 'submanager-password';
        $submanager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);
        $group->userRoles()->attach($submanager, ['role' => 50]);

        // サブ管理者としてログイン
        $this->actingAs($submanager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->assertSet('password', $password)
            ->call('quitGroup')
            ->assertRedirect('/');

        // サブ管理者が退会したか確認（rolesテーブルから削除されているか）
        $this->assertDatabaseMissing('roles', [
            'user_id' => $submanager->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_quitGroup_管理者が退会_サブ管理者あり()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // サブ管理者を追加
        $submanager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($submanager, ['role' => 50]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('quitGroup')
            ->assertSet('showModal', false)
            ->assertSet('showNextManagerModal', true)
            ->assertSet('showModalNobodySubManager', false);

        // データベースの状態は変わっていないはず
        $this->assertDatabaseHas('roles', [
            'user_id' => $manager->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_quitGroup_管理者が退会_サブ管理者なし()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 一般メンバーを追加（サブ管理者はなし）
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('quitGroup')
            ->assertSet('showModal', false)
            ->assertSet('showNextManagerModal', false)
            ->assertSet('showModalNobodySubManager', true);

        // データベースの状態は変わっていないはず
        $this->assertDatabaseHas('roles', [
            'user_id' => $manager->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_validation_成功_quitGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // メンバーを追加
        $password = 'member-password';
        $member = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group->userRoles()->attach($member, ['role' => 100]);

        // メンバーとしてログイン
        $this->actingAs($member);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('quitGroup')
            ->assertHasNoErrors(['password' => 'required'])
            ->assertHasNoErrors(['password' => 'current_password']);
    }

    public function test_validation_失敗_quitGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // メンバーを追加
        $password = 'member-password';
        $member = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group->userRoles()->attach($member, ['role' => 100]);

        // メンバーとしてログイン
        $this->actingAs($member);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // フォームを送信して、メンバーが退会できるか確認
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', '')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(QuitGroupForm::class)
            ->set('password', 'wrong-password')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'current_password']);

        // メンバーが退会したか確認（rolesテーブルから削除されていないか）
        $this->assertDatabaseHas('roles', [
            'user_id' => $member->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_quitGroupForManager_サブ管理者を選択()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // サブ管理者を追加
        $submanager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($submanager, ['role' => 50]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // showNextManagerModalの状態は事前にセットしておく
        Livewire::test(QuitGroupForm::class)
            ->set('selectedUserId', $submanager->id)
            ->call('quitGroupForManager')
            ->assertRedirect('/');

        // 前の管理者が退会したか確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $manager->id,
            'group_id' => $group->id,
        ]);

        // サブ管理者が管理者に昇格したか確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $submanager->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_quitGroupForManager_サブ管理者を選択しない()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // サブ管理者を追加
        $submanager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($submanager, ['role' => 50]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('selectedUserId', '') // ユーザーを選択しない
            ->call('quitGroupForManager')
            ->assertSet('showNextManagerModal', false)
            ->assertSet('showModalFinalConfirmation', true);

        // データベースの状態は変わっていないはず
        $this->assertDatabaseHas('roles', [
            'user_id' => $manager->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_deleteGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 一般メンバーを追加
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('deleteGroup')
            ->assertRedirect('/');

        // グループが削除されたか確認
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);

        // 関連するロールも削除されたか確認
        $this->assertDatabaseMissing('roles', [
            'group_id' => $group->id,
        ]);
    }

    public function test_validation_失敗_deleteGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', '')
            ->call('deleteGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(QuitGroupForm::class)
            ->set('password', 'wrong-password')
            ->call('deleteGroup')
            ->assertHasErrors('password');

        // グループが削除されていないか確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }

    public function test_validation_成功_deleteGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 一般メンバーを追加
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('deleteGroup')
            ->assertHasNoErrors(['password' => 'required'])
            ->assertHasNoErrors(['password' => 'current_password']);
    }

    public function test_quitGroupWhenNobodySubManager()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 一般メンバーを追加
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('quitGroupWhenNobodySubManager')
            ->assertRedirect('/');

        // グループが削除されたか確認
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);

        // 関連するロールも削除されたか確認
        $this->assertDatabaseMissing('roles', [
            'group_id' => $group->id,
        ]);
    }

    public function test_validation_成功_quitGroupWhenNobodySubManager()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 一般メンバーを追加
        $member = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($member, ['role' => 100]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->call('quitGroupWhenNobodySubManager')
            ->assertHasNoErrors(['password' => 'required'])
            ->assertHasNoErrors(['password' => 'current_password']);
    }

    public function test_validation_失敗_quitGroupWhenNobodySubManager()
    {
        // Arrange（準備）
        // 管理者を追加
        $password = 'manager-password';
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 管理者としてログイン
        $this->actingAs($manager);

        // セッションにgroup_idを設定
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', '')
            ->call('quitGroupWhenNobodySubManager')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(QuitGroupForm::class)
            ->set('password', 'wrong-password')
            ->call('quitGroupWhenNobodySubManager')
            ->assertHasErrors('password');

        // グループが削除されていないか確認
        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
        ]);
    }
}
