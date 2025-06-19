<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelSelector;
use App\Models\Group;
use App\Models\Label;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LabelSelectorTest extends TestCase
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
     * ラベル保存機能の基本テスト
     * メモに単一のラベルを関連付ける操作が正しく動作することを確認する
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

        // メモを用意
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
        Livewire::test(LabelSelector::class, ['memoId' => $memo->id])
            ->set('group_id', $group->id)
            ->set('checked', [$label->id => true])
            ->call('saveLabels');

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
     * 複数ラベル保存機能のテスト
     * メモに複数のラベルを関連付ける操作が正しく動作することを確認する
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

        // メモを用意
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // 複数のラベルを用意
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
        Livewire::test(LabelSelector::class, ['memoId' => $memo->id])
            ->set('group_id', $group->id)
            ->set('checked', [
                $label1->id => true,
                $label2->id => true,
                $label3->id => false, // 選択しないラベル
            ])
            ->call('saveLabels');

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
     * ラベル関連付けの更新機能テスト
     * 既存のラベル関連付けが正しく更新されることを確認する
     *
     * @return void
     */
    public function test_save_labels_update_labels()
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

        // メモを用意
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // ラベルを作成
        $label1 = Label::factory()->create([
            'name' => 'テストラベル1',
            'group_id' => $group->id,
        ]);

        $label2 = Label::factory()->create([
            'name' => 'テストラベル2',
            'group_id' => $group->id,
        ]);

        // 最初にラベル1をメモに紐づける
        $memo->labels()->attach($label1->id);

        // 最初の状態を確認
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label1->id,
        ]);

        // Act（実行） - ラベル1の紐づけを解除し、ラベル2を紐づける
        Livewire::test(LabelSelector::class, ['memoId' => $memo->id])
            ->set('group_id', $group->id)
            ->set('checked', [
                $label1->id => false,
                $label2->id => true,
            ])
            ->call('saveLabels');

        // Assert（検証）
        // ラベル1の紐づけが解除されたことを確認
        $this->assertDatabaseMissing('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label1->id,
        ]);

        // ラベル2が紐づけられたことを確認
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label2->id,
        ]);
    }

    /**
     * ラベル関連付けの全削除テスト
     * メモからすべてのラベル関連付けが削除されることを確認する
     *
     * @return void
     */
    public function test_save_labels_remove_all_labels()
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

        // メモを用意
        $memo = Memo::factory()->create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        // ラベルを作成
        $label1 = Label::factory()->create([
            'name' => 'テストラベル1',
            'group_id' => $group->id,
        ]);

        $label2 = Label::factory()->create([
            'name' => 'テストラベル2',
            'group_id' => $group->id,
        ]);

        // 最初にラベルをメモに紐づける
        $memo->labels()->attach([$label1->id, $label2->id]);

        // 最初の状態を確認
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label1->id,
        ]);
        $this->assertDatabaseHas('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label2->id,
        ]);

        // Act（実行） - すべてのラベルの紐づけを解除
        Livewire::test(LabelSelector::class, ['memoId' => $memo->id])
            ->set('group_id', $group->id)
            ->set('checked', [
                $label1->id => false,
                $label2->id => false,
            ])
            ->call('saveLabels');

        // Assert（検証）
        // すべてのラベルの紐づけが解除されたことを確認
        $this->assertDatabaseMissing('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label1->id,
        ]);
        $this->assertDatabaseMissing('label_memo', [
            'memo_id' => $memo->id,
            'label_id' => $label2->id,
        ]);
    }
}
