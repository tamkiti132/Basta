<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\ReportComment;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ReportCommentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_createReport()
    {
        // Arrange（準備）        
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        $comment = Comment::factory()->create([
            'comment' => 'テストコメントです',
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        Livewire::test(ReportComment::class, ['comment_id' => $comment->id])
            ->set('reason', 1)
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport');

        $this->assertDatabaseHas('reports', [
            'contribute_user_id' => $user->id,
            'type' => 3,
            'reason' => 1,
            'detail' => "これはレポートのテスト詳細文です",
        ]);

        $report = Report::where('contribute_user_id', $user->id)
            ->where('type', 3)
            ->where('reason', 1)
            ->where('detail', "これはレポートのテスト詳細文です")
            ->first();

        $this->assertDatabaseHas('comment_type_report_links', [
            'report_id' => $report->id,
            'comment_id' => $comment->id,
        ]);
    }

    public function test_validation_失敗_createReport()
    {
        // Arrange（準備）        
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);

        $comment = Comment::factory()->create([
            'comment' => 'テストコメントです',
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        // reasonのバリデーション
        Livewire::test(ReportComment::class, ['comment_id' => $comment->id])
            ->set('reason', "")
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'required']);

        Livewire::test(ReportComment::class, ['comment_id' => $comment->id])
            ->set('reason', ['aaaa'])
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'integer']);

        // detailのバリデーション
        Livewire::test(ReportComment::class, ['comment_id' => $comment->id])
            ->set('reason', 1)
            ->set('detail', "")
            ->call('createReport')
            ->assertHasErrors(['detail' => 'required']);

        Livewire::test(ReportComment::class, ['comment_id' => $comment->id])
            ->set('reason', 1)
            ->set('detail', 123)
            ->call('createReport')
            ->assertHasErrors(['detail' => 'string']);
    }
}
