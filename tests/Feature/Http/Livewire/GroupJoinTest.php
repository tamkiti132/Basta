<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupJoin;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GroupJoinTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_join_group()
    {
        // Arrange（準備）
        $createGroupUser = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
            'isJoinFreeEnabled' => true,
        ]);

        $group->userRoles()->attach($createGroupUser, ['role' => 10]);

        $testUser = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($testUser);

        // Act（実行） & Assert（検証）
        Livewire::test(GroupJoin::class)
            ->call('joinGroup', $group->id);

        // testUserがメンバー権限を持っているか
        $this->assertDatabaseHas('roles', [
            'user_id' => $testUser->id,
            'role' => 100,
        ]);
    }

    /**
     * 参加不可のグループに参加しようとした場合、参加できないことを確認する
     *
     * @return void
     */
    public function test_join_group_not_allowed()
    {
        // Arrange（準備）
        $createGroupUser = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
            'isJoinFreeEnabled' => false,
        ]);

        $group->userRoles()->attach($createGroupUser, ['role' => 10]);

        $testUser = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($testUser);

        // 参加前に関連がないことを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $testUser->id,
            'group_id' => $group->id,
        ]);

        // Act（実行）
        $component = Livewire::test(GroupJoin::class)
            ->call('joinGroup', $group->id);

        // Assert（検証）
        // 参加不可のため、テーブルには何も追加されていないことを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $testUser->id,
            'group_id' => $group->id,
        ]);
    }
}
