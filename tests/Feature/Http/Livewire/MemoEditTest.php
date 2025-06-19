<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoEdit;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use App\Models\Web_type_feature;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

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

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);

        // Act（実行）
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->set('memo_data.title', '更新後のタイトル')
            ->set('memo_data.shortMemo', '更新後のショートメモ')
            ->set('memo_data.additionalMemo', '更新後の追加メモ')
            ->set('memo_data.web_type_feature.url', 'https://updated.com')
            ->call('update');

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新後のタイトル',
        ]);

        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://updated.com',
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
        $group->userRoles()->attach($user, ['role' => 10]);

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // テスト用の画像を作成
        $book_image = UploadedFile::fake()->image('test.png')->size(2048);

        // Act（実行）
        $component = Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            // 各プロパティの値が各カラムの更新前データでセットされているか確認
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->assertSet('memo_data.title', '更新前のタイトル')
            ->assertSet('memo_data.shortMemo', '更新前のショートメモ')
            ->assertSet('memo_data.additionalMemo', '更新前の追加メモ')
            ->assertSet('book_image_preview', null)
            ->assertSet('book_image_delete_flag', false)

            // 各プロパティの値に各カラムの更新後データをセット
            ->set('memo_data.title', '更新後のタイトル')
            ->set('memo_data.shortMemo', '更新後のショートメモ')
            ->set('memo_data.additionalMemo', '更新後の追加メモ')
            ->set('book_image_preview', $book_image)

            // 各データを更新する
            ->call('update');

        $storedBookImage = $component->get('storedBookImage');

        // Assert（検証）
        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($storedBookImage);

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新後のタイトル',
        ]);

        $this->assertDatabaseHas('book_type_features', [
            'book_photo_path' => basename($storedBookImage),
        ]);

        // ここから、画像を削除するテスト
        // Act（実行）
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('book_image_preview', null)
            ->set('book_image_delete_flag', true)
            ->call('update');

        // Assert（検証）
        // ストレージにファイルが削除されていることを確認
        Storage::disk('public')->assertMissing($storedBookImage);

        // データベース検証
        $this->assertDatabaseMissing('book_type_features', [
            'book_photo_path' => basename($storedBookImage),
        ]);
    }

    public function test_validation_成功_update_web()
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
            'url' => 'https://example.com',
        ]);

        // Act（実行） & Assert（検証）
        // typeのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'web')
            ->call('update')
            ->assertHasNoErrors(['type' => 'required'])
            ->assertHasNoErrors(['type' => 'in']);

        // titleのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.title', '有効なタイトル')
            ->call('update')
            ->assertHasNoErrors(['memo_data.title' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.title', str_repeat('あ', 50))
            ->call('update')
            ->assertHasNoErrors(['memo_data.title' => 'max']);

        // shortMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.shortMemo', '有効なショートメモ')
            ->call('update')
            ->assertHasNoErrors(['memo_data.shortMemo' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.shortMemo', str_repeat('あ', 200))
            ->call('update')
            ->assertHasNoErrors(['memo_data.shortMemo' => 'max']);

        // additionalMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.additionalMemo', null)
            ->call('update')
            ->assertHasNoErrors(['memo_data.additionalMemo' => 'nullable']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.additionalMemo', '有効な追加メモ')
            ->call('update')
            ->assertHasNoErrors(['memo_data.additionalMemo' => 'string']);

        // urlのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'web'])
            ->set('memo_data.web_type_feature.url', 'https://valid-url.com')
            ->call('update')
            ->assertHasNoErrors(['memo_data.web_type_feature.url' => 'url']);
    }

    public function test_validation_成功_update_book()
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

        // Act（実行） & Assert（検証）
        // typeのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->call('update')
            ->assertHasNoErrors(['type' => 'required'])
            ->assertHasNoErrors(['type' => 'in']);

        // titleのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.title', '有効なタイトル')
            ->call('update')
            ->assertHasNoErrors(['memo_data.title' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.title', str_repeat('あ', 50))
            ->call('update')
            ->assertHasNoErrors(['memo_data.title' => 'max']);

        // shortMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.shortMemo', '有効なショートメモ')
            ->call('update')
            ->assertHasNoErrors(['memo_data.shortMemo' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.shortMemo', str_repeat('あ', 200))
            ->call('update')
            ->assertHasNoErrors(['memo_data.shortMemo' => 'max']);

        // additionalMemoのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.additionalMemo', null)
            ->call('update')
            ->assertHasNoErrors(['memo_data.additionalMemo' => 'nullable']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('memo_data.additionalMemo', '有効な追加メモ')
            ->call('update')
            ->assertHasNoErrors(['memo_data.additionalMemo' => 'string']);

        // book_imageのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('book_image_preview', null)
            ->call('update')
            ->assertHasNoErrors(['book_image_preview' => 'nullable']);

        $validImage = UploadedFile::fake()->image('valid.png')->size(1024);
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('book_image_preview', $validImage)
            ->call('update')
            ->assertHasNoErrors(['book_image_preview' => 'image']);

        $maxImage = UploadedFile::fake()->image('max.png')->size(2048);
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->set('book_image_preview', $maxImage)
            ->call('update')
            ->assertHasNoErrors(['book_image_preview' => 'max']);
    }

    public function test_validation_失敗_update_web()
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
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://previous.com',
        ]);

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);

        // Act（実行） & Assert（検証）
        // typeのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => ''])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', '')
            ->call('update')
            ->assertHasErrors(['type' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'hoge'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'hoge')
            ->call('update')
            ->assertHasErrors(['type' => 'in']);

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

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://previous.com',
        ]);
    }

    public function test_validation_失敗_update_book()
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
            'title' => '更新前のタイトル',
            'shortMemo' => '更新前のショートメモ',
            'additionalMemo' => '更新前の追加メモ',
        ]);

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);

        // Act（実行） & Assert（検証）
        // typeのバリデーション
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => ''])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', '')
            ->call('update')
            ->assertHasErrors(['type' => 'required']);

        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'hoge'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'hoge')
            ->call('update')
            ->assertHasErrors(['type' => 'in']);

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

        // book_imageのバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('book_image_preview', $notImage)
            ->call('update')
            ->assertHasErrors(['book_image_preview' => 'image']);

        $book_image = UploadedFile::fake()->image('test.png')->size(2049);
        Livewire::test(MemoEdit::class, ['memo_id' => $memo->id, 'type' => 'book'])
            ->assertSet('memo_id', $memo->id)
            ->assertSet('type', 'book')
            ->set('book_image_preview', $book_image)
            ->call('update')
            ->assertHasErrors(['book_image_preview' => 'max']);

        // データベース検証
        $this->assertDatabaseHas('memos', [
            'title' => '更新前のタイトル',
        ]);
    }
}
