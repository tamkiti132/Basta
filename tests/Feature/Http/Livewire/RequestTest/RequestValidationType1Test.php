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

class RequestValidationType1Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを指定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_validation_request_type_1_成功()
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
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', 'テスト追加情報')
            ->set('reference_url_1', 'https://example.com')
            ->set('uploaded_photo_1', $image)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors();

        // title_1のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_1', str_repeat('a', 100))  // max:100の境界値
            ->set('detail_1', 'a')
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['title_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'a')                  // 最小文字数
            ->set('detail_1', 'a')
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['title_1']);

        // detail_1のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', str_repeat('a', 3000))  // max:3000の境界値（最大長）
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['detail_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'a')  // 最小文字数
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['detail_1']);

        // environment_1のバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 6)  // between:0,6の上限値
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['environment_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 0)  // between:0,6の下限値
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['environment_1']);

        // additional_informationのバリデーション（境界値テスト）
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', str_repeat('a', 3000))  // max:3000の境界値（最大長）
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['additional_information']);

        // additional_information - nullableテスト
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', null)  // nullable
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['additional_information']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', '')  // 空文字列
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['additional_information']);

        // reference_url_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', 'https://example.com')  // 通常のURL
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', 'https://localhost:8000/test?param=value#fragment')  // 複雑なURL
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', 'http://example.com')  // HTTPプロトコル
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1']);

        // reference_url_1 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', null)  // nullable
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', '')  // 空文字列（URLバリデーションで空文字列は許容されるか確認）
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1']);

        // uploaded_photo_1のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('uploaded_photo_1', $image)  // 最大サイズちょうど
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['uploaded_photo_1']);

        $smallImage = UploadedFile::fake()->image('small.png')->size(1);
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('uploaded_photo_1', $smallImage)  // 1KBの小さい画像
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['uploaded_photo_1']);

        // uploaded_photo_1 - nullableテスト
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('uploaded_photo_1', null)  // nullable
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['uploaded_photo_1']);
    }

    public function test_validation_request_type_1_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_1のバリデーションテスト
        // title_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', '')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['title_1' => 'required']);

        Livewire::test(Request::class)
            ->set('title_1', 123)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['title_1' => 'string']);

        Livewire::test(Request::class)
            ->set('title_1', str_repeat('a', 101))
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['title_1' => 'max']);

        // detail_1のバリデーション
        Livewire::test(Request::class)
            ->set('detail_1', '')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['detail_1' => 'required']);

        Livewire::test(Request::class)
            ->set('detail_1', 123)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['detail_1' => 'string']);

        Livewire::test(Request::class)
            ->set('detail_1', str_repeat('a', 3001))
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['detail_1' => 'max']);

        // environment_1のバリデーション
        Livewire::test(Request::class)
            ->set('environment_1', '')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['environment_1' => 'required']);

        Livewire::test(Request::class)
            ->set('environment_1', -1)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['environment_1' => 'between']);

        Livewire::test(Request::class)
            ->set('environment_1', 7)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['environment_1' => 'between']);

        // additional_informationのバリデーション
        Livewire::test(Request::class)
            ->set('additional_information', 123)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['additional_information' => 'string']);

        Livewire::test(Request::class)
            ->set('additional_information', str_repeat('a', 3001))
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['additional_information' => 'max']);

        // reference_url_1のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_1', 'not_url')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['reference_url_1' => 'url']);

        // uploaded_photo_1のバリデーション
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);

        Livewire::test(Request::class)
            ->set('uploaded_photo_1', $notImage)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['uploaded_photo_1' => 'image']);

        $largeKilobyteImage = UploadedFile::fake()->image('test.png')->size(2049);

        Livewire::test(Request::class)
            ->set('uploaded_photo_1', $largeKilobyteImage)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['uploaded_photo_1' => 'max']);
    }
}
