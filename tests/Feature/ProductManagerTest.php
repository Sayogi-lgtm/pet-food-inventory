<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed some categories
        Category::create(['name' => 'Electronics']);
        Category::create(['name' => 'Books']);
    }

    public function test_unauthenticated_user_cannot_access_products_page()
    {
        $response = $this->get(route('products'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_products_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('products'));
        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\ProductManager::class);
    }

    public function test_user_can_create_product()
    {
        $user = User::factory()->create();
        $category = Category::first();

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('category_id', $category->id)
            ->set('name', 'New Test Product')
            ->set('description', 'Test Description')
            ->set('stock', 10)
            ->set('purchase_price', 1000)
            ->set('selling_price', 1500)
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isOpen', false);

        $this->assertDatabaseHas('products', [
            'name' => 'New Test Product',
            'stock' => 10,
            'purchase_price' => 1000,
            'selling_price' => 1500,
        ]);
    }

    public function test_validation_prevents_invalid_product_data()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('name', '') // invalid
            ->set('stock', -5) // invalid (negative)
            ->set('purchase_price', -100) // invalid (negative)
            ->set('selling_price', -50) // invalid (negative)
            ->call('store')
            ->assertHasErrors([
                'category_id' => 'required',
                'name' => 'required',
                'stock' => 'min',
                'purchase_price' => 'min',
                'selling_price' => 'min',
            ]);
    }

    public function test_selling_price_must_be_gte_purchase_price()
    {
        $user = User::factory()->create();
        $category = Category::first();

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('category_id', $category->id)
            ->set('name', 'Underpriced Product')
            ->set('stock', 5)
            ->set('purchase_price', 1000)
            ->set('selling_price', 500) // less than purchase price
            ->call('store')
            ->assertHasErrors(['selling_price' => 'gte']);
    }

    public function test_user_can_edit_product()
    {
        $user = User::factory()->create();
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Original Product',
            'description' => 'Original Desc',
            'stock' => 10,
            'purchase_price' => 100,
            'selling_price' => 150,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->call('edit', $product->id)
            ->assertSet('name', 'Original Product')
            ->set('name', 'Updated Product')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);
    }

    public function test_user_can_delete_product()
    {
        $user = User::factory()->create();
        $category = Category::first();
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'To Delete',
            'description' => 'Desc',
            'stock' => 10,
            'purchase_price' => 100,
            'selling_price' => 150,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->call('delete', $product->id);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_realtime_search_works()
    {
        $user = User::factory()->create();
        $category = Category::first();
        
        Product::create([
            'category_id' => $category->id,
            'name' => 'Apple',
            'stock' => 10,
            'purchase_price' => 10,
            'selling_price' => 15,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Banana',
            'stock' => 10,
            'purchase_price' => 10,
            'selling_price' => 15,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('search', 'Apple')
            ->assertSee('Apple')
            ->assertDontSee('Banana');
    }

    public function test_stock_logs_are_created_on_product_create_and_update()
    {
        $user = User::factory()->create();
        $category = Category::first();

        // 1. Test creation log
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('category_id', $category->id)
            ->set('name', 'Logged Product')
            ->set('stock', 15)
            ->set('purchase_price', 100)
            ->set('selling_price', 150)
            ->call('store')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Logged Product')->first();
        $this->assertNotNull($product);

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'masuk',
            'quantity' => 15,
            'reason' => 'Stok Awal Produk',
        ]);

        // 2. Test edit increase (Restock)
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->call('edit', $product->id)
            ->set('stock', 25) // increase by 10
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Restock',
        ]);

        // 3. Test edit decrease (Koreksi Stok)
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->call('edit', $product->id)
            ->set('stock', 20) // decrease by 5
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('stock_logs', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 5,
            'reason' => 'Koreksi Stok',
        ]);
    }

    public function test_low_stock_filter_and_badge()
    {
        $user = User::factory()->create();
        $category = Category::first();

        // 1. Create a product with normal stock
        $normalProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Normal Stock Product',
            'stock' => 10,
            'purchase_price' => 100,
            'selling_price' => 150,
        ]);

        // 2. Create a product with low stock (<= 4)
        $lowProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Low Stock Product',
            'stock' => 3,
            'purchase_price' => 100,
            'selling_price' => 150,
        ]);

        // 3. Test filter off (both should be visible, and low stock should have alert text)
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->assertSee('Normal Stock Product')
            ->assertSee('Low Stock Product')
            ->assertSee('3 (Stok Menipis)')
            // 4. Test filter on (only low stock should be visible)
            ->set('showLowStock', true)
            ->assertSee('Low Stock Product')
            ->assertDontSee('Normal Stock Product');
    }

    public function test_product_expiry_dates_and_badges()
    {
        $user = User::factory()->create();
        $category = Category::first();

        // 1. Test creation with expired_at
        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->set('category_id', $category->id)
            ->set('name', 'Expired product soon')
            ->set('stock', 10)
            ->set('purchase_price', 100)
            ->set('selling_price', 150)
            ->set('expired_at', today()->addDays(15)->format('Y-m-d'))
            ->call('store')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Expired product soon')->first();
        $this->assertNotNull($product);
        $this->assertEquals(today()->addDays(15)->format('Y-m-d'), $product->expired_at->format('Y-m-d'));

        // 2. Test visual rendering in Livewire
        // Create an already expired product
        Product::create([
            'category_id' => $category->id,
            'name' => 'Past Expiry Product',
            'stock' => 10,
            'purchase_price' => 100,
            'selling_price' => 150,
            'expired_at' => today()->subDays(5),
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->assertSee('Expired product soon')
            ->assertSee('Hampir Expired')
            ->assertSee('Past Expiry Product')
            ->assertSee('KEDALUWARSA');
    }

    public function test_dashboard_summary_indicators()
    {
        $user = User::factory()->create();
        $category = Category::first();

        // 1. Create two products with varied stock and prices
        Product::create([
            'category_id' => $category->id,
            'name' => 'Prod A',
            'stock' => 10,
            'purchase_price' => 1000,
            'selling_price' => 1500,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Prod B',
            'stock' => 5,
            'purchase_price' => 2000,
            'selling_price' => 3000,
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\ProductManager::class)
            ->assertViewHas('totalProducts', 2)
            ->assertViewHas('totalStock', 15)
            ->assertViewHas('totalAsset', 20000)
            ->assertViewHas('totalProfit', 10000)
            ->assertSee('2')
            ->assertSee('15')
            ->assertSee('Rp 20.000')
            ->assertSee('Rp 10.000');
    }
}
