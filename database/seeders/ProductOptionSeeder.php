<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductOption::factory(Product::count()*2)->create();
    }
}
