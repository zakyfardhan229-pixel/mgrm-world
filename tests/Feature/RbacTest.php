<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
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

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->customer())
            ->get('/admin')
            ->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/admin')
            ->assertStatus(200);
    }

    public function test_customer_can_access_shop_and_their_own_orders_pages(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)->get('/toko')->assertStatus(200);
        $this->actingAs($customer)->get('/keranjang')->assertStatus(200);
        $this->actingAs($customer)->get('/pesanan')->assertStatus(200);
    }

    public function test_newly_registered_users_default_to_customer_role(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isAdmin());
        $this->assertEquals(UserRole::Customer, $user->role);
    }
}