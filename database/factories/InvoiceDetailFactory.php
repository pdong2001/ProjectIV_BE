<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\ProductDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceDetail>
 */
class InvoiceDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $product_detail_id = $this->faker->numberBetween(1, ProductDetail::count());
        return [
            'created_by' => $this->faker->numberBetween(1, User::count()),
            'product_detail_id' => $product_detail_id,
            'invoice_id' => $this->faker->numberBetween(1, Invoice::count()),
            'price' => ProductDetail::find($product_detail_id)->out_price??1000,
            'quantity'=> $this->faker->numberBetween(1, 500)
        ];
    }
}
