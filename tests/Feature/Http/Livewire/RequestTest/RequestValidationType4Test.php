<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        Livewire::test(Request::class)
            ->set('email_4', 'test@example.com')
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://example.com')
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors()
            ->assertRedirect('request');
    }

    public function test_validation_request_type_4_失敗()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // type_4のバリデーションテスト
        // email_4のバリデーション
        Livewire::test(Request::class)
            ->set('email_4', '')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['email_4' => 'required']);

        Livewire::test(Request::class)
            ->set('email_4', 123)
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['email_4' => 'string']);

        Livewire::test(Request::class)
            ->set('email_4', 'not_email')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['email_4' => 'email']);

        Livewire::test(Request::class)
            ->set('email_4', str_repeat('a', 256))
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['email_4' => 'max']);


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


        // environment_4のバリデーション
        Livewire::test(Request::class)
            ->set('environment_4', '')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['environment_4' => 'required']);


        // reference_url_4のバリデーション
        Livewire::test(Request::class)
            ->set('reference_url_4', 'not_url')
            ->call('sendRequest', "type_4")
            ->assertHasErrors(['reference_url_4' => 'url']);
    }
}
