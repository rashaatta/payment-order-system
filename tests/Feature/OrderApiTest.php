<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        // Clear permission cache properly using the service container
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create(['name' => 'view all orders']);
        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'update orders']);
        Permission::create(['name' => 'delete orders']);

        $adminRole = Role::create(['name' => 'admin']);
        $customerRole = Role::create(['name' => 'customer']);
        $adminRole->givePermissionTo(['view all orders', 'create orders', 'view orders', 'update orders', 'delete orders']);
        $customerRole->givePermissionTo(['create orders', 'view orders', 'update orders', 'delete orders']);
        $this->user->assignRole('admin');
        $this->user->assignRole('customer');
    }


    /** @test */
    public function a_user_can_create_an_order()
    {
        $data = [
            'product_name' => 'Test Product',
            'quantity' => 3,
            'price' => 100.50,
            'user_id' => $this->user->id
        ];

        // Act: send POST request as an authenticated user
        $response = $this->actingAs($this->user, 'api')->postJson('/api/v1/orders?status=', $data);
        // Assert: check if the response status is 201 and order is created
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', [
            'product_name' => 'Test Product',
            'quantity' => 3,
            'price' => 100.50,
            'status' => 'Pending',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function a_user_can_update_an_order_status()
    {
        // Arrange: create a test order
        $order = Order::factory()->create(['status' => 'Pending', 'user_id' => $this->user->id]);

        // Act: send PATCH request to update the order status
        $response = $this->actingAs($this->user, 'api')
            ->patchJson("/api/v1/orders/{$order->id}/status", ['status' => 'Paid']);

        // Assert: check if the response is 200 and the status is updated
        $response->assertStatus(200);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Paid',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function a_user_can_list_their_orders()
    {
        // Arrange: create 3 orders for the authenticated user
        Order::factory()->count(1)->create(['user_id' => $this->user->id]);

        // Check if the orders are created in the database
        $this->assertEquals(1, $this->user->orders()->count());

        // Act: send GET request to list the orders
        $response = $this->actingAs($this->user, 'api')->getJson('/api/v1/orders?status=');


        // Assert: check if the response contains exactly 3 orders
        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /** @test */
    public function a_user_can_filter_orders_by_status()
    {
        // Arrange: create orders with different statuses
        Order::factory()->create(['status' => 'Pending', 'user_id' => $this->user->id]);
        Order::factory()->create(['status' => 'Paid', 'user_id' => $this->user->id]);

        // Act: send GET request to filter orders by status
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/orders?status=Paid');

        // Assert: check if only the 'Paid' order is returned
        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['status' => 'Paid']);
    }
}
