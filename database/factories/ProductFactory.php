<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $categories = ['shoes', 'clothing', 'shuttlecocks', 'rackets', 'bags', 'accessories'];
        $brands = ['Yonex', 'Li-Ning', 'Carlton', 'Victor', 'Apacs', 'Others'];
        
        return [
            'name' => $this->faker->word . ' ' . ucfirst($this->faker->randomElement($categories)),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 50, 500),
            'quantity' => $this->faker->numberBetween(1, 100),
            'image' => null,
            'category' => $this->faker->randomElement($categories),
            'brand' => $this->faker->randomElement($brands),
        ];
    }
} 