<?php

namespace Database\Factories;

use App\Models\Company; // استيراد موديل الشركة
use Illuminate\Database\Eloquent\Factories\Factory;

class StationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'محطة ' . $this->faker->city, // اسم محطة واقعي
            'address' => $this->faker->address,
            'station_number' => $this->faker->unique()->numerify('ST-####'),
            // ربط المحطة بشركة. سيقوم بإنشاء شركة جديدة تلقائياً إذا لم يتم توفيرها
            'company_id' => Company::factory(),
        ];
    }
}
