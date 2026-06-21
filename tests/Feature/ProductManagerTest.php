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
}
