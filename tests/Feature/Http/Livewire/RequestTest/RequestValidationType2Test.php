<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Models\User;
use Illuminate\Http\UploadedFile;
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

    public function test_validation_成功_request_type_2()
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
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')
            ->set('uploaded_photo_2', $image)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors();

        // function_request_typeのバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['function_request_type' => 'required'])
            ->assertHasNoErrors(['function_request_type' => 'integer'])
            ->assertHasNoErrors(['function_request_type' => 'between']);

        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['function_request_type' => 'between']);

        // title_2のバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', str_repeat('a', 100))
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['title_2' => 'required'])
            ->assertHasNoErrors(['title_2' => 'string'])
            ->assertHasNoErrors(['title_2' => 'max']);

        // detail_2のバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', str_repeat('a', 3000))
            ->set('environment_2', 1)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['detail_2' => 'required'])
            ->assertHasNoErrors(['detail_2' => 'string'])
            ->assertHasNoErrors(['detail_2' => 'max']);

        // environment_2のバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 0)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['environment_2' => 'required'])
            ->assertHasNoErrors(['environment_2' => 'integer'])
            ->assertHasNoErrors(['environment_2' => 'between']);

        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 6)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['environment_2' => 'between']);

        // reference_url_2のバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2' => 'url']);

        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', null)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['reference_url_2' => 'nullable']);

        // uploaded_photo_2のバリデーション
        $image = UploadedFile::fake()->image('test.png')->size(2048);
        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('uploaded_photo_2', $image)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2' => 'image'])
            ->assertHasNoErrors(['uploaded_photo_2' => 'max']);

        Livewire::test(Request::class)
            ->set('function_request_type', 0)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('uploaded_photo_2', null)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2' => 'nullable']);
    }

    public function test_validation_失敗_request_type_2()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // function_request_typeのバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', '')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['function_request_type' => 'required']);

        Livewire::test(Request::class)
            ->set('function_request_type', -1)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['function_request_type' => 'between']);

        Livewire::test(Request::class)
            ->set('function_request_type', 4)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['function_request_type' => 'between']);

        // title_2のバリデーション
        Livewire::test(Request::class)
            ->set('title_2', '')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['title_2' => 'required']);

        Livewire::test(Request::class)
            ->set('title_2', 123)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['title_2' => 'string']);

        Livewire::test(Request::class)
            ->set('title_2', str_repeat('a', 101))
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['title_2' => 'max']);

        // detail_2のバリデーション
        Livewire::test(Request::class)
            ->set('detail_2', '')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['detail_2' => 'required']);

        Livewire::test(Request::class)
            ->set('detail_2', 123)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['detail_2' => 'string']);

        Livewire::test(Request::class)
            ->set('detail_2', str_repeat('a', 3001))
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['detail_2' => 'max']);

        // environment_2のバリデーション
        Livewire::test(Request::class)
            ->set('environment_2', '')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['environment_2' => 'required']);

        Livewire::test(Request::class)
            ->set('environment_2', 'a')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['environment_2' => 'integer']);

        Livewire::test(Request::class)
            ->set('environment_2', -1)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['environment_2' => 'between']);

        Livewire::test(Request::class)
            ->set('environment_2', 7)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['environment_2' => 'between']);

        // reference_url_2のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_2', 'not_url')
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['reference_url_2' => 'url']);

        // uploaded_photo_2のバリデーション
        Livewire::test(Request::class)
            ->set('uploaded_photo_2', null)
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors(['uploaded_photo_2' => 'nullable']);

        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        Livewire::test(Request::class)
            ->set('uploaded_photo_2', $notImage)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['uploaded_photo_2' => 'image']);

        $largeKilobyteImage = UploadedFile::fake()->image('test.png')->size(2049);
        Livewire::test(Request::class)
            ->set('uploaded_photo_2', $largeKilobyteImage)
            ->call('sendRequest', 'type_2')
            ->assertHasErrors(['uploaded_photo_2' => 'max']);
    }
}
