<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    /**
     * Seed sample categories and products with generated monochrome SVG placeholders.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan gadget sehari-hari.'],
            ['name' => 'Fashion', 'description' => 'Pakaian dan alas kaki bergaya minimalis.'],
            ['name' => 'Aksesoris', 'description' => 'Pelengkap penampilan dengan desain simpel.'],
            ['name' => 'Rumah Tangga', 'description' => 'Perlengkapan kebutuhan rumah tangga.'],
            ['name' => 'Olahraga', 'description' => 'Alat dan perlengkapan untuk hidup sehat.'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }

        $products = [
            ['name' => 'Headphone Wireless Pro', 'category' => 'Elektronik', 'price' => 850000, 'stock' => 12, 'is_featured' => true, 'color' => 'black', 'size' => 'L'],
            ['name' => 'Smart Speaker Mini', 'category' => 'Elektronik', 'price' => 450000, 'stock' => 20, 'is_featured' => true, 'color' => 'yellow', 'size' => 'M'],
            ['name' => 'Power Bank 10000mAh', 'category' => 'Elektronik', 'price' => 250000, 'stock' => 30, 'is_featured' => false, 'color' => 'pink', 'size' => 'S'],
            ['name' => 'Keyboard Mekanik Tenkeyless', 'category' => 'Elektronik', 'price' => 550000, 'stock' => 8, 'is_featured' => true, 'color' => 'black', 'size' => 'XL'],
            ['name' => 'Kaos Polos Premium', 'category' => 'Fashion', 'price' => 99000, 'stock' => 50, 'is_featured' => false, 'color' => 'pink', 'size' => 'M'],
            ['name' => 'Jaket Hoodie Hitam', 'category' => 'Fashion', 'price' => 199000, 'stock' => 25, 'is_featured' => true, 'color' => 'black', 'size' => 'L'],
            ['name' => 'Sepatu Sneakers Minimalis', 'category' => 'Fashion', 'price' => 350000, 'stock' => 15, 'is_featured' => false, 'color' => 'yellow', 'size' => 'XL'],
            ['name' => 'Jam Tangan Analog', 'category' => 'Aksesoris', 'price' => 275000, 'stock' => 18, 'is_featured' => false, 'color' => 'black', 'size' => 'S'],
            ['name' => 'Tas Selempang Kulit', 'category' => 'Aksesoris', 'price' => 320000, 'stock' => 10, 'is_featured' => false, 'color' => 'pink', 'size' => 'M'],
            ['name' => 'Kacamata Hitam Aviator', 'category' => 'Aksesoris', 'price' => 150000, 'stock' => 22, 'is_featured' => false, 'color' => 'black', 'size' => 'S'],
            ['name' => 'Lampu Meja LED', 'category' => 'Rumah Tangga', 'price' => 185000, 'stock' => 14, 'is_featured' => false, 'color' => 'yellow', 'size' => 'L'],
            ['name' => 'Tumbler Stainless 500ml', 'category' => 'Rumah Tangga', 'price' => 95000, 'stock' => 40, 'is_featured' => false, 'color' => 'pink', 'size' => 'M'],
            ['name' => 'Set Piring Keramik', 'category' => 'Rumah Tangga', 'price' => 225000, 'stock' => 16, 'is_featured' => false, 'color' => 'black', 'size' => 'L'],
            ['name' => 'Dumbbell 5kg', 'category' => 'Olahraga', 'price' => 175000, 'stock' => 24, 'is_featured' => false, 'color' => 'yellow', 'size' => 'M'],
            ['name' => 'Matras Yoga Lipat', 'category' => 'Olahraga', 'price' => 130000, 'stock' => 19, 'is_featured' => false, 'color' => 'pink', 'size' => 'XL'],
            ['name' => 'Botol Minum Sport 1L', 'category' => 'Olahraga', 'price' => 85000, 'stock' => 0, 'is_featured' => false, 'color' => 'black', 'size' => 'S'],
        ];

        foreach ($products as $product) {
            $category = Category::where('name', $product['category'])->firstOrFail();
            $slug = Str::slug($product['name']);

            Product::create([
                'category_id' => $category->id,
                'name' => $product['name'],
                'slug' => $slug,
                'description' => "Deskripsi {$product['name']}: produk berkualitas dengan desain monokrom minimalis.",
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => $this->createPlaceholderImage($slug, $product['name']),
                'is_active' => true,
                'is_featured' => $product['is_featured'] ?? false,
                'color' => $product['color'] ?? null,
                'size' => $product['size'] ?? null,
            ]);
        }
    }

    /**
     * Create a monochrome SVG placeholder image in public storage.
     */
    private function createPlaceholderImage(string $slug, string $productName): string
    {
        $initial = strtoupper(substr($productName, 0, 1));
        $path = "products/{$slug}.svg";

        $svg = <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 600 600">
                <rect width="600" height="600" fill="#0a0a0a"/>
                <circle cx="300" cy="300" r="180" fill="none" stroke="#262626" stroke-width="2"/>
                <circle cx="300" cy="300" r="120" fill="none" stroke="#262626" stroke-width="1"/>
                <circle cx="300" cy="300" r="60" fill="#262626"/>
                <text x="300" y="330" font-family="Arial, sans-serif" font-size="140" font-weight="bold" fill="#fafafa" text-anchor="middle">{$initial}</text>
            </svg>
            SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
