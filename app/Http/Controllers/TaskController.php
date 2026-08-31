<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Http\Requests\TaskRequest;
use App\Helpers\ScopeHelper;

class TaskController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $isLead       = ScopeHelper::isManagerial($user);
        $isDirektur   = $user->hasAnyRole(['Direktur', 'HD / Direktur']);
        $isSupervisor = ScopeHelper::isGlobal($user);
        $canManage    = ScopeHelper::canManageTasks($user);
        $scopeIds     = ScopeHelper::getScopeUserIds($user);

        $tasks = Task::with(['project', 'engineer', 'engineers', 'creator'])
            ->when($scopeIds !== null, function($query) use ($scopeIds, $user) {
                return $query->where(function($q) use ($scopeIds, $user) {
                    if (count($scopeIds) === 1) {
                        $q->where('engineer_id', $scopeIds[0])
                          ->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                    } else {
                        $q->whereIn('engineer_id', $scopeIds)
                          ->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                    }
                    $q->orWhere('created_by', $user->id);
                });
            })
            ->get();

        $projects  = $isLead ? Project::all() : Project::whereIn('id', $tasks->pluck('project_id')->unique())->get();
        $engineers = ScopeHelper::getAssignableEngineers($user);
        $currentUserId = $user->id;

        return view('tasks.index', compact('tasks', 'projects', 'engineers', 'currentUserId', 'isLead', 'canManage', 'isDirektur', 'isSupervisor'));
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['progress'] = 0;
        $data['attachments'] = 0;
        $data['status'] = $data['status'] ?? 'Assigned';

        // Kelola multi-assignee (engineer_ids)
        $engineerIds = $request->input('engineer_ids', []);
        if (empty($engineerIds) && !empty($data['engineer_id'])) {
            $engineerIds = [(int) $data['engineer_id']];
        }
        if (!empty($engineerIds)) {
            $data['engineer_id'] = $engineerIds[0];
        }
        unset($data['engineer_ids']);
        
        $task = Task::create($data);
        if (!empty($engineerIds)) {
            $task->engineers()->sync($engineerIds);
        }
        $task = $task->load(['project', 'engineer', 'engineers', 'creator']);

        // Kirim notifikasi ke seluruh engineer/tim yang ditugaskan
        foreach ($engineerIds as $engId) {
            \App\Models\Notification::create([
                'user_id' => $engId,
                'title'   => 'Tugas Baru Ditugaskan',
                'message' => 'Anda ditugaskan pada tugas baru: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                'url'     => route('tasks.index'),
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($task, 201);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dibuat dan ditugaskan ke tim!');
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();

        // Hanya Team Leader & Koordinator Helpdesk yang memiliki wewenang untuk mengubah atau memperbarui task
        abort_unless(ScopeHelper::canManageTasks($user), 403, 'Hanya Team Leader / Koordinator yang berhak mengubah atau memperbarui task.');

        $validated = $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'project_id'     => 'sometimes|required|exists:projects,id',
            'engineer_id'    => 'sometimes|nullable|exists:users,id',
            'engineer_ids'   => 'sometimes|nullable|array|min:1',
            'engineer_ids.*' => 'exists:users,id',
            'priority'       => 'sometimes|required|in:High,Medium,Low',
            'deadline'       => 'sometimes|required|date',
            'description'    => 'sometimes|nullable|string',
            'status'         => 'sometimes|required|in:Assigned,In Progress,Waiting Review,Completed',
            'progress'       => 'sometimes|nullable|integer|min:0|max:100',
            'doc_file'       => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Multi-assignee sync
        if ($request->has('engineer_ids') || $request->has('engineer_id')) {
            $engineerIds = $request->input('engineer_ids');
            if (empty($engineerIds) && $request->filled('engineer_id')) {
                $engineerIds = [(int) $request->input('engineer_id')];
            }
            if (!empty($engineerIds)) {
                $task->engineer_id = $engineerIds[0];
                $task->engineers()->sync($engineerIds);
            }
        }
        unset($validated['engineer_ids']);

        // Handle file upload dokumentasi
        if ($request->hasFile('doc_file')) {
            if ($task->doc_file && Storage::disk('public')->exists($task->doc_file)) {
                Storage::disk('public')->delete($task->doc_file);
            }
            $path = $request->file('doc_file')->store('task-docs', 'public');
            $task->doc_file = $path;
            $task->attachments = ($task->attachments ?? 0) + 1;
        }

        // Fill non-doc_file validated fields
        $fieldsToFill = array_filter($validated, fn($v, $k) => $k !== 'doc_file' && $v !== null, ARRAY_FILTER_USE_BOTH);
        $task->fill($fieldsToFill);

        // Auto progress defaults if status changed without explicit progress slider update
        if (isset($validated['status'])) {
            if ($validated['status'] === 'Completed') {
                $task->progress = 100;
            } elseif ($validated['status'] === 'In Progress' && (!isset($validated['progress']) || $validated['progress'] === 0)) {
                $task->progress = max($task->progress, 50);
            } elseif ($validated['status'] === 'Waiting Review' && (!isset($validated['progress']) || $validated['progress'] === 0)) {
                $task->progress = max($task->progress, 90);
            }
        }

        // Cek perubahan field sebelum disave
        $statusChanged = $task->isDirty('status');
        $progressChanged = $task->isDirty('progress');
        $engineerChanged = $task->isDirty('engineer_id');

        $task->save();
        $task = $task->load(['project', 'engineer', 'engineers', 'creator']);

        // Kirim notifikasi jika engineer berubah
        if ($engineerChanged && $task->engineer_id) {
            \App\Models\Notification::create([
                'user_id' => $task->engineer_id,
                'title' => 'Tugas Baru Ditugaskan',
                'message' => 'Anda ditugaskan pada tugas: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                'url' => route('tasks.index'),
                'is_read' => false,
            ]);
        }

        // Kirim notifikasi ke manajer jika diupdate oleh engineer biasa
        $user = auth()->user();
        if ($user && !ScopeHelper::isManagerial($user)) {
            if ($progressChanged || $request->hasFile('doc_file')) {
                $leads = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Direktur', 'Lead Engineer', 'Lead Divisi', 'Team Leader', 'Group Leader']))->get();
                $notificationRecipients = $leads->pluck('id')->push($task->created_by)->unique();
                
                foreach ($notificationRecipients as $recipientId) {
                    if ($recipientId != $user->id) {
                        \App\Models\Notification::create([
                            'user_id' => $recipientId,
                            'title'   => 'Progress Pekerjaan Diperbarui',
                            'message' => 'Engineer ' . $user->name . ' telah memperbarui progress "' . $task->title . '" (Progress: ' . $task->progress . '%).',
                            'url'     => route('tasks.index'),
                            'is_read' => false,
                        ]);
                    }
                }
            }
        }

        // Kirim notifikasi ke Engineer jika manajerial mengubah status task
        if ($user && ScopeHelper::isManagerial($user) && $statusChanged) {
            if ($task->engineer_id) {
                \App\Models\Notification::create([
                    'user_id' => $task->engineer_id,
                    'title'   => 'Status Tugas Diperbarui',
                    'message' => 'Status tugas "' . $task->title . '" telah diubah menjadi ' . $task->status . ' oleh ' . $user->name . '.',
                    'url'     => route('tasks.index'),
                    'is_read' => false,
                ]);
            }
        }

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($task);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        abort_unless(ScopeHelper::canManageProjectsAndTasks(auth()->user()), 403, 'Hanya Team Leader yang berhak menghapus task.');

        $task->delete();

        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            return response()->json(['message' => 'Task berhasil dihapus!']);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task berhasil dihapus!');
    }

    public function getKanbanData()
    {
        $user     = auth()->user();
        $scopeIds = ScopeHelper::getScopeUserIds($user);

        $tasks = Task::with(['project', 'engineer'])
            ->when($scopeIds !== null, function($query) use ($scopeIds) {
                return count($scopeIds) === 1
                    ? $query->where('engineer_id', $scopeIds[0])
                    : $query->whereIn('engineer_id', $scopeIds);
            })
            ->get();

        $columns = ['Assigned', 'In Progress', 'Waiting Review', 'Completed'];
        $data    = [];

        foreach ($columns as $column) {
            $data[$column] = $tasks->where('status', $column)->values();
        }

        return response()->json($data);
    }
}