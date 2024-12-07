<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        Livewire::test(Request::class)
            ->set('email_2', 'test@example.com')
            ->set('function_request_type', 1)
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')
            ->call('sendRequest', 'type_2')
            ->assertHasNoErrors()
            ->assertRedirect('request');
    }

    public function test_validation_request_type_2_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_2のバリデーションテスト
        // email_2のバリデーション
        Livewire::test(Request::class)
            ->set('email_2', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['email_2' => 'required']);

        Livewire::test(Request::class)
            ->set('email_2', 123)
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['email_2' => 'string']);

        Livewire::test(Request::class)
            ->set('email_2', 'not_email')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['email_2' => 'email']);

        Livewire::test(Request::class)
            ->set('email_2', str_repeat('a', 256))
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['email_2' => 'max']);


        // function_request_typeのバリデーション
        Livewire::test(Request::class)
            ->set('function_request_type', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['function_request_type' => 'required']);


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


        // environment_2のバリデーション
        Livewire::test(Request::class)
            ->set('environment_2', '')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['environment_2' => 'required']);


        // reference_url_2のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_2', 'not_url')
            ->call('sendRequest', "type_2")
            ->assertHasErrors(['reference_url_2' => 'url']);
    }
}
