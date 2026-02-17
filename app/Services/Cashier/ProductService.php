<?php

namespace App\Services\Cashier;

use App\Models\Product;

class ProductService
{
    public function getAllProducts()
    {
        return Product::where('is_active', true)->get();
    }

    public function getProductsByCategory($category)
    {
        return Product::where('category', $category)
            ->where('is_active', true)
            ->get();
    }

    public function getProductById($id)
    {
        return Product::find($id);
    }
}