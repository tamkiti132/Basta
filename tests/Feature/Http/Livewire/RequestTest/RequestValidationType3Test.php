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

    public function test_sendRequest_check_send_mail_type_3()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // テスト用の画像を作成
        $image = UploadedFile::fake()->image('test.png')->size(2048);

        Mail::fake();


        // Act（実行） & Assert（検証）
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
            ->assertRedirect('request');

        // 送信元 ・ 送信先のメールアドレスは app/Mail/SendRequestMail.php に記載
        Mail::assertSent(SendRequestMail::class, function ($mail) {
            return $mail->hasTo('basta.h.a.132@gmail.com') &&
                $mail->hasFrom('test@example.com') &&
                $mail->hasSubject('セキュリティ脆弱性の報告');
        });
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
            ->assertHasNoErrors()
            ->assertRedirect('request');
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


        // explanationのバリデーション
        Livewire::test(Request::class)
            ->set('explanation', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['explanation' => 'string']);


        // steps_to_reproduceのバリデーション
        Livewire::test(Request::class)
            ->set('steps_to_reproduce', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['steps_to_reproduce' => 'required']);

        Livewire::test(Request::class)
            ->set('steps_to_reproduce', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['steps_to_reproduce' => 'string']);


        // abuse_methodのバリデーション
        Livewire::test(Request::class)
            ->set('abuse_method', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['abuse_method' => 'string']);


        // workaroundのバリデーション
        Livewire::test(Request::class)
            ->set('workaround', 123)
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['workaround' => 'string']);


        // environment_3のバリデーション
        Livewire::test(Request::class)
            ->set('environment_3', '')
            ->call('sendRequest', "type_3")
            ->assertHasErrors(['environment_3' => 'required']);


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
