<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('channel_manager', 'like', "%{$search}%")
                  ->orWhere('product_category', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $vendors = $query->paginate(15)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vendors);
        }

        return view('vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'department'       => 'nullable|string|max:255',
            'channel_manager'  => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'address'          => 'nullable|string',
            'product_category' => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $vendor = Vendor::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor / Distributor berhasil ditambahkan!',
                'vendor'  => $vendor
            ]);
        }

        return redirect()->route('vendors.index')->with('success', 'Vendor / Distributor berhasil ditambahkan!');
    }

    public function show(Request $request, Vendor $vendor)
    {
        $vendor->load('creator');
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($vendor);
        }
        return view('vendors.show', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'department'       => 'nullable|string|max:255',
            'channel_manager'  => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:50',
            'email'            => 'nullable|email|max:255',
            'address'          => 'nullable|string',
            'product_category' => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $vendor->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data vendor berhasil diperbarui!',
                'vendor'  => $vendor
            ]);
        }

        return redirect()->route('vendors.show', $vendor->id)->with('success', 'Data vendor berhasil diperbarui!');
    }

    public function destroy(Request $request, Vendor $vendor)
    {
        $vendor->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data vendor berhasil dihapus!'
            ]);
        }

        return redirect()->route('vendors.index')->with('success', 'Data vendor berhasil dihapus!');
    }
}
