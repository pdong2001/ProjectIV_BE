<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductDetail>
 */
class ProductDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $totalQuantity = $this->faker->numberBetween(1000, 100000);
        return [
            'product_id' => $this->faker->randomElement(Product::get(['id'])),
            'option_name' => $this->faker->randomElement(['Size', 'Color']),
            'option_value' => $this->faker->name(),
            'in_price' => $this->faker->numberBetween(10000, 500000),
            'out_price' => $this->faker->numberBetween(15000, 550000),
            'total_quantity' => $totalQuantity,
            'remaining_quantity' => $this->faker->numberBetween(0, $totalQuantity),
            'unit' => $this->faker->randomElement(['Cái','Chiếc','Hộp','Thùng','Chai']),
            'created_by' => $this->faker->numberBetween(1, 50)
        ];
    }
}
