<?php

namespace App\Http\Controllers;

use App\Helpers\ScopeHelper;
use App\Models\EngineerActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class EngineerActivityLogController extends Controller
{
    /**
     * Tampilkan halaman utama Activity Log Engineer / Tim Lapangan
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        $isLead   = ScopeHelper::isManagerial($authUser);

        $query = EngineerActivityLog::with(['engineer', 'project']);

        // Jika bukan managerial/lead, batasi hanya log diri sendiri
        if (!$isLead) {
            $query->where('user_id', $authUser->id);
        } else {
            // Managerial/Lead bisa filter per engineer tertentu
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter Pencarian (Keyword)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('engineer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('client', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Tipe Aktivitas
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Proyek
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        // Hitung ringkasan metrics (berdasarkan filter/scope yang aktif)
        $metricsQuery = clone $query;
        $totalActivities = $metricsQuery->count();
        $totalCompleted  = (clone $metricsQuery)->where('status', 'Selesai')->count();
        $totalInProgress = (clone $metricsQuery)->where('status', 'Sedang Berjalan')->count();
        $totalDelayed    = (clone $metricsQuery)->where('status', 'Ditunda')->count();
        $activeEngineersCount = (clone $metricsQuery)->distinct('user_id')->count('user_id');

        // Ambil data aktivitas dengan pagination
        $activities = $query->orderBy('activity_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12)
                            ->withQueryString();

        // Data pendukung dropdown filter & form input
        $projects = Project::orderBy('name')->get(['id', 'name', 'client']);

        $engineers = collect();
        if ($isLead) {
            $engineers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', [
                    'Network Engineer', 'Security Engineer', 'Field Support (EOS)', 'Field Support',
                    'Managed Service', 'Engineer', 'Engineer L1', 'Engineer L2', 'Maintenance',
                    'Lead Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Maintenance'
                ]);
            })->orderBy('name')->get(['id', 'name', 'email']);

            if ($engineers->isEmpty()) {
                $engineers = User::orderBy('name')->get(['id', 'name', 'email']);
            }
        }

        $activityTypes = [
            'Instalasi / Penarikan Kabel',
            'Konfigurasi Router/Switch/Firewall',
            'Troubleshooting Jaringan',
            'Maintenance Rutin',
            'Survey Lokasi',
            'Dokumentasi & BA',
            'Testing & Commissioning',
            'Koordinasi & Meeting Teknis',
            'Lainnya',
        ];

        return view('engineer.activity_log.index', compact(
            'activities',
            'isLead',
            'totalActivities',
            'totalCompleted',
            'totalInProgress',
            'totalDelayed',
            'activeEngineersCount',
            'projects',
            'engineers',
            'activityTypes'
        ));
    }

    /**
     * Simpan catatan aktivitas engineer baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_date' => 'required|date',
            'activity_type' => 'required|string|max:100',
            'description'   => 'required|string',
            'project_id'    => 'nullable|exists:projects,id',
            'location'      => 'nullable|string|max:255',
            'status'        => 'required|in:Selesai,Sedang Berjalan,Ditunda',
            'start_time'    => 'nullable',
            'end_time'      => 'nullable',
            'notes'         => 'nullable|string',
        ]);

        EngineerActivityLog::create([
            'user_id'       => auth()->id(),
            'activity_date' => $validated['activity_date'],
            'activity_type' => $validated['activity_type'],
            'description'   => $validated['description'],
            'project_id'    => $validated['project_id'] ?? null,
            'location'      => $validated['location'] ?? null,
            'status'        => $validated['status'],
            'start_time'    => $validated['start_time'] ?? null,
            'end_time'      => $validated['end_time'] ?? null,
            'notes'         => $validated['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Catatan aktivitas berhasil disimpan!');
    }

    /**
     * Hapus catatan aktivitas engineer
     */
    public function destroy(EngineerActivityLog $log)
    {
        $authUser = auth()->user();
        $isLead   = ScopeHelper::isManagerial($authUser);

        if ($authUser->id !== $log->user_id && !$isLead) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus log aktivitas ini.');
        }

        $log->delete();

        return redirect()->back()->with('success', 'Catatan aktivitas berhasil dihapus.');
    }
}
