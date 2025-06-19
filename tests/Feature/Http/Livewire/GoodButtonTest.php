<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\GoodButton;
use App\Models\Group;
use App\Models\Memo;
use App\Models\User;
use App\Models\Web_type_feature;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class GoodButtonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_toggle_good()
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

        $memo = Memo::factory()->create([
            'user_id' => $user->id,
            'group_id' => $group->id,
            'title' => 'テストタイトル',
        ]);

        $web_type_feature = Web_type_feature::factory()->create([
            'memo_id' => $memo->id,
            'url' => 'https://example.com',
        ]);

        $this->assertDatabaseHas('memos', [
            'title' => 'テストタイトル',
        ]);

        // Act（実行）  && Assert（検証）
        // 『goods』テーブルにデータがないことを確認
        $this->assertDatabaseMissing('goods', [
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);

        // いいねを押す
        Livewire::test(GoodButton::class, ['memo' => $memo])
            ->call('toggleGood');

        // 『goods』テーブルにデータがあることを確認
        $this->assertDatabaseHas('goods', [
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);

        // いいねをもう一度押す
        Livewire::test(GoodButton::class, ['memo' => $memo])
            ->call('toggleGood');

        // 『goods』テーブルにデータがないことを確認
        $this->assertDatabaseMissing('goods', [
            'user_id' => $user->id,
            'memo_id' => $memo->id,
        ]);
    }
}
