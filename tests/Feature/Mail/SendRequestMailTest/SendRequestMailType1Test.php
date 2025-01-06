<?php

namespace Tests\Feature\Mail;

use App\Mail\SendRequestMail;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SendRequestMailType1Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_sendRequest_check_mail_content_type_1()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $this->actingAs($user);

        // テスト用の画像を作成
        $image = UploadedFile::fake()->image('test.png')->size(2048);

        $mailable = new SendRequestMail('type_1', [
            'title_1' => 'テストタイトル',
            'detail_1' => 'テスト詳細',
            'environment_1' => 0,
            'additional_information' => 'テスト追加情報',
            'reference_url_1' => 'https://example.com',
            'uploaded_photo_1' => $image,
        ]);

        // Act（実行） && Assert（検証）
        $mailable->assertSeeInHtml('サービスの不具合の報告');
        $mailable->assertSeeInHtml('テストタイトル');
        $mailable->assertSeeInHtml('テスト詳細');
        $mailable->assertSeeInHtml('パソコンWindowsブラウザ');
        $mailable->assertSeeInHtml('テスト追加情報');
        $mailable->assertSeeInHtml('https://example.com');

        $mailable->assertSeeInText('サービスの不具合の報告');
        $mailable->assertSeeInText('テストタイトル');
        $mailable->assertSeeInText('テスト詳細');
        $mailable->assertSeeInText('パソコンWindowsブラウザ');
        $mailable->assertSeeInText('テスト追加情報');
        $mailable->assertSeeInText('https://example.com');

        $mailable->assertHasAttachment(
            Attachment::fromPath($image->getRealPath())
                ->as($image->getClientOriginalName())
                ->withMime($image->getMimeType())
        );
    }
}
