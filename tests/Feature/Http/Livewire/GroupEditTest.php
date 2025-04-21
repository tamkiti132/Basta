<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupEdit;
use App\Mail\InviteMail;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

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

    public function test_updateGroupInfo()
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

        // データベースにデータが保存されていることを確認
        $this->assertDatabaseHas('groups', [
            'name' => '更新後のグループ名',
            'introduction' => '更新後のグループ紹介文',
            'group_photo_path' => basename($storedImage),
        ]);


        // ここから、画像を削除するテスト
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('group_image_preview', null)
            ->set('group_image_delete_flag', true)
            ->call('updateGroupInfo');


        // ストレージにファイルが削除されていることを確認
        Storage::disk('public')->assertMissing($storedImage);

        // データベースにデータが削除されていることを確認
        $this->assertDatabaseMissing('groups', [
            'group_photo_path' => basename($storedImage),
        ]);
    }



    public function test_validation_失敗_updateGroupInfo()
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
        // テキストファイル
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        // 2048KB以上の画像
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

            // 2048KB以上の画像
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
            ->assertHasErrors(['group_data.introduction' => 'max'])
        ;


        // Assert（検証）
        $this->assertDatabaseHas('groups', [
            'name' => '更新前のグループ名',
            'introduction' => '更新前のグループ紹介文',
        ]);
    }

    public function test_sendInviteToGroupMail_check_send_mail()
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
            ->assertRedirect('group/group_edit/' . $group->id);

        // 送信元 ・ 送信先のメールアドレスは app/Mail/InviteMail.php に記載
        Mail::assertSent(InviteMail::class, function ($mail) {
            return $mail->hasTo('target@example.com') &&
                $mail->hasFrom('test@example.com') &&
                $mail->hasSubject('Basta グループ招待');
        });
    }

    public function test_validation_失敗_sendInviteToGroupMail()
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
        // emailのバリデーション
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', '')
            ->call('sendInviteToGroupMail')
            ->assertHasErrors(['email' => 'required']);

        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->set('email', 123)
            ->call('sendInviteToGroupMail')
            ->assertHasErrors(['email' => 'string']);

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
