<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RequestValidationType4Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを指定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_validation_request_type_4_成功()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // テスト用の画像を作成
        $image = UploadedFile::fake()->image('test.png')->size(2048);

        // Act（実行） & Assert（検証）
        // 基本ケース - 全フィールド入力
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://example.com')
            ->set('uploaded_photo_4', $image)
            ->call('sendRequest', 'type_4')
            ->assertRedirect('request')
            ->assertHasNoErrors();

        // title_4のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_4', str_repeat('a', 100))  // max:100の境界値
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['title_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'a')                  // 最小文字数
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['title_4']);

        // detail_4のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', str_repeat('a', 3000))  // max:3000の境界値
            ->set('environment_4', 1)
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['detail_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'a')                 // 最小文字数
            ->set('environment_4', 1)
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['detail_4']);

        // environment_4のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 6)              // between:0,6の上限値
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['environment_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 0)              // between:0,6の下限値
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['environment_4']);

        // reference_url_4のバリデーション
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://example.com')  // 通常のURL
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['reference_url_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://localhost:8000/test?param=value#fragment')  // 複雑なURL
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['reference_url_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'http://example.com')  // HTTPプロトコル
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['reference_url_4']);

        // reference_url_4 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', null)         // nullable
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['reference_url_4']);

        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', '')           // 空文字列
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['reference_url_4']);

        // uploaded_photo_4のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('uploaded_photo_4', $image)      // 最大サイズちょうど
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['uploaded_photo_4']);

        $smallImage = UploadedFile::fake()->image('small.png')->size(1);
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('uploaded_photo_4', $smallImage) // 1KBの小さい画像
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['uploaded_photo_4']);

        // uploaded_photo_4 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('uploaded_photo_4', null)        // nullable
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors(['uploaded_photo_4']);
    }

    public function test_validation_request_type_4_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_4のバリデーションテスト
        // title_4のバリデーション
        Livewire::test(Request::class)
            ->set('title_4', '')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['title_4' => 'required']);

        Livewire::test(Request::class)
            ->set('title_4', 123)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['title_4' => 'string']);

        Livewire::test(Request::class)
            ->set('title_4', str_repeat('a', 101))
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['title_4' => 'max']);

        // detail_4のバリデーション
        Livewire::test(Request::class)
            ->set('detail_4', '')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['detail_4' => 'required']);

        Livewire::test(Request::class)
            ->set('detail_4', 123)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['detail_4' => 'string']);

        Livewire::test(Request::class)
            ->set('detail_4', str_repeat('a', 3001))
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['detail_4' => 'max']);

        // environment_4のバリデーション
        Livewire::test(Request::class)
            ->set('environment_4', '')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['environment_4' => 'required']);

        Livewire::test(Request::class)
            ->set('environment_4', -1)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['environment_4' => 'between']);

        Livewire::test(Request::class)
            ->set('environment_4', 7)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['environment_4' => 'between']);

        // reference_url_4のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_4', 'not_url')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['reference_url_4' => 'url']);

        // uploaded_photo_4のバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);

        Livewire::test(Request::class)
            ->set('uploaded_photo_4', $notImage)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['uploaded_photo_4' => 'image']);

        $largeKilobyteImage = UploadedFile::fake()->image('test.png')->size(2049);

        Livewire::test(Request::class)
            ->set('uploaded_photo_4', $largeKilobyteImage)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['uploaded_photo_4' => 'max']);
    }
}
