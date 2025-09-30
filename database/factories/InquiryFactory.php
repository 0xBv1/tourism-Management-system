<?php

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'guest_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(),
            'tour_name' => $this->faker->words(3, true),
            'nationality' => $this->faker->country(),
            'number_pax' => $this->faker->numberBetween(1, 10),
            'arrival_date' => $this->faker->date(),
            'departure_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'client_id' => null,
            'assigned_to' => User::factory(),
            'booking_file_id' => null,
            'confirmed_at' => null,
            'completed_at' => null,
        ];
    }
}

