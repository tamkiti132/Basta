<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\LabelAdder;
use App\Models\Group;
use App\Models\Label;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
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

    public function test_saveLabels()
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
}
