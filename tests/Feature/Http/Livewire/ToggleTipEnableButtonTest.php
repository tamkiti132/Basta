<?php

namespace Tests\Feature\Http\Livewire;

use App\Http\Livewire\ToggleTipEnableButton;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ToggleTipEnableButtonTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    // テスト用のロケールを設定
    app()->setLocale('testing');
    // テスト用のストレージを設定
    Storage::fake('public');
  }

  public function test_updatedIsTipEnabled()
  {
    // Arrange（準備）
    $user = User::factory()->create([
      'suspension_state' => 0,
    ]);
    $this->actingAs($user);

    $group = Group::factory()->create([
      'name' => 'テストグループ',
      'suspension_state' => 0,
      'isTipEnabled' => 0,
    ]);
    $group->userRoles()->attach($user, ['role' => 10]);

    // Act（実行） & Assert（検証）
    // データベース上のisTipEnabledが0であることを確認
    // （投げ銭機能が無効な状態）
    $this->assertDatabaseHas('groups', [
      'name' => $group->name,
      'isTipEnabled' => 0,
    ]);

    // ここから、投げ銭機能を有効にするテスト

    // updatedIsTipEnabledメソッドは、
    // isTipEnabledプロパティの値が変更されると自動的に呼ばれるメソッドであるため、
    // 直接isTipEnabledプロパティを更新することで、updatedIsTipEnabledメソッドが呼ばれる。    
    Livewire::test(ToggleTipEnableButton::class, ['groupId' => $group->id])
      ->set('isTipEnabled', true);

    // データベース上のisTipEnabledが1に更新されていることを確認
    // （投げ銭機能が有効な状態）
    $this->assertDatabaseHas('groups', [
      'name' => $group->name,
      'isTipEnabled' => 1,
    ]);

    // ここから、投げ銭機能を無効にするテスト
    Livewire::test(ToggleTipEnableButton::class, ['groupId' => $group->id])
      ->set('isTipEnabled', false);

    // データベース上のisTipEnabledが0に更新されていることを確認
    // （投げ銭機能が無効な状態）
    $this->assertDatabaseHas('groups', [
      'name' => $group->name,
      'isTipEnabled' => 0,
    ]);
  }
}
