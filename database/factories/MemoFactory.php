<?php

namespace Database\Factories;

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
            'title' => $this->faker->realText(30),
            'shortMemo' => $this->faker->realText(100),
            'additionalMemo' => $this->faker->realText(200),
            'type' => $this->faker->numberBetween(1, 2),
        ];
    }
}
