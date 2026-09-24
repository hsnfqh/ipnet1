<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use App\Models\Division;
use App\Models\User;

class SalesProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab  = $request->input('tab', 'Opportunity'); // default tab

        $query = Project::with(['division', 'creator'])->latest();

        // If user is sales, strictly filter to their assigned / created projects
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'PMO', 'Project Manager']);
        if (!$isManagerial && $user->hasAnyRole(['Sales', 'BusDev', 'Account Manager'])) {
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('sales_name', $user->name);
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('project_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->input('division_id'));
        }

        if ($request->filled('approval_status')) {
            $query->where('acquire_status', $request->input('approval_status'));
        }

        $allProjects = $query->get();

        // Calculate counts for tabs
        $counts = [
            'draft'       => $allProjects->where('status', 'Draft')->count(),
            'opportunity' => $allProjects->filter(function($p) {
                return in_array($p->status, ['Opportunity', 'Planning']) && $p->sales_stage !== 'Closed Won' && !in_array($p->status, ['In Progress', 'On Progress']);
            })->count(),
            'in_progress' => $allProjects->filter(function($p) {
                return in_array($p->status, ['In Progress', 'On Progress']) || $p->sales_stage === 'Closed Won';
            })->count(),
            'pending'     => $allProjects->whereIn('status', ['Pending', 'Waiting Approval'])->count(),
        ];

        // Filter projects for active tab
        $filteredProjects = match($tab) {
            'Draft'       => $allProjects->where('status', 'Draft')->values(),
            'Opportunity' => $allProjects->filter(function($p) {
                return in_array($p->status, ['Opportunity', 'Planning']) && $p->sales_stage !== 'Closed Won' && !in_array($p->status, ['In Progress', 'On Progress']);
            })->values(),
            'In Progress' => $allProjects->filter(function($p) {
                return in_array($p->status, ['In Progress', 'On Progress']) || $p->sales_stage === 'Closed Won';
            })->values(),
            'Pending'     => $allProjects->whereIn('status', ['Pending', 'Waiting Approval'])->values(),
            default       => $allProjects->values(),
        };

        $clients   = Client::orderBy('name')->get();
        $divisions = Division::orderBy('name')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'projects'  => $filteredProjects,
                'counts'    => $counts,
                'clients'   => $clients,
                'divisions' => $divisions,
            ]);
        }

        return view('sales.projects.index', compact('filteredProjects', 'counts', 'clients', 'divisions', 'tab'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'client'          => 'required|string|max:255',
            'division_id'     => 'nullable|exists:divisions,id',
            'contract_value'  => 'nullable|numeric|min:0',
            'start_date'      => 'nullable|date',
            'deadline'        => 'nullable|date',
            'status'          => 'required|string|in:Draft,Opportunity,Planning,On Progress,Pending,Completed',
            'project_type'    => 'nullable|string|max:100',
            'sales_name'      => 'nullable|string|max:255',
            'description'     => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['sales_name'] = $validated['sales_name'] ?? auth()->user()->name;

        $project = Project::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil ditambahkan!',
                'project' => $project
            ]);
        }

        return redirect()->route('sales.projects.index', ['tab' => $validated['status']])->with('success', 'Project berhasil ditambahkan!');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'client'          => 'required|string|max:255',
            'division_id'     => 'nullable|exists:divisions,id',
            'contract_value'  => 'nullable|numeric|min:0',
            'start_date'      => 'nullable|date',
            'deadline'        => 'nullable|date',
            'status'          => 'required|string|in:Draft,Opportunity,Planning,On Progress,Pending,Completed',
            'project_type'    => 'nullable|string|max:100',
            'sales_name'      => 'nullable|string|max:255',
            'description'     => 'nullable|string',
        ]);

        $project->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil diperbarui!',
                'project' => $project
            ]);
        }

        return redirect()->route('sales.projects.index')->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Request $request, Project $project)
    {
        $project->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project berhasil dihapus!'
            ]);
        }

        return redirect()->route('sales.projects.index')->with('success', 'Project berhasil dihapus!');
    }
}
