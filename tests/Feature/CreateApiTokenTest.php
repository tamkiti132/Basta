<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

// Laravel導入時からあるデフォルトのテストコード
// class CreateApiTokenTest extends TestCase
// {
//     // 現在、APIトークンの機能を使用していないため、コメントアウトしておきます。
//     public function test_api_tokens_can_be_created(): void
//     {
//         if (! Features::hasApiFeatures()) {
//             $this->markTestSkipped('API support is not enabled.');

//             return;
//         }

//         $this->actingAs($user = User::factory()->withPersonalTeam()->create());

//         Livewire::test(ApiTokenManager::class)
//             ->set(['createApiTokenForm' => [
//                 'name' => 'Test Token',
//                 'permissions' => [
//                     'read',
//                     'update',
//                 ],
//             ]])
//             ->call('createApiToken');

//         $this->assertCount(1, $user->fresh()->tokens);
//         $this->assertEquals('Test Token', $user->fresh()->tokens->first()->name);
//         $this->assertTrue($user->fresh()->tokens->first()->can('read'));
//         $this->assertFalse($user->fresh()->tokens->first()->can('delete'));
//     }
// }
