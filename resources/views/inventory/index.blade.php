@extends('layouts.app')

@section('title', 'Inventory - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Inventory'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6" x-data="inventoryManager()">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Search & Action Bar --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-wrap items-center gap-3 w-full lg:w-auto flex-1">
                    <div class="relative flex-1 sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search product code, name, category..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                    </div>

                    <select name="category" onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center justify-between lg:justify-end gap-4">
                    <div class="text-xs text-gray-400 font-medium whitespace-nowrap">
                        Total: <span class="text-gray-700 font-bold">{{ $items->total() }}</span> SKU
                    </div>

                    <button @click="openAddProductModal()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-[13px] font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Produk</span>
                    </button>
                </div>
            </div>

            {{-- Inventory Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                <th class="py-4 px-6">KODE PRODUK</th>
                                <th class="py-4 px-6">NAMA PRODUK</th>
                                <th class="py-4 px-6 text-center">QTY</th>
                                <th class="py-4 px-6">KATEGORI</th>
                                <th class="py-4 px-6">TANGGAL</th>
                                <th class="py-4 px-6 text-center">STOK AKHIR</th>
                                <th class="py-4 px-6 text-center">STATUS</th>
                                <th class="py-4 px-6 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($items as $item)
                                @php
                                    $lastTx = $item->transactions->first();
                                    $txDate = $lastTx ? \Carbon\Carbon::parse($lastTx->transaction_date)->format('d M Y') : $item->created_at->format('d M Y');
                                    $statusBadge = match($item->status) {
                                        'Tersedia' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Menipis'  => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default    => 'bg-red-50 text-red-700 border-red-200',
                                    };
                                    $categoryBadge = match($item->category) {
                                        'Switch'                       => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'Firewall'                     => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'Router'                       => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'Access Point'                 => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'Server'                       => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'SFP / Module', 'SFP Module'   => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'Accessories'                  => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'License', 'License / Software'=> 'bg-violet-50 text-violet-700 border-violet-200',
                                        default                        => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-4 px-6 font-mono font-bold text-gray-900 text-xs">
                                        {{ $item->product_code }}
                                    </td>
                                    <td class="py-4 px-6 font-medium text-gray-900">
                                        {{ $item->product_name }}
                                        @if($item->unit_price > 0)
                                            <div class="text-[11px] text-gray-400">Est. Rp {{ number_format($item->unit_price, 0, ',', '.') }} / {{ $item->unit }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-center font-semibold text-gray-700">
                                        {{ $lastTx ? ($lastTx->type == 'in' ? '+'.$lastTx->qty : '-'.$lastTx->qty) : $item->stock }} {{ $item->unit }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $categoryBadge }}">
                                            {{ $item->category ?: 'Umum' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500 text-xs whitespace-nowrap">
                                        {{ $txDate }}
                                    </td>
                                    <td class="py-4 px-6 text-center font-bold text-gray-900">
                                        {{ $item->stock }} <span class="text-xs text-gray-400 font-normal">{{ $item->unit }}</span>
                                    </td>
                                    <td class="py-4 px-6 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-lg border {{ $statusBadge }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <button @click="openEditModal({{ json_encode($item) }})" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-200 shadow-sm transition">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400 text-sm">
                                        Belum ada produk di dalam inventory.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

            {{-- Modal Add Product --}}
            <template x-teleport="body">
                <div x-show="isAddProductOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isAddProductOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Tambah Produk Baru</h3>
                            <button @click="isAddProductOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Produk (SKU) *</label>
                                    <input type="text" name="product_code" required placeholder="Contoh: SW-CS-9300-24P" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori *</label>
                                    <select name="category" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        <option value="Switch">Switch</option>
                                        <option value="Router">Router</option>
                                        <option value="Firewall">Firewall</option>
                                        <option value="Access Point">Access Point</option>
                                        <option value="Server">Server</option>
                                        <option value="SFP / Module">SFP / Module</option>
                                        <option value="Accessories">Accessories</option>
                                        <option value="License">License / Software</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Produk / Perangkat *</label>
                                <input type="text" name="product_name" required placeholder="Contoh: Cisco Catalyst 9300 24-Port PoE+" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Stok Awal *</label>
                                    <input type="number" name="stock" min="0" value="0" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Satuan *</label>
                                    <input type="text" name="unit" value="Unit" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" name="unit_price" min="0" value="0" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keterangan / Spesifikasi</label>
                                <textarea name="description" rows="2" placeholder="Spesifikasi ringkas perangkat..." 
                                          class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isAddProductOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">Simpan Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Modal Barang Masuk --}}
            <template x-teleport="body">
                <div x-show="isStockInOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isStockInOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Catat Barang Masuk</h3>
                            <button @click="isStockInOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('inventory.stock-in') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Produk / Perangkat *</label>
                                <select name="inventory_item_id" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($items as $it)
                                        <option value="{{ $it->id }}">{{ $it->product_code }} - {{ $it->product_name }} (Stok: {{ $it->stock }} {{ $it->unit }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jumlah Masuk (Qty) *</label>
                                    <input type="number" name="qty" min="1" value="1" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Masuk *</label>
                                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. PO / Surat Jalan / Vendor</label>
                                <input type="text" name="reference_no" placeholder="Contoh: PO-BPT-2026-088 / Restock Distributor" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" placeholder="Keterangan kondisi fisik barang, nomor seri, dll..." 
                                          class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isStockInOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">Simpan Barang Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Modal Barang Keluar --}}
            <template x-teleport="body">
                <div x-show="isStockOutOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isStockOutOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Catat Barang Keluar (Alokasi Project)</h3>
                            <button @click="isStockOutOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('inventory.stock-out') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Produk / Perangkat *</label>
                                <select name="inventory_item_id" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($items as $it)
                                        <option value="{{ $it->id }}">{{ $it->product_code }} - {{ $it->product_name }} (Tersedia: {{ $it->stock }} {{ $it->unit }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jumlah Keluar (Qty) *</label>
                                    <input type="number" name="qty" min="1" value="1" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Keluar *</label>
                                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Project / Klien / Keperluan</label>
                                <input type="text" name="reference_no" placeholder="Contoh: Project SD-WAN Adira Finance / POC Siloam" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" placeholder="Nama teknisi pengambil / PIC delivery..." 
                                          class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isStockOutOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">Simpan Barang Keluar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Modal Edit Product --}}
            <template x-teleport="body">
                <div x-show="isEditProductOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isEditProductOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900">Edit Produk Inventory</h3>
                            <button @click="isEditProductOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form :action="'/inventory/' + editForm.id" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kode Produk (SKU) *</label>
                                    <input type="text" name="product_code" x-model="editForm.product_code" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori *</label>
                                    <select name="category" x-model="editForm.category" required class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        <option value="Switch">Switch</option>
                                        <option value="Router">Router</option>
                                        <option value="Firewall">Firewall</option>
                                        <option value="Access Point">Access Point</option>
                                        <option value="Server">Server</option>
                                        <option value="SFP / Module">SFP / Module</option>
                                        <option value="Accessories">Accessories</option>
                                        <option value="License">License / Software</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Produk / Perangkat *</label>
                                <input type="text" name="product_name" x-model="editForm.product_name" required 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jumlah Stok *</label>
                                    <input type="number" name="stock" min="0" x-model="editForm.stock" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Satuan *</label>
                                    <input type="text" name="unit" x-model="editForm.unit" required 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" name="unit_price" min="0" x-model="editForm.unit_price" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keterangan / Spesifikasi</label>
                                <textarea name="description" rows="2" x-model="editForm.description" placeholder="Spesifikasi ringkas perangkat..." 
                                          class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isEditProductOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">Perbarui Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

<script>
    function inventoryManager() {
        return {
            isAddProductOpen: false,
            isStockInOpen: false,
            isStockOutOpen: false,
            isEditProductOpen: false,

            editForm: {
                id: null,
                product_code: '',
                product_name: '',
                category: 'Switch',
                stock: 0,
                unit: 'Unit',
                unit_price: 0,
                description: ''
            },

            openAddProductModal() {
                this.isAddProductOpen = true;
            },
            openEditModal(item) {
                this.editForm = {
                    id: item.id,
                    product_code: item.product_code || '',
                    product_name: item.product_name || '',
                    category: item.category || 'Switch',
                    stock: item.stock || 0,
                    unit: item.unit || 'Unit',
                    unit_price: item.unit_price || 0,
                    description: item.description || ''
                };
                this.isEditProductOpen = true;
            },
            openStockInModal() {
                this.isStockInOpen = true;
            },
            openStockOutModal() {
                this.isStockOutOpen = true;
            }
        }
    }
</script>
@endsection
