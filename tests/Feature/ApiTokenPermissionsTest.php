<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

// Laravel導入時からあるデフォルトのテストコード
// // 現在、APIトークンの機能を使用していないため、コメントアウトしておきます。
// class ApiTokenPermissionsTest extends TestCase
// {
//     public function test_api_token_permissions_can_be_updated(): void
//     {
//         if (! Features::hasApiFeatures()) {
//             $this->markTestSkipped('API support is not enabled.');

//             return;
//         }

//         $this->actingAs($user = User::factory()->withPersonalTeam()->create());

//         $token = $user->tokens()->create([
//             'name' => 'Test Token',
//             'token' => Str::random(40),
//             'abilities' => ['create', 'read'],
//         ]);

//         Livewire::test(ApiTokenManager::class)
//             ->set(['managingPermissionsFor' => $token])
//             ->set(['updateApiTokenForm' => [
//                 'permissions' => [
//                     'delete',
//                     'missing-permission',
//                 ],
//             ]])
//             ->call('updateApiToken');

//         $this->assertTrue($user->fresh()->tokens->first()->can('delete'));
//         $this->assertFalse($user->fresh()->tokens->first()->can('read'));
//         $this->assertFalse($user->fresh()->tokens->first()->can('missing-permission'));
//     }
// }
