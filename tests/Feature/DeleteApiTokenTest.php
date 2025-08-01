<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Livewire\ApiTokenManager;
use Livewire\Livewire;
use Tests\TestCase;

// Laravel導入時からあるデフォルトのテストコード
// 現在、APIトークンの機能を使用していないため、コメントアウトしておきます。
// class DeleteApiTokenTest extends TestCase
// {
//     public function test_api_tokens_can_be_deleted(): void
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
//             ->set(['apiTokenIdBeingDeleted' => $token->id])
//             ->call('deleteApiToken');

//         $this->assertCount(0, $user->fresh()->tokens);
//     }
// }
