<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\ProductQrCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the product QR code (admin endpoint, available for any product).
     */
    public function qr(Product $product): Response
    {
        // Admins may view the QR of any product, including deactivated ones,
        // for inventory / label-printing purposes.

        $svg = Cache::remember("product-qr-{$product->slug}", 3600, fn () => ProductQrCode::svg($product));

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Display a paginated list of products with search and filters.
     */
    public function index(Request $request): View
    {
        $categories = Category::orderBy('name')->get();

        $colors = Product::query()
            ->whereNotNull('color')
            ->distinct()
            ->orderBy('color')
            ->pluck('color');

        $sizes = Product::query()
            ->whereNotNull('size')
            ->distinct()
            ->orderBy('size')
            ->pluck('size');

        $products = Product::query()
            ->with('category')

            // Search
            ->when(
                filled($request->search),
                fn ($query) => $query->search($request->search)
            )

            // Category
            ->when(
                filled($request->category),
                fn ($query) => $query->where('category_id', $request->category)
            )

            // Active / inactive
            ->when(
                filled($request->status),
                fn ($query) => $query->where(
                    'is_active',
                    $request->status === 'active'
                )
            )

            // Featured
            ->when(
                filled($request->featured),
                fn ($query) => $query->where(
                    'is_featured',
                    $request->featured === 'featured'
                )
            )

            // Availability
            ->when(
                $request->availability === 'in_stock',
                fn ($query) => $query->inStock()
            )
            ->when(
                $request->availability === 'out_of_stock',
                fn ($query) => $query->where('stock', 0)
            )

            // Price
            ->when(
                filled($request->price),
                fn ($query) => $query->priceRange($request->price)
            )

            // Color
            ->when(
                filled($request->color),
                fn ($query) => $query->color($request->color)
            )

            // Size
            ->when(
                filled($request->size),
                fn ($query) => $query->size($request->size)
            )

            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'colors' => $colors,
            'sizes' => $sizes,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],

            // Product attributes
            'is_active' => $validated['is_active'] ?? false,
            'is_featured' => $validated['is_featured'] ?? false,
            'color' => $validated['color'] ?? null,
            'size' => $validated['size'] ?? null,

            // Image (legacy support)
            'image' => $this->storeImage($request),
        ]);

        // Store multiple images if provided
        $this->storeProductImages($request, $product);

        return Redirect::route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product): View
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the given product.
     */
    public function update(
        ProductRequest $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validated();

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug(
                $validated['name'],
                $product->id
            ),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],

            // Product attributes
            'is_active' => $validated['is_active'] ?? false,
            'is_featured' => $validated['is_featured'] ?? false,
            'color' => $validated['color'] ?? null,
            'size' => $validated['size'] ?? null,

            // Image (legacy support)
            'image' => $this->storeImage($request, $product),
        ]);

        // Store multiple images if provided
        $this->storeProductImages($request, $product);

        return Redirect::route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the given product along with its stored image.
     *
     * Products that have been ordered at least once cannot be deleted
     * permanently because the order history must remain intact for
     * reporting and audit purposes. In that case the product is
     * rejected with a clear error message.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->hasBeenOrdered()) {
            return back()->with(
                'error',
                'Produk tidak dapat dihapus karena sudah pernah dipesan. Nonaktifkan produk saja untuk menyembunyikannya dari katalog.'
            );
        }

        if ($product->image !== null) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with(
            'success',
            'Produk berhasil dihapus.'
        );
    }

    /**
     * Persist an uploaded product image and return its storage path.
     */
    private function storeImage(
        ProductRequest $request,
        ?Product $product = null
    ): ?string {
        if (! $request->hasFile('image')) {
            return $product?->image;
        }

        $path = $request->file('image')->store(
            'products',
            'public'
        );

        if ($product?->image !== null && $product->image !== $path) {
            Storage::disk('public')->delete($product->image);
        }

        return $path;
    }
    /**
     * Generate a unique slug based on the given name.
     */
    private function uniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->whereKeyNot($ignoreId)
                )
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Store multiple product images in the gallery.
     */
    private function storeProductImages(
        ProductRequest $request,
        Product $product
    ): void {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $files = $request->file('gallery_images');
        $sortOrder = $product->images()->max('sort_order') ?? 0;
        $needsPrimary = $product->image === null && ! $product->images()->exists();

        foreach ($files as $file) {
            $path = $file->store('products/gallery', 'public');

            $sortOrder++;

            // If the product has no main image and this is the first
            // gallery image, promote it as primary.
            $isPrimary = $needsPrimary && $sortOrder === 1;

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'sort_order' => $sortOrder,
            ]);

            $needsPrimary = false;
        }
    }

    /**
     * Delete a product image.
     */
    public function deleteImage(ProductImage $productImage): RedirectResponse
    {
        $product = $productImage->product;

        if (Storage::disk('public')->exists($productImage->image_path)) {
            Storage::disk('public')->delete($productImage->image_path);
        }

        $wasPrimary = $productImage->is_primary;

        $productImage->delete();

        // If the primary image was deleted, promote the first remaining
        // gallery image so the product always has a clear main image.
        if ($wasPrimary) {
            $nextImage = $product->images()->first();
            if ($nextImage) {
                $product->images()->update(['is_primary' => false]);
                $nextImage->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Gambar produk berhasil dihapus.');
    }

    /**
     * Set a product image as primary.
     */
    public function setPrimaryImage(ProductImage $productImage): RedirectResponse
    {
        $product = $productImage->product;

        // Remove primary flag from all images
        $product->images()->update(['is_primary' => false]);

        // Set this image as primary
        $productImage->update(['is_primary' => true]);

        return back()->with('success', 'Gambar utama berhasil diperbarui.');
    }
}
