<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\GalleryImage;

class GalleryController extends Controller
{
    public function getImages()
    {
        $images = GalleryImage::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'images' => $images->map(function($image) {
                return [
                    'id' => $image->id,
                    'name' => $image->original_name,
                    'path' => $image->path,
                    'url' => Storage::disk('public')->url($image->path),
                    'size' => $image->size,
                    'created_at' => $image->created_at->diffForHumans()
                ];
            })
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('gallery', $filename, 'public');

        $image = GalleryImage::create([
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $path,
            'size' => $size,
            'mime_type' => $file->getMimeType()
        ]);

        return response()->json([
            'success' => true,
            'image' => [
                'id' => $image->id,
                'name' => $originalName,
                'path' => $path,
                'url' => Storage::disk('public')->url($path)
            ]
        ]);
    }
}