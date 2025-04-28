<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\QuitGroupFormOfMemberEditPage;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class QuitGroupFormOfMemberEditPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_quitGroup()
    {
        // Arrange（準備）
        // 管理者（強制退会させる側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 強制退会される側のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);


        // このグループに紐づくユーザーの権限情報が存在していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        Livewire::test(QuitGroupFormOfMemberEditPage::class)
            ->set('password', 'password')
            ->set('user_id', $user->id)
            ->call('quitGroup')
            ->assertEmitted('quitGroupMember');  // イベントが発火されたことを確認

        // このグループに紐づくユーザーの権限情報が存在していないことを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_validation_成功_quitGroup()
    {
        // Arrange（準備）
        // 管理者（強制退会させる側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 強制退会される側のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);

        // このグループに紐づくユーザーの権限情報が存在していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);

        // Act（実行） & Assert（検証）
        // passwordのバリデーション
        Livewire::test(QuitGroupFormOfMemberEditPage::class)
            ->set('password', 'password')
            ->set('user_id', $user->id)
            ->call('quitGroup')
            ->assertHasNoErrors(['password' => 'required'])
            ->assertHasNoErrors(['password' => 'current_password']);
    }

    public function test_validation_失敗_quitGroup()
    {
        // Arrange（準備）
        // 管理者（強制退会させる側のユーザー）
        $manager = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($manager);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // 強制退会される側のユーザー
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($user, ['role' => 100]);


        // このグループに紐づくユーザーの権限情報が存在していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);

        // セッションにグループIDをセット
        session()->put('group_id', $group->id);


        // Act（実行） & Assert（検証）
        // password
        Livewire::test(QuitGroupFormOfMemberEditPage::class)
            ->set('password', '')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(QuitGroupFormOfMemberEditPage::class)
            ->set('password', 'aaaa')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'current_password']);

        // このグループに紐づくユーザーの権限情報が存在していることを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);
    }
}
