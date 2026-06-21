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
    
    // Form fields
    public $productId = null;
    public $category_id = '';
    public $name = '';
    public $description = '';
    public $stock = 0;
    public $purchase_price = 0;
    public $selling_price = 0;

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
    ];

    public function updatingSearch()
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

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'category_id' => $this->category_id,
                'name' => $this->name,
                'description' => $this->description,
                'stock' => $this->stock,
                'purchase_price' => $this->purchase_price,
                'selling_price' => $this->selling_price,
            ]
        );

        session()->flash('message', $this->productId ? 'Produk berhasil diperbarui!' : 'Produk berhasil ditambahkan!');

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
        
        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        session()->flash('message', 'Produk berhasil dihapus!');
    }

    public function render()
    {
        $products = Product::with('category')
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view('livewire.product-manager', [
            'products' => $products,
            'categories' => $categories,
        ])->layout('layouts.app');
    }
}
