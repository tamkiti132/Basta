<?php

namespace Tests\Feature\Mail;

use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendRequestMailType4Test extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // テスト用のロケールを設定
    app()->setLocale('testing');

    // テスト用のストレージを設定
    Storage::fake('public');
  }

  public function test_sendRequest_check_mail_content_type_4()
  {
    // Arrange（準備）
    $user = User::factory()->create([
      'suspension_state' => 0,
    ]);

    $this->actingAs($user);

    // テスト用の画像を作成
    $image = UploadedFile::fake()->image('test.png')->size(2048);

    $mailable = new SendRequestMail('type_4', [
      'title_4' => 'テストタイトル',
      'detail_4' => 'テスト詳細',
      'environment_4' => 0,
      'reference_url_4' => 'https://example.com',
      'uploaded_photo_4' => $image,
    ]);

    // Act（実行） && Assert（検証）
    $mailable->assertSeeInHtml('その他お問い合わせ');
    $mailable->assertSeeInHtml('テストタイトル');
    $mailable->assertSeeInHtml('テスト詳細');
    $mailable->assertSeeInHtml('パソコンWindowsブラウザ');
    $mailable->assertSeeInHtml('https://example.com');

    $mailable->assertSeeInText('その他お問い合わせ');
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
