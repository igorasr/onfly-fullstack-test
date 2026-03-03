<?php

namespace Database\Factories;

use App\Enums\TravelRequestStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'requester_id' => User::factory(),
            'requester_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            'departure_date' => $this->faker->dateTimeBetween('+1 day', '+15 days')->format('Y-m-d'),
            'return_date' => $this->faker->dateTimeBetween('+16 days', '+30 days')->format('Y-m-d'),
            'status' => TravelRequestStatus::Requested,
        ];
    }
}
