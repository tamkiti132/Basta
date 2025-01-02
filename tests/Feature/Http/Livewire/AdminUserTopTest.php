<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\AdminUserTop;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserTopTest extends TestCase
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
        // 運営トップユーザー（ユーザーを削除する側）
        $adminTop = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営トップユーザーの権限を設定
        // 運営トップユーザーの場合、グループに所属していないので、group_idはnullとなる
        $adminTop->groupRoles()->attach($adminTop, [
            'role' => 3,
            'group_id' => null,
        ]);
        $this->actingAs($adminTop);

        // 運営ユーザー（削除されるユーザー）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);

        // データベースに運営ユーザーが存在することを確認
        $this->assertDatabaseHas('users', ['id' => $admin->id]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(AdminUserTop::class)
            ->call('deleteUser', $admin->id);

        // Assert（検証）
        // ユーザーと関連データが削除されたことを確認
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_suspendUser()
    {
        // Arrange（準備）
        // 運営トップユーザー（ユーザーを削除する側）
        $adminTop = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営トップユーザーの権限を設定
        // 運営トップユーザーの場合、グループに所属していないので、group_idはnullとなる
        $adminTop->groupRoles()->attach($adminTop, [
            'role' => 3,
            'group_id' => null,
        ]);
        $this->actingAs($adminTop);

        // 運営ユーザー（削除されるユーザー）
        $admin = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);

        // データベースに運営ユーザーが存在することを確認
        $this->assertDatabaseHas('users', ['id' => $admin->id]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(AdminUserTop::class)
            ->call('suspendUser', $admin->id);

        // Assert（検証）
        // ユーザーと関連データが削除されたことを確認
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'suspension_state' => 1
        ]);
    }

    public function test_liftSuspendUser()
    {
        // Arrange（準備）
        // 運営トップユーザー（ユーザーを削除する側）
        $adminTop = User::factory()->create([
            'suspension_state' => 0,
        ]);
        // 運営トップユーザーの権限を設定
        // 運営トップユーザーの場合、グループに所属していないので、group_idはnullとなる
        $adminTop->groupRoles()->attach($adminTop, [
            'role' => 3,
            'group_id' => null,
        ]);
        $this->actingAs($adminTop);

        // 運営ユーザー（削除されるユーザー）
        $admin = User::factory()->create([
            'suspension_state' => 1,
        ]);
        // 運営ユーザーの権限を設定
        // 運営ユーザーの場合、グループに所属していないので、group_idはnullとなる
        $admin->groupRoles()->attach($admin, [
            'role' => 5,
            'group_id' => null,
        ]);

        // データベースに運営ユーザーが存在することを確認
        $this->assertDatabaseHas('users', ['id' => $admin->id]);


        // Act（実行）
        // Livewireコンポーネントをテスト
        Livewire::test(AdminUserTop::class)
            ->call('liftSuspendUser', $admin->id);

        // Assert（検証）
        // ユーザーと関連データが削除されたことを確認
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'suspension_state' => 0
        ]);
    }
}
