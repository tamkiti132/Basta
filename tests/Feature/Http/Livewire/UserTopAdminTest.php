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

class UserTopAdminTest extends TestCase
{
    public function test_deleteUser()
    {
        // Arrange（準備）

        // テスト用のユーザーと関連データを作成
        $user = User::factory()->create();

        $group = Group::factory()->create();


        // ユーザーをグループに追加
        $group->user()->attach($user->id);


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
}
