<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
        'is_featured',
        'color',
        'size',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Scope to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter products by search keyword.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to only include featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include products with stock available.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopePriceRange(Builder $query, string $range): Builder
    {
        return match ($range) {
            'under_340' => $query->where('price', '<', 340000),
            '340_410' => $query->whereBetween('price', [340000, 410000]),
            '410_480' => $query->whereBetween('price', [410000, 480000]),
            '480_plus' => $query->where('price', '>=', 480000),
            default => $query,
        };
    }

    public function scopeColor(Builder $query, string $color): Builder
    {
        return $query->where('color', $color);
    }

    public function scopeSize(Builder $query, string $size): Builder
    {
        return $query->where('size', $size);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Normalize the color value to lowercase so that the catalog filter
     * and the admin form always use the same canonical values.
     */
    public function setColorAttribute(?string $value): void
    {
        $this->attributes['color'] = $value === null
            ? null
            : Str::lower($value);
    }

    /**
     * Order items referencing this product (historical orders).
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Determine if the product has been ordered at least once.
     */
    public function hasBeenOrdered(): bool
    {
        return $this->orderItems()->exists();
    }

    /**
     * Absolute URL of the product image, or a generated placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image !== null && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        return $this->placeholderImageUrl();
    }

    /**
     * Generated monochrome SVG placeholder, so the UI never shows a broken image.
     */
    private function placeholderImageUrl(): string
    {
        $initial = strtoupper(substr($this->name, 0, 1));

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
