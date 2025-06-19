<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Memo>
 */
class MemoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'title' => fake()->realText(30),
            'shortMemo' => fake()->realText(100),
            'additionalMemo' => fake()->realText(200),
            'type' => fake()->numberBetween(1, 2),
        ];
    }
}
