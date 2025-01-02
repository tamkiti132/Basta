<?php

namespace Tests\Feature\Http\Livewire;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Group;
use App\Http\Livewire\QuitGroupForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class QuitGroupFormTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_quitGroup_メンバーorサブ管理者()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // メンバーを追加
        $password = 'member-password';
        $member = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group->userRoles()->attach($member, ['role' => 100]);

        // メンバーとしてログイン
        $this->actingAs($member);

        // Act（実行） & Assert（検証）
        // グループトップページにアクセスして、QuitGroupFormコンポーネントが存在するか確認
        $this->get('/group/top/' . $group->id)
            ->assertSeeLivewire(QuitGroupForm::class);

        // フォームを送信して、メンバーが退会できるか確認
        Livewire::test(QuitGroupForm::class)
            ->set('password', $password)
            ->assertSet('password', $password)
            ->call('quitGroup')
            ->assertRedirect('/');

        // メンバーが退会したか確認（rolesテーブルから削除されているか）
        $this->assertDatabaseMissing('roles', [
            'user_id' => $member->id,
            'group_id' => $group->id,
        ]);
    }

    public function test_validation_失敗_quitGroup()
    {
        // Arrange（準備）
        // 管理者を追加
        $manager = User::factory()->create([
            'suspension_state' => 0,
        ]);

        $group = Group::factory()->create([
            'suspension_state' => 0,
        ]);
        $group->userRoles()->attach($manager, ['role' => 10]);

        // メンバーを追加
        $password = 'member-password';
        $member = User::factory()->create([
            'suspension_state' => 0,
            'password' => Hash::make($password),
        ]);

        $group->userRoles()->attach($member, ['role' => 100]);

        // メンバーとしてログイン
        $this->actingAs($member);

        // Act（実行） & Assert（検証）
        // グループトップページにアクセスして、QuitGroupFormコンポーネントが存在するか確認
        $this->get('/group/top/' . $group->id)
            ->assertSeeLivewire(QuitGroupForm::class);

        // フォームを送信して、メンバーが退会できるか確認
        // passwordのバリデーション
        Livewire::test(QuitGroupForm::class)
            ->set('password', '')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'required']);

        Livewire::test(QuitGroupForm::class)
            ->set('password', 'aaaa')
            ->call('quitGroup')
            ->assertHasErrors(['password' => 'current_password']);

        // メンバーが退会したか確認（rolesテーブルから削除されていないか）
        $this->assertDatabaseHas('roles', [
            'user_id' => $member->id,
            'group_id' => $group->id,
        ]);
    }
}
