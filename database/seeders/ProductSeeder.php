<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Sourdough Bread',
                'category' => 'breads',
                'stock' => 15,
                'price' => 180.00,
                'description' => 'Traditional sourdough with crispy crust',
                'image_path' => null
            ],
            [
                'name' => 'Chocolate Cake',
                'category' => 'cakes',
                'stock' => 8,
                'price' => 250.00,
                'description' => 'Rich chocolate layered cake',
                'image_path' => null
            ],
            [
                'name' => 'Caramel Latte',
                'category' => 'beverages',
                'stock' => 25,
                'price' => 150.00,
                'description' => 'Espresso with caramel and steamed milk',
                'image_path' => null
            ],
            [
                'name' => 'Croissant',
                'category' => 'breads',
                'stock' => 12,
                'price' => 95.00,
                'description' => 'Buttery flaky pastry',
                'image_path' => null
            ],
            [
                'name' => 'Cheesecake',
                'category' => 'cakes',
                'stock' => 5,
                'price' => 220.00,
                'description' => 'Creamy New York style',
                'image_path' => null
            ],
            [
                'name' => 'Matcha Tea',
                'category' => 'beverages',
                'stock' => 30,
                'price' => 130.00,
                'description' => 'Japanese green tea',
                'image_path' => null
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}