<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'contribute_user_id' => User::factory(),
            'type' => fake()->numberBetween(1, 4),
            'reason' => fake()->numberBetween(1, 4),
            'detail' => fake()->realText(200),
        ];
    }
}
