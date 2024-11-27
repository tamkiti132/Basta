<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use App\Models\Group;
use App\Http\Livewire\GroupJoin;

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

    public function test_Livewireコンポーネントが存在している()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        $this->get('/group_join')
            ->assertSeeLivewire(GroupJoin::class);
    }

    public function test_joinGroup()
    {
        // Arrange（準備）
        $createGroupUser = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
            'isJoinFreeEnabled' => true,
        ]);

        $group->user()->attach($createGroupUser);
        $group->userRoles()->attach($createGroupUser, ['role' => 10]);

        $testUser = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($testUser);

        // $this->dumpdb();

        // Act（実行） & Assert（検証）
        $this->get('/group_join')
            ->assertSeeLivewire(GroupJoin::class);

        Livewire::test(GroupJoin::class)
            ->call('joinGroup', $group->id);

        // testUserがgroupに参加しているか
        $this->assertDatabaseHas('group_user', [
            'group_id' => $group->id,
            'user_id' => $testUser->id,
        ]);

        // testUserがメンバー権限を持っているか
        $this->assertDatabaseHas('roles', [
            'user_id' => $testUser->id,
            'role' => 100,
        ]);
    }
}
