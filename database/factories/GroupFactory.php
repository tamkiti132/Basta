<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'group_photo_path' => null,
            'name' => fake()->name(50),
            'introduction' => fake()->realText(200),
            'isJoinFreeEnabled' => fake()->boolean(),
            'isTipEnabled' => fake()->boolean(),
            'suspension_state' => fake()->boolean(),
        ];
    }
}
