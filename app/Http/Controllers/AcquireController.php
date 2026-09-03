<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Division;
use App\Helpers\ScopeHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AcquireController extends Controller
{
    /**
     * Sales names options (excluding Riko Wijaya per user request)
     */
    protected array $salesTeam = ['Ribka', 'Widodo', 'Raiza'];

    public function index(Request $request)
    {
        $user = auth()->user();

        // Query projects in Acquire stage (or all pipeline projects)
        $query = Project::query()->with(['division', 'creator'])->latest();

        // Global KPI Stats for Acquire Pipeline
        $totalLeads = Project::count();
        $inNegotiationCount = Project::whereIn('acquire_status', ['Kualifikasi Kebutuhan', 'Penawaran Komersial'])->count();
        $dealPoCount = Project::where('acquire_status', 'Deal / PO Terbit')->count();
        $handoverDesignCount = Project::where('stage', 'Design')->orWhere('acquire_status', 'Handover to Design')->count();

        // Format projects for table view
        $projects = $query->get()->map(function ($p) {
            return [
                'id'              => $p->id,
                'name'            => $p->name,
                'client'          => $p->client,
                'sales_name'      => $p->sales_name ?: 'Ribka',
                'contract_value'  => (float) ($p->contract_value ?? 0),
                'contract_formatted' => $p->contract_value ? 'Rp ' . number_format($p->contract_value, 0, ',', '.') : '-',
                'po_number'       => $p->po_number ?: '-',
                'po_file'         => $p->po_file ? asset('storage/' . $p->po_file) : null,
                'po_file_raw'     => $p->po_file,
                'acquire_status'  => $p->acquire_status ?: 'Deal / PO Terbit',
                'stage'           => $p->stage ?: 'Acquire',
                'start_date'      => $p->start_date ? $p->start_date->format('Y-m-d') : null,
                'deadline'        => $p->deadline ? $p->deadline->format('Y-m-d') : null,
                'deadline_formatted' => $p->deadline ? $p->deadline->format('d M Y') : '-',
                'division_id'     => $p->division_id,
                'division_name'   => $p->division?->name ?? 'Lintas Divisi',
                'description'     => $p->description,
                'is_ready_handover' => in_array($p->acquire_status, ['Deal / PO Terbit', 'Handover to Design']) || !empty($p->po_number),
            ];
        });

        $divisions = Division::orderBy('name')->get();
        $salesList = $this->salesTeam;

        return view('acquire.index', compact(
            'projects',
            'totalLeads',
            'inNegotiationCount',
            'dealPoCount',
            'handoverDesignCount',
            'divisions',
            'salesList'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'client'         => 'required|string|max:255',
            'sales_name'     => 'required|string|max:100',
            'contract_value' => 'nullable|numeric|min:0',
            'po_number'      => 'nullable|string|max:100',
            'acquire_status' => 'required|string',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date|after_or_equal:start_date',
            'division_id'    => 'nullable|exists:divisions,id',
            'description'    => 'nullable|string',
            'po_file'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('po_file')) {
            $filePath = $request->file('po_file')->store('po_documents', 'public');
        }

        $project = Project::create([
            'name'           => $validated['name'],
            'client'         => $validated['client'],
            'sales_name'     => $validated['sales_name'],
            'contract_value' => $validated['contract_value'] ?? null,
            'po_number'      => $validated['po_number'] ?? null,
            'po_file'        => $filePath,
            'acquire_status' => $validated['acquire_status'],
            'stage'          => 'Acquire',
            'process_status' => 'In Progress',
            'start_date'     => $validated['start_date'] ?? now()->toDateString(),
            'deadline'       => $validated['deadline'] ?? now()->addDays(30)->toDateString(),
            'division_id'    => $validated['division_id'] ?? null,
            'description'    => $validated['description'] ?? null,
            'created_by'     => auth()->id(),
            'status'         => 'Planning',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Peluang / Kontrak Sales berhasil didaftarkan.',
                'project' => $project,
            ]);
        }

        return redirect()->route('acquire.index')->with('success', 'Peluang / Kontrak Sales berhasil didaftarkan.');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'client'         => 'required|string|max:255',
            'sales_name'     => 'required|string|max:100',
            'contract_value' => 'nullable|numeric|min:0',
            'po_number'      => 'nullable|string|max:100',
            'acquire_status' => 'required|string',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date|after_or_equal:start_date',
            'division_id'    => 'nullable|exists:divisions,id',
            'description'    => 'nullable|string',
            'po_file'        => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('po_file')) {
            if ($project->po_file && Storage::disk('public')->exists($project->po_file)) {
                Storage::disk('public')->delete($project->po_file);
            }
            $validated['po_file'] = $request->file('po_file')->store('po_documents', 'public');
        }

        $project->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data peluang berhasil diperbarui.',
                'project' => $project,
            ]);
        }

        return redirect()->route('acquire.index')->with('success', 'Data peluang berhasil diperbarui.');
    }

    public function handoverToDesign(Request $request, Project $project)
    {
        $project->update([
            'stage'          => 'Design',
            'acquire_status' => 'Handover to Design',
            'process_status' => 'In Progress',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Handover 1 Berhasil: Proyek resmi diserahkan ke Tim Design (Pre-Sales & Solution Architect).',
                'project' => $project,
            ]);
        }

        return redirect()->route('acquire.index')->with('success', 'Handover 1 Berhasil: Proyek resmi diserahkan ke Tim Design.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data peluang berhasil dihapus.']);
        }

        return redirect()->route('acquire.index')->with('success', 'Data peluang berhasil dihapus.');
    }
}
