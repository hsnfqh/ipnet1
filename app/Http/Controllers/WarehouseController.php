<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarehouseItem;

class WarehouseController extends Controller
{
    /**
     * Dashboard & Master Inventaris Gudang
     */
    public function index(Request $request)
    {
        $query = WarehouseItem::with('creator');

        // Filter Pencarian Teks
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

        // Filter Jenis Barang
        if ($request->filled('item_type') && $request->item_type !== 'Semua') {
            $query->where('item_type', $request->item_type);
        }

        // Filter Kategori
        if ($request->filled('category') && $request->category !== 'Semua') {
            $query->where('category', $request->category);
        }

        // Filter Status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // Filter Lokasi
        if ($request->filled('location_type') && $request->location_type !== 'Semua') {
            $query->where('location_type', $request->location_type);
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        // Kartu Metrik Statistik
        $metrics = [
            'total_items'      => WarehouseItem::count(),
            'ready_stock'      => WarehouseItem::where('status', 'Tersedia')->count(),
            'backup_spare'     => WarehouseItem::where('category', 'Backup / Spare')->count(),
            'active_poc'       => WarehouseItem::where('status', 'POC / Pinjaman')->count(),
            'disti_loan'       => WarehouseItem::where('category', 'Pinjaman')->where('loan_source_type', 'disti')->count(),
            'customer_deployed'=> WarehouseItem::where('location_type', 'Customer')->count(),
        ];

        $itemTypes      = WarehouseItem::getItemTypes();
        $componentTypes = WarehouseItem::getComponentTypes();
        $cableTypes     = WarehouseItem::getCableTypes();
        $workUnits      = WarehouseItem::getWorkUnits();
        $categories     = WarehouseItem::getCategories();
        $statuses       = WarehouseItem::getStatuses();

        return view('warehouse.index', compact(
            'items',
            'metrics',
            'itemTypes',
            'componentTypes',
            'cableTypes',
            'workUnits',
            'categories',
            'statuses'
        ));
    }

    /**
     * Simpan Perangkat / Barang Baru ke Gudang
     */
    public function store(Request $request)
    {
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

        // Auto-generate Item Code unik: WH-2026-XXXX
        $countThisYear = WarehouseItem::withTrashed()->whereYear('created_at', date('Y'))->count() + 1;
        $validated['item_code'] = 'WH-' . date('Y') . '-' . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        $item = WarehouseItem::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perangkat inventaris berhasil didaftarkan ke gudang!',
                'data'    => $item,
            ], 201);
        }

        return redirect()->route('warehouse.dashboard')->with('success', "Barang {$item->name} ({$item->item_code}) berhasil didaftarkan ke gudang!");
    }

    /**
     * Perbarui Data Perangkat
     */
    public function update(Request $request, WarehouseItem $item)
    {
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

        return redirect()->route('warehouse.dashboard')->with('success', "Data barang {$item->item_code} berhasil diperbarui!");
    }

    /**
     * Hapus Data Perangkat
     */
    public function destroy(WarehouseItem $item)
    {
        $code = $item->item_code;
        $item->delete();

        return redirect()->route('warehouse.dashboard')->with('success', "Barang {$code} berhasil dihapus dari inventaris.");
    }
}
