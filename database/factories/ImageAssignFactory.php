<?php

namespace Database\Factories;

use App\Models\Blob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImageAssign>
 */
class ImageAssignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'blob_id' => $this->faker->numberBetween(1, Blob::count()),
            'imageable_id' => $this->faker->numberBetween(0, 50),
            'imageable_type' => $this->faker->randomElement(['App\Models\Product','App\Models\ProductDetail']),
        ];
    }
}
