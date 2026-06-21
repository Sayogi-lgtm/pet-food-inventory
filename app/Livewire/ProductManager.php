<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;

    // Search and Pagination parameters
    public $search = '';
    public $showLowStock = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'showLowStock' => ['except' => false],
    ];
    
    // Form fields
    public $productId = null;
    public $category_id = '';
    public $name = '';
    public $description = '';
    public $stock = 0;
    public $purchase_price = 0;
    public $selling_price = 0;
    public $expired_at = '';

    // Modal state
    public $isOpen = false;
    public $isEditMode = false;

    // Validation rules
    protected $rules = [
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|min:3|max:255',
        'description' => 'nullable|string',
        'stock' => 'required|integer|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0|gte:purchase_price',
        'expired_at' => 'nullable|date',
    ];

    protected $messages = [
        'category_id.required' => 'Kategori wajib dipilih.',
        'category_id.exists' => 'Kategori tidak valid.',
        'name.required' => 'Nama produk wajib diisi.',
        'name.min' => 'Nama produk minimal 3 karakter.',
        'stock.required' => 'Stok wajib diisi.',
        'stock.integer' => 'Stok harus berupa angka bulat.',
        'stock.min' => 'Stok tidak boleh negatif.',
        'purchase_price.required' => 'Harga beli wajib diisi.',
        'purchase_price.numeric' => 'Harga beli harus berupa angka.',
        'purchase_price.min' => 'Harga beli tidak boleh negatif.',
        'selling_price.required' => 'Harga jual wajib diisi.',
        'selling_price.numeric' => 'Harga jual harus berupa angka.',
        'selling_price.min' => 'Harga jual tidak boleh negatif.',
        'selling_price.gte' => 'Harga jual tidak boleh kurang dari harga beli.',
        'expired_at.date' => 'Tanggal kedaluwarsa harus berupa format tanggal yang valid.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingShowLowStock()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->productId = null;
        $this->category_id = '';
        $this->name = '';
        $this->description = '';
        $this->stock = 0;
        $this->purchase_price = 0;
        $this->selling_price = 0;
        $this->expired_at = '';
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        $oldStock = 0;
        $isNewProduct = true;

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $oldStock = $product->stock;
            $isNewProduct = false;

            $product->update([
                'category_id' => $this->category_id,
                'name' => $this->name,
                'description' => $this->description,
                'stock' => $this->stock,
                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
                'expired_at' => $this->expired_at ?: null,
            ]);
        } else {
            $product = Product::create([
                'category_id' => $this->category_id,
                'name' => $this->name,
                'description' => $this->description,
                'stock' => $this->stock,
                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
                'expired_at' => $this->expired_at ?: null,
            ]);
        }

        // Handle Stock Logging
        if ($isNewProduct) {
            if ($product->stock > 0) {
                \App\Models\StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $product->stock,
                    'reason' => 'Stok Awal Produk',
                ]);
            }
        } else {
            $difference = $this->stock - $oldStock;
            if ($difference > 0) {
                \App\Models\StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $difference,
                    'reason' => 'Restock',
                ]);
            } elseif ($difference < 0) {
                \App\Models\StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'keluar',
                    'quantity' => abs($difference),
                    'reason' => 'Koreksi Stok',
                ]);
            }
        }

        session()->flash('message', $isNewProduct ? 'Produk berhasil ditambahkan!' : 'Produk berhasil diperbarui!');

        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->category_id = $product->category_id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->stock = $product->stock;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->expired_at = $product->expired_at ? $product->expired_at->format('Y-m-d') : '';
        
        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        session()->flash('message', 'Produk berhasil dihapus!');
    }

    public function logout(\App\Livewire\Actions\Logout $logout)
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        $query = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%');

        if ($this->showLowStock) {
            $query->where('stock', '<', 5);
        }

        $products = $query->orderBy('id', 'asc')->paginate(10);

        $categories = Category::all();

        // Calculate summary indicators for the dashboard (independent of search filters)
        $totalProducts = Product::count();
        $totalStock = Product::sum('stock');
        $totalAsset = Product::selectRaw('SUM(stock * purchase_price) as total_asset')->value('total_asset') ?? 0;
        $totalProfit = Product::selectRaw('SUM(stock * (selling_price - purchase_price)) as total_profit')->value('total_profit') ?? 0;

        return view('livewire.product-manager', [
            'products' => $products,
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'totalStock' => $totalStock,
            'totalAsset' => $totalAsset,
            'totalProfit' => $totalProfit,
        ])->layout('layouts.app');
    }
}
