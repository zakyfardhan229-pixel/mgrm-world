<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CommunityImage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'image',
        'caption',
    ];

    /**
     * Absolute URL of the community image, or a generated placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if (Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        $svg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" width="600" height="600">
                <rect width="600" height="600" fill="#0a0a0a"/>
                <text x="300" y="330" font-family="Arial, sans-serif" font-size="140" font-weight="bold" fill="#fafafa" text-anchor="middle">?</text>
            </svg>
            SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
