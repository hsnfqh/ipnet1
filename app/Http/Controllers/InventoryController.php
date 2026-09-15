<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with(['transactions' => function($q) {
            $q->latest()->limit(5);
        }])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_code', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $items = $query->paginate(15)->withQueryString();
        $categories = InventoryItem::select('category')->distinct()->whereNotNull('category')->pluck('category');

        $recentTransactions = InventoryTransaction::with(['item', 'creator'])->latest()->take(10)->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'items'              => $items,
                'categories'         => $categories,
                'recentTransactions' => $recentTransactions
            ]);
        }

        return view('inventory.index', compact('items', 'categories', 'recentTransactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:inventory_items,product_code',
            'product_name' => 'required|string|max:255',
            'category'     => 'nullable|string|max:100',
            'stock'        => 'required|integer|min:0',
            'unit'         => 'required|string|max:50',
            'unit_price'   => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $validated['status'] = $validated['stock'] > 5 ? 'Tersedia' : ($validated['stock'] > 0 ? 'Menipis' : 'Habis');

        $item = InventoryItem::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk baru berhasil ditambahkan ke inventory!',
                'item'    => $item
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:inventory_items,product_code,' . $item->id,
            'product_name' => 'required|string|max:255',
            'category'     => 'nullable|string|max:100',
            'stock'        => 'required|integer|min:0',
            'unit'         => 'required|string|max:50',
            'unit_price'   => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
        ]);

        $validated['status'] = $validated['stock'] > 5 ? 'Tersedia' : ($validated['stock'] > 0 ? 'Menipis' : 'Habis');

        $item->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diperbarui!',
                'item'    => $item
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy(Request $request, InventoryItem $item)
    {
        $item->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari inventory!'
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'qty'               => 'required|integer|min:1',
            'transaction_date'  => 'required|date',
            'reference_no'      => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        DB::transaction(function() use ($validated) {
            $item = InventoryItem::lockForUpdate()->findOrFail($validated['inventory_item_id']);
            $item->stock += $validated['qty'];
            $item->status = $item->stock > 5 ? 'Tersedia' : ($item->stock > 0 ? 'Menipis' : 'Habis');
            $item->save();

            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'in',
                'qty'               => $validated['qty'],
                'transaction_date'  => $validated['transaction_date'],
                'reference_no'      => $validated['reference_no'] ?? null,
                'notes'             => $validated['notes'] ?? null,
                'created_by'        => auth()->id(),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pencatatan Barang Masuk berhasil disimpan!'
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Barang Masuk berhasil dicatat!');
    }

    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'inventory_item_id' => 'required|exists:inventory_items,id',
            'qty'               => 'required|integer|min:1',
            'transaction_date'  => 'required|date',
            'reference_no'      => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        $item = InventoryItem::findOrFail($validated['inventory_item_id']);
        if ($item->stock < $validated['qty']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi! Stok saat ini: {$item->stock} {$item->unit}"
                ], 422);
            }
            return back()->with('error', "Stok tidak mencukupi! Stok saat ini: {$item->stock} {$item->unit}");
        }

        DB::transaction(function() use ($validated) {
            $item = InventoryItem::lockForUpdate()->findOrFail($validated['inventory_item_id']);
            $item->stock -= $validated['qty'];
            $item->status = $item->stock > 5 ? 'Tersedia' : ($item->stock > 0 ? 'Menipis' : 'Habis');
            $item->save();

            InventoryTransaction::create([
                'inventory_item_id' => $item->id,
                'type'              => 'out',
                'qty'               => $validated['qty'],
                'transaction_date'  => $validated['transaction_date'],
                'reference_no'      => $validated['reference_no'] ?? null,
                'notes'             => $validated['notes'] ?? null,
                'created_by'        => auth()->id(),
            ]);
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pencatatan Barang Keluar berhasil disimpan!'
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Barang Keluar berhasil dicatat!');
    }
}
