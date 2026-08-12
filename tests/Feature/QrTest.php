<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    private function customer(): User
    {
        return User::factory()->create(['role' => UserRole::Customer]);
    }

    private function activeProduct(): Product
    {
        return Product::factory()->create(['name' => 'Produk QR', 'is_active' => true]);
    }

    private function inactiveProduct(): Product
    {
        return Product::factory()->create(['name' => 'Produk Arsip', 'is_active' => false]);
    }

    // ----- Public QR endpoint -----

    public function test_public_qr_returns_svg_for_active_product(): void
    {
        $product = $this->activeProduct();

        $this->get(route('shop.qr', $product, false))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_public_qr_returns_404_for_inactive_product(): void
    {
        $product = $this->inactiveProduct();

        $this->get(route('shop.qr', $product, false))
            ->assertNotFound();
    }

    // ----- Product detail page shows QR -----

    public function test_product_detail_page_shows_qr_image(): void
    {
        $product = $this->activeProduct();
        $qrPath = route('shop.qr', $product, false);

        $this->get("/produk/{$product->slug}")
            ->assertOk()
            ->assertSee($qrPath, false);
    }

    // ----- Admin QR endpoint -----

    public function test_admin_can_view_qr_for_active_product(): void
    {
        $product = $this->activeProduct();

        $this->actingAs($this->admin())
            ->get("/admin/produk/{$product->id}/qr")
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_admin_can_view_qr_for_inactive_product(): void
    {
        $product = $this->inactiveProduct();

        $this->actingAs($this->admin())
            ->get("/admin/produk/{$product->id}/qr")
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_customer_cannot_view_admin_qr_endpoint(): void
    {
        $product = $this->activeProduct();

        $this->actingAs($this->customer())
            ->get("/admin/produk/{$product->id}/qr")
            ->assertForbidden();
    }

    public function test_guest_cannot_view_admin_qr_endpoint(): void
    {
        $product = $this->activeProduct();

        $this->get("/admin/produk/{$product->id}/qr")
            ->assertRedirect('login');
    }

    // ----- Caching / repeated requests -----

    public function test_qr_endpoint_returns_svg_content(): void
    {
        $product = $this->activeProduct();

        $response = $this->get(route('shop.qr', $product, false));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }
}
