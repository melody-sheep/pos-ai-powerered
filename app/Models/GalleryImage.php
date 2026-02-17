<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'filename',
        'path',
        'size',
        'mime_type'
    ];

    protected $casts = [
        'size' => 'integer'
    ];

    public function getSizeInKbAttribute()
    {
        return round($this->size / 1024, 2) . ' KB';
    }
}