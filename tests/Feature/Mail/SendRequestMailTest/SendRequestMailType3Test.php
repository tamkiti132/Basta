<?php

namespace Tests\Feature\Mail;

use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendRequestMailType3Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_send_request_check_mail_content_type_3()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // テスト用の画像を作成
        $image = UploadedFile::fake()->image('test.png')->size(2048);

        $mailable = new SendRequestMail('type_3', [
            'title_3' => 'テストタイトル',
            'detail_3' => 'テスト詳細',
            'explanation' => 'テスト技術的説明',
            'steps_to_reproduce' => 'テスト再現手順',
            'abuse_method' => 'テスト悪用方法',
            'workaround' => 'テスト回避策',
            'environment_3' => 0,
            'reference_url_3' => 'https://example.com',
            'uploaded_photo_3' => $image,
        ]);

        // Act（実行） && Assert（検証）
        // エンベロープ（件名、送信元、宛先）の検証
        $mailable->assertHasSubject('セキュリティ脆弱性の報告');
        $mailable->assertFrom('test@example.com');
        $mailable->assertTo('basta.h.a.132@gmail.com');

        // HTML版の検証
        $mailable->assertSeeInHtml('セキュリティ脆弱性の報告');
        $mailable->assertSeeInHtml('テストタイトル');
        $mailable->assertSeeInHtml('テスト詳細');
        $mailable->assertSeeInHtml('テスト技術的説明');
        $mailable->assertSeeInHtml('テスト再現手順');
        $mailable->assertSeeInHtml('テスト悪用方法');
        $mailable->assertSeeInHtml('テスト回避策');
        $mailable->assertSeeInHtml('パソコンWindowsブラウザ');
        $mailable->assertSeeInHtml('https://example.com');

        // テキスト版の検証
        $mailable->assertSeeInText('セキュリティ脆弱性の報告');
        $mailable->assertSeeInText('テストタイトル');
        $mailable->assertSeeInText('テスト詳細');
        $mailable->assertSeeInText('テスト技術的説明');
        $mailable->assertSeeInText('テスト再現手順');
        $mailable->assertSeeInText('テスト悪用方法');
        $mailable->assertSeeInText('テスト回避策');
        $mailable->assertSeeInText('パソコンWindowsブラウザ');
        $mailable->assertSeeInText('https://example.com');

        // 添付ファイルの検証
        $mailable->assertHasAttachment(
            Attachment::fromPath($image->getRealPath())
                ->as($image->getClientOriginalName())
                ->withMime($image->getMimeType())
        );
    }
}
