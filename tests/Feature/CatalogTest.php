<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_products_are_hidden_from_catalog(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Aktif', 'is_active' => true]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Nonaktif', 'is_active' => false]);

        $this->get('/toko')
            ->assertOk()
            ->assertSee('Produk Aktif')
            ->assertDontSee('Produk Nonaktif');
    }

    public function test_inactive_product_detail_returns_404(): void
    {
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->get("/produk/{$inactive->slug}")->assertNotFound();
    }

    public function test_catalog_filters_by_color(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Hitam', 'color' => 'black']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Kuning', 'color' => 'yellow']);

        $this->get('/toko?color=black')
            ->assertOk()
            ->assertSee('Produk Hitam')
            ->assertDontSee('Produk Kuning');
    }

    public function test_catalog_filters_by_size(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Ukuran L', 'size' => 'L']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Ukuran S', 'size' => 'S']);

        $this->get('/toko?size=S')
            ->assertOk()
            ->assertSee('Ukuran S')
            ->assertDontSee('Ukuran L');
    }

    public function test_catalog_filters_by_featured(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Unggulan', 'is_featured' => true]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Biasa', 'is_featured' => false]);

        $this->get('/toko?product_type=featured')
            ->assertOk()
            ->assertSee('Produk Unggulan')
            ->assertDontSee('Produk Biasa');
    }

    public function test_catalog_filters_by_availability(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Ada Stok', 'stock' => 3]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Stok Habis', 'stock' => 0]);

        $this->get('/toko?availability=in_stock')
            ->assertOk()
            ->assertSee('Ada Stok')
            ->assertDontSee('Stok Habis');
    }

    public function test_catalog_sorts_by_price_ascending(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Mahal', 'price' => 900000]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Murah', 'price' => 10000]);

        $response = $this->get('/toko?sort=price_low')->assertOk();

        $this->assertTrue(
            strpos($response->getContent(), 'Produk Murah') < strpos($response->getContent(), 'Produk Mahal')
        );
    }

    public function test_catalog_search_finds_name_and_description(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk A', 'description' => 'deskripsi unik sekali']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk B', 'description' => 'biasa saja']);

        $this->get('/toko?search=unik')
            ->assertOk()
            ->assertSee('Produk A')
            ->assertDontSee('Produk B');
    }

    public function test_product_detail_shows_color_and_size(): void
    {
        $product = Product::factory()->create(['color' => 'black', 'size' => 'XL', 'stock' => 5]);

        $this->get("/produk/{$product->slug}")
            ->assertOk()
            ->assertSee('Black', false)
            ->assertSee('XL');
    }

    public function test_homepage_loads_with_shop_now_cta(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('SHOP NOW');
    }

    public function test_about_page_loads(): void
    {
        $this->get('/tentang')
            ->assertOk()
            ->assertSee('Mengenal MGRM World')
            ->assertSee('Visi')
            ->assertSee('Misi');
    }

    public function test_archives_page_lists_inactive_products_only(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Aktif', 'is_active' => true]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Produk Arsip', 'is_active' => false]);

        $this->get('/arsip')
            ->assertOk()
            ->assertSee('Produk Arsip')
            ->assertDontSee('Produk Aktif');
    }

    public function test_archives_page_is_display_only(): void
    {
        Product::factory()->create(['name' => 'Produk Arsip', 'is_active' => false, 'stock' => 5]);

        $this->get('/arsip')
            ->assertOk()
            ->assertSee('Produk Arsip')
            ->assertSee('Tidak tersedia')
            ->assertDontSee('cart.store')
            ->assertDontSee('Buy It Now')
            ->assertDontSee('Add to Cart');
    }
}
