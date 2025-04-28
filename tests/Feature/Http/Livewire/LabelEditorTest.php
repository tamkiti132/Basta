<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelEditor;
use App\Models\Group;
use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LabelEditorTest extends TestCase
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
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // Act（実行）
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル')
            ->call('createLabel');

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);
    }

    public function test_validation_成功_createLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // Act（実行） & Assert（検証）
        // 基本ケース
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル')
            ->call('createLabel')
            ->assertHasNoErrors();

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', '新しいラベル123')
            ->call('createLabel')
            ->assertHasNoErrors(['labelName' => 'required'])
            ->assertHasNoErrors(['labelName' => 'string']);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', str_repeat('あ', 30))
            ->call('createLabel')
            ->assertHasNoErrors(['labelName' => 'max']);

        $another_group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $label = Label::factory()->create([
            'name' => 'ユニークラベル',
            'group_id' => $group->id,
        ]);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $another_group->id)
            ->set('labelName', 'ユニークラベル')
            ->call('createLabel')
            ->assertHasNoErrors(['labelName' => 'unique']);
    }

    public function test_validation_失敗_createLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 重複テスト用のデータを作成
        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', '')
            ->call('createLabel')
            ->assertHasErrors(['labelName' => 'required']);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', str_repeat('a', 31))
            ->call('createLabel')
            ->assertHasErrors(['labelName' => 'max']);

        Livewire::test(LabelEditor::class)
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
        $group->userRoles()->attach($manager, ['role' => 10]);

        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // データベースに更新前のデータが存在することを確認
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);

        // Act（実行）
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->set('labelName', 'テストラベル2')
            ->call('updateLabel', $label->id, 'テストラベル2');

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル2',
        ]);
    }

    public function test_validation_成功_updateLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        // 基本ケース
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, '更新ラベル名')
            ->assertHasNoErrors();

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, str_repeat('あ', 30))
            ->assertHasNoErrors(['newName' => 'max']);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, '更新ラベル123')
            ->assertHasNoErrors(['newName' => 'required'])
            ->assertHasNoErrors(['newName' => 'string']);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, $label->name)
            ->assertHasNoErrors(['newName' => 'unique']);

        // 異なるグループでは同名ラベルに更新可能（unique制約はグループごとに適用）
        $group1 = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group2 = Group::factory()->create([
            'suspension_state' => 0,
        ]);

        // グループ1にラベルを作成
        $shared_label_name = '他グループと同名ラベル';
        $label1 = Label::factory()->create([
            'name' => $shared_label_name,
            'group_id' => $group1->id,
        ]);

        // グループ2のラベル
        $label2 = Label::factory()->create([
            'name' => '更新前ラベル',
            'group_id' => $group2->id,
        ]);

        // グループ2のラベルを、グループ1のラベルと同じ名前に更新できることを確認
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group2->id)
            ->call('updateLabel', $label2->id, $shared_label_name)
            ->assertHasNoErrors(['newName' => 'unique']);

        // データベース検証
        $this->assertDatabaseHas('labels', [
            'name' => $shared_label_name,
            'group_id' => $group1->id,
        ]);
        $this->assertDatabaseHas('labels', [
            'name' => $shared_label_name,
            'group_id' => $group2->id,
        ]);
    }

    public function test_validation_失敗_updateLabel()
    {
        // Arrange（準備）
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 重複テスト用のデータを作成
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
        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, '')
            ->assertHasErrors(['newName' => 'required'])
            ->assertHasErrors(['newName' => 'string']);

        Livewire::test(LabelEditor::class)
            ->set('group_id', $group->id)
            ->call('updateLabel', $label->id, str_repeat('a', 31))
            ->assertHasErrors(['newName' => 'max']);

        Livewire::test(LabelEditor::class)
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
        $group->userRoles()->attach($manager, ['role' => 10]);

        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // データベースに更新前のデータが存在することを確認
        $this->assertDatabaseHas('labels', [
            'name' => 'テストラベル',
        ]);

        // Act（実行）
        Livewire::test(LabelEditor::class)
            ->call('deleteLabel', $label->id);

        // Assert（検証）
        // データベース検証
        $this->assertDatabaseMissing('labels', [
            'name' => 'テストラベル',
        ]);
    }
}
