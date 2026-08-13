<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create(['role' => UserRole::Customer]);
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'payment_method' => 'transfer',
            'notes' => null,
        ], $overrides);
    }

    private function addToCart(User $user, Product $product, int $quantity = 1): void
    {
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_checkout(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_customer_can_place_order_successfully(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 100000, 'stock' => 10]);
        $this->addToCart($user, $product, 2);

        $response = $this->actingAs($user)
            ->post('/checkout', $this->checkoutPayload())
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertEquals(OrderStatus::Pending, $order->status);
        $this->assertEquals('200000.00', $order->total);
        $this->assertSame('ORD-', substr($order->order_number, 0, 4));
        $this->assertEquals($user->id, $order->user_id);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'subtotal' => '200000.00',
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
        $this->assertDatabaseCount('cart_items', 0);

        $response->assertSessionHas('success');
    }

    public function test_customer_can_pay_with_cod(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 5]);
        $this->addToCart($user, $product);

        $this->actingAs($user)
            ->post('/checkout', $this->checkoutPayload(['payment_method' => 'cod']))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);
    }

    public function test_checkout_validation_fails_for_missing_fields(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 5]);
        $this->addToCart($user, $product);

        $this->actingAs($user)
            ->post('/checkout', $this->checkoutPayload(['customer_name' => '', 'phone' => '', 'address' => '', 'payment_method' => 'ewallet']))
            ->assertSessionHasErrors(['customer_name', 'phone', 'address', 'payment_method']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_checkout_rolls_back_when_stock_is_insufficient(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 1]);
        $this->addToCart($user, $product, 3);

        $this->actingAs($user)
            ->post('/checkout', $this->checkoutPayload())
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_checkout_with_empty_cart_is_rejected(): void
    {
        $this->actingAs($this->customer())
            ->post('/checkout', $this->checkoutPayload())
            ->assertSessionHas('error');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_multiple_cart_items_are_summed_correctly(): void
    {
        $user = $this->customer();
        $first = Product::factory()->create(['price' => 100000, 'stock' => 10]);
        $second = Product::factory()->create(['price' => 25000, 'stock' => 10]);
        $this->addToCart($user, $first, 2);
        $this->addToCart($user, $second, 3);

        $this->actingAs($user)
            ->post('/checkout', $this->checkoutPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['total' => '275000.00']);
        $this->assertDatabaseHas('products', ['id' => $first->id, 'stock' => 8]);
        $this->assertDatabaseHas('products', ['id' => $second->id, 'stock' => 7]);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_cancelling_an_order_restores_stock(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Pending]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 3,
            'subtotal' => number_format((float) $product->price * 3, 2, '.', ''),
        ]);

        $this->actingAs($this->admin())
            ->patch("/admin/pesanan/{$order->id}/status", ['status' => 'cancelled'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 13]);
    }

    public function test_cancelling_an_order_twice_does_not_restore_stock_twice(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 5]);
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => OrderStatus::Cancelled]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
            'subtotal' => number_format((float) $product->price * 2, 2, '.', ''),
        ]);

        $this->actingAs($this->admin())
            ->patch("/admin/pesanan/{$order->id}/status", ['status' => 'cancelled'])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_buy_now_checkout_page_shows_only_the_selected_item(): void
    {
        $user = $this->customer();
        $first = Product::factory()->create(['name' => 'Produk Biasa', 'price' => 50000, 'stock' => 5]);
        $second = Product::factory()->create(['name' => 'Produk Beli Sekarang', 'price' => 75000, 'stock' => 5]);
        $this->addToCart($user, $first, 1);
        $this->addToCart($user, $second, 1);

        $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $second->id]])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Produk Beli Sekarang')
            ->assertDontSee('Produk Biasa');
    }

    public function test_buy_now_checkout_orders_only_the_selected_item(): void
    {
        $user = $this->customer();
        $first = Product::factory()->create(['price' => 50000, 'stock' => 10]);
        $second = Product::factory()->create(['price' => 75000, 'stock' => 10]);
        $this->addToCart($user, $first, 2);
        $this->addToCart($user, $second, 1);

        $response = $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $second->id]])
            ->post('/checkout', $this->checkoutPayload())
            ->assertRedirect();

        $order = Order::firstOrFail();

        $this->assertEquals('75000.00', $order->total);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $second->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id, 'product_id' => $first->id]);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $first->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('products', ['id' => $first->id, 'stock' => 10]);
        $this->assertDatabaseHas('products', ['id' => $second->id, 'stock' => 9]);

        $response->assertSessionMissing('buy_now');
    }

    public function test_stale_buy_now_intent_falls_back_to_full_cart(): void
    {
        $user = $this->customer();
        $first = Product::factory()->create(['name' => 'Produk A', 'price' => 50000, 'stock' => 5]);
        $this->addToCart($user, $first, 1);

        $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => 999]])
            ->get('/checkout')
            ->assertOk()
            ->assertSee('Produk A')
            ->assertSessionMissing('buy_now');
    }

    public function test_customer_cannot_view_another_users_order(): void
    {
        $otherUser = $this->customer();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($this->customer())
            ->get("/pesanan/{$order->id}")
            ->assertNotFound();
    }

    public function test_customer_can_view_their_own_order_detail(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 5]);
        $order = Order::factory()->create(['user_id' => $user->id, 'total' => '50000.00']);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => '50000.00',
            'quantity' => 1,
            'subtotal' => '50000.00',
        ]);

        $this->actingAs($user)
            ->get("/pesanan/{$order->id}")
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Rp 50.000');
    }

    public function test_qris_generate_returns_svg_and_does_not_create_order_yet(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 2);

        $response = $this->actingAs($user)
            ->post('/checkout/qris/generate', $this->checkoutPayload(['payment_method' => 'qris']))
            ->assertOk()
            ->assertJsonStructure(['token', 'svg']);

        $this->assertNotEmpty($response->json('token'));
        $this->assertStringContainsString('<svg', $response->json('svg'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_qris_generate_missing_fields_is_rejected(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product);

        $this->actingAs($user)
            ->post('/checkout/qris/generate', $this->checkoutPayload(['customer_name' => '']))
            ->assertSessionHasErrors('customer_name');
    }

    public function test_qris_confirm_with_relative_signed_url_shows_confirmation_page(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 1);

        $token = 'test-token-123';
        Cache::put("qris:{$token}", [
            'user_id' => $user->id,
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'notes' => null,
            'buy_now_product_id' => null,
            'cart_lines' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], now()->addHour());

        // Build a *relative* signed URL, then request it through a different host to
        // confirm the signature stays valid regardless of the accessing host.
        $relative = URL::signedRoute('checkout.qris.confirm', ['token' => $token], null, false);

        $this->withServerVariables(['HTTP_HOST' => 'random-ngrok-domain.ngrok.app'])
            ->get($relative)
            ->assertOk()
            ->assertSee('Konfirmasi Pembayaran QRIS')
            ->assertSee($product->name)
            ->assertSee('Rp 50.000');

        // Scanning only shows the confirmation page; no order is created yet.
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_qris_process_after_confirm_creates_paid_order(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 1);

        $token = 'test-token-456';
        Cache::put("qris:{$token}", [
            'user_id' => $user->id,
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'notes' => null,
            'buy_now_product_id' => null,
            'cart_lines' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], now()->addHour());

        $this->post("/checkout/qris/process/{$token}")
            ->assertRedirect(route('checkout.qris.success', $token));

        $order = Order::firstOrFail();
        $this->assertEquals(PaymentMethod::Qris, $order->payment_method);
        $this->assertEquals(OrderStatus::Paid, $order->status);
        $this->assertEquals('50000.00', $order->total);
        $this->assertEquals($user->id, $order->user_id);
    }

    public function test_qris_token_can_only_be_used_once(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 1);

        $token = 'single-use-token';
        Cache::put("qris:{$token}", [
            'user_id' => $user->id,
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'notes' => null,
            'buy_now_product_id' => null,
            'cart_lines' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], now()->addHour());

        $this->post("/checkout/qris/process/{$token}")->assertRedirect();
        $this->assertEquals(1, Order::count());

        $this->post("/checkout/qris/process/{$token}")->assertStatus(410);
        $this->assertEquals(1, Order::count());
    }

    public function test_qris_success_page_shows_completed_order(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 1);

        $token = 'success-token';
        Cache::put("qris:{$token}", [
            'user_id' => $user->id,
            'customer_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
            'notes' => null,
            'buy_now_product_id' => null,
            'cart_lines' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ], now()->addHour());

        $this->post("/checkout/qris/process/{$token}")->assertRedirect();

        $order = Order::firstOrFail();

        $this->get("/checkout/qris/success/{$token}")
            ->assertOk()
            ->assertSee('Pembayaran Berhasil')
            ->assertSee($order->order_number)
            ->assertSee('Rp 50.000');
    }

    public function test_qris_status_reports_pending_then_confirmed(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['price' => 50000, 'stock' => 10, 'is_active' => true]);
        $this->addToCart($user, $product, 1);

        $token = $this->actingAs($user)
            ->post('/checkout/qris/generate', $this->checkoutPayload(['payment_method' => 'qris']))
            ->assertOk()
            ->json('token');

        // Still waiting for the scan.
        $this->actingAs($user)
            ->getJson("/checkout/qris/status/{$token}")
            ->assertOk()
            ->assertJson(['status' => 'pending']);

        // A different user must not be able to poll this token.
        $this->actingAs($this->customer())
            ->getJson("/checkout/qris/status/{$token}")
            ->assertForbidden();

        // The scanning device confirms the payment.
        $this->post("/checkout/qris/process/{$token}")->assertRedirect();

        $order = Order::firstOrFail();

        // The desktop now sees the payment was confirmed and gets the order id.
        $this->actingAs($user)
            ->getJson("/checkout/qris/status/{$token}")
            ->assertOk()
            ->assertJson(['status' => 'confirmed', 'order_id' => $order->id]);
    }

    public function test_qris_status_returns_expired_for_unknown_token(): void
    {
        $this->actingAs($this->customer())
            ->getJson('/checkout/qris/status/unknown-token')
            ->assertStatus(410)
            ->assertJson(['status' => 'expired']);
    }
}
