<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StockLogIndexTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product1;
    protected $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Admin John']);
        
        $cat = Category::create(['name' => 'Pets Food']);
        
        $this->product1 = Product::create([
            'category_id' => $cat->id,
            'name' => 'Kibble Cat',
            'stock' => 20,
            'purchase_price' => 10,
            'selling_price' => 15,
        ]);

        $this->product2 = Product::create([
            'category_id' => $cat->id,
            'name' => 'Bone Chew Dog',
            'stock' => 10,
            'purchase_price' => 12,
            'selling_price' => 18,
        ]);

        // Seed some stock logs
        StockLog::create([
            'product_id' => $this->product1->id,
            'user_id' => $this->user->id,
            'type' => 'masuk',
            'quantity' => 20,
            'reason' => 'Stok Awal Produk',
        ]);

        StockLog::create([
            'product_id' => $this->product2->id,
            'user_id' => $this->user->id,
            'type' => 'keluar',
            'quantity' => 2,
            'reason' => 'Koreksi Stok',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_stock_logs_page()
    {
        $response = $this->get(route('stock-logs'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_stock_logs_page()
    {
        $response = $this->actingAs($this->user)->get(route('stock-logs'));
        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\StockLogIndex::class);
    }

    public function test_renders_stock_logs_with_details()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\StockLogIndex::class)
            ->assertSee('Kibble Cat')
            ->assertSee('Bone Chew Dog')
            ->assertSee('Pets Food')
            ->assertSee('Masuk')
            ->assertSee('Keluar')
            ->assertSee('+20')
            ->assertSee('-2')
            ->assertSee('Stok Awal Produk')
            ->assertSee('Koreksi Stok')
            ->assertSee('Admin John');
    }

    public function test_can_search_by_product_name()
    {
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\StockLogIndex::class)
            ->set('search', 'Kibble')
            ->assertSee('Kibble Cat')
            ->assertDontSee('Bone Chew Dog');
    }

    public function test_can_filter_by_type()
    {
        // Filter 'masuk'
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\StockLogIndex::class)
            ->set('filterType', 'masuk')
            ->assertSee('Kibble Cat')
            ->assertDontSee('Bone Chew Dog');

        // Filter 'keluar'
        Livewire::actingAs($this->user)
            ->test(\App\Livewire\StockLogIndex::class)
            ->set('filterType', 'keluar')
            ->assertSee('Bone Chew Dog')
            ->assertDontSee('Kibble Cat');
    }
}
