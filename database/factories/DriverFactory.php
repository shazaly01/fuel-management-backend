<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'license_number' => $this->faker->unique()->numerify('#####-###'), // رقم رخصة فريد
            'phone_number' => $this->faker->unique()->phoneNumber(),
            'status' => $this->faker->randomElement(['available', 'on_trip', 'unavailable']), // حالة عشوائية من قائمة
        ];
    }
}
