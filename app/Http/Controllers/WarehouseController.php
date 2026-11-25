<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\WarehouseItem;
use App\Models\WarehouseProduct;
use App\Models\WarehouseCategory;
use App\Models\WarehouseUnit;
use App\Models\WarehouseSupplier;
use App\Models\WarehouseLocation;
use App\Models\WarehousePurchaseOrder;
use App\Models\WarehousePoItem;
use App\Models\WarehouseStockOpname;
use App\Models\WarehouseSoItem;
use App\Models\WarehouseDeliveryOrder;
use App\Models\WarehouseDoItem;
use App\Models\User;

class WarehouseController extends Controller
{
    /**
     * Membersihkan seluruh data inventaris & transaksi warehouse secara menyeluruh
     * (Hanya modul warehouse, modul lain tidak disentuh)
     */
    public function performWarehouseDataCleanup(): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $tables = [
                'warehouse_so_items',
                'warehouse_stock_opnames',
                'warehouse_do_items',
                'warehouse_delivery_orders',
                'warehouse_po_items',
                'warehouse_purchase_orders',
                'warehouse_products',
                'warehouse_items',
                'warehouse_suppliers',
                'warehouse_categories',
                'warehouse_units',
            ];
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Warehouse cleanup error: ' . $e->getMessage());
        }
    }

    /**
     * Route manual untuk membersihkan semua data warehouse
     */
    public function clearAllWarehouseData()
    {
        $this->performWarehouseDataCleanup();
        cache()->forever('warehouse_cleanup_done_2026', true);
        return redirect()->route('warehouse.dashboard')->with('success', 'Seluruh data inventaris & katalog warehouse berhasil dibersihkan!');
    }

    /**
     * Memastikan struktur database siap tanpa auto-seeding dummy yang tidak diinginkan
     */
    protected function ensureTableReady(): void
    {
        try {
            if (Schema::hasTable('warehouse_items')) {
                if (!Schema::hasColumn('warehouse_items', 'device_status')) {
                    Schema::table('warehouse_items', function (Blueprint $table) {
                        $table->string('device_status', 50)->default('Aktif')->after('status');
                    });
                }
                if (!Schema::hasColumn('warehouse_items', 'image')) {
                    Schema::table('warehouse_items', function (Blueprint $table) {
                        $table->string('image')->nullable()->after('device_status');
                    });
                }
            }
            if (Schema::hasTable('warehouse_suppliers')) {
                if (!Schema::hasColumn('warehouse_suppliers', 'contacts')) {
                    Schema::table('warehouse_suppliers', function (Blueprint $table) {
                        $table->text('contacts')->nullable()->after('email');
                    });
                }
                if (!Schema::hasColumn('warehouse_suppliers', 'brands')) {
                    Schema::table('warehouse_suppliers', function (Blueprint $table) {
                        $table->text('brands')->nullable()->after('products_supplied');
                    });
                }
            }
        } catch (\Throwable $e) {}

        if (!Schema::hasTable('warehouse_products')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        // Pastikan Master Data Perangkat Warehouse IPNET selalu tersedia
        $this->ensureDefaultWarehouseItems();

        // Konsolidasi ke 1 Gudang Utama Resmi (IPNET hanya memiliki 1 gudang pusat terpusat)
        try {
            $mainWh = WarehouseLocation::where('code', 'WH-JKT-01')->first() ?? WarehouseLocation::first();
            if ($mainWh) {
                $otherWhIds = WarehouseLocation::where('id', '!=', $mainWh->id)->pluck('id');
                if ($otherWhIds->isNotEmpty()) {
                    WarehouseProduct::whereIn('warehouse_id', $otherWhIds)->update([
                        'warehouse_id' => $mainWh->id,
                        'warehouse_name' => $mainWh->name,
                    ]);
                    WarehousePurchaseOrder::whereIn('warehouse_id', $otherWhIds)->update([
                        'warehouse_id' => $mainWh->id,
                        'warehouse_name' => $mainWh->name,
                    ]);
                    WarehouseDeliveryOrder::whereIn('warehouse_id', $otherWhIds)->update([
                        'warehouse_id' => $mainWh->id,
                        'warehouse_name' => $mainWh->name,
                    ]);
                    WarehouseStockOpname::whereIn('warehouse_id', $otherWhIds)->update([
                        'warehouse_id' => $mainWh->id,
                        'warehouse_name' => $mainWh->name,
                    ]);
                    WarehouseLocation::whereIn('id', $otherWhIds)->delete();
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Memastikan data master perangkat fisik gudang resmi selalu tersedia dan tidak hilang
     */
    protected function ensureDefaultWarehouseItems(): void
    {
        try {
            $defaultItems = [
                [
                    'item_code'           => 'WH-2026-0001',
                    'name'                => 'MX240',
                    'brand'               => 'Juniper Networks',
                    'item_type'           => 'Router',
                    'component_type'      => 'Perangkat Utuh',
                    'serial_number'       => 'JN11CD60CAFC',
                    'product_number'      => 'MX240BASE-AC-HIGH',
                    'po_number'           => '079/2010',
                    'category'            => 'Backup / Spare',
                    'customer_sales_name' => 'Raiza Maindoka',
                    'poc_sales_name'      => 'Raiza Maindoka',
                    'location_type'       => 'Gudang IPNET',
                    'warehouse_rack'      => 'Rak Utama A-01',
                    'status'              => 'Tersedia',
                    'device_status'       => 'Perlu dilakukan Pengecekan',
                    'image'               => 'warehouse/devices/mx240_router_chassis.jpg',
                    'notes'               => 'Universal Edge Router 3D chassis Juniper MX240 AC High Power.',
                ],
                [
                    'item_code'           => 'WH-2026-0002',
                    'name'                => 'EX8200',
                    'brand'               => 'Juniper Networks',
                    'item_type'           => 'Switch',
                    'component_type'      => 'Modul / Card',
                    'serial_number'       => 'BD1711222780',
                    'product_number'      => 'EX8200-48T',
                    'po_number'           => '059/2011',
                    'category'            => 'Backup / Spare',
                    'customer_sales_name' => 'Raiza Maindoka',
                    'poc_sales_name'      => 'Raiza Maindoka',
                    'location_type'       => 'Gudang IPNET',
                    'warehouse_rack'      => 'Rak Utama A-01',
                    'status'              => 'Tersedia',
                    'device_status'       => 'Aktif',
                    'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                    'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200.',
                ],
                [
                    'item_code'           => 'WH-2026-0003',
                    'name'                => 'EX8200',
                    'brand'               => 'Juniper Networks',
                    'item_type'           => 'Switch',
                    'component_type'      => 'Modul / Card',
                    'serial_number'       => 'BD1711192676',
                    'product_number'      => 'EX8200-48T',
                    'po_number'           => '059/2011',
                    'category'            => 'Backup / Spare',
                    'customer_sales_name' => 'Raiza Maindoka',
                    'poc_sales_name'      => 'Raiza Maindoka',
                    'location_type'       => 'Gudang IPNET',
                    'warehouse_rack'      => 'Rak Utama A-01',
                    'status'              => 'Tersedia',
                    'device_status'       => 'Perlu dilakukan Pengecekan',
                    'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                    'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200. Perlu dilakukan pengecekan port.',
                ],
                [
                    'item_code'           => 'WH-2026-0004',
                    'name'                => 'EX8200',
                    'brand'               => 'Juniper Networks',
                    'item_type'           => 'Switch',
                    'component_type'      => 'Modul / Card',
                    'serial_number'       => 'BD1710381623',
                    'product_number'      => 'EX8200-48T',
                    'po_number'           => '059/2011',
                    'category'            => 'Backup / Spare',
                    'customer_sales_name' => 'Nabylla Berlianita',
                    'poc_sales_name'      => 'Nabylla Berlianita',
                    'location_type'       => 'Gudang IPNET',
                    'warehouse_rack'      => 'Rak Utama A-01',
                    'status'              => 'Tersedia',
                    'device_status'       => 'Aktif',
                    'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                    'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200.',
                ],
                [
                    'item_code'           => 'WH-2026-0005',
                    'name'                => 'EX8200',
                    'brand'               => 'Juniper Networks',
                    'item_type'           => 'Switch',
                    'component_type'      => 'Modul / Card',
                    'serial_number'       => 'BB1710452964',
                    'product_number'      => 'EX8200-8XS',
                    'po_number'           => '059/2011',
                    'category'            => 'Stock',
                    'customer_sales_name' => 'Raiza Maindoka',
                    'poc_sales_name'      => 'Raiza Maindoka',
                    'location_type'       => 'Gudang IPNET',
                    'warehouse_rack'      => 'Rak Utama A-01',
                    'status'              => 'Tersedia',
                    'device_status'       => 'Aktif',
                    'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                    'notes'               => 'Line card 8-port 10GbE SFP+ untuk chassis Juniper EX8200.',
                ],
            ];

            foreach ($defaultItems as $itemData) {
                $existing = WarehouseItem::where('item_code', $itemData['item_code'])
                    ->orWhere('serial_number', $itemData['serial_number'])
                    ->first();

                if (!$existing) {
                    WarehouseItem::create($itemData);
                } else {
                    $updates = [];
                    if (empty($existing->customer_sales_name) || in_array($existing->customer_sales_name, ['-', 'Others', 'Lainnya'])) {
                        $updates['customer_sales_name'] = $itemData['customer_sales_name'];
                    }
                    if (empty($existing->poc_sales_name) || in_array($existing->poc_sales_name, ['-', 'Others', 'Lainnya'])) {
                        $updates['poc_sales_name'] = $itemData['poc_sales_name'];
                    }
                    if (empty($existing->image)) {
                        $updates['image'] = $itemData['image'];
                    }
                    if (empty($existing->device_status)) {
                        $updates['device_status'] = $itemData['device_status'];
                    }
                    if (!empty($updates)) {
                        $existing->update($updates);
                    }
                }
            }

            if (WarehouseSupplier::where('name', 'PT. SISTECH KHARISMA')->count() === 0) {
                WarehouseSupplier::create([
                    'name'           => 'PT. SISTECH KHARISMA',
                    'code'           => 'SUP-SISTECH-01',
                    'contact_person' => 'Alfin & Cindy',
                    'phone'          => '+622135056683 / 3807668',
                    'email'          => 'sales@sistech.co.id',
                    'address'        => 'Jl. Ir. H Juanda IV No. 3B-C Jakarta-Pusat 10120',
                    'status'         => 'Aktif',
                    'brands'         => json_encode(['Juniper', 'Cisco', 'Palo Alto']),
                    'contacts'       => json_encode([
                        ['name' => 'Alfin & Cindy', 'phone' => '+622135056683 / 3807668', 'email' => 'sales@sistech.co.id']
                    ]),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ensureDefaultWarehouseItems error: ' . $e->getMessage());
        }
    }

    /**
     * Helper untuk menghitung metrik umum sistem warehouse
     */
    protected function getMetrics(): array
    {
        $this->ensureTableReady();

        $totalItemsCount = WarehouseItem::count();
        $totalItemModels = WarehouseItem::distinct('name')->whereNotNull('name')->where('name', '!=', '')->count('name');

        $totalStockUnits = $totalItemsCount > 0 ? $totalItemsCount : (WarehouseProduct::sum('current_stock') ?: 0);
        $totalProducts   = $totalItemModels > 0 ? $totalItemModels : WarehouseProduct::count();

        $totalValuation  = WarehouseProduct::sum(DB::raw('current_stock * unit_price'));
        $lowStockCount   = WarehouseItem::where('device_status', 'like', '%Pengecekan%')->count();
        if ($lowStockCount === 0) {
            $lowStockCount = WarehouseProduct::whereColumn('current_stock', '<=', 'min_stock')->count();
        }

        $totalCategories = WarehouseItem::distinct('category')->whereNotNull('category')->where('category', '!=', '')->count('category');
        if ($totalCategories === 0) {
            $totalCategories = WarehouseCategory::count();
        }

        $totalUnits = WarehouseItem::distinct('component_type')->whereNotNull('component_type')->where('component_type', '!=', '')->count('component_type');
        if ($totalUnits === 0) {
            $totalUnits = WarehouseUnit::count();
        }

        $totalSuppliers = WarehouseSupplier::count();
        if ($totalSuppliers === 0) {
            $totalSuppliers = WarehouseItem::distinct('brand')->whereNotNull('brand')->where('brand', '!=', '')->count('brand');
        }

        return [
            'total_products'    => $totalProducts,
            'total_stock_units' => $totalStockUnits,
            'total_valuation'   => $totalValuation,
            'low_stock_count'   => $lowStockCount,
            'total_categories'  => $totalCategories,
            'total_units'       => $totalUnits,
            'total_suppliers'   => $totalSuppliers,
            'total_warehouses'  => 1,
            'active_po'         => WarehousePurchaseOrder::whereIn('status', ['Diproses', 'Disetujui', 'Diterima Sebagian'])->count(),
            'pending_do'        => WarehouseDeliveryOrder::whereIn('status', ['Draft', 'Siap Dikirim', 'Dalam Pengiriman'])->count(),
            'completed_so'      => WarehouseStockOpname::where('status', 'Selesai')->count(),
            
            // Legacy & physical item metric support (Sesuai Produk & Perangkat)
            'total_items'       => $totalItemsCount,
            'ready_stock'       => WarehouseItem::where('status', 'Tersedia')->count(),
            'backup_spare'      => WarehouseItem::where('category', 'Backup / Spare')->count(),
            'active_poc'        => WarehouseItem::where('status', 'POC / Pinjaman')->count(),
            'disti_loan'        => WarehouseItem::where('category', 'Pinjaman')->where('loan_source_type', 'disti')->count(),
            'internal_loan'     => WarehouseItem::where('category', 'Pinjaman')->where('loan_source_type', 'ipnet')->count(),
            'customer_deployed' => WarehouseItem::where('location_type', 'Customer')->count(),
            'warehouse_stock'   => WarehouseItem::whereIn('location_type', ['Gudang IPNET', 'Warehouse IPNET'])->count(),
            'maintenance'       => WarehouseItem::where('status', 'Maintenance')->count(),
        ];
    }

    // =========================================================================
    // 1. DASHBOARD WAREHOUSE
    // =========================================================================
    public function dashboard(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // 5 Produk dengan stok menipis / perlu pengecekan
        $lowStockProducts = WarehouseItem::where('device_status', 'like', '%Pengecekan%')
            ->select('name', 'product_number as sku', 'warehouse_rack as rack_position', DB::raw('count(*) as current_stock'), DB::raw('1 as min_stock'))
            ->groupBy('name', 'product_number', 'warehouse_rack')
            ->get();
        if ($lowStockProducts->isEmpty()) {
            $lowStockProducts = WarehouseProduct::whereColumn('current_stock', '<=', 'min_stock')
                ->orderBy('current_stock', 'asc')
                ->take(5)
                ->get();
        }

        // 5 Purchase Order Terkini
        $recentPOs = WarehousePurchaseOrder::with(['supplier', 'warehouseLocation'])
            ->latest()
            ->take(5)
            ->get();

        // 5 Delivery Order Terkini
        $recentDOs = WarehouseDeliveryOrder::with(['warehouseLocation'])
            ->latest()
            ->take(5)
            ->get();

        // Breakdown Stok per Kategori (Sinkron dengan Produk & Perangkat untuk Donut Chart & List)
        $categoryBreakdown = WarehouseItem::select('category as name', DB::raw('count(*) as products_sum_current_stock'), DB::raw('count(distinct name) as products_count'))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category')
            ->get();
        if ($categoryBreakdown->isEmpty()) {
            $categoryBreakdown = WarehouseCategory::withCount('products')
                ->withSum('products', 'current_stock')
                ->get();
        }

        // Top Produk dengan Stok Fisik Terbanyak di Rak (Sinkron dengan Produk & Perangkat untuk Bar Chart)
        $topStockProducts = WarehouseItem::select('name', 'product_number as sku', 'warehouse_rack as rack_position', DB::raw('count(*) as current_stock'), DB::raw('1 as min_stock'))
            ->groupBy('name', 'product_number', 'warehouse_rack')
            ->orderBy('current_stock', 'desc')
            ->take(6)
            ->get();
        if ($topStockProducts->isEmpty()) {
            $topStockProducts = WarehouseProduct::orderBy('current_stock', 'desc')
                ->take(6)
                ->get();
        }

        // Gudang Utama Tunggal IPNET
        $primaryWarehouse = WarehouseLocation::where('code', 'WH-JKT-01')->first() 
            ?? WarehouseLocation::first();

        // 6 Produk Baru
        $recentProducts = WarehouseItem::latest()->take(6)->get();
        if ($recentProducts->isEmpty()) {
            $recentProducts = WarehouseProduct::latest()->take(6)->get();
        }

        // Status PO & DO untuk ringkasan operasional logistik
        $poStatusCounts = [
            'Draft'     => WarehousePurchaseOrder::where('status', 'Draft')->count(),
            'Diproses'  => WarehousePurchaseOrder::where('status', 'Diproses')->count(),
            'Disetujui' => WarehousePurchaseOrder::where('status', 'Disetujui')->count(),
            'Selesai'   => WarehousePurchaseOrder::where('status', 'Selesai')->count(),
        ];

        $doStatusCounts = [
            'Draft'            => WarehouseDeliveryOrder::where('status', 'Draft')->count(),
            'Siap Dikirim'     => WarehouseDeliveryOrder::where('status', 'Siap Dikirim')->count(),
            'Dalam Pengiriman' => WarehouseDeliveryOrder::where('status', 'Dalam Pengiriman')->count(),
            'Terkirim'         => WarehouseDeliveryOrder::where('status', 'Terkirim & Diterima')->count(),
        ];

        return view('warehouse.dashboard', compact(
            'metrics',
            'lowStockProducts',
            'recentPOs',
            'recentDOs',
            'categoryBreakdown',
            'topStockProducts',
            'primaryWarehouse',
            'recentProducts',
            'poStatusCounts',
            'doStatusCounts'
        ));
    }

    // =========================================================================
    // 2. PRODUK (MASTER PRODUK & PERANGKAT)
    // =========================================================================
    public function products(Request $request)
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
                  ->orWhere('disti_name', 'like', "%{$search}%")
                  ->orWhere('customer_sales_name', 'like', "%{$search}%")
                  ->orWhere('poc_sales_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('item_type') && $request->item_type !== 'Semua') {
            $query->where('item_type', 'like', '%' . trim($request->item_type) . '%');
        }

        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', 'like', '%' . trim($request->category) . '%');
        }

        if ($request->filled('sales_name') && $request->sales_name !== 'Semua') {
            if ($request->sales_name === 'none' || $request->sales_name === 'Tanpa AM') {
                $query->where(function ($q) {
                    $q->whereNull('customer_sales_name')
                      ->orWhere('customer_sales_name', '')
                      ->orWhere('customer_sales_name', '-');
                })->where(function ($q) {
                    $q->whereNull('poc_sales_name')
                      ->orWhere('poc_sales_name', '')
                      ->orWhere('poc_sales_name', '-');
                });
            } else {
                $sales = trim($request->sales_name);
                $firstName = explode(' ', $sales)[0] ?? '';
                $query->where(function ($q) use ($sales, $firstName) {
                    $q->where('customer_sales_name', $sales)
                      ->orWhere('poc_sales_name', $sales)
                      ->orWhere('customer_sales_name', 'like', "%{$sales}%")
                      ->orWhere('poc_sales_name', 'like', "%{$sales}%");
                    if (strlen($firstName) >= 3) {
                        $q->orWhere('customer_sales_name', 'like', "%{$firstName}%")
                          ->orWhere('poc_sales_name', 'like', "%{$firstName}%");
                    }
                });
            }
        }

        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', 'like', '%' . trim($request->status) . '%');
        }

        if ($request->filled('location_type') && $request->location_type !== 'Semua') {
            $query->where('location_type', 'like', '%' . trim($request->location_type) . '%');
        }

        $items = $query->latest()->paginate(15)->withQueryString();
        $metrics = $this->getMetrics();

        $itemTypes      = WarehouseItem::getItemTypes();
        $componentTypes = WarehouseItem::getComponentTypes();
        $cableTypes     = WarehouseItem::getCableTypes();
        $workUnits      = WarehouseItem::getWorkUnits();
        $categories     = WarehouseItem::getCategories();
        $statuses       = WarehouseItem::getStatuses();
        $locations      = ['Gudang IPNET', 'Customer'];
        $deviceModels   = WarehouseItem::getDeviceModels();
        $deviceBrands   = WarehouseItem::getDeviceBrands();
        $modelStockCounts = WarehouseItem::groupBy('name')->selectRaw('name, count(*) as count')->pluck('count', 'name');

        $salesUsers = User::where('status', 'Active')
            ->where(function($q) {
                $q->whereHas('roles', fn($r) => $r->whereIn('name', ['Sales', 'Account Manager']))
                  ->orWhere('position', 'like', '%Sales%')
                  ->orWhere('position', 'like', '%Account Manager%');
            })
            ->orderBy('name')
            ->get(['id', 'name']);
        if ($salesUsers->isEmpty()) {
            $salesUsers = User::where('status', 'Active')->orderBy('name')->get(['id', 'name']);
        }
        $existingSalesNames = $salesUsers->pluck('name')->toArray();

        // Always ensure official AMs are available in the dropdown
        $standardAms = ['Raiza Maindoka', 'Nabylla Berlianita'];
        foreach ($standardAms as $stdAm) {
            if (!in_array($stdAm, $existingSalesNames)) {
                $salesUsers->push((object)['id' => null, 'name' => $stdAm]);
                $existingSalesNames[] = $stdAm;
            }
        }

        $customAmNames = WarehouseItem::whereNotNull('customer_sales_name')
            ->where('customer_sales_name', '!=', '')
            ->whereNotIn('customer_sales_name', ['Others', 'Lainnya', '-'])
            ->distinct()
            ->pluck('customer_sales_name')
            ->toArray();
        foreach ($customAmNames as $cAm) {
            if (!in_array($cAm, $existingSalesNames)) {
                $salesUsers->push((object)['id' => null, 'name' => $cAm]);
                $existingSalesNames[] = $cAm;
            }
        }
        $customPocAmNames = WarehouseItem::whereNotNull('poc_sales_name')
            ->where('poc_sales_name', '!=', '')
            ->whereNotIn('poc_sales_name', ['Others', 'Lainnya', '-'])
            ->distinct()
            ->pluck('poc_sales_name')
            ->toArray();
        foreach ($customPocAmNames as $pAm) {
            if (!in_array($pAm, $existingSalesNames)) {
                $salesUsers->push((object)['id' => null, 'name' => $pAm]);
                $existingSalesNames[] = $pAm;
            }
        }

        $deviceStatuses = WarehouseItem::getDeviceStatuses();

        return view('warehouse.products.index', compact(
            'items',
            'metrics',
            'itemTypes',
            'componentTypes',
            'cableTypes',
            'workUnits',
            'categories',
            'statuses',
            'deviceStatuses',
            'locations',
            'deviceModels',
            'deviceBrands',
            'modelStockCounts',
            'salesUsers'
        ));
    }

    public function storeProduct(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'                => 'nullable|string|max:255',
            'brand'               => 'nullable|string|max:100',
            'item_type'           => 'nullable|string|max:100',
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
            'location_type'       => 'required|string|in:Gudang IPNET,Warehouse IPNET,Customer',
            'warehouse_rack'      => 'nullable|string|max:100',
            'customer_address'    => 'nullable|string',
            'customer_pic'        => 'nullable|string|max:150',
            'customer_phone'      => 'nullable|string|max:50',
            'customer_sales_name' => 'nullable|string|max:150',
            'status'              => 'required|string|max:50',
            'device_status'       => 'nullable|string|max:50',
            'image'               => 'nullable',
            'poc_customer_name'   => 'nullable|string|max:255',
            'poc_pic_name'        => 'nullable|string|max:150',
            'poc_pic_phone'       => 'nullable|string|max:50',
            'poc_sales_name'      => 'nullable|string|max:150',
            'sales_name'          => 'nullable|string|max:150',
            'poc_duration'        => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        if ($request->filled('sales_name')) {
            $validated['customer_sales_name'] = $request->sales_name;
            $validated['poc_sales_name']      = $request->sales_name;
        } elseif (!empty($validated['customer_sales_name'])) {
            $validated['poc_sales_name']      = $validated['customer_sales_name'];
        } elseif (!empty($validated['poc_sales_name'])) {
            $validated['customer_sales_name'] = $validated['poc_sales_name'];
        }

        $validated['device_status'] = $request->input('device_status', 'Aktif');

        if ($request->hasFile('image')) {
            $validated['image'] = \App\Helpers\FileUploadHelper::storePublicly($request->file('image'), 'warehouse/devices');
        }

        $validated['name'] = !empty($validated['name']) ? $validated['name'] : (!empty($validated['product_number']) ? $validated['product_number'] : (!empty($validated['serial_number']) ? $validated['serial_number'] : 'Perangkat'));
        $validated['brand'] = !empty($validated['brand']) ? $validated['brand'] : '-';
        $validated['item_type'] = !empty($validated['item_type']) ? $validated['item_type'] : 'Perangkat';

        $validated['item_code'] = WarehouseItem::generateItemCode();
        $validated['created_by'] = auth()->id();

        $item = WarehouseItem::create($validated);

        // Sync with WarehouseProduct catalog
        try {
            WarehouseProduct::firstOrCreate(
                ['sku' => $item->item_code],
                [
                    'name'           => $item->name,
                    'brand'          => $item->brand,
                    'category_name'  => $item->category,
                    'rack_position'  => $item->warehouse_rack,
                    'unit_name'      => 'Unit',
                    'current_stock'  => 1,
                    'min_stock'      => 1,
                    'status'         => 'Tersedia',
                    'specifications' => $item->notes,
                ]
            );
        } catch (\Throwable $e) {}

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perangkat baru berhasil didaftarkan ke inventaris!',
                'data'    => $item,
            ]);
        }

        return redirect()->route('warehouse.products.index')->with('success', "Barang {$item->item_code} berhasil didaftarkan ke inventaris!");
    }

    /**
     * Check serial number / barcode existence for bluetooth scanner
     */
    public function scanCheck(Request $request)
    {
        $this->ensureTableReady();
        $query = trim($request->input('query', ''));

        if (empty($query)) {
            return response()->json(['found' => false, 'message' => 'Query kosong']);
        }

        $item = WarehouseItem::where('serial_number', $query)
            ->orWhere('item_code', $query)
            ->first();

        if ($item) {
            return response()->json([
                'found' => true,
                'item'  => [
                    'id'             => $item->id,
                    'item_code'      => $item->item_code,
                    'name'           => $item->name,
                    'brand'          => $item->brand,
                    'item_type'      => $item->item_type,
                    'component_type' => $item->component_type,
                    'serial_number'  => $item->serial_number,
                    'product_number' => $item->product_number,
                    'po_number'      => $item->po_number,
                    'category'       => $item->category,
                    'location_type'  => $item->location_type,
                    'warehouse_rack' => $item->warehouse_rack,
                    'status'         => $item->status,
                    'notes'          => $item->notes,
                ],
                'message' => "Serial Number [{$query}] sudah terdaftar di sistem ({$item->status})!"
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => "Serial Number [{$query}] belum terdaftar (tersedia untuk didaftarkan)."
        ]);
    }

    /**
     * Batch store items scanned via Bluetooth Scanner
     */
    public function batchStoreProduct(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'brand'               => 'nullable|string|max:100',
            'item_type'           => 'required|string|max:100',
            'component_type'      => 'nullable|string|max:100',
            'product_number'      => 'nullable|string|max:100',
            'po_number'           => 'nullable|string|max:100',
            'category'            => 'required|string|max:50',
            'location_type'       => 'required|string|in:Gudang IPNET,Warehouse IPNET,Customer',
            'warehouse_rack'      => 'nullable|string|max:100',
            'status'              => 'required|string|max:50',
            'notes'               => 'nullable|string',
            'serial_numbers'      => 'required|array|min:1',
            'serial_numbers.*'    => 'required|string|max:100',
        ]);

        $createdItems = [];
        $duplicateSns = [];

        DB::beginTransaction();
        try {
            $countThisYear = WarehouseItem::withTrashed()->whereYear('created_at', date('Y'))->count();

            foreach ($validated['serial_numbers'] as $sn) {
                $cleanSn = trim($sn);
                if (empty($cleanSn)) continue;

                // Check duplicate in database
                $existing = WarehouseItem::where('serial_number', $cleanSn)->first();
                if ($existing) {
                    $duplicateSns[] = $cleanSn;
                    continue;
                }

                $countThisYear++;
                $itemCode = 'WH-' . date('Y') . '-' . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

                $item = WarehouseItem::create([
                    'item_code'      => $itemCode,
                    'name'           => $validated['name'],
                    'brand'          => $validated['brand'],
                    'item_type'      => $validated['item_type'],
                    'component_type' => $validated['component_type'] ?? 'Perangkat Utuh',
                    'serial_number'  => $cleanSn,
                    'product_number' => $validated['product_number'],
                    'po_number'      => $validated['po_number'],
                    'category'       => $validated['category'],
                    'location_type'  => $validated['location_type'],
                    'warehouse_rack' => $validated['warehouse_rack'] ?: 'Rak Utama A-01',
                    'status'         => $validated['status'],
                    'notes'          => !empty($validated['notes']) ? $validated['notes'] . " (Scan Bluetooth)" : "Scan Bluetooth",
                    'created_by'     => auth()->id(),
                ]);

                $createdItems[] = $item;
            }

            // Sync with catalog
            if (!empty($createdItems)) {
                $first = $createdItems[0];
                try {
                    $prodSku = $validated['product_number'] ?: $first->name;
                    $prod = WarehouseProduct::firstOrNew(['sku' => $prodSku]);
                    if ($prod->exists) {
                        $prod->increment('current_stock', count($createdItems));
                    } else {
                        $prod->fill([
                            'sku'           => $prodSku,
                            'name'          => $first->name,
                            'brand'         => $first->brand,
                            'category_name' => $first->category,
                            'rack_position' => $first->warehouse_rack,
                            'unit_name'     => 'Unit',
                            'current_stock' => count($createdItems),
                            'min_stock'     => 1,
                            'status'        => 'Tersedia',
                            'specifications'=> $first->notes,
                        ])->save();
                    }
                } catch (\Throwable $e) {}
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'count'      => count($createdItems),
                'duplicates' => $duplicateSns,
                'message'    => count($createdItems) . ' unit berhasil didaftarkan ke inventaris gudang!' . 
                                (!empty($duplicateSns) ? ' (' . count($duplicateSns) . ' SN duplikat dilewati: ' . implode(', ', $duplicateSns) . ')' : ''),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses batch scan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProduct(Request $request, $id)
    {
        $this->ensureTableReady();
        $item = WarehouseItem::findOrFail($id);

        $validated = $request->validate([
            'name'                => 'nullable|string|max:255',
            'brand'               => 'nullable|string|max:100',
            'item_type'           => 'nullable|string|max:100',
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
            'location_type'       => 'required|string|in:Gudang IPNET,Warehouse IPNET,Customer',
            'warehouse_rack'      => 'nullable|string|max:100',
            'customer_address'    => 'nullable|string',
            'customer_pic'        => 'nullable|string|max:150',
            'customer_phone'      => 'nullable|string|max:50',
            'customer_sales_name' => 'nullable|string|max:150',
            'status'              => 'required|string|max:50',
            'device_status'       => 'nullable|string|max:50',
            'image'               => 'nullable',
            'poc_customer_name'   => 'nullable|string|max:255',
            'poc_pic_name'        => 'nullable|string|max:150',
            'poc_pic_phone'       => 'nullable|string|max:50',
            'poc_sales_name'      => 'nullable|string|max:150',
            'sales_name'          => 'nullable|string|max:150',
            'poc_duration'        => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        if ($request->filled('sales_name')) {
            $validated['customer_sales_name'] = $request->sales_name;
            $validated['poc_sales_name']      = $request->sales_name;
        } elseif (!empty($validated['customer_sales_name'])) {
            $validated['poc_sales_name']      = $validated['customer_sales_name'];
        } elseif (!empty($validated['poc_sales_name'])) {
            $validated['customer_sales_name'] = $validated['poc_sales_name'];
        }

        $validated['device_status'] = $request->input('device_status', $item->device_status ?: 'Aktif');

        if ($request->hasFile('image')) {
            $validated['image'] = \App\Helpers\FileUploadHelper::storePublicly($request->file('image'), 'warehouse/devices');
        } elseif ($request->input('remove_image') === '1') {
            $validated['image'] = null;
        }

        if (empty($validated['name'])) {
            $validated['name'] = !empty($validated['product_number']) ? $validated['product_number'] : (!empty($validated['serial_number']) ? $validated['serial_number'] : ($item->name ?: 'Perangkat'));
        }
        if (empty($validated['brand'])) {
            $validated['brand'] = $item->brand ?: '-';
        }
        if (empty($validated['item_type'])) {
            $validated['item_type'] = $item->item_type ?: 'Perangkat';
        }

        $item->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data perangkat berhasil diperbarui!',
                'data'    => $item->fresh(),
            ]);
        }

        return redirect()->route('warehouse.products.index')->with('success', "Data barang {$item->item_code} berhasil diperbarui!");
    }

    public function destroyProduct($id, Request $request = null)
    {
        $this->ensureTableReady();
        $item = WarehouseItem::find($id);
        $prod = WarehouseProduct::find($id);

        $name = $item?->name ?? $prod?->name;
        $code = $item?->item_code ?? $prod?->sku;
        $prodNum = $item?->product_number;

        if ($item) {
            $item->delete();
        }

        if ($prod) {
            $prod->delete();
        } elseif ($name || $code || $prodNum) {
            try {
                // Hapus juga produk katalog yang bersesuaian
                WarehouseProduct::where('sku', $code)
                    ->orWhere('sku', $prodNum)
                    ->orWhere('name', $name)
                    ->delete();
            } catch (\Throwable $e) {}
        }

        if (($request && $request->wantsJson()) || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Barang " . ($code ?: 'perangkat') . " berhasil dihapus dari inventaris."
            ]);
        }

        return redirect()->route('warehouse.products.index')->with('success', "Barang " . ($code ?: 'perangkat') . " berhasil dihapus dari inventaris.");
    }

    // =========================================================================
    // 3. KATEGORI PRODUK
    // =========================================================================
    public function categories(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseCategory::withCount('products')->withSum('products', 'current_stock');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $categories = $query->latest()->paginate(12)->withQueryString();

        return view('warehouse.categories.index', compact('categories', 'metrics'));
    }

    public function storeCategory(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50|unique:warehouse_categories,code',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:50',
            'status'      => 'required|string|in:Aktif,Nonaktif',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'CAT-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 3));
        }

        WarehouseCategory::create($validated);

        return redirect()->route('warehouse.categories.index')->with('success', 'Kategori produk berhasil ditambahkan!');
    }

    public function updateCategory(Request $request, $id)
    {
        $this->ensureTableReady();
        $cat = WarehouseCategory::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:50|unique:warehouse_categories,code,' . $cat->id,
            'description' => 'nullable|string',
            'status'      => 'required|string|in:Aktif,Nonaktif',
        ]);

        $cat->update($validated);

        return redirect()->route('warehouse.categories.index')->with('success', "Kategori {$cat->name} berhasil diperbarui!");
    }

    public function destroyCategory($id)
    {
        $this->ensureTableReady();
        $cat = WarehouseCategory::findOrFail($id);
        $name = $cat->name;
        $cat->delete();

        return redirect()->route('warehouse.categories.index')->with('success', "Kategori {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 4. SATUAN (UNITS)
    // =========================================================================
    public function units(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseUnit::withCount('products');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('short_name', 'like', "%{$s}%");
            });
        }

        $units = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('warehouse.units.index', compact('units', 'metrics'));
    }

    public function storeUnit(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:warehouse_units,code',
            'short_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        WarehouseUnit::create($validated);

        return redirect()->route('warehouse.units.index')->with('success', 'Satuan baru berhasil ditambahkan!');
    }

    public function updateUnit(Request $request, $id)
    {
        $this->ensureTableReady();
        $unit = WarehouseUnit::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:warehouse_units,code,' . $unit->id,
            'short_name'  => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $unit->update($validated);

        return redirect()->route('warehouse.units.index')->with('success', "Satuan {$unit->name} berhasil diperbarui!");
    }

    public function destroyUnit($id)
    {
        $this->ensureTableReady();
        $unit = WarehouseUnit::findOrFail($id);
        $name = $unit->name;
        $unit->delete();

        return redirect()->route('warehouse.units.index')->with('success', "Satuan {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 5. SUPPLIER
    // =========================================================================
    public function suppliers(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseSupplier::withCount(['products', 'purchaseOrders']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('contact_person', 'like', "%{$s}%")
                  ->orWhere('products_supplied', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $suppliers = $query->latest()->paginate(10)->withQueryString();

        return view('warehouse.suppliers.index', compact('suppliers', 'metrics'));
    }

    public function storeSupplier(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => 'nullable|string|max:50|unique:warehouse_suppliers,code',
            'contact_person'    => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:255',
            'email'             => 'nullable|string|max:255',
            'contacts'          => 'nullable|array',
            'brands'            => 'nullable|array',
            'address'           => 'nullable|string',
            'products_supplied' => 'nullable|string|max:500',
            'status'            => 'required|string|in:Aktif,Nonaktif',
            'notes'             => 'nullable|string',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'SUP-' . date('Y') . '-' . rand(100, 999);
        }

        // Process multiple contacts
        if ($request->has('contacts') && is_array($request->contacts)) {
            $cleanedContacts = array_values(array_filter($request->contacts, function($c) {
                return !empty($c['name']) || !empty($c['phone']) || !empty($c['email']);
            }));
            if (!empty($cleanedContacts)) {
                $validated['contacts'] = $cleanedContacts;
                $validated['contact_person'] = collect($cleanedContacts)->pluck('name')->filter()->implode(' & ');
                $validated['phone'] = collect($cleanedContacts)->pluck('phone')->filter()->implode(' / ');
                $validated['email'] = collect($cleanedContacts)->pluck('email')->filter()->first() ?: ($validated['email'] ?? null);
            }
        }

        // Process multiple brands
        if ($request->has('brands') && is_array($request->brands)) {
            $cleanedBrands = array_values(array_filter(array_map('trim', $request->brands)));
            if (!empty($cleanedBrands)) {
                $validated['brands'] = $cleanedBrands;
                $validated['products_supplied'] = implode(', ', $cleanedBrands);
            }
        } elseif (!empty($validated['products_supplied'])) {
            $validated['brands'] = array_values(array_filter(array_map('trim', explode(',', $validated['products_supplied']))));
        }

        WarehouseSupplier::create($validated);

        return redirect()->route('warehouse.suppliers.index')->with('success', 'Supplier / Distributor berhasil didaftarkan!');
    }

    public function updateSupplier(Request $request, $id)
    {
        $this->ensureTableReady();
        $supplier = WarehouseSupplier::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'code'              => 'required|string|max:50|unique:warehouse_suppliers,code,' . $supplier->id,
            'contact_person'    => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:255',
            'email'             => 'nullable|string|max:255',
            'contacts'          => 'nullable|array',
            'brands'            => 'nullable|array',
            'address'           => 'nullable|string',
            'products_supplied' => 'nullable|string|max:500',
            'status'            => 'required|string|in:Aktif,Nonaktif',
            'notes'             => 'nullable|string',
        ]);

        // Process multiple contacts
        if ($request->has('contacts') && is_array($request->contacts)) {
            $cleanedContacts = array_values(array_filter($request->contacts, function($c) {
                return !empty($c['name']) || !empty($c['phone']) || !empty($c['email']);
            }));
            if (!empty($cleanedContacts)) {
                $validated['contacts'] = $cleanedContacts;
                $validated['contact_person'] = collect($cleanedContacts)->pluck('name')->filter()->implode(' & ');
                $validated['phone'] = collect($cleanedContacts)->pluck('phone')->filter()->implode(' / ');
                $validated['email'] = collect($cleanedContacts)->pluck('email')->filter()->first() ?: ($validated['email'] ?? null);
            }
        }

        // Process multiple brands
        if ($request->has('brands') && is_array($request->brands)) {
            $cleanedBrands = array_values(array_filter(array_map('trim', $request->brands)));
            if (!empty($cleanedBrands)) {
                $validated['brands'] = $cleanedBrands;
                $validated['products_supplied'] = implode(', ', $cleanedBrands);
            }
        } elseif (!empty($validated['products_supplied'])) {
            $validated['brands'] = array_values(array_filter(array_map('trim', explode(',', $validated['products_supplied']))));
        }

        $supplier->update($validated);

        return redirect()->route('warehouse.suppliers.index')->with('success', "Data supplier {$supplier->name} berhasil diperbarui!");
    }

    public function destroySupplier($id)
    {
        $this->ensureTableReady();
        $supplier = WarehouseSupplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('warehouse.suppliers.index')->with('success', "Supplier {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 6. WAREHOUSE (MASTER LOKASI GUDANG)
    // =========================================================================
    public function warehouses(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseLocation::withCount(['products', 'purchaseOrders', 'deliveryOrders', 'stockOpnames'])
            ->withSum('products', 'current_stock');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('pic_name', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%");
            });
        }

        $warehouses = $query->latest()->paginate(10)->withQueryString();

        return view('warehouse.warehouses.index', compact('warehouses', 'metrics'));
    }

    public function storeWarehouse(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'nullable|string|max:50|unique:warehouse_locations,code',
            'pic_name'      => 'nullable|string|max:150',
            'pic_phone'     => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'capacity_desc' => 'nullable|string|max:255',
            'status'        => 'required|string|in:Operasional,Penuh,Maintenance',
            'notes'         => 'nullable|string',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'WH-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $validated['name']), 0, 3)) . '-' . rand(10, 99);
        }

        WarehouseLocation::create($validated);

        return redirect()->route('warehouse.warehouses.index')->with('success', 'Lokasi gudang baru berhasil ditambahkan!');
    }

    public function updateWarehouse(Request $request, $id)
    {
        $this->ensureTableReady();
        $wh = WarehouseLocation::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'code'          => 'required|string|max:50|unique:warehouse_locations,code,' . $wh->id,
            'pic_name'      => 'nullable|string|max:150',
            'pic_phone'     => 'nullable|string|max:50',
            'address'       => 'nullable|string',
            'capacity_desc' => 'nullable|string|max:255',
            'status'        => 'required|string|in:Operasional,Penuh,Maintenance',
            'notes'         => 'nullable|string',
        ]);

        $wh->update($validated);

        return redirect()->route('warehouse.warehouses.index')->with('success', "Data gudang {$wh->name} berhasil diperbarui!");
    }

    public function destroyWarehouse($id)
    {
        $this->ensureTableReady();
        $wh = WarehouseLocation::findOrFail($id);
        $name = $wh->name;
        $wh->delete();

        return redirect()->route('warehouse.warehouses.index')->with('success', "Gudang {$name} berhasil dihapus.");
    }

    // =========================================================================
    // 7. PURCHASE ORDER (PO)
    // =========================================================================
    public function purchaseOrders(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehousePurchaseOrder::with(['supplier', 'warehouseLocation', 'items', 'creator']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('po_number', 'like', "%{$s}%")
                  ->orWhere('supplier_name', 'like', "%{$s}%")
                  ->orWhere('warehouse_name', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('supplier_id') && $request->supplier_id !== 'all') {
            $query->where('supplier_id', $request->supplier_id);
        }

        $purchaseOrders = $query->latest()->paginate(10)->withQueryString();

        $suppliers  = WarehouseSupplier::where('status', 'Aktif')->get();
        $warehouses = WarehouseLocation::where('status', 'Operasional')->get();
        $products   = WarehouseProduct::orderBy('name')->get();

        return view('warehouse.purchase_orders.index', compact(
            'purchaseOrders',
            'metrics',
            'suppliers',
            'warehouses',
            'products'
        ));
    }

    public function storePurchaseOrder(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'po_number'     => 'nullable|string|max:50|unique:warehouse_purchase_orders,po_number',
            'po_date'       => 'required|date',
            'supplier_id'   => 'nullable|exists:warehouse_suppliers,id',
            'warehouse_id'  => 'nullable|exists:warehouse_locations,id',
            'expected_date' => 'nullable|date',
            'status'        => 'required|string|in:Draft,Diproses,Disetujui,Diterima Sebagian,Selesai,Dibatalkan',
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:warehouse_products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit'       => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if (empty($validated['po_number'])) {
            $validated['po_number'] = 'PO-IPN-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        $sup = !empty($validated['supplier_id']) ? WarehouseSupplier::find($validated['supplier_id']) : null;
        $wh  = !empty($validated['warehouse_id']) ? WarehouseLocation::find($validated['warehouse_id']) : null;

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalAmount += ($item['quantity'] * $item['unit_price']);
        }

        $po = WarehousePurchaseOrder::create([
            'po_number'      => $validated['po_number'],
            'po_date'        => $validated['po_date'],
            'supplier_id'    => $validated['supplier_id'],
            'supplier_name'  => $sup ? $sup->name : null,
            'warehouse_id'   => $validated['warehouse_id'],
            'warehouse_name' => $wh ? $wh->name : null,
            'expected_date'  => $validated['expected_date'],
            'total_amount'   => $totalAmount,
            'status'         => $validated['status'],
            'notes'          => $validated['notes'],
            'created_by'     => auth()->id(),
        ]);

        foreach ($validated['items'] as $itemData) {
            WarehousePoItem::create([
                'purchase_order_id' => $po->id,
                'product_id'        => $itemData['product_id'] ?? null,
                'product_name'      => $itemData['product_name'],
                'quantity'          => $itemData['quantity'],
                'unit'              => $itemData['unit'],
                'unit_price'        => $itemData['unit_price'],
                'subtotal'          => $itemData['quantity'] * $itemData['unit_price'],
                'received_qty'      => ($validated['status'] === 'Selesai') ? $itemData['quantity'] : 0,
            ]);

            // Jika langsung diset Selesai, tambahkan stok ke produk
            if ($validated['status'] === 'Selesai' && !empty($itemData['product_id'])) {
                WarehouseProduct::where('id', $itemData['product_id'])->increment('current_stock', $itemData['quantity']);
            }
        }

        return redirect()->route('warehouse.purchase_orders.index')->with('success', "Purchase Order {$po->po_number} berhasil dibuat!");
    }

    public function updatePoStatus(Request $request, $id)
    {
        $this->ensureTableReady();
        $po = WarehousePurchaseOrder::with('items')->findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:Draft,Diproses,Disetujui,Diterima Sebagian,Selesai,Dibatalkan',
        ]);

        $oldStatus = $po->status;
        $newStatus = $request->status;

        $po->update(['status' => $newStatus]);

        // Jika berubah jadi 'Selesai', otomatis tambahkan stok produk dan update received_qty
        if ($newStatus === 'Selesai' && $oldStatus !== 'Selesai') {
            foreach ($po->items as $item) {
                $item->update(['received_qty' => $item->quantity]);
                if ($item->product_id) {
                    WarehouseProduct::where('id', $item->product_id)->increment('current_stock', $item->quantity);
                }
            }
        }

        return redirect()->route('warehouse.purchase_orders.index')->with('success', "Status PO {$po->po_number} berhasil diubah ke {$newStatus}!");
    }

    public function destroyPurchaseOrder($id)
    {
        $this->ensureTableReady();
        $po = WarehousePurchaseOrder::findOrFail($id);
        $num = $po->po_number;
        $po->delete();

        return redirect()->route('warehouse.purchase_orders.index')->with('success', "Purchase Order {$num} berhasil dihapus.");
    }

    // =========================================================================
    // 8. STOCK OPNAME
    // =========================================================================
    public function stockOpname(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseStockOpname::with(['warehouseLocation', 'items', 'creator']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('so_number', 'like', "%{$s}%")
                  ->orWhere('warehouse_name', 'like', "%{$s}%")
                  ->orWhere('auditor_name', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $stockOpnames = $query->latest()->paginate(10)->withQueryString();

        $warehouses = WarehouseLocation::where('status', 'Operasional')->get();
        $products   = WarehouseProduct::orderBy('name')->get();

        return view('warehouse.stock_opname.index', compact(
            'stockOpnames',
            'metrics',
            'warehouses',
            'products'
        ));
    }

    public function storeStockOpname(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'so_number'    => 'nullable|string|max:50|unique:warehouse_stock_opnames,so_number',
            'so_date'      => 'required|date',
            'warehouse_id' => 'required|exists:warehouse_locations,id',
            'auditor_name' => 'required|string|max:150',
            'status'       => 'required|string|in:Draft,Menunggu Approval,Selesai',
            'notes'        => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.product_id'       => 'nullable|exists:warehouse_products,id',
            'items.*.product_name'     => 'required|string',
            'items.*.system_stock'     => 'required|integer|min:0',
            'items.*.physical_stock'   => 'required|integer|min:0',
            'items.*.condition_status' => 'required|string',
            'items.*.notes'            => 'nullable|string',
        ]);

        if (empty($validated['so_number'])) {
            $validated['so_number'] = 'SO-' . date('Y-m') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        $wh = WarehouseLocation::find($validated['warehouse_id']);

        $totalChecked = count($validated['items']);
        $totalDiff    = 0;
        foreach ($validated['items'] as $item) {
            $totalDiff += abs($item['physical_stock'] - $item['system_stock']);
        }

        $so = WarehouseStockOpname::create([
            'so_number'           => $validated['so_number'],
            'so_date'             => $validated['so_date'],
            'warehouse_id'        => $validated['warehouse_id'],
            'warehouse_name'      => $wh ? $wh->name : null,
            'auditor_name'        => $validated['auditor_name'],
            'status'              => $validated['status'],
            'notes'               => $validated['notes'],
            'total_items_checked' => $totalChecked,
            'total_discrepancy'   => $totalDiff,
            'created_by'          => auth()->id(),
        ]);

        foreach ($validated['items'] as $itemData) {
            $diff = $itemData['physical_stock'] - $itemData['system_stock'];
            WarehouseSoItem::create([
                'stock_opname_id'  => $so->id,
                'product_id'       => $itemData['product_id'] ?? null,
                'item_code'        => $itemData['product_id'] ? WarehouseProduct::find($itemData['product_id'])->sku : null,
                'product_name'     => $itemData['product_name'],
                'system_stock'     => $itemData['system_stock'],
                'physical_stock'   => $itemData['physical_stock'],
                'difference'       => $diff,
                'condition_status' => $itemData['condition_status'],
                'notes'            => $itemData['notes'] ?? null,
            ]);

            // Jika status langsung diset Selesai, lakukan sinkronisasi stok fisik ke master produk
            if ($validated['status'] === 'Selesai' && !empty($itemData['product_id'])) {
                WarehouseProduct::where('id', $itemData['product_id'])->update(['current_stock' => $itemData['physical_stock']]);
            }
        }

        return redirect()->route('warehouse.stock_opname.index')->with('success', "Dokumen Stock Opname {$so->so_number} berhasil dicatat!");
    }

    public function syncStockOpname(Request $request, $id)
    {
        $this->ensureTableReady();
        $so = WarehouseStockOpname::with('items')->findOrFail($id);

        foreach ($so->items as $item) {
            if ($item->product_id) {
                WarehouseProduct::where('id', $item->product_id)->update(['current_stock' => $item->physical_stock]);
            }
        }

        $so->update(['status' => 'Selesai']);

        return redirect()->route('warehouse.stock_opname.index')->with('success', "Stok sistem berhasil disinkronkan sesuai hasil fisik Stock Opname {$so->so_number}!");
    }

    public function destroyStockOpname($id)
    {
        $this->ensureTableReady();
        $so = WarehouseStockOpname::findOrFail($id);
        $num = $so->so_number;
        $so->delete();

        return redirect()->route('warehouse.stock_opname.index')->with('success', "Dokumen Stock Opname {$num} berhasil dihapus.");
    }

    // =========================================================================
    // 9. DENAH GUDANG (WAREHOUSE FLOOR PLAN & RACK ALLOCATION)
    // =========================================================================
    public function denahGudang(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        // Data alokasi rak dan zona penyimpanan sesuai cetak biru resmi gudang
        $racks = [
            [
                'code'        => 'Rack 1',
                'name'        => 'Rak 1 (Anita)',
                'pic'         => 'Anita',
                'role'        => 'PIC Rak / AM',
                'category'    => 'Modul & Komponen',
                'color'       => 'rose',
                'position'    => 'Sisi Kiri dekat Kardus Susun',
                'description' => 'Penyimpanan modul antarmuka, spare parts, dan perangkat inventaris Anita.',
            ],
            [
                'code'        => 'Rack 2',
                'name'        => 'Rak 2 (Anton & Telco)',
                'pic'         => 'Anton, Telco',
                'role'        => 'PIC Rak / Tim Telco',
                'category'    => 'Kabel & Patch Cord / Telco',
                'color'       => 'blue',
                'position'    => 'Deret Bawah Tengah',
                'description' => 'Penyimpanan kabel FO, patch cord, roll kabel UTP, dan kelengkapan Telco.',
            ],
            [
                'code'        => 'Rack 3',
                'name'        => 'Rak 3 (Raiza, Telco, IPNET 1)',
                'pic'         => 'Raiza, Telco, IPNET 1',
                'role'        => 'Account Manager / Telco / Ops',
                'category'    => 'Switch & Transceiver SFP',
                'color'       => 'emerald',
                'position'    => 'Sisi Kanan Paling Ujung',
                'description' => 'Unit switch access, modul transceiver optik, dan material integrasi proyek.',
            ],
            [
                'code'        => 'Rack 4',
                'name'        => 'Rak 4 (Raiza)',
                'pic'         => 'Raiza Maindoka',
                'role'        => 'Account Manager',
                'category'    => 'Switch Distribution & Line Card',
                'color'       => 'amber',
                'position'    => 'Blok Tengah Depan (Lorong 90cm)',
                'description' => 'Perangkat Switch Juniper EX8200, Line Card, dan Router MX240 milik AM Raiza.',
            ],
            [
                'code'        => 'Rack 5',
                'name'        => 'Rak 5 (Telco)',
                'pic'         => 'Telco',
                'role'        => 'Divisi Telco',
                'category'    => 'Transmisi Gelombang & Radio Link',
                'color'       => 'purple',
                'position'    => 'Blok Tengah Belakang (Lorong 60cm)',
                'description' => 'Perangkat transmisi wireless, microwave, switch telco, dan modem BTS.',
            ],
            [
                'code'        => 'Rack 6',
                'name'        => 'Rak 6 (Widodo)',
                'pic'         => 'Widodo',
                'role'        => 'PIC Rak / Technical Support',
                'category'    => 'Server & Backup Rackmount',
                'color'       => 'indigo',
                'position'    => 'Blok Kanan Depan (Lorong 60cm)',
                'description' => 'Server rackmount, storage appliance, dan unit backup sistem.',
            ],
            [
                'code'        => 'Rack 7',
                'name'        => 'Rak 7 (Raiza & IPNET 1)',
                'pic'         => 'Raiza, IPNET 1',
                'role'        => 'AM & Tim Proyek Lapangan',
                'category'    => 'Buffer Stock & Staging Project',
                'color'       => 'teal',
                'position'    => 'Blok Kanan Belakang (Lorong 90cm)',
                'description' => 'Perangkat siap deploy implementasi proyek pelanggan & cadangan staging.',
            ],
        ];

        $boxZones = [
            [
                'code'        => 'Kardus Pintu',
                'name'        => 'Kardus Susun (Pintu Masuk Gudang)',
                'pic'         => 'Calista, Raiza, Telco',
                'description' => 'Susunan kardus kemasan resmi pabrik (Juniper, Cisco) dan unit baru tiba.',
            ],
            [
                'code'        => 'Kardus Dinding',
                'name'        => 'Kardus Susun (Dinding Atas)',
                'pic'         => 'IPNET 1 & IPNET 2',
                'description' => 'Kardus material terminasi, toolkit teknisi lapangan tim IPNET 1 & 2.',
            ],
            [
                'code'        => 'Kardus Depan',
                'name'        => 'Kardus Susun (Area 2.30m x 2.95m)',
                'pic'         => 'Logistik Gudang',
                'description' => 'Penyimpanan box material besar, accessories rackmount, dan kardus buffer.',
            ],
        ];

        $officeZones = [
            ['name' => 'Meja Admin 1', 'desc' => 'Stasiun kerja administrasi gudang & inventory tracking.'],
            ['name' => 'Meja Admin 2', 'desc' => 'Stasiun kerja verifikasi PO & DO logistik keluar-masuk.'],
            ['name' => 'Meja Admin 3', 'desc' => 'Stasiun kerja supervisor gudang & kontrol stock opname.'],
            ['name' => 'Floor Marking Tape', 'desc' => 'Area staging & transit pergerakan barang (Safety aisle).'],
        ];

        // Fetch warehouse items for quick reference
        $items = WarehouseItem::all();

        return view('warehouse.denah', compact('metrics', 'racks', 'boxZones', 'officeZones', 'items'));
    }

    // =========================================================================
    // 10. DELIVERY ORDER (DO)
    // =========================================================================
    public function deliveryOrders(Request $request)
    {
        $this->ensureTableReady();
        $metrics = $this->getMetrics();

        $query = WarehouseDeliveryOrder::with(['warehouseLocation', 'items', 'creator']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('do_number', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%")
                  ->orWhere('recipient_company', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('courier_name', 'like', "%{$s}%")
                  ->orWhere('tracking_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $deliveryOrders = $query->latest()->paginate(10)->withQueryString();

        $warehouses = WarehouseLocation::where('status', 'Operasional')->get();
        $products   = WarehouseProduct::orderBy('name')->get();
        $availableItems = WarehouseItem::where('status', 'Tersedia')->orderBy('name')->get();

        return view('warehouse.delivery_orders.index', compact(
            'deliveryOrders',
            'metrics',
            'warehouses',
            'products',
            'availableItems'
        ));
    }

    public function storeDeliveryOrder(Request $request)
    {
        $this->ensureTableReady();

        $validated = $request->validate([
            'do_number'         => 'nullable|string|max:50|unique:warehouse_delivery_orders,do_number',
            'do_date'           => 'required|date',
            'reference_no'      => 'nullable|string|max:100',
            'warehouse_id'      => 'required|exists:warehouse_locations,id',
            'recipient_name'    => 'required|string|max:150',
            'recipient_company' => 'nullable|string|max:255',
            'recipient_phone'   => 'nullable|string|max:50',
            'delivery_address'  => 'required|string',
            'courier_name'      => 'nullable|string|max:150',
            'vehicle_no'        => 'nullable|string|max:50',
            'tracking_no'       => 'nullable|string|max:100',
            'allocation_type'   => 'nullable|string|in:proyek,poc,pinjaman_ipnet,pinjaman_disti',
            'duration_days'     => 'nullable|integer',
            'status'            => 'required|string|in:Draft,Siap Dikirim,Dalam Pengiriman,Terkirim & Diterima,Batal',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.product_id'    => 'nullable',
            'items.*.warehouse_item_id' => 'nullable|exists:warehouse_items,id',
            'items.*.product_name'  => 'required|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit'          => 'required|string',
            'items.*.notes'         => 'nullable|string',
        ]);

        if (empty($validated['do_number'])) {
            $validated['do_number'] = 'DO-IPN-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }

        $wh = WarehouseLocation::find($validated['warehouse_id']);

        $do = WarehouseDeliveryOrder::create([
            'do_number'         => $validated['do_number'],
            'do_date'           => $validated['do_date'],
            'reference_no'      => $validated['reference_no'],
            'warehouse_id'      => $validated['warehouse_id'],
            'warehouse_name'    => $wh ? $wh->name : 'Gudang Utama IPNET',
            'recipient_name'    => $validated['recipient_name'],
            'recipient_company' => $validated['recipient_company'],
            'recipient_phone'   => $validated['recipient_phone'],
            'delivery_address'  => $validated['delivery_address'],
            'courier_name'      => $validated['courier_name'],
            'vehicle_no'        => $validated['vehicle_no'],
            'tracking_no'       => $validated['tracking_no'],
            'status'            => $validated['status'],
            'notes'             => $validated['notes'],
            'created_by'        => auth()->id(),
        ]);

        $allocType = $request->input('allocation_type', 'proyek');

        foreach ($validated['items'] as $itemData) {
            WarehouseDoItem::create([
                'delivery_order_id' => $do->id,
                'product_id'        => !empty($itemData['product_id']) && is_numeric($itemData['product_id']) ? $itemData['product_id'] : null,
                'product_name'      => $itemData['product_name'],
                'serial_number'     => $itemData['serial_number'] ?? null,
                'quantity'          => $itemData['quantity'],
                'unit'              => $itemData['unit'],
                'notes'             => $itemData['notes'] ?? null,
            ]);

            // Jika item fisik WarehouseItem dipilih atau serial number cocok, update status fisik barang
            $whItem = null;
            if (!empty($itemData['warehouse_item_id'])) {
                $whItem = WarehouseItem::find($itemData['warehouse_item_id']);
            } elseif (!empty($itemData['serial_number'])) {
                $whItem = WarehouseItem::where('serial_number', $itemData['serial_number'])->first();
            }

            if ($whItem) {
                $targetStatus = match($allocType) {
                    'poc' => 'POC / Pinjaman',
                    'pinjaman_ipnet', 'pinjaman_disti' => 'Pinjaman',
                    default => 'Pelanggan'
                };

                $whItem->update([
                    'status'              => $targetStatus,
                    'location_type'       => 'Customer',
                    'warehouse_rack'      => null,
                    'customer_address'    => $validated['delivery_address'],
                    'customer_pic'        => $validated['recipient_name'],
                    'customer_phone'      => $validated['recipient_phone'],
                    'customer_sales_name' => auth()->user()->name ?? 'Sales Officer',
                    'poc_customer_name'   => $validated['recipient_company'] ?: $validated['recipient_name'],
                    'poc_pic_name'        => $validated['recipient_name'],
                    'poc_pic_phone'       => $validated['recipient_phone'],
                    'notes'               => "Teralokasi via {$do->do_number} (" . strtoupper($allocType) . "): " . ($itemData['notes'] ?? $validated['notes']),
                ]);
            }

            // Kurangi stok di katalog WarehouseProduct
            if (!empty($itemData['product_id']) && is_numeric($itemData['product_id'])) {
                WarehouseProduct::where('id', $itemData['product_id'])->decrement('current_stock', $itemData['quantity']);
            } elseif ($whItem) {
                WarehouseProduct::where('name', $whItem->name)->orWhere('sku', $whItem->product_number)->decrement('current_stock', 1);
            }
        }

        return redirect()->route('warehouse.delivery_orders.index')->with('success', "Surat Jalan Delivery Order {$do->do_number} berhasil diterbitkan dan stok fisik teralokasi!");
    }

    public function updateDoStatus(Request $request, $id)
    {
        $this->ensureTableReady();
        $do = WarehouseDeliveryOrder::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:Draft,Siap Dikirim,Dalam Pengiriman,Terkirim & Diterima,Batal',
        ]);

        $do->update(['status' => $request->status]);

        return redirect()->route('warehouse.delivery_orders.index')->with('success', "Status Surat Jalan {$do->do_number} berhasil diubah ke {$request->status}!");
    }

    public function destroyDeliveryOrder($id)
    {
        $this->ensureTableReady();
        $do = WarehouseDeliveryOrder::findOrFail($id);
        $num = $do->do_number;
        $do->delete();

        return redirect()->route('warehouse.delivery_orders.index')->with('success', "Surat Jalan {$num} berhasil dihapus.");
    }

    // =========================================================================
    // LEGACY METHODS (Barang Masuk, Barang Keluar, Items, Reports)
    // =========================================================================
    public function index(Request $request)
    {
        return $this->products($request);
    }

    public function barangMasuk(Request $request)
    {
        return $this->purchaseOrders($request);
    }

    public function barangKeluar(Request $request)
    {
        return $this->deliveryOrders($request);
    }

    public function reports(Request $request)
    {
        return $this->dashboard($request);
    }

    public function history(Request $request)
    {
        return $this->deliveryOrders($request);
    }

    public function peminjamanPoc(Request $request)
    {
        return $this->deliveryOrders($request);
    }

    public function allocateOut(Request $request)
    {
        return redirect()->route('warehouse.delivery_orders.index');
    }

    public function returnToWarehouse(Request $request, $item)
    {
        $this->ensureTableReady();
        $whItem = ($item instanceof WarehouseItem) ? $item : WarehouseItem::findOrFail($item);
        
        $rack = $request->input('warehouse_rack', 'Rak Gudang IPNET');
        $notes = $request->input('notes', 'Pengembalian fisik unit selesai');

        $whItem->update([
            'status'           => 'Tersedia',
            'location_type'    => 'Gudang IPNET',
            'warehouse_rack'   => $rack,
            'customer_address' => null,
            'customer_pic'     => null,
            'customer_phone'   => null,
            'poc_customer_name'=> null,
            'poc_pic_name'     => null,
            'notes'            => 'Dikembalikan ke rak gudang: ' . $notes,
        ]);

        $prod = WarehouseProduct::where('name', $whItem->name)->orWhere('sku', $whItem->product_number)->first();
        if ($prod) {
            $prod->increment('current_stock', 1);
        }

        return redirect()->route('warehouse.delivery_orders.index')->with('success', "Perangkat {$whItem->name} (SN: {$whItem->serial_number}) berhasil dikembalikan ke {$rack}!");
    }

    public function store(Request $request)
    {
        return $this->storeProduct($request);
    }

    public function update(Request $request, WarehouseItem $item)
    {
        return $this->updateProduct($request, $item->id);
    }

    public function destroy(WarehouseItem $item)
    {
        return $this->destroyProduct($item->id);
    }
}
