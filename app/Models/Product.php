<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'quantity',
        'image',
        'category',
        'brand',
    ];

    /**
     * Get the full URL for the product image
     */
    public function getImageUrlAttribute()
    {
        try {
            if (!$this->image) {
                return null;
            }
            
            // If it's already a Cloudinary URL (starts with http/https), return it directly
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            
            // Fallback for old local storage paths (for backward compatibility)
            // If image path already includes 'products/', use it as is
            // Otherwise, prepend 'products/' if it's just a filename
            $imagePath = $this->image;
            if (!str_starts_with($imagePath, 'products/')) {
                $imagePath = 'products/' . $imagePath;
            }
            
            return Storage::url($imagePath);
        } catch (\Exception $e) {
            \Log::error('Failed to get product image URL', [
                'product_id' => $this->id,
                'image' => $this->image,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
