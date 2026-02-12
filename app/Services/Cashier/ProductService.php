<?php

namespace App\Services\Cashier;

class ProductService
{
    protected $products = [
        [
            'id' => 1,
            'name' => 'Sourdough Bread',
            'category' => 'breads',
            'price' => 180.00,
            'description' => 'Traditional sourdough with crispy crust',
            'image' => '🍞'
        ],
        [
            'id' => 2,
            'name' => 'Chocolate Cake',
            'category' => 'cakes',
            'price' => 250.00,
            'description' => 'Rich chocolate layered cake',
            'image' => '🍰'
        ],
        [
            'id' => 3,
            'name' => 'Caramel Latte',
            'category' => 'beverages',
            'price' => 150.00,
            'description' => 'Espresso with caramel and steamed milk',
            'image' => '☕'
        ],
        [
            'id' => 4,
            'name' => 'Croissant',
            'category' => 'breads',
            'price' => 95.00,
            'description' => 'Buttery flaky pastry',
            'image' => '🥐'
        ],
        [
            'id' => 5,
            'name' => 'Cheesecake',
            'category' => 'cakes',
            'price' => 220.00,
            'description' => 'Creamy New York style',
            'image' => '🧀'
        ],
        [
            'id' => 6,
            'name' => 'Matcha Tea',
            'category' => 'beverages',
            'price' => 130.00,
            'description' => 'Japanese green tea',
            'image' => '🍵'
        ]
    ];

    public function getAllProducts()
    {
        return $this->products;
    }

    public function getProductsByCategory($category)
    {
        return array_filter($this->products, function($product) use ($category) {
            return $product['category'] === $category;
        });
    }

    public function getProductById($id)
    {
        $key = array_search($id, array_column($this->products, 'id'));
        return $key !== false ? $this->products[$key] : null;
    }
}
