<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the product that owns this image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Absolute URL of the product image.
     */
    public function getImageUrlAttribute(): string
    {
        if (Storage::disk('public')->exists($this->image_path)) {
            return Storage::disk('public')->url($this->image_path);
        }

        return $this->placeholderImageUrl();
    }

    /**
     * Generated monochrome SVG placeholder.
     */
    private function placeholderImageUrl(): string
    {
        $initial = strtoupper(substr($this->product?->name ?? 'P', 0, 1));

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="600" height="600">
                <rect width="600" height="600" fill="#0a0a0a"/>
                <circle cx="300" cy="300" r="180" fill="none" stroke="#262626" stroke-width="2"/>
                <circle cx="300" cy="300" r="120" fill="none" stroke="#262626" stroke-width="1"/>
                <text x="300" y="330" font-family="Arial, sans-serif" font-size="140" font-weight="bold" fill="#fafafa" text-anchor="middle">{$initial}</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}

