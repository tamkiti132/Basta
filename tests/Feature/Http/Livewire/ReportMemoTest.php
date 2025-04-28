<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\ReportMemo;
use App\Models\Group;
use App\Models\Memo;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ReportMemoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_createReport()
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
            'title' => "テストタイトルです"
        ]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 1)
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertDispatchedBrowserEvent('flash-message');

        $this->assertDatabaseHas('reports', [
            'contribute_user_id' => $user->id,
            'type' => 2,
            'reason' => 1,
            'detail' => "これはレポートのテスト詳細文です",
        ]);

        $report = Report::where('contribute_user_id', $user->id)
            ->where('type', 2)
            ->where('reason', 1)
            ->where('detail', "これはレポートのテスト詳細文です")
            ->first();

        $this->assertDatabaseHas('memo_type_report_links', [
            'report_id' => $report->id,
            'memo_id' => $memo->id,
        ]);
    }

    public function test_validation_成功_createReport()
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
            'title' => "テストタイトルです"
        ]);

        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // reasonのバリデーション
        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 1)
            ->call('createReport')
            ->assertHasNoErrors(['reason' => 'required'])
            ->assertHasNoErrors(['reason' => 'integer'])
            ->assertHasNoErrors(['reason' => 'between']);

        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 4)
            ->call('createReport')
            ->assertHasNoErrors(['reason' => 'between']);

        // detailのバリデーション
        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasNoErrors(['detail' => 'required'])
            ->assertHasNoErrors(['detail' => 'string']);
    }

    public function test_validation_失敗_createReport()
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
            'title' => "テストタイトルです"
        ]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        // reasonのバリデーション
        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', "")
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'required']);

        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', ['aaaa'])
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'integer']);

        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 0)
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'between']);

        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 5)
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'between']);

        // detailのバリデーション
        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 1)
            ->set('detail', "")
            ->call('createReport')
            ->assertHasErrors(['detail' => 'required']);

        Livewire::test(ReportMemo::class, ['memo_id' => $memo->id])
            ->set('reason', 1)
            ->set('detail', 123)
            ->call('createReport')
            ->assertHasErrors(['detail' => 'string']);
    }
}
