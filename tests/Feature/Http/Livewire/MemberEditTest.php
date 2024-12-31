<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemberEdit;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MemberEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_blockMember()
    {
        // Arrange（準備）
        // 管理者（ブロックする側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // ブロックされるユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);


        // ブロックされるユーザーがブロックされていない状態であることを確認
        $this->assertDatabaseMissing('block_states', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）        
        Livewire::test(MemberEdit::class, ['group_id' => $group->id])
            ->call('blockMember', $user->id);

        // ブロックされるユーザーがブロックされている状態であることを確認
        $this->assertDatabaseHas('block_states', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_liftBlockMember()
    {
        // Arrange（準備）
        // 管理者（ブロック解除する側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // ブロック解除されるユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);

        // ユーザーをブロック状態にしておく
        $user->blockedGroup()->attach($group->id);


        // ブロックされるユーザーがブロックされている状態であることを確認
        $this->assertDatabaseHas('block_states', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        Livewire::test(MemberEdit::class, ['group_id' => $group->id])
            ->call('liftBlockMember', $user->id);

        // ブロックされるユーザーがブロックされていない状態であることを確認
        $this->assertDatabaseMissing('block_states', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_quitUser()
    {
        // Arrange（準備）
        // 管理者（強制退会させる側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 強制退会される側のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);


        // このグループに紐づくユーザーの権限情報が存在していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        Livewire::test(MemberEdit::class, ['group_id' => $group->id])
            ->call('quitUser', $user->id);


        // このグループに紐づくユーザーの権限情報が存在していないることを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);
    }

    public function test_updateRole()
    {
        // Arrange（準備）
        // 管理者（権限切り替えする側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 権限切り替えされる側のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);


        // 権限切り替えされる側のユーザーの権限が100（メンバー権限）であることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemberEdit::class, ['group_id' => $group->id])
            ->call('updateRole', $user->id, 50);

        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 50,
        ]);
    }
}
