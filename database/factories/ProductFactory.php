<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // --- بداية التعديل ---
            // استخدام unique()->word لتوليد كلمة فريدة في كل مرة.
            // هذا يحل مشكلة استنفاد الخيارات المحدودة.
            'name' => 'منتج ' . $this->faker->unique()->word,
            // --- نهاية التعديل ---
            'description' => $this->faker->sentence(),
        ];
    }
}
