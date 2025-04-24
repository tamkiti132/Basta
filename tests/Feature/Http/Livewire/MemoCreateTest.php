<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoCreate;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class MemoCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_store_web()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_title', 'webのテストタイトル')
            ->set('web_shortMemo', 'webのテストショートメモ')
            ->set('web_additionalMemo', 'webのテスト追加メモ')
            ->set('url', 'https://example.com')
            ->call('store', 'web');

        // データベースにメモが存在するか
        $this->assertDatabaseHas('memos', [
            'title' => 'webのテストタイトル',
        ]);

        // データベースにweb_type_featureが存在するか
        $this->assertDatabaseHas('web_type_features', [
            'url' => 'https://example.com',
        ]);
    }

    public function test_validation_失敗_store_web()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);


        // Act（実行） & Assert（検証）
        // typeのバリデーション
        // 0:web, 1:book
        // 実際にデータベースに保存されるtypeは数字。 0:web, 1:book
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->call('store', '')
            ->assertHasErrors(['type' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->call('store', 'hoge')
            ->assertHasErrors(['type' => 'in']);

        // web_titleのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_title', '')
            ->call('store', 'web')
            ->assertHasErrors(['web_title' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_title', str_repeat('a', 51))
            ->call('store', 'web')
            ->assertHasErrors(['web_title' => 'max']);

        // web_shortMemoのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_shortMemo', '')
            ->call('store', 'web')
            ->assertHasErrors(['web_shortMemo' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_shortMemo', str_repeat('a', 201))
            ->call('store', 'web')
            ->assertHasErrors(['web_shortMemo' => 'max']);


        // web_additionalMemoのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('web_additionalMemo', 123)
            ->call('store', 'web')
            ->assertHasErrors(['web_additionalMemo' => 'string']);

        // urlのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('url', '')
            ->call('store', 'web')
            ->assertHasErrors(['url' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('url', 'not_url')
            ->call('store', 'web')
            ->assertHasErrors(['url' => 'url']);

        // データベースにメモが存在しないことを確認
        $this->assertDatabaseEmpty('memos');

        // web_type_featureが存在しないことを確認
        $this->assertDatabaseEmpty('web_type_features');
    }

    /**
     * 本タイプのメモが保存できることをテスト（画像あり）
     *
     * @return void
     */
    public function test_store_book()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // テスト用の画像を作成
        $book_image = UploadedFile::fake()->image('test.png')->size(2048);


        // Act（実行） & Assert（検証）
        $component = Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_title', '本のテストタイトル')
            ->set('book_shortMemo', '本のテストショートメモ')
            ->set('book_additionalMemo', '本のテスト追加メモ')
            ->set('book_image', $book_image)
            ->call('store', 'book');

        $storedBookImage = $component->get('storedBookImage');

        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($storedBookImage);

        // データベースにメモが存在するか
        $this->assertDatabaseHas('memos', [
            'title' => '本のテストタイトル',
        ]);

        // データベースにbook_type_featureが存在するか
        $this->assertDatabaseHas('book_type_features', [
            'book_photo_path' => basename($storedBookImage),
        ]);
    }

    /**
     * 本タイプのメモが保存できることをテスト（画像なし）
     *
     * @return void
     */
    public function test_store_book_without_image()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_title', '画像なしの本のテストタイトル')
            ->set('book_shortMemo', '画像なしの本のテストショートメモ')
            ->set('book_additionalMemo', '画像なしの本のテスト追加メモ')
            // book_imageはセットしない
            ->call('store', 'book');

        // データベースにメモが存在するか
        $this->assertDatabaseHas('memos', [
            'title' => '画像なしの本のテストタイトル',
            'shortMemo' => '画像なしの本のテストショートメモ',
            'additionalMemo' => '画像なしの本のテスト追加メモ',
            'type' => 1, // 本タイプ
        ]);

        // book_type_featuresテーブルのレコード数が0（画像関連データがない）であることを確認
        $this->assertDatabaseEmpty('book_type_features');
    }

    public function test_validation_失敗_store_book()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);


        // Act（実行） & Assert（検証）
        // typeのバリデーション
        // 0:web, 1:book
        // 実際にデータベースに保存されるtypeは数字。 0:web, 1:book
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->call('store', '')
            ->assertHasErrors(['type' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->call('store', 'hoge')
            ->assertHasErrors(['type' => 'in']);

        // book_titleのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_title', '')
            ->call('store', 'book')
            ->assertHasErrors(['book_title' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_title', str_repeat('a', 51))
            ->call('store', 'book')
            ->assertHasErrors(['book_title' => 'max']);

        // book_shortMemoのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_shortMemo', '')
            ->call('store', 'book')
            ->assertHasErrors(['book_shortMemo' => 'required']);

        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_shortMemo', str_repeat('a', 201))
            ->call('store', 'book')
            ->assertHasErrors(['book_shortMemo' => 'max']);

        // book_additionalMemoのバリデーション
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_additionalMemo', 123)
            ->call('store', 'book')
            ->assertHasErrors(['book_additionalMemo' => 'string']);

        // book_imageのバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_image', $notImage)
            ->call('store', 'book')
            ->assertHasErrors(['book_image' => 'image']);

        // 2048KB以上の画像
        $book_image = UploadedFile::fake()->image('test.png')->size(2049);
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_image', $book_image)
            ->call('store', 'book')
            ->assertHasErrors(['book_image' => 'max']);

        // データベースにメモが存在しないことを確認
        $this->assertDatabaseEmpty('memos');

        // book_type_featureが存在しないことを確認
        $this->assertDatabaseEmpty('book_type_features');
    }
}
