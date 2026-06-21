<div class="py-6 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Riwayat Log Stok</h1>
                <p class="mt-1 text-sm text-slate-505">Pantau seluruh aktivitas keluar masuk stok barang secara mendetail.</p>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <div class="bg-white p-5 rounded-2xl border border-[#EBE6DC]/80 shadow-sm flex flex-col md:flex-row items-center gap-4 justify-between">
            <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <!-- Search Box -->
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.602 10.602Z" />
                        </svg>
                    </span>
                    <input 
                        type="text" 
                        wire:model.live="search"
                        placeholder="Cari nama produk..." 
                        class="block w-full pl-10 pr-3 py-2 border border-[#EBE6DC] rounded-xl text-sm bg-[#FAF8F5] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#7D7463] focus:border-[#7D7463] transition-all placeholder-slate-400 shadow-sm"
                    />
                </div>

                <!-- Dropdown Type Filter -->
                <div class="w-full md:w-48">
                    <select 
                        wire:model.live="filterType"
                        class="block w-full rounded-xl border-[#EBE6DC] shadow-sm focus:border-[#7D7463] focus:ring focus:ring-[#7D7463]/20 text-sm bg-[#FAF8F5] focus:bg-white"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="masuk">Masuk</option>
                        <option value="keluar">Keluar</option>
                    </select>
                </div>
            </div>

            @if($search || $filterType)
                <button 
                    wire:click="resetFilters" 
                    class="text-sm text-[#7D7463] hover:text-[#6D6556] font-medium transition"
                >
                    Reset Filter
                </button>
            @endif
        </div>

        <!-- Stock Logs Table Card -->
        <div class="bg-white rounded-2xl border border-[#EBE6DC]/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#EFEAE0] border-b border-[#EBE6DC] text-[10px] font-bold text-[#7D7463] uppercase tracking-wider">
                            <th class="py-4 px-6">Tanggal / Waktu</th>
                            <th class="py-4 px-6">Nama Produk</th>
                            <th class="py-4 px-6">Kategori</th>
                            <th class="py-4 px-6 text-center">Tipe</th>
                            <th class="py-4 px-6 text-center">Jumlah</th>
                            <th class="py-4 px-6">Alasan</th>
                            <th class="py-4 px-6">Admin / Pengguna</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EBE6DC]/40 text-sm text-slate-700">
                        @forelse($stockLogs as $log)
                            <tr class="hover:bg-[#EFEAE0]/20 transition duration-150">
                                <td class="py-4 px-6 text-slate-500 font-medium">
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">
                                    {{ $log->product->name ?? 'Produk Dihapus' }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($log->product && $log->product->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#EFEAE0]/85 text-[#7D7463] border border-[#EBE6DC]/40">
                                            {{ $log->product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($log->type === 'masuk')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                            Keluar
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-bold">
                                    @if($log->type === 'masuk')
                                        <span class="text-emerald-600">
                                            +{{ $log->quantity }}
                                        </span>
                                    @else
                                        <span class="text-rose-600">
                                            -{{ $log->quantity }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-medium">
                                    {{ $log->reason }}
                                </td>
                                <td class="py-4 px-6 text-slate-500 font-medium">
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        {{ $log->user->name ?? 'System' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <span class="font-medium text-slate-500">Tidak ada riwayat log stok</span>
                                        @if($search || $filterType)
                                            <span class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau filter Anda.</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($stockLogs->hasPages())
                <div class="px-6 py-4 border-t border-[#EBE6DC]/40 bg-[#FAF8F5]">
                    {{ $stockLogs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
