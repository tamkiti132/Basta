<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\GroupCreate;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class GroupCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_storeGroup_画像あり()
    {
        // Arrange（準備）
        $user = User::factory()->create();
        $this->actingAs($user);

        // テスト用の画像を作成
        $group_image = UploadedFile::fake()->image('test.png')->size(2048);

        // Act（実行）
        $component = Livewire::test(GroupCreate::class)
            ->set('group_image', $group_image)
            ->set('group_name', 'Test Group')
            ->set('introduction', 'Test Introduction')
            ->call('storeGroup');

        $storedImage = $component->get('storedImage');


        // Assert（検証）
        // ストレージにファイルが保存されていることを確認
        Storage::disk('public')->assertExists($storedImage);

        // データベース検証
        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group',
            'introduction' => 'Test Introduction',
            'group_photo_path' => basename($storedImage),
        ]);

        // ユーザーが管理者として関連付けられていることを確認
        $group = Group::where('name', 'Test Group')->first();
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_storeGroup_画像なし()
    {
        // Arrange（準備）
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act（実行）
        Livewire::test(GroupCreate::class)
            ->set('group_name', 'Test Group Without Image')
            ->set('introduction', 'Test Introduction Without Image')
            ->call('storeGroup');

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseHas('groups', [
            'name' => 'Test Group Without Image',
            'introduction' => 'Test Introduction Without Image',
        ]);

        // group_photo_pathがnullであることを確認
        $group = Group::where('name', 'Test Group Without Image')->first();
        $this->assertNull($group->group_photo_path);

        // ユーザーが管理者として関連付けられていることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 10,
        ]);
    }

    public function test_validation_成功_storeGroup()
    {
        // Arrange（準備）
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // 基本ケース - 全フィールド入力
        Livewire::test(GroupCreate::class)
            ->set('group_name', 'テストグループ')
            ->set('introduction', 'テスト紹介文')
            ->set('group_image', UploadedFile::fake()->image('test.png')->size(1024))
            ->call('storeGroup')
            ->assertHasNoErrors();

        // group_imageのバリデーション
        $maxImage = UploadedFile::fake()->image('max.png')->size(2048);
        Livewire::test(GroupCreate::class)
            ->set('group_name', 'テストグループ')
            ->set('introduction', 'テスト紹介文')
            ->set('group_image', $maxImage)
            ->call('storeGroup')
            ->assertHasNoErrors(['group_image' => 'max']);

        Livewire::test(GroupCreate::class)
            ->set('group_name', 'テストグループ')
            ->set('introduction', 'テスト紹介文')
            ->set('group_image', null)
            ->call('storeGroup')
            ->assertHasNoErrors(['group_image' => 'nullable']);

        // group_nameのバリデーション
        Livewire::test(GroupCreate::class)
            ->set('group_name', str_repeat('あ', 50))
            ->set('introduction', 'テスト紹介文')
            ->call('storeGroup')
            ->assertHasNoErrors(['group_name' => 'max']);

        // introductionのバリデーション
        Livewire::test(GroupCreate::class)
            ->set('group_name', 'テストグループ')
            ->set('introduction', str_repeat('あ', 200))
            ->call('storeGroup')
            ->assertHasNoErrors(['introduction' => 'max']);
    }

    public function test_validation_失敗_storeGroup()
    {
        // Arrange（準備）
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act（実行） & Assert（検証）
        // group_imageのバリデーション        
        $notImage = UploadedFile::fake()->create('notImage.txt', 100);
        Livewire::test(GroupCreate::class)
            ->set('group_image', $notImage)
            ->call('storeGroup')
            ->assertHasErrors(['group_image' => 'image']);

        $group_image = UploadedFile::fake()->image('test.png')->size(2049);
        Livewire::test(GroupCreate::class)
            ->set('group_image', $group_image)
            ->call('storeGroup')
            ->assertHasErrors(['group_image' => 'max']);

        // group_nameのバリデーション
        Livewire::test(GroupCreate::class)
            ->set('group_name', '')
            ->call('storeGroup')
            ->assertHasErrors(['group_name' => 'required']);

        Livewire::test(GroupCreate::class)
            ->set('group_name', str_repeat('a', 51))
            ->call('storeGroup')
            ->assertHasErrors(['group_name' => 'max']);

        // introductionのバリデーション
        Livewire::test(GroupCreate::class)
            ->set('introduction', '')
            ->call('storeGroup')
            ->assertHasErrors(['introduction' => 'required']);

        Livewire::test(GroupCreate::class)
            ->set('introduction', str_repeat('a', 201))
            ->call('storeGroup')
            ->assertHasErrors(['introduction' => 'max']);

        // データベース検証
        $this->assertDatabaseEmpty('groups');
    }
}
