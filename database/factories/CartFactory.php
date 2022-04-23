<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\ProductDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'customer_id' => $this->faker->randomElement(Customer::get('id')),
            'quantity' => $this->faker->numberBetween(0,10),
            'product_detail_id' => $this->faker->randomElement(ProductDetail::get('id'))
        ];
    }
}
