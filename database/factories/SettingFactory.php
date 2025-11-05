<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // --- بداية التعديل ---
        // تعديل ليتوافق مع أعمدة 'key' و 'value'
        return [
            'key' => $this->faker->unique()->slug(3), // e.g., 'some-setting-key'
            'value' => ['data' => $this->faker->sentence],
        ];
        // --- نهاية التعديل ---
    }
}
