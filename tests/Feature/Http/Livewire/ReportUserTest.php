<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\ReportUser;
use App\Models\Group;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ReportUserTest extends TestCase
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
        // レポートを投稿する側のユーザー
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // レポートを投稿する対象のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 100]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        Livewire::test(ReportUser::class, ['user_id' => $user->id])
            ->set('reason', 1)
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport');

        $this->assertDatabaseHas('reports', [
            'contribute_user_id' => $manager->id,
            'type' => 1,
            'reason' => 1,
            'detail' => "これはレポートのテスト詳細文です",
        ]);

        $report = Report::where('contribute_user_id', $manager->id)
            ->where('type', 1)
            ->where('reason', 1)
            ->where('detail', "これはレポートのテスト詳細文です")
            ->first();

        $this->assertDatabaseHas('user_type_report_links', [
            'report_id' => $report->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_validation_失敗_createReport()
    {
        // Arrange（準備）
        // レポートを投稿する側のユーザー
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($manager);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // レポートを投稿する対象のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 100]);

        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        // reasonのバリデーション
        Livewire::test(ReportUser::class, ['user_id' => $user->id])
            ->set('reason', "")
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'required']);

        Livewire::test(ReportUser::class, ['user_id' => $user->id])
            ->set('reason', ['aaaa'])
            ->set('detail', "これはレポートのテスト詳細文です")
            ->call('createReport')
            ->assertHasErrors(['reason' => 'integer']);

        // detailのバリデーション
        Livewire::test(ReportUser::class, ['user_id' => $user->id])
            ->set('reason', 1)
            ->set('detail', "")
            ->call('createReport')
            ->assertHasErrors(['detail' => 'required']);

        Livewire::test(ReportUser::class, ['user_id' => $user->id])
            ->set('reason', 1)
            ->set('detail', 123)
            ->call('createReport')
            ->assertHasErrors(['detail' => 'string']);
    }
}
