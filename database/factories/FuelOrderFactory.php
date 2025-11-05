<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\OrderStatus; // <-- تعديل
use App\Models\Product;
use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\Factory;

class FuelOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'station_id' => Station::factory(),
            'product_id' => Product::factory(),

            // --- بداية التعديل ---
            // ربط صحيح مع OrderStatusFactory
            'order_status_id' => OrderStatus::factory(),
            // --- نهاية التعديل ---

            'quantity' => $this->faker->numberBetween(5000, 20000),
            'order_date' => $this->faker->dateTimeThisMonth(),
            'delivery_date' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
