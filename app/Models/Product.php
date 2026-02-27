<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'rating',
        'stock',
        'price',
        'image_path',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer'
    ];

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path 
            ? asset('storage/' . $this->image_path)
            : null;
    }

    // Check stock status
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) return 'out';
        if ($this->stock < 6) return 'low';
        return 'in';
    }
}
