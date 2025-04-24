<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RequestValidationType2Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを指定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_validation_request_type_2_成功()
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
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')
            ->set('uploaded_photo_2', $image)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors();

        // function_request_typeのバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', '0')     // between:0,3の下限値
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['function_request_type']);

        Livewire::test(Request::class)
            ->set('function_request_type', '3')     // between:0,3の上限値
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['function_request_type']);

        // title_2のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', str_repeat('a', 100))  // max:100の境界値
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['title_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'a')                  // 最小文字数
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['title_2']);

        // detail_2のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', str_repeat('a', 3000))  // max:3000の境界値
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['detail_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'a')                 // 最小文字数
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['detail_2']);

        // environment_2のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 6)              // between:0,6の上限値
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['environment_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 0)              // between:0,6の下限値
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['environment_2']);

        // reference_url_2のバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')  // 通常のURL
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://localhost:8000/test?param=value#fragment')  // 複雑なURL
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'http://example.com')  // HTTPプロトコル
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2']);

        // reference_url_2 - nullableテスト
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', null)         // nullable
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2']);

        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', '')           // 空文字列
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2']);

        // uploaded_photo_2のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('uploaded_photo_2', $image)      // 最大サイズちょうど
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2']);

        $smallImage = UploadedFile::fake()->image('small.png')->size(1);
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('uploaded_photo_2', $smallImage) // 1KBの小さい画像
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2']);

        // uploaded_photo_2 - nullableテスト
        Livewire::test(Request::class)
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('uploaded_photo_2', null)        // nullable
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2']);
    }

    public function test_validation_request_type_2_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_2のバリデーションテスト
        // function_request_typeのバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['function_request_type' => 'required']);

        Livewire::test(Request::class)
            ->set('function_request_type', -1)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['function_request_type' => 'between']);

        Livewire::test(Request::class)
            ->set('function_request_type', 4)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['function_request_type' => 'between']);


        // title_2のバリデーション
        Livewire::test(Request::class)
            ->set('title_2', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['title_2' => 'required']);

        Livewire::test(Request::class)
            ->set('title_2', 123)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['title_2' => 'string']);

        Livewire::test(Request::class)
            ->set('title_2', str_repeat('a', 101))
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['title_2' => 'max']);


        // detail_2のバリデーション
        Livewire::test(Request::class)
            ->set('detail_2', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['detail_2' => 'required']);

        Livewire::test(Request::class)
            ->set('detail_2', 123)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['detail_2' => 'string']);

        Livewire::test(Request::class)
            ->set('detail_2', str_repeat('a', 3001))
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['detail_2' => 'max']);


        // environment_2のバリデーション
        Livewire::test(Request::class)
            ->set('environment_2', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['environment_2' => 'required']);

        Livewire::test(Request::class)
            ->set('environment_2', -1)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['environment_2' => 'between']);

        Livewire::test(Request::class)
            ->set('environment_2', 7)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['environment_2' => 'between']);


        // reference_url_2のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_2', 'not_url')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['reference_url_2' => 'url']);

        // uploaded_photo_2のバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);

        Livewire::test(Request::class)
            ->set('uploaded_photo_2', $notImage)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['uploaded_photo_2' => 'image']);

        $largeKilobyteImage = UploadedFile::fake()->image('test.png')->size(2049);

        Livewire::test(Request::class)
            ->set('uploaded_photo_2', $largeKilobyteImage)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['uploaded_photo_2' => 'max']);
    }
}
