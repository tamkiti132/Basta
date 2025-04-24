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

class RequestValidationType3Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを指定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_validation_request_type_3_成功()
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
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('explanation', 'テスト技術的説明')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('abuse_method', 'テスト悪用方法')
            ->set('workaround', 'テスト回避策')
            ->set('environment_3', 1)
            ->set('reference_url_3', 'https://example.com')
            ->set('uploaded_photo_3', $image)
            ->call('sendRequest', 'type_3')
            ->assertRedirect('request')
            ->assertHasNoErrors();

        // title_3のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', str_repeat('a', 100))  // max:100の境界値
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['title_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'a')                  // 最小文字数
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['title_3']);

        // detail_3のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', str_repeat('a', 3000))  // max:3000の境界値
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['detail_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'a')                 // 最小文字数
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['detail_3']);

        // explanationのバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('explanation', str_repeat('a', 3000))  // max:3000の境界値
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['explanation']);

        // explanation - nullableテスト
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('explanation', null)             // nullable
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['explanation']);

        // steps_to_reproduceのバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', str_repeat('a', 3000))  // max:3000の境界値
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['steps_to_reproduce']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'a')       // 最小文字数
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['steps_to_reproduce']);

        // abuse_methodのバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('abuse_method', str_repeat('a', 3000))  // max:3000の境界値
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['abuse_method']);

        // abuse_method - nullableテスト
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('abuse_method', null)            // nullable
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['abuse_method']);

        // workaroundのバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('workaround', str_repeat('a', 3000))  // max:3000の境界値
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['workaround']);

        // workaround - nullableテスト
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('workaround', null)              // nullable
            ->set('environment_3', 1)
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['workaround']);

        // environment_3のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 6)              // between:0,6の上限値
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['environment_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 0)              // between:0,6の下限値
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['environment_3']);

        // reference_url_3のバリデーション
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('reference_url_3', 'https://example.com')  // 通常のURL
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['reference_url_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('reference_url_3', 'https://localhost:8000/test?param=value#fragment')  // 複雑なURL
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['reference_url_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('reference_url_3', 'http://example.com')  // HTTPプロトコル
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['reference_url_3']);

        // reference_url_3 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('reference_url_3', null)         // nullable
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['reference_url_3']);

        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('reference_url_3', '')           // 空文字列
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['reference_url_3']);

        // uploaded_photo_3のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('uploaded_photo_3', $image)      // 最大サイズちょうど
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['uploaded_photo_3']);

        $smallImage = UploadedFile::fake()->image('small.png')->size(1);
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('uploaded_photo_3', $smallImage) // 1KBの小さい画像
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['uploaded_photo_3']);

        // uploaded_photo_3 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_3', 'テストタイトル')
            ->set('detail_3', 'テスト詳細')
            ->set('steps_to_reproduce', 'テスト再現手順')
            ->set('environment_3', 1)
            ->set('uploaded_photo_3', null)        // nullable
            ->call('sendRequest', 'type_3')
            ->assertHasNoErrors(['uploaded_photo_3']);
    }

    public function test_validation_request_type_3_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_3のバリデーションテスト
        // title_3のバリデーション
        Livewire::test(Request::class)
            ->set('title_3', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['title_3' => 'required']);

        Livewire::test(Request::class)
            ->set('title_3', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['title_3' => 'string']);

        Livewire::test(Request::class)
            ->set('title_3', str_repeat('a', 101))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['title_3' => 'max']);

        // detail_3のバリデーション
        Livewire::test(Request::class)
            ->set('detail_3', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['detail_3' => 'required']);

        Livewire::test(Request::class)
            ->set('detail_3', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['detail_3' => 'string']);

        Livewire::test(Request::class)
            ->set('detail_3', str_repeat('a', 3001))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['detail_3' => 'max']);

        // explanationのバリデーション
        Livewire::test(Request::class)
            ->set('explanation', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['explanation' => 'string']);

        Livewire::test(Request::class)
            ->set('explanation', str_repeat('a', 3001))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['explanation' => 'max']);

        // steps_to_reproduceのバリデーション
        Livewire::test(Request::class)
            ->set('steps_to_reproduce', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['steps_to_reproduce' => 'required']);

        Livewire::test(Request::class)
            ->set('steps_to_reproduce', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['steps_to_reproduce' => 'string']);

        Livewire::test(Request::class)
            ->set('steps_to_reproduce', str_repeat('a', 3001))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['steps_to_reproduce' => 'max']);

        // abuse_methodのバリデーション
        Livewire::test(Request::class)
            ->set('abuse_method', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['abuse_method' => 'string']);

        Livewire::test(Request::class)
            ->set('abuse_method', str_repeat('a', 3001))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['abuse_method' => 'max']);

        // workaroundのバリデーション
        Livewire::test(Request::class)
            ->set('workaround', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['workaround' => 'string']);

        Livewire::test(Request::class)
            ->set('workaround', str_repeat('a', 3001))
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['workaround' => 'max']);

        // environment_3のバリデーション
        Livewire::test(Request::class)
            ->set('environment_3', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['environment_3' => 'required']);

        Livewire::test(Request::class)
            ->set('environment_3', -1)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['environment_3' => 'between']);

        Livewire::test(Request::class)
            ->set('environment_3', 7)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['environment_3' => 'between']);

        // reference_url_3のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_3', 'not_url')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['reference_url_3' => 'url']);

        // uploaded_photo_3のバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);

        Livewire::test(Request::class)
            ->set('uploaded_photo_3', $notImage)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['uploaded_photo_3' => 'image']);

        $largeKilobyteImage = UploadedFile::fake()->image('test.png')->size(2049);

        Livewire::test(Request::class)
            ->set('uploaded_photo_3', $largeKilobyteImage)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['uploaded_photo_3' => 'max']);
    }
}
