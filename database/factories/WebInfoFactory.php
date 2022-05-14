<?php

namespace Database\Factories;

use App\Models\Blob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebInfo>
 */
class WebInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->title(),
            'title' => $this->faker->name(),
            'link' => 'https://php.pdong-vps.tk',
            'content' => $this->faker->randomHtml(),
            'blob_id' => $this->faker->randomElement(Blob::all())->id,
        ];
    }
}
