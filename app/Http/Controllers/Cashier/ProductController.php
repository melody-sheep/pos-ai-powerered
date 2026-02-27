<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\StoreProductRequest;
use App\Models\Product;
use App\Services\Cashier\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        
        // Get products from database
        $products = Product::where('category', $category)
            ->where('is_active', true)
            ->get();
        
        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'category' => $category
            ]);
        }
        
        return response()->json([
            'products' => $products,
            'category' => $category
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $data['image_path'] = $path;
            }
            
            // Set product as active
            $data['is_active'] = true;
            
            // Create product
            $product = Product::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Product added successfully',
                'product' => $product
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:breads,cakes,beverages',
            'rating' => 'nullable|in:none,top_rated,recommended,best_selling,new_arrival,popular',
            'stock' => 'sometimes|integer|min:0',
            'price' => 'sometimes|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        
        $product->update($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}
