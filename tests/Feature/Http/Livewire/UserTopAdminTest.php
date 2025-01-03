<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Models\Report;
use App\Models\Memo;
use App\Models\Comment;
use App\Models\Memo_type_report_link;
use App\Models\Comment_type_report_link;
use App\Models\User_type_report_link;
use App\Models\Group_type_report_link;
use Livewire\Livewire;
use App\Http\Livewire\UserTopAdmin;
use Illuminate\Support\Facades\Storage;

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


        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id
        ]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'memo_id' => $memo->id
        ]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(UserTopAdmin::class)
            ->set('deleteTargetUserId', $user->id)
            ->call('deleteUser');

        // Assert（検証）
        // ユーザーと関連データが削除されたことを確認
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('memos', ['id' => $memo->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
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


        // ユーザーのsuspension_stateが0になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 0]);


        // Act（実行）
        Livewire::test(UserTopAdmin::class)
            ->call('suspendUser', $user->id);

        // Assert（検証）
        // ユーザーのsuspension_stateが1になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 1]);
    }

    public function test_liftSuspendUser()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを利用停止解除する側）
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
            'suspension_state' => 1,
        ]);
        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        // 一般ユーザーの権限を設定
        $group->userRoles()->attach($user, ['role' => 10]);


        // ユーザーのsuspension_stateが1になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 1]);


        // Act（実行）
        Livewire::test(UserTopAdmin::class)
            ->call('liftSuspendUser', $user->id);

        // Assert（検証）
        // ユーザーのsuspension_stateが0になっていることを確認
        $this->assertDatabaseHas('users', ['id' => $user->id, 'suspension_state' => 0]);
    }
}
