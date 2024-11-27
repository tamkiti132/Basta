<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoEdit;
use App\Models\Book_type_feature;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use App\Models\Web_type_feature;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class MemoEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_update_web()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://previous.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // web_type_featureのデータが保存されているか
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.title', '更新後のタイトル')
            ->set('memo_data.shortMemo', '更新後のショートメモ')
            ->set('memo_data.additionalMemo', '更新後の追加メモ')
            ->set('memo_data.web_type_feature.url', 'https://updated.com')
            ->call('update')
        ;

        // データベースにメモが更新されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新後のタイトル',
        ]);

        // web_type_featureが更新されているか
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://updated.com',
        ]);
    }


    public function test_validate_update_web()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://previous.com',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // web_type_featureのデータが保存されているか
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);

        // Act（実行） & Assert（検証）
        // titleのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.title', '')
            ->call('update')
            ->assertHasErrors(['memo_data.title' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.title', str_repeat('a', 51))
            ->call('update')
            ->assertHasErrors(['memo_data.title' => 'max']);


        // shortMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.shortMemo', '')
            ->call('update')
            ->assertHasErrors(['memo_data.shortMemo' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.shortMemo', str_repeat('a', 201))
            ->call('update')
            ->assertHasErrors(['memo_data.shortMemo' => 'max']);


        // additionalMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.additionalMemo', 123)
            ->call('update')
            ->assertHasErrors(['memo_data.additionalMemo' => 'string']);


        // urlのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.web_type_feature.url', '')
            ->call('update')
            ->assertHasErrors(['memo_data.web_type_feature.url' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.web_type_feature.url', 'not_url')
            ->call('update')
            ->assertHasErrors(['memo_data.web_type_feature.url' => 'url']);

        // データベースにメモが更新されていないことを確認
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // web_type_featureが更新されていないことを確認
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);
    }


    public function test_update_book()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.title', '更新後のタイトル')
            ->set('memo_data.shortMemo', '更新後のショートメモ')
            ->set('memo_data.additionalMemo', '更新後の追加メモ')
            ->call('update')
        ;

        // データベースにメモが更新されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新後のタイトル',
        ]);
    }


    public function test_validate_update_book()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        // memoのデータが保存されているか
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // Act（実行） & Assert（検証）
        // titleのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.title', '')
            ->call('update')
            ->assertHasErrors(['memo_data.title' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.title', str_repeat('a', 51))
            ->call('update')
            ->assertHasErrors(['memo_data.title' => 'max']);


        // shortMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.shortMemo', '')
            ->call('update')
            ->assertHasErrors(['memo_data.shortMemo' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.shortMemo', str_repeat('a', 201))
            ->call('update')
            ->assertHasErrors(['memo_data.shortMemo' => 'max']);


        // additionalMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('memo_data.additionalMemo', 123)
            ->call('update')
            ->assertHasErrors(['memo_data.additionalMemo' => 'string']);

        // データベースにメモが更新されていないことを確認
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);
    }
}
