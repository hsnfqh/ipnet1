<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehouseItem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Memastikan database telah termigrasi & terisi data dummy awal
     */
    protected function ensureTableReady(): void
    {
        if (!Schema::hasTable('warehouse_items')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'WarehouseSeeder', '--force' => true]);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Helper untuk menghitung metrik umum inventaris
     */
    protected function getMetrics(): array
    {
        $this->ensureTableReady();

        return [
            'total_items'       => WarehouseItem::count(),
            'ready_stock'       => WarehouseItem::where('status', 'Tersedia')->count(),
            'backup_spare'      => WarehouseItem::where('category', 'Backup / Spare')->count(),
            'active_poc'        => WarehouseItem::where('status', 'POC / Pinjaman')->count(),
            'disti_loan'        => WarehouseItem::where('category', 'Pinjaman')->where('loan_source_type', 'disti')->count(),
            'internal_loan'     => WarehouseItem::where('category', 'Pinjaman')->where('loan_source_type', 'ipnet')->count(),
            'customer_deployed' => WarehouseItem::where('location_type', 'Customer')->count(),
            'warehouse_stock'   => WarehouseItem::where('location_type', 'Warehouse IPNET')->count(),
            'maintenance'       => WarehouseItem::where('status', 'Maintenance')->count(),
        ];
    }

    /**
     * MENU UTAMA 1: Dashboard Eksekutif & Ringkasan Inventaris
     */
    public function dashboard(Request $request)
    {
        $this->ensureTableReady();

        $metrics = $this->getMetrics();

        // Statistik breakdown per Jenis Barang
        $byType = WarehouseItem::select('item_type', DB::raw('count(*) as total'))
            ->groupBy('item_type')
            ->orderByDesc('total')
            ->get();

        // Statistik breakdown per Unit Kerja IPNET 1-5 (dari barang pinjaman/proyek)
        $byWorkUnit = WarehouseItem::select('ipnet_work_unit', DB::raw('count(*) as total'))
            ->whereNotNull('ipnet_work_unit')
            ->groupBy('ipnet_work_unit')
            ->orderBy('ipnet_work_unit')
            ->get();

        // 6 Perangkat Terkini
        $recentItems = WarehouseItem::with('creator')->latest()->take(6)->get();

        // Perangkat Sedang Uji Coba / POC Aktif
        $pocItems = WarehouseItem::where('status', 'POC / Pinjaman')->latest()->take(5)->get();

        // Perangkat Pinjaman Distributor
        $distiLoans = WarehouseItem::where('category', 'Pinjaman')
            ->where('loan_source_type', 'disti')
            ->latest()
            ->take(5)
            ->get();

        return view('warehouse.dashboard', compact(
            'metrics',
            'byType',
            'byWorkUnit',
            'recentItems',
            'pocItems',
            'distiLoans'
        ));
    }

    /**
     * MENU UTAMA 2: Master Data Barang (Katalog Lengkap)
     */
    public function index(Request $request)
    {
        $this->ensureTableReady();

        $query = WarehouseItem::with('creator');

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->where('item_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('product_number', 'like', "%{$search}%")
                  ->orWhere('po_number', 'like', "%{$search}%")
                  ->orWhere('contract_number', 'like', "%{$search}%")
                  ->orWhere('poc_customer_name', 'like', "%{$search}%")
                  ->orWhere('disti_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('item_type') && $request->item_type !== 'Semua') {
            $query->where('item_type', $request->item_type);
        }

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_type') && $request->location_type !== 'Semua') {
            $query->where('location_type', $request->location_type);
        }

        $items = $query->latest()->paginate(15)->withQueryString();
        $metrics = $this->getMetrics();

        $itemTypes      = WarehouseItem::getItemTypes();
        $componentTypes = WarehouseItem::getComponentTypes();
        $cableTypes     = WarehouseItem::getCableTypes();
        $workUnits      = WarehouseItem::getWorkUnits();
        $categories     = WarehouseItem::getCategories();
        $statuses       = WarehouseItem::getStatuses();
        $locations      = ['Warehouse IPNET', 'Customer'];

        return view('warehouse.index', compact(
            'items', 
            'metrics',
            'itemTypes',
            'componentTypes',
            'cableTypes',
            'workUnits',
            'categories',
            'statuses',
            'locations'
        ));
    }

    /**
     * MENU UTAMA 3: Kategori dan Satuan / Komponen
     */
    public function categories(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // Rekap Jenis Barang
        $itemTypes = WarehouseItem::select('item_type', DB::raw('count(*) as count'))
            ->groupBy('item_type')
            ->pluck('count', 'item_type')
            ->toArray();

        // Rekap Tipe Komponen
        $componentTypes = WarehouseItem::select('component_type', DB::raw('count(*) as count'))
            ->whereNotNull('component_type')
            ->groupBy('component_type')
            ->pluck('count', 'component_type')
            ->toArray();

        // Rekap Tipe Kabel
        $cableTypes = WarehouseItem::select('cable_type', DB::raw('count(*) as count'))
            ->whereNotNull('cable_type')
            ->groupBy('cable_type')
            ->pluck('count', 'cable_type')
            ->toArray();

        // Rekap Kategori
        $categoryTypes = WarehouseItem::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return view('warehouse.categories', compact(
            'metrics',
            'itemTypes',
            'componentTypes',
            'cableTypes',
            'categoryTypes'
        ));
    }

    /**
     * TRANSAKSI INVENTORI 1: Barang Masuk (Penerimaan PO / Vendor / Return)
     */
    public function barangMasuk(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseItem::with('creator');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('item_code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('po_number', 'like', "%{$s}%")
                  ->orWhere('disti_name', 'like', "%{$s}%");
            });
        }

        // Tampilkan barang yang berlokasi di gudang atau memiliki PO/disti
        $items = $query->latest()->paginate(15)->withQueryString();

        return view('warehouse.barang_masuk', compact('items', 'metrics'));
    }

    /**
     * TRANSAKSI INVENTORI: Barang Keluar (Implementasi Proyek & Peminjaman / POC)
     */
    public function barangKeluar(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $baseQuery = WarehouseItem::where(function($q) {
            $q->where('location_type', 'Customer')
              ->orWhere('status', 'POC / Pinjaman')
              ->orWhere('category', 'Pinjaman')
              ->orWhereNotNull('contract_number');
        });

        // Tab counts
        $counts = [
            'all'            => (clone $baseQuery)->count(),
            'proyek'         => (clone $baseQuery)->where('location_type', 'Customer')->where('status', '!=', 'POC / Pinjaman')->count(),
            'poc'            => (clone $baseQuery)->where('status', 'POC / Pinjaman')->count(),
            'pinjaman_disti' => (clone $baseQuery)->where('category', 'Pinjaman')->where('loan_source_type', 'disti')->count(),
            'pinjaman_ipnet' => (clone $baseQuery)->where('category', 'Pinjaman')->where('loan_source_type', 'ipnet')->count(),
        ];

        $query = WarehouseItem::with('creator')
            ->where(function($q) {
                $q->where('location_type', 'Customer')
                  ->orWhere('status', 'POC / Pinjaman')
                  ->orWhere('category', 'Pinjaman')
                  ->orWhereNotNull('contract_number');
            });

        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'proyek') {
                $query->where('location_type', 'Customer')->where('status', '!=', 'POC / Pinjaman');
            } elseif ($request->type === 'poc') {
                $query->where('status', 'POC / Pinjaman');
            } elseif ($request->type === 'pinjaman_disti') {
                $query->where('category', 'Pinjaman')->where('loan_source_type', 'disti');
            } elseif ($request->type === 'pinjaman_ipnet') {
                $query->where('category', 'Pinjaman')->where('loan_source_type', 'ipnet');
            }
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('item_code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('contract_number', 'like', "%{$s}%")
                  ->orWhere('poc_customer_name', 'like', "%{$s}%")
                  ->orWhere('disti_name', 'like', "%{$s}%")
                  ->orWhere('ipnet_pic_name', 'like', "%{$s}%")
                  ->orWhere('customer_pic', 'like', "%{$s}%");
            });
        }

        $items = $query->latest()->paginate(15)->withQueryString();
        $availableItems = WarehouseItem::where('location_type', 'Warehouse IPNET')->orderBy('name')->get();
        $workUnits = WarehouseItem::getWorkUnits();

        return view('warehouse.barang_keluar', compact('items', 'metrics', 'counts', 'availableItems', 'workUnits'));
    }

    /**
     * Alokasikan Pengeluaran Barang / Peminjaman
     */
    public function allocateOut(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'warehouse_item_id'   => 'required|exists:warehouse_items,id',
            'allocation_type'     => 'required|string|in:proyek,poc,pinjaman_disti,pinjaman_ipnet',
            'recipient_name'      => 'required|string|max:255',
            'pic_name'            => 'nullable|string|max:150',
            'pic_phone'           => 'nullable|string|max:50',
            'sales_name'          => 'nullable|string|max:150',
            'duration'            => 'nullable|string|max:100',
            'reference_number'    => 'nullable|string|max:100',
            'delivery_address'    => 'nullable|string',
            'ipnet_work_unit'     => 'nullable|string|max:50',
            'notes'               => 'nullable|string',
        ]);

        $item = WarehouseItem::findOrFail($validated['warehouse_item_id']);

        if ($validated['allocation_type'] === 'poc') {
            $item->location_type      = 'Customer';
            $item->status             = 'POC / Pinjaman';
            $item->category           = 'Pinjaman';
            $item->poc_customer_name  = $validated['recipient_name'];
            $item->poc_pic_name       = $validated['pic_name'];
            $item->poc_pic_phone      = $validated['pic_phone'];
            $item->poc_sales_name     = $validated['sales_name'];
            $item->poc_duration       = $validated['duration'];
            if (!empty($validated['reference_number'])) {
                $item->contract_number = $validated['reference_number'];
            }
            if (!empty($validated['delivery_address'])) {
                $item->customer_address = $validated['delivery_address'];
            }
        } elseif ($validated['allocation_type'] === 'pinjaman_disti') {
            $item->location_type      = 'Customer';
            $item->status             = 'POC / Pinjaman';
            $item->category           = 'Pinjaman';
            $item->loan_source_type   = 'disti';
            $item->disti_name         = $validated['recipient_name'];
            $item->disti_pic          = $validated['pic_name'];
            $item->disti_phone        = $validated['pic_phone'];
            $item->disti_duration     = $validated['duration'];
            if (!empty($validated['delivery_address'])) {
                $item->disti_address = $validated['delivery_address'];
            }
            if (!empty($validated['reference_number'])) {
                $item->contract_number = $validated['reference_number'];
            }
        } elseif ($validated['allocation_type'] === 'pinjaman_ipnet') {
            $item->location_type      = 'Warehouse IPNET';
            $item->status             = 'POC / Pinjaman';
            $item->category           = 'Pinjaman';
            $item->loan_source_type   = 'ipnet';
            $item->ipnet_pic_name     = $validated['pic_name'] ?: $validated['recipient_name'];
            $item->ipnet_phone        = $validated['pic_phone'];
            $item->ipnet_duration     = $validated['duration'];
            $item->ipnet_work_unit    = $validated['recipient_name'] ?: ($validated['ipnet_work_unit'] ?: 'IPNET 1');
            if (!empty($validated['reference_number'])) {
                $item->contract_number = $validated['reference_number'];
            }
        } else {
            // Proyek Pelanggan
            $item->location_type       = 'Customer';
            $item->status              = 'Terpasang';
            $item->customer_pic        = $validated['pic_name'];
            $item->customer_phone      = $validated['pic_phone'];
            $item->customer_sales_name = $validated['sales_name'];
            if (!empty($validated['delivery_address'])) {
                $item->customer_address = $validated['delivery_address'];
            }
            if (!empty($validated['reference_number'])) {
                $item->contract_number = $validated['reference_number'];
            }
        }

        if (!empty($validated['notes'])) {
            $prefix = "[Pengeluaran " . date('d/m/Y') . "]: ";
            $item->notes = $item->notes ? ($item->notes . "\n" . $prefix . $validated['notes']) : ($prefix . $validated['notes']);
        }

        $item->save();

        return redirect()->route('warehouse.transactions.out')->with('success', "Pengeluaran perangkat {$item->item_code} ({$item->name}) berhasil dicatat & dialokasikan!");
    }

    /**
     * Kembalikan Perangkat ke Gudang (Return to Warehouse)
     */
    public function returnToWarehouse(Request $request, WarehouseItem $item)
    {
        $this->ensureTableReady();

        $rack = $request->input('warehouse_rack', 'Rak Utama A-01');

        $item->location_type = 'Warehouse IPNET';
        $item->warehouse_rack = $rack;
        $item->status = 'Tersedia';
        if ($request->filled('notes')) {
            $prefix = "[Kembali ke Gudang " . date('d/m/Y') . "]: ";
            $item->notes = $item->notes ? ($item->notes . "\n" . $prefix . $request->notes) : ($prefix . $request->notes);
        }
        $item->save();

        return redirect()->route('warehouse.transactions.out')->with('success', "Perangkat {$item->item_code} ({$item->name}) berhasil dikembalikan ke Gudang IPNET (Rak: {$rack})!");
    }

    /**
     * TRANSAKSI INVENTORI 3: Peminjaman & POC (Distributor & Customer)
     */
    public function peminjamanPoc(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseItem::with('creator')
            ->where(function($q) {
                $q->where('category', 'Pinjaman')
                  ->orWhere('status', 'POC / Pinjaman');
            });

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('item_code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('poc_customer_name', 'like', "%{$s}%")
                  ->orWhere('disti_name', 'like', "%{$s}%")
                  ->orWhere('ipnet_pic_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'poc') {
                $query->where('status', 'POC / Pinjaman');
            } elseif ($request->type === 'disti') {
                $query->where('loan_source_type', 'disti');
            } elseif ($request->type === 'internal') {
                $query->where('loan_source_type', 'ipnet');
            }
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        return view('warehouse.peminjaman_poc', compact('items', 'metrics'));
    }

    /**
     * TRANSAKSI INVENTORI 4: Stock Opname & Audit Rak Gudang
     */
    public function stockOpname(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // Rekapitulasi per Rak Gudang
        $racks = WarehouseItem::select('warehouse_rack', DB::raw('count(*) as count'))
            ->whereNotNull('warehouse_rack')
            ->groupBy('warehouse_rack')
            ->orderBy('warehouse_rack')
            ->get();

        $query = WarehouseItem::with('creator')->where('location_type', 'Warehouse IPNET');

        if ($request->filled('rack') && $request->rack !== 'all') {
            $query->where('warehouse_rack', $request->rack);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('item_code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('warehouse_rack', 'like', "%{$s}%");
            });
        }

        $categories = WarehouseItem::getCategories();
        $items = $query->latest()->paginate(15)->withQueryString();

        return view('warehouse.stock_opname', compact('items', 'racks', 'metrics', 'categories'));
    }

    /**
     * MONITORING & LAPORAN 1: Laporan Inventori
     */
    public function reports(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // Breakdown per Kategori
        $byCategory = WarehouseItem::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->get();

        // Breakdown per Status
        $byStatus = WarehouseItem::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Breakdown per Jenis
        $byType = WarehouseItem::select('item_type', DB::raw('count(*) as total'))
            ->groupBy('item_type')
            ->orderByDesc('total')
            ->get();

        // Breakdown per Unit Kerja IPNET 1-5
        $byUnit = WarehouseItem::select('ipnet_work_unit', DB::raw('count(*) as total'))
            ->whereNotNull('ipnet_work_unit')
            ->groupBy('ipnet_work_unit')
            ->orderBy('ipnet_work_unit')
            ->get();

        $items = WarehouseItem::with('creator')->latest()->take(50)->get();

        return view('warehouse.reports', compact(
            'metrics',
            'byCategory',
            'byStatus',
            'byType',
            'byUnit',
            'items'
        ));
    }

    /**
     * MONITORING & LAPORAN 2: Riwayat Pergerakan Stok
     */
    public function history(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // Mengambil seluruh log pembaruan dan pembuatan perangkat
        $items = WarehouseItem::with('creator')
            ->latest('updated_at')
            ->paginate(20);

        return view('warehouse.history', compact('items', 'metrics'));
    }

    /**
     * Simpan Data Perangkat Baru
     */
    public function store(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'brand'               => 'nullable|string|max:100',
            'item_type'           => 'required|string|max:100',
            'component_type'      => 'nullable|string|max:100',
            'cable_type'          => 'nullable|string|max:100',
            'serial_number'       => 'nullable|string|max:100',
            'product_number'      => 'nullable|string|max:100',
            'po_number'           => 'nullable|string|max:100',
            'contract_number'     => 'nullable|string|max:100',
            'category'            => 'required|string|max:50',
            'loan_source_type'    => 'nullable|string|in:disti,ipnet',
            'disti_name'          => 'nullable|string|max:255',
            'disti_address'       => 'nullable|string',
            'disti_pic'           => 'nullable|string|max:150',
            'disti_phone'         => 'nullable|string|max:50',
            'disti_duration'      => 'nullable|string|max:100',
            'ipnet_pic_name'      => 'nullable|string|max:150',
            'ipnet_work_unit'     => 'nullable|string|max:50',
            'ipnet_phone'         => 'nullable|string|max:50',
            'ipnet_duration'      => 'nullable|string|max:100',
            'location_type'       => 'required|string|in:Warehouse IPNET,Customer',
            'warehouse_rack'      => 'nullable|string|max:100',
            'customer_address'    => 'nullable|string',
            'customer_pic'        => 'nullable|string|max:150',
            'customer_phone'      => 'nullable|string|max:50',
            'customer_sales_name' => 'nullable|string|max:150',
            'status'              => 'required|string|max:50',
            'poc_customer_name'   => 'nullable|string|max:255',
            'poc_pic_name'        => 'nullable|string|max:150',
            'poc_pic_phone'       => 'nullable|string|max:50',
            'poc_sales_name'      => 'nullable|string|max:150',
            'poc_duration'        => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        $validated['item_code'] = WarehouseItem::generateItemCode();
        $validated['created_by'] = auth()->id();

        $item = WarehouseItem::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perangkat baru berhasil ditambahkan ke inventaris!',
                'data'    => $item,
            ]);
        }

        return redirect()->back()->with('success', "Barang {$item->item_code} berhasil didaftarkan ke inventaris!");
    }

    /**
     * Perbarui Data Perangkat
     */
    public function update(Request $request, WarehouseItem $item)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'brand'               => 'nullable|string|max:100',
            'item_type'           => 'required|string|max:100',
            'component_type'      => 'nullable|string|max:100',
            'cable_type'          => 'nullable|string|max:100',
            'serial_number'       => 'nullable|string|max:100',
            'product_number'      => 'nullable|string|max:100',
            'po_number'           => 'nullable|string|max:100',
            'contract_number'     => 'nullable|string|max:100',
            'category'            => 'required|string|max:50',
            'loan_source_type'    => 'nullable|string|in:disti,ipnet',
            'disti_name'          => 'nullable|string|max:255',
            'disti_address'       => 'nullable|string',
            'disti_pic'           => 'nullable|string|max:150',
            'disti_phone'         => 'nullable|string|max:50',
            'disti_duration'      => 'nullable|string|max:100',
            'ipnet_pic_name'      => 'nullable|string|max:150',
            'ipnet_work_unit'     => 'nullable|string|max:50',
            'ipnet_phone'         => 'nullable|string|max:50',
            'ipnet_duration'      => 'nullable|string|max:100',
            'location_type'       => 'required|string|in:Warehouse IPNET,Customer',
            'warehouse_rack'      => 'nullable|string|max:100',
            'customer_address'    => 'nullable|string',
            'customer_pic'        => 'nullable|string|max:150',
            'customer_phone'      => 'nullable|string|max:50',
            'customer_sales_name' => 'nullable|string|max:150',
            'status'              => 'required|string|max:50',
            'poc_customer_name'   => 'nullable|string|max:255',
            'poc_pic_name'        => 'nullable|string|max:150',
            'poc_pic_phone'       => 'nullable|string|max:50',
            'poc_sales_name'      => 'nullable|string|max:150',
            'poc_duration'        => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        $item->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data perangkat berhasil diperbarui!',
                'data'    => $item->fresh(),
            ]);
        }

        return redirect()->back()->with('success', "Data barang {$item->item_code} berhasil diperbarui!");
    }

    /**
     * Hapus Data Perangkat
     */
    public function destroy(WarehouseItem $item)
    {
        $code = $item->item_code;
        $item->delete();

        return redirect()->back()->with('success', "Barang {$code} berhasil dihapus dari inventaris.");
    }
}
