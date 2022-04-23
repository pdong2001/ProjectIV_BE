<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blob>
 */
class BlobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['brown-bear-notebook.jpg','customizable-mug.jpg','hummingbird-notebook.jpg','voluptatum-delenti-atques.jpg']),
            'created_by' => $this->faker->numberBetween(1,User::count())
        ];
    }
}
