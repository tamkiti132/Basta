<?php

namespace Tests\Feature\Mail;

use App\Mail\InviteMail;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InviteMailTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_invite_mail_check_mail_content()
    {
        // Arrange（準備）
        // 招待メールを送信するユーザー
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => 'テストグループ名',
            'introduction' => 'テストグループ紹介文',
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        // 招待されるユーザー
        $target_user = User::factory()->create([
            'nickname' => 'テストニックネーム',
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        $mailable = new InviteMail($group, $target_user);

        // Act（実行） && Assert（検証）
        // 件名の検証
        $mailable->assertHasSubject('Basta グループ招待');

        // 送信者と受信者の検証
        $mailable->assertFrom('test@example.com');
        $mailable->assertTo('target@example.com');

        // コンテンツの検証（HTML版）
        $mailable->assertSeeInHtml('テストニックネーム');
        $mailable->assertSeeInHtml('テストグループ名');
        $mailable->assertSeeInHtml('テストグループ紹介文');
        $mailable->assertSeeInHtml('グループに参加する');
        $mailable->assertSeeInHtml('/invite/join-group');

        // コンテンツの検証（テキスト版）
        $mailable->assertSeeInText('テストニックネーム');
        $mailable->assertSeeInText('テストグループ名');
        $mailable->assertSeeInText('テストグループ紹介文');
        $mailable->assertSeeInText('グループの管理者から招待を受けています');

        // 一時的なURLの検証
        $mailable->assertSeeInHtml('signature=');
        $mailable->assertSeeInHtml('expires=');
        $mailable->assertSeeInText('signature=');
        $mailable->assertSeeInText('expires=');
    }
}
