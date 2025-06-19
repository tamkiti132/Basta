<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nickname' => substr(fake()->name(), 0, 13), // 最大13文字まで
            'username' => '@'.(string) Str::ulid(),
            'email' => fake()->unique()->safeEmail(),
            // 'email_verified_at' => null,
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'google_id' => null,
            // 'current_team_id' => null,
            'profile_photo_path' => null,
            'suspension_state' => fake()->boolean(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    // public function unverified(): static
    // {
    //     return $this->state(function (array $attributes) {
    //         return [
    //             'email_verified_at' => null,
    //         ];
    //     });
    // }

    /**
     * Indicate that the user should have a personal team.
     */
    // public function withPersonalTeam(): static
    // {
    //     if (! Features::hasTeamFeatures()) {
    //         return $this->state([]);
    //     }

    //     return $this->has(
    //         Team::factory()
    //             ->state(function (array $attributes, User $user) {
    //                 return ['name' => $user->name . '\'s Team', 'user_id' => $user->id, 'personal_team' => true];
    //             }),
    //         'ownedTeams'
    //     );
    // }
}
