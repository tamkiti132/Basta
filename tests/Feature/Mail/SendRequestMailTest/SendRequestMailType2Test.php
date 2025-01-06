<?php

namespace Tests\Feature\Mail;

use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendRequestMailType2Test extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // テスト用のロケールを設定
    app()->setLocale('testing');

    // テスト用のストレージを設定
    Storage::fake('public');
  }

  public function test_sendRequest_check_mail_content_type_2()
  {
    // Arrange（準備）
    $user = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $this->actingAs($user);

    // テスト用の画像を作成
    $image = UploadedFile::fake()->image('test.png')->size(2048);

    $mailable = new SendRequestMail('type_2', [
      'email_2' => 'test@example.com',
      'function_request_type' => 0,
      'title_2' => 'テストタイトル',
      'detail_2' => 'テスト詳細',
      'environment_2' => 0,
      'reference_url_2' => 'https://example.com',
      'uploaded_photo_2' => $image,
    ]);

    // Act（実行） && Assert（検証）
    $mailable->assertSeeInHtml('サービス機能の追加・改善リクエスト');
    $mailable->assertSeeInHtml('新機能のリクエスト');
    $mailable->assertSeeInHtml('テストタイトル');
    $mailable->assertSeeInHtml('テスト詳細');
    $mailable->assertSeeInHtml('パソコンWindowsブラウザ');
    $mailable->assertSeeInHtml('https://example.com');

    $mailable->assertSeeInText('サービス機能の追加・改善リクエスト');
    $mailable->assertSeeInText('新機能のリクエスト');
    $mailable->assertSeeInText('テストタイトル');
    $mailable->assertSeeInText('テスト詳細');
    $mailable->assertSeeInText('パソコンWindowsブラウザ');
    $mailable->assertSeeInText('https://example.com');

    $mailable->assertHasAttachment(
      Attachment::fromPath($image->getRealPath())
        ->as($image->getClientOriginalName())
        ->withMime($image->getMimeType())
    );
  }
}
