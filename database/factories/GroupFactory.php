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
            'name' => $this->faker->name(20),
            'introduction' => $this->faker->realText(50),
            'isJoinFreeEnabled' => $this->faker->boolean(),
            'isTipEnabled' => $this->faker->boolean(),
        ];
    }
}
