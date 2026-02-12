<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Services\Cashier\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $category = $request->get('category', 'breads');
        $products = $this->productService->getProductsByCategory($category);
        
        if ($request->ajax()) {
            return response()->json([
                'products' => array_values($products),
                'category' => $category
            ]);
        }
        
        return response()->json([
            'products' => array_values($products),
            'category' => $category
        ]);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
