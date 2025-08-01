<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupEdit;
use App\Mail\InviteMail;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GroupEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_update_group_info()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => '更新前のグループ名',
            'introduction' => '更新前のグループ紹介文',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        // テスト用の画像を作成
        $group_image = UploadedFile::fake()->image('test.png')->size(2048);

        // Act（実行）  & Assert（検証）
        $component = Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            // 各プロパティの値が各カラムの更新前データでセットされているか確認
            ->assertSet('group_id', $group->id)
            ->assertSet('group_data.name', $group->name)
            ->assertSet('group_data.introduction', $group->introduction)
            ->assertSet('group_image_preview', null)

            // 各プロパティの値に各カラムの更新後データをセット
            ->set('group_image_preview', $group_image)
            ->set('group_image_delete_flag', false)
            ->set('group_data.name', '更新後のグループ名')
            ->set('group_data.introduction', '更新後のグループ紹介文')

            // 各データを更新する
            ->call('updateGroupInfo');

        $storedImage = $component->get('storedImage');

        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($storedImage);

        // データベース検証
        $this->assertDatabaseHas('groups', [
            'name' => '更新後のグループ名',
            'introduction' => '更新後のグループ紹介文',
            'group_photo_path' => basename($storedImage),
        ]);

        // ここから、画像を削除するテスト
        // Act（実行）
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_image_preview', null)
            ->set('group_image_delete_flag', true)
            ->call('updateGroupInfo');

        // Assert（検証）
        // ストレージにファイルが削除されていることを確認
        Storage::disk('public')->assertMissing($storedImage);

        // データベース検証
        $this->assertDatabaseMissing('groups', [
            'group_photo_path' => basename($storedImage),
        ]);
    }

    public function test_validation_成功_update_group_info()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => '更新前のグループ名',
            'introduction' => '更新前のグループ紹介文',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        // Act（実行） & Assert（検証）
        // 基本ケース - 全フィールド入力
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_image_preview', UploadedFile::fake()->image('test.png')->size(1024))
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->call('updateGroupInfo')
            ->assertHasNoErrors();

        // group_image_previewのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->set('group_image_preview', null)
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_image_preview' => 'nullable']);

        $Image = UploadedFile::fake()->image('test.png');
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->set('group_image_preview', $Image)
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_image_preview' => 'image']);

        $maxImage = UploadedFile::fake()->image('max.png')->size(2048);
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->set('group_image_preview', $maxImage)
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_image_preview' => 'max']);

        // group_data.nameのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.name' => 'required']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', '文字列123')
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.name' => 'string']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', str_repeat('あ', 50))
            ->set('group_data.introduction', 'テストグループ紹介文')
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.name' => 'max']);

        // group_data.introductionのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', 'テスト紹介文')
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.introduction' => 'required']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', '文字列と記号!@#')
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.introduction' => 'string']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_data.name', 'テストグループ名')
            ->set('group_data.introduction', str_repeat('あ', 200))
            ->call('updateGroupInfo')
            ->assertHasNoErrors(['group_data.introduction' => 'max']);
    }

    public function test_validation_失敗_update_group_info()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => '更新前のグループ名',
            'introduction' => '更新前のグループ紹介文',
            'suspension_state' => 0,
        ]);

        $group->userRoles()->attach($user, ['role' => 10]);

        // バリデーションに失敗するテスト用の画像を作成
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        $groupImage = UploadedFile::fake()->image('test.png')->size(2049);

        // Act（実行） & Assert（検証）
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            // 各プロパティの値が各カラムの更新前データでセットされているか確認
            ->assertSet('group_id', $group->id)
            ->assertSet('group_data.name', $group->name)
            ->assertSet('group_data.introduction', $group->introduction)
            ->assertSet('group_image_preview', null)

            // group_imageのバリデーション
            ->set('group_image_preview', $notImage)
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_image_preview' => 'image'])

            ->set('group_image_preview', $groupImage)
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_image_preview' => 'max'])

            // group_nameのバリデーション
            ->set('group_data.name', '')
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_data.name' => 'required'])

            ->set('group_data.name', str_repeat('a', 51))
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_data.name' => 'max'])

            // introductionのバリデーション
            ->set('group_data.introduction', '')
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_data.introduction' => 'required'])

            ->set('group_data.introduction', str_repeat('a', 201))
            ->call('updateGroupInfo')
            ->assertHasErrors(['group_data.introduction' => 'max']);

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseHas('groups', [
            'name' => '更新前のグループ名',
            'introduction' => '更新前のグループ紹介文',
        ]);
    }

    public function test_validation_成功_send_invite_to_group_mail()
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

        Mail::fake();

        // 招待されるユーザー（基本テスト用）
        $target_user = User::factory()->create([
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        // Act（実行） & Assert（検証）
        // emailのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', $target_user->email)
            ->call('sendInviteToGroupMail')
            ->assertHasNoErrors(['email' => 'required']);

        $special_user = User::factory()->create([
            'email' => 'valid.email+format@example.co.jp',
            'suspension_state' => 0,
        ]);
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', $special_user->email)
            ->call('sendInviteToGroupMail')
            ->assertHasNoErrors(['email' => 'email']);

        $exists_user = User::factory()->create([
            'email' => 'exists-test@example.com',
            'suspension_state' => 0,
        ]);
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', $exists_user->email)
            ->call('sendInviteToGroupMail')
            ->assertHasNoErrors(['email' => 'exists:users,email']);
    }

    public function test_send_invite_to_group_mail_メール送信確認()
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
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        Mail::fake();

        // Act（実行）  & Assert（検証）
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', $target_user->email)
            ->call('sendInviteToGroupMail')
            ->assertRedirect('group/group_edit/'.$group->id);

        // 送信元 ・ 送信先のメールアドレスは app/Mail/InviteMail.php に記載
        Mail::assertSent(InviteMail::class, function ($mail) {
            return $mail->hasTo('target@example.com') &&
                $mail->hasFrom('test@example.com') &&
                $mail->hasSubject('Basta グループ招待');
        });
    }

    public function test_validation_失敗_send_invite_to_group_mail()
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
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        Mail::fake();

        // Act（実行） & Assert（検証）
        // emailのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', '')
            ->call('sendInviteToGroupMail')
            ->assertHasErrors(['email' => 'required']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', 'not_email')
            ->call('sendInviteToGroupMail')
            ->assertHasErrors(['email' => 'email']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', 'not_exists@example.com')
            ->call('sendInviteToGroupMail')
            ->assertHasErrors(['email' => 'exists:users,email']);
    }
}
