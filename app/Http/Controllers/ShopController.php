<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CommunityImage;
use App\Models\Product;
use App\Support\ProductQrCode;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display the product catalog with search and category filters.
     */
    public function beranda(): View
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->with('category')
            ->active()
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        return view('shop.beranda', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function index(): View
    {
        $categories = Category::orderBy('name')->get();

        $products = Product::query()
            ->with('category')
            ->active()
            ->when(request('search'), fn ($query, $search) => $query->search($search))
            ->when(request('category'), fn ($query, $category) => $query->whereHas('category', fn ($query) => $query->where('slug', $category)))
            ->when(request('product_type') === 'featured', fn ($query) => $query->featured())
            ->when(request('availability') === 'in_stock', fn ($query) => $query->inStock())
            ->when(request('price'), fn ($query, $price) => $query->priceRange($price))
            ->when(request('color'), fn ($query, $color) => $query->color($color))
            ->when(request('size'), fn ($query, $size) => $query->size($size))
            ->when(request('sort'), fn ($query, $sort) => match ($sort) {
                'featured' => $query->orderByDesc('is_featured')->orderByDesc('id'),
                'newest' => $query->orderByDesc('created_at'),
                'price_low' => $query->orderBy('price'),
                'price_high' => $query->orderByDesc('price'),
                default => $query->orderByDesc('id'),
            }, fn ($query) => $query->orderByDesc('id'))
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * Display a single active product.
     */
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $relatedProducts = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->limit(4)
            ->get();

        return view('shop.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Display the about page.
     */
    public function about(): View
    {
        return view('shop.about');
    }

    /**
     * Display deactivated products. Display only, not purchasable.
     */
    public function archives(): View
    {
        $products = Product::query()
            ->with('category')
            ->where('is_active', false)
            ->orderByDesc('id')
            ->paginate(12);

        return view('shop.archives', [
            'products' => $products,
        ]);
    }

    /**
     * Display the community gallery in a masonry layout.
     */
    public function community(): View
    {
        $images = CommunityImage::query()
            ->orderByDesc('id')
            ->paginate(60);

        return view('shop.community', [
            'images' => $images,
        ]);
    }

    /**
     * Display the product QR code (public endpoint, active products only).
     */
    public function qr(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        $svg = Cache::remember("product-qr-{$product->slug}", 3600, fn () => ProductQrCode::svg($product));

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
