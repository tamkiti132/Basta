<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelEditorMypage;
use App\Models\Group;
use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LabelEditorMypageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_createLabel()
    {
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);


        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル')
            ->call('createLabel');

        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);
    }

    public function test_バリデーション_失敗_createLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);


        // 重複テスト用のデータを作成
        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->set('labelName', '')
            ->call('createLabel')
            ->assertHasErrors(['labelName' => 'required']);

        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->set('labelName', str_repeat('a', 31))
            ->call('createLabel')
            ->assertHasErrors(['labelName' => 'max']);

        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル')
            ->call('createLabel')
            ->assertHasErrors(['labelName' => 'unique']);
    }

    public function test_updateLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // データベースに更新前のデータが存在することを確認
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル2')
            ->call('updateLabel', $label->id, 'テストラベル2');

        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル2',
        ]);
    }

    public function test_バリデーション_失敗_updateLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 重複デスト用のデータを作成
        // （ラベルの重複テストをするためには、２つのラベルのデータが必要）
        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // 重複テストをする際は、こっちのデータの名前を変えて重複させる
        Label::factory()->create([
            'name' => 'テストラベル2',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, '')
            ->assertHasErrors(['newName' => 'required']);

        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, str_repeat('a', 31))
            ->assertHasErrors(['newName' => 'max']);

        Livewire::test(LabelEditorMypage::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, 'テストラベル2')
            ->assertHasErrors(['newName' => 'unique']);
    }

    public function test_deleteLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // データベースに更新前のデータが存在することを確認
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelEditorMypage::class)
            ->call('deleteLabel', $label->id);

        $this->assertDatabaseMissing('labels', [
            'name' => 'テストラベル',
        ]);
    }
}
