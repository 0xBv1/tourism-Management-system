<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'inquiry_id' => \App\Models\Inquiry::factory(),
            'sender_id' => \App\Models\User::factory(),
            'message' => $this->faker->sentence(),
            'read_at' => null,
        ];
    }
}
