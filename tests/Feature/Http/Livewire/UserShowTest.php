<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\UserShow;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');

        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_deleteUser()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを削除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー（削除されるユーザー）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(UserShow::class, ['user_id' => $user->id])
            ->set('deleteTargetUserId', $user->id)
            ->call('deleteUser');

        // Assert（検証）
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }


    public function test_suspend()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを削除する側）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー（削除されるユーザー）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(UserShow::class, ['user_id' => $user->id])
            ->call('suspend');

        // Assert（検証）
        $this->assertDatabaseHas('users', ['suspension_state' => 1]);
    }

    public function test_liftSuspend()
    {
        // Arrange（準備）
        // 運営ユーザー（ユーザーを削除する側）
        $admin = User::factory()->create([
            'suspension_state' => 1,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);
        $this->actingAs($admin);

        // 一般ユーザー（削除されるユーザー）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(UserShow::class, ['user_id' => $user->id])
            ->call('liftSuspend');

        // Assert（検証）
        $this->assertDatabaseHas('users', ['suspension_state' => 0]);
    }
}
