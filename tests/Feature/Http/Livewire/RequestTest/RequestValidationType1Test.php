<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
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
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        Livewire::test(Request::class)
            ->set('email_1', 'test@example.com')
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', '1')
            ->set('additional_information', 'テスト追加情報')
            ->set('reference_url_1', 'https://example.com')
            ->call('sendRequest', 'type_1')
            ->assertHasNoErrors()
            ->assertRedirect('request');
    }

    public function test_validation_request_type_1_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_1のバリデーションテスト
        // email_1のバリデーション
        Livewire::test(Request::class)
            ->set('email_1', '')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['email_1' => 'required']);

        Livewire::test(Request::class)
            ->set('email_1', 123)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['email_1' => 'string']);

        Livewire::test(Request::class)
            ->set('email_1', 'not_email')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['email_1' => 'email']);

        Livewire::test(Request::class)
            ->set('email_1', str_repeat('a', 256))
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['email_1' => 'max']);


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


        // environment_1のバリデーション
        Livewire::test(Request::class)
            ->set('environment_1', '')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['environment_1' => 'required']);


        // additional_informationのバリデーション
        Livewire::test(Request::class)
            ->set('additional_information', 123)
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['additional_information' => 'string']);


        // reference_url_1のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_1', 'not_url')
            ->call('sendRequest', "type_1")
            ->assertHasErrors(['reference_url_1' => 'url']);
    }
}
