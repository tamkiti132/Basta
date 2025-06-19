<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class InviteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正常系：有効なパラメータでグループに参加できることを検証
     */
    public function test_join_group_with_valid_parameters()
    {
        // Arrange（準備）
        // グループを作成
        $group = Group::factory()->create([
            'name' => 'テストグループ名',
            'introduction' => 'テストグループ紹介文',
            'suspension_state' => 0,
        ]);

        // 招待されるユーザー
        $target_user = User::factory()->create([
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        // 署名付きURLを作成（有効期限24時間）
        $url = URL::temporarySignedRoute(
            'invite.joinGroup',
            now()->addHours(24),
            [
                'group_id' => $group->id,
                'target_user_id' => $target_user->id,
            ]
        );

        // ユーザーとしてログイン
        $this->actingAs($target_user);

        // Act（実行）
        $response = $this->get($url);

        // Assert（検証）
        $response->assertRedirect(route('group.index', ['group_id' => $group->id]));
        $response->assertSessionHas('success', '招待されたグループに参加しました。');

        // データベースにユーザーとグループの関連が作成されたことを確認
        $this->assertDatabaseHas('roles', [
            'user_id' => $target_user->id,
            'group_id' => $group->id,
            'role' => 100,
        ]);
    }

    /**
     * 異常系：存在しないグループIDの場合のエラー処理を検証
     */
    public function test_join_group_with_nonexistent_group()
    {
        // Arrange（準備）
        // 存在しないグループID
        $nonexistent_group_id = 999;

        // 招待されるユーザー
        $target_user = User::factory()->create([
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        // 署名付きURLを作成（有効期限24時間）
        $url = URL::temporarySignedRoute(
            'invite.joinGroup',
            now()->addHours(24),
            [
                'group_id' => $nonexistent_group_id,
                'target_user_id' => $target_user->id,
            ]
        );

        // ユーザーとしてログイン
        $this->actingAs($target_user);

        // Act（実行）
        $response = $this->get($url);

        // Assert（検証）
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('error', '招待されたグループが見つかりません。');
    }

    /**
     * 異常系：招待されていないユーザーでログインした場合のエラー処理を検証
     */
    public function test_join_group_with_different_user()
    {
        // Arrange（準備）
        // グループを作成
        $group = Group::factory()->create([
            'name' => 'テストグループ名',
            'introduction' => 'テストグループ紹介文',
            'suspension_state' => 0,
        ]);

        // 招待されるユーザー
        $target_user = User::factory()->create([
            'email' => 'target@example.com',
            'suspension_state' => 0,
        ]);

        // 別のユーザー（招待されていない）でログイン
        $another_user = User::factory()->create([
            'email' => 'another@example.com',
            'suspension_state' => 0,
        ]);

        // 署名付きURLを作成（有効期限24時間）
        $url = URL::temporarySignedRoute(
            'invite.joinGroup',
            now()->addHours(24),
            [
                'group_id' => $group->id,
                'target_user_id' => $target_user->id,
            ]
        );

        // 別のユーザーとしてログイン
        $this->actingAs($another_user);

        // Act（実行）
        $response = $this->get($url);

        // Assert（検証）
        $response->assertRedirect(route('index'));
        $response->assertSessionHas('error', "グループ招待機能は、\n招待されたユーザーでログインした場合のみ実行できます。");

        // データベースにユーザーとグループの関連が作成されていないことを確認
        $this->assertDatabaseMissing('roles', [
            'user_id' => $target_user->id,
            'group_id' => $group->id,
        ]);
    }
}
