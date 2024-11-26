<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
