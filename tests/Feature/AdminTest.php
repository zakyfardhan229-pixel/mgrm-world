<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/produk', [
                'category_id' => $category->id,
                'name' => 'Produk Baru',
                'description' => 'Deskripsi produk baru.',
                'price' => 150000,
                'stock' => 12,
                'image' => UploadedFile::fake()->image('produk.jpeg', 400, 400),
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.produk.index'));

        $product = Product::where('name', 'Produk Baru')->firstOrFail();

        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_image_rejects_unsupported_extension(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/produk', [
                'category_id' => $category->id,
                'name' => 'Produk Gagal',
                'price' => 10000,
                'stock' => 1,
                'image' => UploadedFile::fake()->create('virus.exe', 100),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_image_rejects_oversized_file(): void
    {
        Storage::fake('public');

        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/produk', [
                'category_id' => $category->id,
                'name' => 'Produk Terlalu Besar',
                'price' => 10000,
                'stock' => 1,
                'image' => UploadedFile::fake()->image('besar.png')->size(3000),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Lama', 'price' => 10000, 'stock' => 5]);

        $this->actingAs($this->admin())
            ->put("/admin/produk/{$product->id}", [
                'category_id' => $product->category_id,
                'name' => 'Nama Baru',
                'description' => null,
                'price' => 20000,
                'stock' => 9,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.produk.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Nama Baru',
            'slug' => 'nama-baru',
            'price' => '20000.00',
            'stock' => 9,
        ]);
    }

    public function test_admin_can_create_product_with_color_size_and_featured(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin())
            ->post('/admin/produk', [
                'category_id' => $category->id,
                'name' => 'Produk Warna',
                'description' => 'Deskripsi produk warna.',
                'price' => 100000,
                'stock' => 5,
                'is_active' => '1',
                'is_featured' => '1',
                'color' => 'Black',
                'size' => 'XL',
            ])
            ->assertRedirect(route('admin.produk.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Produk Warna',
            'is_active' => true,
            'is_featured' => true,
            'color' => 'black',
            'size' => 'XL',
        ]);
    }

    public function test_admin_update_can_change_color_size_and_featured(): void
    {
        $product = Product::factory()->create([
            'is_featured' => true,
            'color' => 'black',
            'size' => 'L',
        ]);

        $this->actingAs($this->admin())
            ->put("/admin/produk/{$product->id}", [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'description' => null,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_active' => '1',
                'is_featured' => '1',
                'color' => 'Pink',
                'size' => 'M',
            ])
            ->assertRedirect(route('admin.produk.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_active' => true,
            'is_featured' => true,
            'color' => 'pink',
            'size' => 'M',
        ]);
    }

    public function test_admin_update_with_unchecked_featured_disables_it(): void
    {
        $product = Product::factory()->create(['is_featured' => true]);

        $this->actingAs($this->admin())
            ->put("/admin/produk/{$product->id}", [
                'category_id' => $product->category_id,
                'name' => $product->name,
                'description' => null,
                'price' => $product->price,
                'stock' => $product->stock,
                'is_active' => '1',
                'is_featured' => '0',
            ])
            ->assertRedirect(route('admin.produk.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_featured' => false]);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())
            ->delete("/admin/produk/{$product->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_create_category_and_delete_empty_one(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/kategori', ['name' => 'Gadget', 'description' => 'Kategori gadget.'])
            ->assertRedirect(route('admin.kategori.index'));

        $category = Category::where('name', 'Gadget')->firstOrFail();
        $this->assertEquals('gadget', $category->slug);

        $this->actingAs($this->admin())
            ->delete("/admin/kategori/{$category->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAs($this->admin())
            ->delete("/admin/kategori/{$category->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::Pending]);

        $this->actingAs($this->admin())
            ->patch("/admin/pesanan/{$order->id}/status", ['status' => OrderStatus::Paid->value])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid']);
    }

    public function test_order_status_rejects_invalid_value(): void
    {
        $order = Order::factory()->create();

        $this->actingAs($this->admin())
            ->patch("/admin/pesanan/{$order->id}/status", ['status' => 'hacked'])
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
    }

    public function test_report_aggregates_revenue_excluding_cancelled_orders(): void
    {
        Order::factory()->create(['total' => 100000, 'status' => OrderStatus::Completed, 'created_at' => now()->subDays(2)]);
        Order::factory()->create(['total' => 50000, 'status' => OrderStatus::Completed, 'created_at' => now()->subDays(2)]);
        Order::factory()->create(['total' => 999999, 'status' => OrderStatus::Cancelled, 'created_at' => now()->subDays(2)]);

        $this->actingAs($this->admin())
            ->get(route('admin.reports.index', [
                'from' => now()->subDays(3)->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Rp 150.000')
            ->assertSee('Total Transaksi');
    }

    public function test_report_filters_by_date_range(): void
    {
        Order::factory()->create(['total' => 100000, 'status' => OrderStatus::Completed, 'created_at' => now()->subDays(30)]);
        Order::factory()->create(['total' => 200000, 'status' => OrderStatus::Completed, 'created_at' => now()->subDays(2)]);

        $this->actingAs($this->admin())
            ->get(route('admin.reports.index', [
                'from' => now()->subDays(7)->toDateString(),
                'to' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Rp 200.000')
            ->assertDontSee('Rp 100.000');
    }

    public function test_report_rejects_invalid_date_range(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.reports.index', [
                'from' => now()->toDateString(),
                'to' => now()->subDays(7)->toDateString(),
            ]))
            ->assertSessionHasErrors('to');
    }
}
