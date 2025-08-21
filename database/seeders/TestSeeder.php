<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample products with different categories
        $products = [
            [
                'name' => 'Yonex Badminton Shoes',
                'description' => 'Professional badminton shoes with excellent grip',
                'price' => 299.99,
                'quantity' => 10,
                'category' => 'shoes',
                'brand' => 'Yonex'
            ],
            [
                'name' => 'Li-Ning Shuttlecocks',
                'description' => 'Tournament grade shuttlecocks, pack of 12',
                'price' => 45.99,
                'quantity' => 50,
                'category' => 'shuttlecocks',
                'brand' => 'Li-Ning'
            ],
            [
                'name' => 'Victor Racket',
                'description' => 'Professional badminton racket with carbon fiber',
                'price' => 199.99,
                'quantity' => 15,
                'category' => 'rackets',
                'brand' => 'Victor'
            ],
            [
                'name' => 'Apacs Badminton Bag',
                'description' => 'Large capacity badminton bag with shoe compartment',
                'price' => 89.99,
                'quantity' => 8,
                'category' => 'bags',
                'brand' => 'Apacs'
            ],
            [
                'name' => 'Carlton Badminton Shirt',
                'description' => 'Comfortable and breathable badminton shirt',
                'price' => 59.99,
                'quantity' => 25,
                'category' => 'clothing',
                'brand' => 'Carlton'
            ],
            [
                'name' => 'Yonex Grip Tape',
                'description' => 'High-quality grip tape for racket handles',
                'price' => 12.99,
                'quantity' => 100,
                'category' => 'accessories',
                'brand' => 'Yonex'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
