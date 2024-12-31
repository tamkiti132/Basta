<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoShow;
use App\Models\Book_type_feature;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use App\Models\Web_type_feature;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class MemoShowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_deleteMemo_web()
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
            'title' => 'テストタイトル',
            'shortMemo' => 'テストショートメモ',
            'additionalMemo' => 'テスト追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://test.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);

        // web_type_featureのデータが保存されているか
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://test.com',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'web', 'group_id' => $group->id])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->assertSet('group_id', $group->id)
            ->call('deleteMemo', $memo->id)
        ;

        // データベースにメモが存在しないことを確認
        $this->assertDatabaseMissing('memos', [
            'title' => 'テストタイトル',
        ]);

        // web_type_featureが存在しないことを確認
        $this->assertDatabaseMissing('web_type_features', [
            'url' => 'https://test.com',
        ]);
    }

    public function test_deleteMemo_book()
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
            'title' => 'テストタイトル',
            'shortMemo' => 'テストショートメモ',
            'additionalMemo' => 'テスト追加メモ',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);


        // Act（実行） & Assert（検証）
        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'book', 'group_id' => $group->id])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->assertSet('group_id', $group->id)
            ->call('deleteMemo', $memo->id)
        ;

        // データベースにメモが存在しないことを確認
        $this->assertDatabaseMissing('memos', [
            'title' => 'テストタイトル',
        ]);
    }

    public function test_storeComment()
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
            'title' => 'テストタイトル',
            'shortMemo' => 'テストショートメモ',
            'additionalMemo' => 'テスト追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://test.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'web', 'group_id' => $group->id])
            ->set('comment', 'テストコメント')
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->assertSet('group_id', $group->id)
            ->call('storeComment')
        ;

        // commentのデータが保存されているか
        $this->assertDatabaseHas('comments', [
            'comment' => 'テストコメント',
            'memo_id' => $memo->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_validate_storeComment()
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
            'title' => 'テストタイトル',
            'shortMemo' => 'テストショートメモ',
            'additionalMemo' => 'テスト追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://test.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);

        // Act（実行） & Assert（検証）
        // コメントのバリデーション
        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'web', 'group_id' => $group->id])
            ->set('comment', '')
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->assertSet('group_id', $group->id)
            ->call('storeComment')
            ->assertHasErrors(['comment' => 'required']);

        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'web', 'group_id' => $group->id])
            ->set('comment', ['a', 'b'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->assertSet('group_id', $group->id)
            ->call('storeComment')
            ->assertHasErrors(['comment' => 'string']);

        // commentのデータが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'comment' => 'テストコメント',
            'memo_id' => $memo->id,
            'user_id' => $user->id,
        ]);
    }


    public function test_deleteComment()
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
            'title' => 'テストタイトル',
            'shortMemo' => 'テストショートメモ',
            'additionalMemo' => 'テスト追加メモ',
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'memo_id' => $memo->id,
            'comment' => 'テストコメント',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://test.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);

        // commentのデータが保存されているか
        $this->assertDatabaseHas('comments', [
            'comment' => 'テストコメント',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoShow::class, ['memo_id' => $memo->id, 'type' => 'web', 'group_id' => $group->id])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->assertSet('group_id', $group->id)
            ->call('deleteComment', $comment->id)
        ;

        // commentのデータが削除されているか
        $this->assertDatabaseMissing('comments', [
            'comment' => 'テストコメント',
            'memo_id' => $memo->id,
            'user_id' => $user->id,
        ]);
    }
}
