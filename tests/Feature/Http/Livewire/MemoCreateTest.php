<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\MemoCreate;
use App\Models\Group;
use App\Models\User;
use GuzzleHttp\Psr7\UploadedFile;
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
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // MemoCreateコンポーネントが存在するか確認
        $this->get('/group/memo_create/' . $group->id)
            ->assertSeeLivewire(MemoCreate::class);

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

    public function test_validation_store_web()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // MemoCreateコンポーネントが存在するか確認
        $this->get('/group/memo_create/' . $group->id)
            ->assertSeeLivewire(MemoCreate::class);

        // Act（実行） & Assert（検証）
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
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // MemoCreateコンポーネントが存在するか確認
        $this->get('/group/memo_create/' . $group->id)
            ->assertSeeLivewire(MemoCreate::class);

        // Act（実行） & Assert（検証）
        Livewire::test(MemoCreate::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            ->set('book_title', '本のテストタイトル')
            ->set('book_shortMemo', '本のテストショートメモ')
            ->set('book_additionalMemo', '本のテスト追加メモ')
            ->call('store', 'book');

        // データベースにメモが存在するか
        $this->assertDatabaseHas('memos', [
            'title' => '本のテストタイトル',
        ]);
    }

    public function test_validation_store_book()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // MemoCreateコンポーネントが存在するか確認
        $this->get('/group/memo_create/' . $group->id)
            ->assertSeeLivewire(MemoCreate::class);

        // Act（実行） & Assert（検証）
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

        // データベースにメモが存在しないことを確認
        $this->assertDatabaseEmpty('memos');
    }
}
