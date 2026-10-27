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

class WarehouseController extends Controller
{
    /**
     * Memastikan database telah termigrasi & terisi data dummy awal
     */
    protected function ensureTableReady(): void
    {
        if (!Schema::hasTable('warehouse_products') || WarehouseProduct::count() === 0) {
            try {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('db:seed', ['--class' => 'WarehouseSeeder', '--force' => true]);
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Helper untuk menghitung metrik umum sistem warehouse
     */
    protected function getMetrics(): array
    {
        $this->ensureTableReady();

        $totalValuation = WarehouseProduct::sum(DB::raw('current_stock * unit_price'));
        $lowStockCount  = WarehouseProduct::whereColumn('current_stock', '<=', 'min_stock')->count();

        return [
            'total_products'    => WarehouseProduct::count(),
            'total_valuation'   => $totalValuation,
            'low_stock_count'   => $lowStockCount,
            'total_categories'  => WarehouseCategory::count(),
            'total_units'       => WarehouseUnit::count(),
            'total_suppliers'   => WarehouseSupplier::count(),
            'total_warehouses'  => WarehouseLocation::count(),
            'active_po'         => WarehousePurchaseOrder::whereIn('status', ['Diproses', 'Disetujui', 'Diterima Sebagian'])->count(),
            'pending_do'        => WarehouseDeliveryOrder::whereIn('status', ['Draft', 'Siap Dikirim', 'Dalam Pengiriman'])->count(),
            'completed_so'      => WarehouseStockOpname::where('status', 'Selesai')->count(),
            
            // Legacy metric support
            'total_items'       => WarehouseItem::count(),
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

        // 5 Produk dengan stok menipis / perlu restock
        $lowStockProducts = WarehouseProduct::whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('current_stock', 'asc')
            ->take(5)
            ->get();

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

        // Breakdown Stok per Kategori
        $categoryBreakdown = WarehouseCategory::withCount('products')
            ->withSum('products', 'current_stock')
            ->get();

        // Rekap per Gudang
        $warehouseBreakdown = WarehouseLocation::withCount('products')
            ->withSum('products', 'current_stock')
            ->get();

        // 6 Produk Baru
        $recentProducts = WarehouseProduct::latest()->take(6)->get();

        return view('warehouse.dashboard', compact(
            'metrics',
            'lowStockProducts',
            'recentPOs',
            'recentDOs',
            'categoryBreakdown',
            'warehouseBreakdown',
            'recentProducts'
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
        $locations      = ['Gudang IPNET', 'Customer'];
        $deviceModels   = WarehouseItem::getDeviceModels();
        $deviceBrands   = WarehouseItem::getDeviceBrands();

        return view('warehouse.products.index', compact(
            'items',
            'metrics',
            'itemTypes',
            'componentTypes',
            'cableTypes',
            'workUnits',
            'categories',
            'statuses',
            'locations',
            'deviceModels',
            'deviceBrands'
        ));
    }

    public function storeProduct(Request $request)
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
            'location_type'       => 'required|string|in:Gudang IPNET,Warehouse IPNET,Customer',
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

    public function updateProduct(Request $request, $id)
    {
        $this->ensureTableReady();
        $item = WarehouseItem::findOrFail($id);

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
            'location_type'       => 'required|string|in:Gudang IPNET,Warehouse IPNET,Customer',
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

        return redirect()->route('warehouse.products.index')->with('success', "Data barang {$item->item_code} berhasil diperbarui!");
    }

    public function destroyProduct($id, Request $request = null)
    {
        $this->ensureTableReady();
        $item = WarehouseItem::findOrFail($id);
        $code = $item->item_code;
        $item->delete();

        if (($request && $request->wantsJson()) || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Barang {$code} berhasil dihapus dari inventaris."
            ]);
        }

        return redirect()->route('warehouse.products.index')->with('success', "Barang {$code} berhasil dihapus dari inventaris.");
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
            'contact_person'    => 'nullable|string|max:150',
            'phone'             => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:150',
            'address'           => 'nullable|string',
            'products_supplied' => 'nullable|string|max:255',
            'status'            => 'required|string|in:Aktif,Nonaktif',
            'notes'             => 'nullable|string',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'SUP-' . date('Y') . '-' . rand(100, 999);
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
            'contact_person'    => 'nullable|string|max:150',
            'phone'             => 'nullable|string|max:50',
            'email'             => 'nullable|email|max:150',
            'address'           => 'nullable|string',
            'products_supplied' => 'nullable|string|max:255',
            'status'            => 'required|string|in:Aktif,Nonaktif',
            'notes'             => 'nullable|string',
        ]);

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
    // 9. DELIVERY ORDER (DO)
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

        return view('warehouse.delivery_orders.index', compact(
            'deliveryOrders',
            'metrics',
            'warehouses',
            'products'
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
            'status'            => 'required|string|in:Draft,Siap Dikirim,Dalam Pengiriman,Terkirim & Diterima,Batal',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.product_id'    => 'nullable|exists:warehouse_products,id',
            'items.*.product_name'  => 'required|string',
            'items.*.serial_number' => 'nullable|string',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.unit'          => 'required|string',
            'items.*.notes'         => 'nullable|string',
        ]);

        if (empty($validated['do_number'])) {
            $validated['do_number'] = 'DO-IPN-' . date('Y') . '-' . str_pad(rand(1, 999), 4, '0', STR_PAD_LEFT);
        }

        $wh = WarehouseLocation::find($validated['warehouse_id']);

        $do = WarehouseDeliveryOrder::create([
            'do_number'         => $validated['do_number'],
            'do_date'           => $validated['do_date'],
            'reference_no'      => $validated['reference_no'],
            'warehouse_id'      => $validated['warehouse_id'],
            'warehouse_name'    => $wh ? $wh->name : null,
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

        foreach ($validated['items'] as $itemData) {
            WarehouseDoItem::create([
                'delivery_order_id' => $do->id,
                'product_id'        => $itemData['product_id'] ?? null,
                'product_name'      => $itemData['product_name'],
                'serial_number'     => $itemData['serial_number'] ?? null,
                'quantity'          => $itemData['quantity'],
                'unit'              => $itemData['unit'],
                'notes'             => $itemData['notes'] ?? null,
            ]);

            // Jika DO diset Dalam Pengiriman atau Terkirim, kurangi stok produk
            if (in_array($validated['status'], ['Dalam Pengiriman', 'Terkirim & Diterima']) && !empty($itemData['product_id'])) {
                WarehouseProduct::where('id', $itemData['product_id'])->decrement('current_stock', $itemData['quantity']);
            }
        }

        return redirect()->route('warehouse.delivery_orders.index')->with('success', "Surat Jalan Delivery Order {$do->do_number} berhasil diterbitkan!");
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

    public function returnToWarehouse(WarehouseItem $item)
    {
        return redirect()->route('warehouse.delivery_orders.index');
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
