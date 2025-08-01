<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

// Laravel導入時からあるデフォルトのテストコード
class PasswordConfirmationTest extends TestCase
{
    // 現在、チーム機能を使用していないため、コメントアウトしておきます。
    // public function test_confirm_password_screen_can_be_rendered(): void
    // {
    //     $user = User::factory()->withPersonalTeam()->create();

    //     $response = $this->actingAs($user)->get('/user/confirm-password');

    //     $response->assertStatus(200);
    // }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/user/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
