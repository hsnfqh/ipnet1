<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('projects')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->paginate(15)->withQueryString();
        $totalProjects = \App\Models\Project::count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($clients);
        }

        return view('clients.index', compact('clients', 'totalProjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'pic_name'   => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $client = Client::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Klien berhasil ditambahkan!',
                'client'  => $client
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Klien berhasil ditambahkan!');
    }

    public function show(Request $request, Client $client)
    {
        $client->load(['projects', 'creator']);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($client);
        }
        return view('clients.show', compact('client'));
    }

    public function edit(Request $request, Client $client)
    {
        return $this->show($request, $client);
    }

    public function update(Request $request, Client $client)
    {
        // Support array of departments if submitted as array
        if (is_array($request->input('department'))) {
            $deptClean = implode(', ', array_filter($request->input('department')));
            $request->merge(['department' => $deptClean]);
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'pic_name'   => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string',
            'notes'      => 'nullable|string',
        ]);

        $client->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data klien berhasil diperbarui!',
                'client'  => $client
            ]);
        }

        return redirect()->back()->with('success', 'Data klien berhasil diperbarui!');
    }

    public function destroy(Request $request, Client $client)
    {
        $client->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data klien berhasil dihapus!'
            ]);
        }

        return redirect()->route('clients.index')->with('success', 'Data klien berhasil dihapus!');
    }
}
