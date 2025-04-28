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

    public function test_validation_成功_request_type_1()
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

        // title_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'a')
            ->set('detail_1', 'a')
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['title_1' => 'required'])
            ->assertHasNoErrors(['title_1' => 'string']);

        Livewire::test(Request::class)
            ->set('title_1', str_repeat('a', 100))
            ->set('detail_1', 'a')
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['title_1' => 'max']);

        // detail_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'a')
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['detail_1' => 'required'])
            ->assertHasNoErrors(['detail_1' => 'string']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', str_repeat('a', 3000))
            ->set('environment_1', 1)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['detail_1' => 'max']);

        // environment_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 6)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['environment_1' => 'required'])
            ->assertHasNoErrors(['environment_1' => 'integer'])
            ->assertHasNoErrors(['environment_1' => 'between']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 0)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['environment_1' => 'required'])
            ->assertHasNoErrors(['environment_1' => 'integer'])
            ->assertHasNoErrors(['environment_1' => 'between']);

        // additional_informationのバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', null)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['additional_information' => 'nullable'])
            ->assertHasNoErrors(['additional_information' => 'string']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', str_repeat('a', 3000))
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['additional_information' => 'max']);


        // reference_url_1のバリデーション
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', null)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1' => 'nullable']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('reference_url_1', 'https://example.com')
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['reference_url_1' => 'url']);


        // uploaded_photo_1のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('uploaded_photo_1', null)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['uploaded_photo_1' => 'nullable']);

        Livewire::test(Request::class)
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('uploaded_photo_1', $image)
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors(['uploaded_photo_1' => 'image'])
            ->assertHasNoErrors(['uploaded_photo_1' => 'max']);
    }

    public function test_validation_失敗_request_type_1()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
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

        Livewire::test(Request::class)
            ->set('environment_1', 'string')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['environment_1' => 'integer']);

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
