<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Livewire\Livewire;
use Tests\TestCase;

// Laravel導入時からあるデフォルトのテストコード
// 現在、チーム機能を使用していないため、コメントアウトしておきます。
// class TwoFactorAuthenticationSettingsTest extends TestCase
// {
//     public function test_two_factor_authentication_can_be_enabled(): void
//     {
//         if (! Features::canManageTwoFactorAuthentication()) {
//             $this->markTestSkipped('Two factor authentication is not enabled.');

//             return;
//         }

//         $this->actingAs($user = User::factory()->create());

//         $this->withSession(['auth.password_confirmed_at' => time()]);

//         Livewire::test(TwoFactorAuthenticationForm::class)
//             ->call('enableTwoFactorAuthentication');

//         $user = $user->fresh();

//         $this->assertNotNull($user->two_factor_secret);
//         $this->assertCount(8, $user->recoveryCodes());
//     }

//     public function test_recovery_codes_can_be_regenerated(): void
//     {
//         if (! Features::canManageTwoFactorAuthentication()) {
//             $this->markTestSkipped('Two factor authentication is not enabled.');

//             return;
//         }

//         $this->actingAs($user = User::factory()->create());

//         $this->withSession(['auth.password_confirmed_at' => time()]);

//         $component = Livewire::test(TwoFactorAuthenticationForm::class)
//             ->call('enableTwoFactorAuthentication')
//             ->call('regenerateRecoveryCodes');

//         $user = $user->fresh();

//         $component->call('regenerateRecoveryCodes');

//         $this->assertCount(8, $user->recoveryCodes());
//         $this->assertCount(8, array_diff($user->recoveryCodes(), $user->fresh()->recoveryCodes()));
//     }

//     public function test_two_factor_authentication_can_be_disabled(): void
//     {
//         if (! Features::canManageTwoFactorAuthentication()) {
//             $this->markTestSkipped('Two factor authentication is not enabled.');

//             return;
//         }

//         $this->actingAs($user = User::factory()->create());

//         $this->withSession(['auth.password_confirmed_at' => time()]);

//         $component = Livewire::test(TwoFactorAuthenticationForm::class)
//             ->call('enableTwoFactorAuthentication');

//         $this->assertNotNull($user->fresh()->two_factor_secret);

//         $component->call('disableTwoFactorAuthentication');

//         $this->assertNull($user->fresh()->two_factor_secret);
//     }
// }
