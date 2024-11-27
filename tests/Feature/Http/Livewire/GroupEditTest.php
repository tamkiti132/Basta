<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GroupEdit;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
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


        // Act（実行）  & Assert（検証）
        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            // public $group_data;　の値をテスト
            ->assertSet('group_data.name', $group->name)
            ->assertSet('group_data.introduction', $group->introduction)

            ->set('group_data.name', '更新後のグループ名')
            ->set('group_data.introduction', '更新後のグループ紹介文')
            ->call('updateGroupInfo')

            ->assertSet('group_data.name', '更新後のグループ名')
            ->assertSet('group_data.introduction', '更新後のグループ紹介文')
        ;


        // Assert（検証）
        $this->assertDatabaseHas('groups', [
            'name' => '更新後のグループ名',
            'introduction' => '更新後のグループ紹介文',
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


        // まず、管理者としてグループ編集画面にアクセスできるかテスト
        $this->get('group/group_edit/' . $group->id)
            ->assertSeeLivewire(GroupEdit::class);


        Livewire::test(GroupEdit::class, ['group_id' => $group->id])
            ->assertSet('group_id', $group->id)
            // public $group_data;　の値をテスト
            ->assertSet('group_data.name', $group->name)
            ->assertSet('group_data.introduction', $group->introduction)

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
