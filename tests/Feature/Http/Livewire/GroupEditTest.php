<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupEdit;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
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

        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);


        // まず、管理者としてグループ編集画面にアクセスできるかテスト
        $this->get('group/group_edit/' . $group->id)
            ->assertSeeLivewire(GroupEdit::class);


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

        // Assert（検証）
        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($storedImage);

        // データベースにデータが保存されていることを確認
        $this->assertDatabaseHas('groups', [
            'name' => '更新後のグループ名',
            'introduction' => '更新後のグループ紹介文',
            'group_photo_path' => basename($storedImage),
        ]);
    }


    public function test_バリデーション_失敗_updateGroupInfo()
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

        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        // バリデーションに失敗するテスト用の画像を作成
        // テキストファイル
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        // 2048KB以上の画像
        $groupImage = UploadedFile::fake()->image('test.png')->size(2049);


        // まず、管理者としてグループ編集画面にアクセスできるかテスト
        $this->get('group/group_edit/' . $group->id)
            ->assertSeeLivewire(GroupEdit::class);

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
}
