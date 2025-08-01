<?php

namespace Tests\Feature\Http\Livewire\RequestTest;

use App\Http\Livewire\Request;
use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_send_request_check_send_mail_type_1()
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
            ->set('title_1', 'テストタイトル')
            ->set('detail_1', 'テスト詳細')
            ->set('environment_1', 1)
            ->set('additional_information', 'テスト追加情報')
            ->set('reference_url_1', 'https://example.com')
            ->set('uploaded_photo_1', $image)
            ->call('sendRequest', 'type_1')
            ->assertRedirect('request');

        // 送信元 ・ 送信先のメールアドレスは app/Mail/SendRequestMail.php に記載
        Mail::assertSent(SendRequestMail::class, function ($mail) {
            return $mail->hasTo('basta.h.a.132@gmail.com') &&
              $mail->hasFrom('test@example.com') &&
              $mail->hasSubject('サービスの不具合の報告');
        });
    }

    public function test_send_request_check_send_mail_type_2()
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
            ->set('function_request_type', '0')
            ->set('title_2', 'テストタイトル')
            ->set('detail_2', 'テスト詳細')
            ->set('environment_2', 1)
            ->set('reference_url_2', 'https://example.com')
            ->set('uploaded_photo_2', $image)
            ->call('sendRequest', 'type_2')
            ->assertRedirect('request');

        // 送信元 ・ 送信先のメールアドレスは app/Mail/SendRequestMail.php に記載
        Mail::assertSent(SendRequestMail::class, function ($mail) {
            return $mail->hasTo('basta.h.a.132@gmail.com') &&
              $mail->hasFrom('test@example.com') &&
              $mail->hasSubject('サービス機能の追加・改善リクエスト');
        });
    }

    public function test_send_request_check_send_mail_type_3()
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

    public function test_send_request_check_send_mail_type_4()
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

    public function test_validation_成功_send_request_request_type()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        Livewire::test(Request::class)
            ->call('sendRequest', 'type_4')
            ->assertSessionMissing('error');
    }

    public function test_validation_失敗_send_request_request_type()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        Livewire::test(Request::class)
            ->call('sendRequest', 'type_5')
            ->assertSessionHas('error', '不正なリクエストです。');
    }
}
