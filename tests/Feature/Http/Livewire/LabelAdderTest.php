<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelAdder;
use App\Models\Group;
use App\Models\Label;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LabelAdderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    /**
     * 単一のラベルをメモに保存するテスト
     *
     * @return void
     */
    public function test_save_labels()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        // メモを用意（新規メモ作成を想定）
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // ラベルを用意
        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelAdder::class)
            ->set('group_id', $group->id)
            ->set('checked', [$label->id => true])
            ->call('saveLabels', $memo->id);

        // ラベルがデータベースに保存されていることを確認
        $this->assertDatabaseHas('labels', [
            'name' => $label->name,
            'group_id' => $label->group_id,
        ]);

        // ラベルがメモに紐づいていることを確認
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label->id,
        ]);
    }

    /**
     * 複数のラベルをメモに保存するテスト
     * 選択したラベルのみがメモに紐づけられ、選択していないラベルは紐づけられないことを確認する
     *
     * @return void
     */
    public function test_save_labels_with_multiple_labels()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        // メモを用意（新規メモ作成を想定）
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // 複数ラベルを用意
        $label1 = Label::factory()->create([
            'name' => 'テストラベル1',
            'group_id' => $group->id,
        ]);

        $label2 = Label::factory()->create([
            'name' => 'テストラベル2',
            'group_id' => $group->id,
        ]);

        $label3 = Label::factory()->create([
            'name' => 'テストラベル3',
            'group_id' => $group->id,
        ]);

        // Act（実行） & Assert（検証）
        Livewire::test(LabelAdder::class)
            ->set('group_id', $group->id)
            ->set('checked', [
                $label1->id => true,
                $label2->id => true,
                $label3->id => false, // 選択しないラベル
            ])
            ->call('saveLabels', $memo->id);

        // 選択したラベルがメモに紐づいていることを確認
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label1->id,
        ]);

        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label2->id,
        ]);

        // 選択しなかったラベルはメモに紐づいていないことを確認
        $this->assertDatabaseMissing('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label3->id,
        ]);
    }

    /**
     * ラベルを選択せずにメモを保存するテスト
     * ラベルが選択されていない場合、メモにラベルが紐づけられないことを確認する
     *
     * @return void
     */
    public function test_save_labels_with_no_labels_selected()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 10]);

        // メモを用意（新規メモ作成を想定）
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // ラベルを用意（選択しない）
        $label = Label::factory()->create([
            'name' => 'テストラベル',
            'group_id' => $group->id,
        ]);

        // Act（実行） - 空の選択で実行
        Livewire::test(LabelAdder::class)
            ->set('group_id', $group->id)
            ->set('checked', [])
            ->call('saveLabels', $memo->id);

        // Assert（検証）
        // ラベルとメモが紐づいていないことを確認
        $this->assertDatabaseMissing('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label->id,
        ]);
    }
}
