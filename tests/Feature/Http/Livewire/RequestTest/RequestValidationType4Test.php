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

    public function test_sendRequest_check_send_mail_type_4()
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
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://example.com')
            ->set('uploaded_photo_4', $image)
            ->call('sendRequest', 'type_4')
            ->assertRedirect('request');

        // 送信元 ・ 送信先のメールアドレスは app/Mail/SendRequestMail.php に記載
        Mail::assertSent(SendRequestMail::class, function ($mail) {
            return $mail->hasTo('basta.h.a.132@gmail.com') &&
                $mail->hasFrom('test@example.com') &&
                $mail->hasSubject('その他お問い合わせ');
        });
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
        Livewire::test(Request::class)
            ->set('title_4', 'テストタイトル')
            ->set('detail_4', 'テスト詳細')
            ->set('environment_4', 1)
            ->set('reference_url_4', 'https://example.com')
            ->set('uploaded_photo_4', $image)
            ->call('sendRequest', 'type_4')
            ->assertHasNoErrors()
            ->assertRedirect('request');
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
