<?php

namespace Database\Factories;

use App\Models\Driver; // استيراد موديل السائق
use Illuminate\Database\Eloquent\Factories\Factory;

class TruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'truck_number' => $this->faker->bothify('??-####'), // e.g., AB-1234
            'truck_type' => $this->faker->randomElement(['Iveco', 'MAN', 'Mercedes']),
            'color' => $this->faker->safeColorName(),
            'trailer_number' => $this->faker->optional()->bothify('TR-####'), // قد يكون للشاحنة مقطورة أو لا
            // ربط الشاحنة بسائق
            'driver_id' => Driver::factory(),
        ];
    }
}
