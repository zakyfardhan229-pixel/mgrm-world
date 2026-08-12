<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create(['role' => UserRole::Customer]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_cart(): void
    {
        $this->get('/keranjang')->assertRedirect('/login');
    }

    public function test_customer_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($this->customer())
            ->post('/keranjang', ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_the_same_product_increments_quantity(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)->post('/keranjang', ['product_id' => $product->id, 'quantity' => 2]);
        $this->actingAs($user)->post('/keranjang', ['product_id' => $product->id, 'quantity' => 3]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_cannot_add_more_than_available_stock(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->actingAs($this->customer())
            ->post('/keranjang', ['product_id' => $product->id, 'quantity' => 5])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cannot_add_inactive_or_nonexistent_product(): void
    {
        $inactive = Product::factory()->create(['is_active' => false]);

        $this->actingAs($this->customer())
            ->post('/keranjang', ['product_id' => $inactive->id, 'quantity' => 1])
            ->assertNotFound();
    }

    public function test_customer_can_update_cart_quantity(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->patch("/keranjang/{$cartItem->id}", ['quantity' => 7])
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'quantity' => 7]);
    }

    public function test_customer_can_remove_cart_item(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create();
        $cartItem = CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->delete("/keranjang/{$cartItem->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_customer_cannot_update_quantity_beyond_stock(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['stock' => 2]);
        $cartItem = CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->patch("/keranjang/{$cartItem->id}", ['quantity' => 3])
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'quantity' => 1]);
    }

    public function test_buy_now_redirects_to_checkout_and_sets_intent(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->customer())
            ->post('/keranjang', ['product_id' => $product->id, 'quantity' => 2, 'buy_now' => '1'])
            ->assertRedirect(route('checkout.index'));

        $response->assertSessionHas('buy_now', ['product_id' => $product->id]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_removing_buy_now_item_clears_buy_now_intent(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create();
        $cartItem = CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $response = $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $product->id]])
            ->delete("/keranjang/{$cartItem->id}")
            ->assertRedirect();

        $response->assertSessionMissing('buy_now');
    }

    public function test_customer_cannot_modify_another_users_cart_item(): void
    {
        $otherUser = $this->customer();
        $product = Product::factory()->create();
        $foreignItem = CartItem::create(['user_id' => $otherUser->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($this->customer())
            ->patch("/keranjang/{$foreignItem->id}", ['quantity' => 3])
            ->assertNotFound();

        $this->assertDatabaseHas('cart_items', ['id' => $foreignItem->id, 'quantity' => 1]);
    }
}
