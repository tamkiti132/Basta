<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\ToggleJoinFreeEnableButton;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ToggleJoinFreeEnableButtonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // テスト用のロケールを設定
        app()->setLocale('testing');
        // テスト用のストレージを設定
        Storage::fake('public');
    }

    public function test_updatedIsJoinFreeEnabled()
    {
        // Arrange（準備）
        $user = User::factory()->create([
            'suspension_state' => 0,
        ]);
        $this->actingAs($user);

        $group = Group::factory()->create([
            'name' => 'テストグループ',
            'suspension_state' => 0,
            'isJoinFreeEnabled' => 0,
        ]);
        $group->user()->attach($user);
        $group->userRoles()->attach($user, ['role' => 10]);

        // Act（実行） & Assert（検証）
        // データベース上のis_join_free_enabledが0であることを確認
        // （自由参加が制限されている状態）
        $this->assertDatabaseHas('groups', [
            'name' => $group->name,
            'isJoinFreeEnabled' => 0,
        ]);


        // ここから、自由参加を許可するテスト

        // updatedIsJoinFreeEnabledメソッドは、
        // isJoinFreeEnabledプロパティの値が変更されると自動的に呼ばれるメソッドであるため、
        // 直接isJoinFreeEnabledプロパティを更新することで、updatedIsJoinFreeEnabledメソッドが呼ばれる。    
        Livewire::test(ToggleJoinFreeEnableButton::class, ['groupId' => $group->id])
            ->set('isJoinFreeEnabled', true);

        // データベース上のis_join_free_enabledが1に更新されていることを確認
        // （自由参加が許可されている状態）
        $this->assertDatabaseHas('groups', [
            'name' => $group->name,
            'isJoinFreeEnabled' => 1,
        ]);


        // ここから、自由参加を制限するテスト
        Livewire::test(ToggleJoinFreeEnableButton::class, ['groupId' => $group->id])
            ->set('isJoinFreeEnabled', false);

        // データベース上のis_join_free_enabledが0に更新されていることを確認
        // （自由参加が制限されている状態）
        $this->assertDatabaseHas('groups', [
            'name' => $group->name,
            'isJoinFreeEnabled' => 0,
        ]);
    }
}
