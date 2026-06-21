<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Inventaris Produk</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola data produk, stok, dan harga penjualan Anda secara real-time.</p>
            </div>
            <div>
                <button 
                    wire:click="create"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 gap-2"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Produk
                </button>
            </div>
        </div>

        <!-- Success Flash Message -->
        @if (session()->has('message'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, 4000)"
                class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg flex items-center justify-between shadow-sm transition-opacity duration-300"
            >
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="font-medium text-sm">{{ session('message') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        <!-- Filter & Search Section -->
        <div class="mb-6 bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col md:flex-row items-center gap-4">
            <div class="relative w-full md:w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Cari nama produk..." 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-250 rounded-lg text-sm bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400"
                />
            </div>
            @if($search)
                <button 
                    wire:click="$set('search', '')" 
                    class="text-sm text-gray-500 hover:text-indigo-600 font-medium transition"
                >
                    Clear Search
                </button>
            @endif
        </div>

        <!-- Inventory Table Card -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Kategori</th>
                            <th class="py-4 px-6">Nama Produk</th>
                            <th class="py-4 px-6">Deskripsi</th>
                            <th class="py-4 px-6 text-center">Stok</th>
                            <th class="py-4 px-6 text-right">Harga Beli</th>
                            <th class="py-4 px-6 text-right">Harga Jual</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-xs text-gray-400">#{{ $product->id }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-800 border border-indigo-100/50">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $product->name }}</td>
                                <td class="py-4 px-6 text-gray-500 max-w-xs truncate" title="{{ $product->description }}">
                                    {{ $product->description ?: '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $product->stock > 10 ? 'bg-emerald-50 text-emerald-800' : 'bg-rose-50 text-rose-800' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-medium text-gray-600">
                                    Rp {{ number_format($product->purchase_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right font-medium text-indigo-600">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            wire:click="edit({{ $product->id }})"
                                            class="p-1.5 text-gray-400 hover:text-indigo-600 rounded hover:bg-gray-100 transition-colors"
                                            title="Edit Produk"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                        <button 
                                            wire:click="delete({{ $product->id }})"
                                            onclick="confirm('Apakah Anda yakin ingin menghapus produk ini?') || event.stopImmediatePropagation()"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 rounded hover:bg-gray-100 transition-colors"
                                            title="Hapus Produk"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                        <span class="font-medium text-gray-500">Tidak ada produk ditemukan</span>
                                        @if($search)
                                            <span class="text-xs text-gray-400">Coba ubah kata kunci pencarian Anda.</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Modal (Alpine.js integration for transitions) -->
    <div 
        x-data="{ show: @entangle('isOpen') }" 
        x-show="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 backdrop-blur-sm transition-opacity"
            @click="@this.closeModal()"
        ></div>

        <!-- Modal Wrapper -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100"
            >
                <!-- Modal Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-150 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button 
                        wire:click="closeModal" 
                        class="text-gray-400 hover:text-gray-600 transition"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form wire:submit.prevent="store" class="p-6 space-y-4">
                    <!-- Category Selection -->
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Kategori Produk <span class="text-rose-500">*</span></label>
                        <select 
                            wire:model="category_id" 
                            id="category_id" 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('category_id') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Product Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            id="name" 
                            placeholder="Contoh: Asus ROG Ally"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('name') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        />
                        @error('name')
                            <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea 
                            wire:model="description" 
                            id="description" 
                            rows="3" 
                            placeholder="Keterangan lengkap produk..."
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('description') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                        ></textarea>
                        @error('description')
                            <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Grid Layout for Stock and Prices -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Stock -->
                        <div>
                            <label for="stock" class="block text-sm font-semibold text-gray-700 mb-1">Stok <span class="text-rose-500">*</span></label>
                            <input 
                                type="number" 
                                min="0"
                                wire:model="stock" 
                                id="stock" 
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('stock') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                            />
                            @error('stock')
                                <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-1">Harga Beli <span class="text-rose-500">*</span></label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input 
                                    type="number" 
                                    min="0"
                                    wire:model="purchase_price" 
                                    id="purchase_price" 
                                    class="block w-full rounded-lg border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('purchase_price') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                />
                            </div>
                            @error('purchase_price')
                                <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Selling Price -->
                        <div>
                            <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-1">Harga Jual <span class="text-rose-500">*</span></label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input 
                                    type="number" 
                                    min="0"
                                    wire:model="selling_price" 
                                    id="selling_price" 
                                    class="block w-full rounded-lg border-gray-300 pl-9 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm @error('selling_price') border-rose-300 focus:border-rose-500 focus:ring-rose-500 @enderror"
                                />
                            </div>
                            @error('selling_price')
                                <span class="text-rose-600 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Modal Actions / Footer -->
                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg text-sm bg-white hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-gray-300"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-sm shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
