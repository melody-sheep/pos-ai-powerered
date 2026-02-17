<?php

namespace App\Http\Requests\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add your authorization logic if needed
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|in:breads,cakes,beverages',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'category.required' => 'Please select a category',
            'category.in' => 'Invalid category selected',
            'stock.required' => 'Stock quantity is required',
            'stock.min' => 'Stock cannot be negative',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be greater than 0',
            'price.numeric' => 'Please enter a valid price',
            'image.image' => 'File must be an image',
            'image.max' => 'Image size cannot exceed 2MB'
        ];
    }
}