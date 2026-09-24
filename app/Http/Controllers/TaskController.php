<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Http\Requests\TaskRequest;
use App\Helpers\ScopeHelper;
use App\Helpers\FileUploadHelper;

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
        $hasTaskUser  = Schema::hasTable('task_user');

        $withRelations = ['project', 'engineer', 'creator'];
        if ($hasTaskUser) {
            $withRelations[] = 'engineers';
        }

        // Auto-sinkronkan Tiket SLA / Insiden Maintenance ke Daftar Tugas secara satu pintu
        if (ScopeHelper::isMaintenance($user) || $isSupervisor || $isDirektur) {
            $activeTickets = \App\Models\ManagedServiceTicket::with(['assignedEngineer', 'asset', 'project'])
                ->whereIn('status', ['Open', 'In Progress', 'Pending Vendor', 'Resolved', 'Closed'])
                ->get();

            foreach ($activeTickets as $ticket) {
                \App\Http\Controllers\ManagedServiceController::syncTicketToTaskAndSchedule($ticket);
            }
        }

        // Auto-cleanup: Hapus task penugasan tim jika jadwal agenda dengan judul tersebut berkategori Meeting / Day Off
        try {
            $meetingScheduleTitles = \App\Models\Schedule::where(function($q) {
                $q->whereIn('category', ['Meeting', 'Day Off', 'Meeting Klien / Principal', 'Sesi PoC & Lab', 'PoC & Demo'])
                  ->orWhere('category', 'like', '%Meeting%')
                  ->orWhere('category', 'like', '%meeting%');
            })->pluck('title')->filter()->unique()->toArray();

            if (!empty($meetingScheduleTitles)) {
                Task::whereIn('title', $meetingScheduleTitles)->delete();
            }
        } catch (\Exception $e) {}

        $tasks = Task::with($withRelations)
            ->when($scopeIds !== null, function($query) use ($scopeIds, $user, $hasTaskUser) {
                return $query->where(function($q) use ($scopeIds, $user, $hasTaskUser) {
                    if (count($scopeIds) === 1) {
                        $q->where('engineer_id', $scopeIds[0]);
                        if ($hasTaskUser) {
                            $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                        }
                    } else {
                        $q->whereIn('engineer_id', $scopeIds);
                        if ($hasTaskUser) {
                            $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                        }
                    }
                    $q->orWhere('created_by', $user->id);
                });
            })
            ->orderByDesc('deadline')
            ->orderByDesc('created_at')
            ->get();

        // Project khusus untuk modal Buat & Assign Task: semua project yang relevan dengan divisi/scope user
        $divisionId = $user->division_id;
        $isGlobal   = ScopeHelper::isGlobal($user);

        $formProjectsQuery = Project::query()
            ->whereNotIn('status', ['Draft', 'draft'])
            ->where('stage', '!=', 'Draft');

        if ($divisionId && !$isGlobal) {
            $teamUserIds = ScopeHelper::getScopeUserIds($user) ?? [];
            $projectIdsWithTeamTasks = Task::whereIn('engineer_id', $teamUserIds)->pluck('project_id')->filter()->unique();

            $formProjectsQuery->where(function($q) use ($divisionId, $user, $projectIdsWithTeamTasks) {
                $q->where('division_id', $divisionId)
                  ->orWhere('created_by', $user->id);
                if ($projectIdsWithTeamTasks->isNotEmpty()) {
                    $q->orWhereIn('id', $projectIdsWithTeamTasks);
                }
            });
        }
        $formProjects = $formProjectsQuery->orderBy('name')->get();

        // Project untuk filter board (mencakup project divisi + project task yang sedang ada di board)
        $taskProjectIds = $tasks->pluck('project_id')->filter()->unique();
        $boardProjects = Project::whereIn('id', $taskProjectIds)->get();
        $projects = $formProjects->merge($boardProjects)->unique('id')->values();

        $engineers = ScopeHelper::getAssignableEngineers($user);
        $currentUserId = $user->id;
        $isPmo = ScopeHelper::isPmo($user);
        $isEngineer = $user->hasAnyRole(['Network Engineer', 'Security Engineer', 'Field Support (EOS)', 'Field Support', 'Engineer', 'Maintenance', 'Engineer L1', 'Engineer L2']);
        $isMaintenance = ScopeHelper::isMaintenance($user);
        $msTickets = collect([]);
        if ($isMaintenance || $isSupervisor || $isDirektur) {
            $msTickets = \App\Models\ManagedServiceTicket::with(['assignedEngineer', 'asset', 'project'])
                ->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])
                ->latest()
                ->get();
        }

        return view('tasks.index', compact('tasks', 'projects', 'formProjects', 'engineers', 'currentUserId', 'isLead', 'canManage', 'isDirektur', 'isSupervisor', 'isPmo', 'isEngineer', 'isMaintenance', 'msTickets'));
    }

    public function store(TaskRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['progress'] = 0;
        $data['attachments'] = 0;
        $data['status'] = $data['status'] ?? 'Assigned';

        // Kelola project 'other' / project baru yang ditulis sendiri oleh user / fallback SLA Maintenance
        if ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
            $projectName = trim($request->input('new_project_name'));
            if (!empty($projectName)) {
                $project = Project::firstOrCreate(
                    ['name' => $projectName],
                    [
                        'client'       => 'Internal / Lainnya',
                        'location'     => 'On-Site / Lapangan',
                        'start_date'   => now()->toDateString(),
                        'deadline'     => $request->filled('deadline') ? substr($request->input('deadline'), 0, 10) : now()->addMonth()->toDateString(),
                        'status'       => 'Planning',
                        'project_type' => 'One-Time Project',
                        'created_by'   => auth()->id(),
                    ]
                );
                $data['project_id'] = $project->id;
            }
        } elseif (empty($data['project_id'])) {
            $defaultSlaProject = Project::firstOrCreate(
                ['name' => 'Layanan SLA & Maintenance Support'],
                [
                    'client'       => 'Klien Managed Service',
                    'location'     => 'On-Site / Remote Support',
                    'start_date'   => now()->toDateString(),
                    'deadline'     => now()->addYear()->toDateString(),
                    'status'       => 'In Progress',
                    'project_type' => 'Maintenance SLA',
                    'division_id'  => 3,
                    'created_by'   => auth()->id(),
                ]
            );
            $data['project_id'] = $defaultSlaProject->id;
        }
        unset($data['new_project_name']);

        // Kelola rentang tanggal (start_date & deadline/end_date)
        $startDateStr = $request->filled('start_date') ? substr($request->input('start_date'), 0, 10) : null;
        $endDateStr   = $request->filled('end_date') 
            ? substr($request->input('end_date'), 0, 10) 
            : ($request->filled('deadline') ? substr($request->input('deadline'), 0, 10) : null);

        if (!$startDateStr && $endDateStr) {
            $startDateStr = $endDateStr;
        }
        if (!$endDateStr && $startDateStr) {
            $endDateStr = $startDateStr;
        }
        if (!$startDateStr && !$endDateStr) {
            $startDateStr = now()->toDateString();
            $endDateStr = $startDateStr;
        }

        $data['start_date'] = $startDateStr;
        unset($data['end_date']);

        // Kelola deadline & opsional jam (deadline_time)
        $deadlineTime = $request->filled('deadline_time') ? trim($request->input('deadline_time')) : null;
        if (!empty($deadlineTime)) {
            if (strlen($deadlineTime) === 5) {
                $deadlineTime .= ':00';
            }
            $data['deadline'] = $endDateStr . ' ' . $deadlineTime;
            $data['deadline_time'] = $deadlineTime;
        } else {
            $data['deadline'] = $endDateStr . ' 00:00:00';
            $data['deadline_time'] = null;
        }

        // Kelola multi-assignee (engineer_ids)
        $engineerIds = $request->input('engineer_ids', []);
        if (empty($engineerIds) && !empty($data['engineer_id'])) {
            $engineerIds = [(int) $data['engineer_id']];
        }
        if (!empty($engineerIds)) {
            $data['engineer_id'] = $engineerIds[0];
        }
        unset($data['engineer_ids']);
        
        $hasTaskUser = Schema::hasTable('task_user');
        $task = Task::create($data);
        if ($hasTaskUser && !empty($engineerIds)) {
            $task->engineers()->sync($engineerIds);
        }
        $withLoad = ['project', 'engineer', 'creator'];
        if ($hasTaskUser) {
            $withLoad[] = 'engineers';
        }
        $task = $task->load($withLoad);

        // Kirim notifikasi ke seluruh engineer/tim yang ditugaskan
        $creator = auth()->user();
        $creatorName = $creator ? $creator->name : 'Team Leader';
        foreach ($engineerIds as $engId) {
            \App\Models\Notification::create([
                'user_id' => (int) $engId,
                'title'   => 'Tugas Baru Ditugaskan',
                'message' => 'Anda ditugaskan oleh ' . $creatorName . ' pada tugas: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                'url'     => route('tasks.index'),
                'is_read' => false,
            ]);
        }

        // Otomatis sinkronkan Task baru ke Jadwal Kerja (Schedule) untuk setiap hari dalam rentang
        if ($task->deadline) {
            $taskStartDate = $task->start_date ? $task->start_date->format('Y-m-d') : $task->deadline->format('Y-m-d');
            $taskEndDate   = $task->deadline->format('Y-m-d');
            $taskTime = $task->deadline_time 
                ? substr($task->deadline_time, 0, 5) 
                : ($task->deadline->format('H:i') !== '00:00' ? $task->deadline->format('H:i') : null);

            $schedCategory = (ScopeHelper::isMaintenance(auth()->user()) || preg_match('/^\[(PM|CM|INC|REQ|CR|TCK)-[0-9\-]+\]/', $task->title)) 
                ? 'Preventive Maintenance' 
                : 'Task';

            $curDate = \Carbon\Carbon::parse($taskStartDate);
            $endDate = \Carbon\Carbon::parse($taskEndDate);
            if ($curDate->gt($endDate)) {
                $temp = $curDate;
                $curDate = $endDate;
                $endDate = $temp;
            }

            while ($curDate->lte($endDate)) {
                if ($taskStartDate === $taskEndDate || !$curDate->isSunday()) {
                    $curStr = $curDate->toDateString();
                    $newSchedule = \App\Models\Schedule::updateOrCreate(
                        [
                            'title' => $task->title,
                            'date'  => $curStr,
                        ],
                        [
                            'project_id'  => $task->project_id,
                            'category'    => $schedCategory,
                            'engineer_id' => $task->engineer_id,
                            'start_time'  => $taskTime ? $taskTime . ':00' : null,
                            'end_time'    => $taskTime ? date('H:i:s', strtotime($taskTime . ' +3 hours')) : null,
                            'location'    => $task->project ? ($task->project->location ?? 'On-Site Client') : 'On-Site Client',
                            'description' => $task->description ?? ('Pengerjaan task: ' . $task->title),
                            'created_by'  => $task->created_by ?? auth()->id(),
                        ]
                    );

                    if (Schema::hasTable('schedule_user') && !empty($engineerIds)) {
                        $newSchedule->engineers()->sync($engineerIds);
                    }
                }
                $curDate->addDay();
            }
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

        $rules = [
            'title'             => 'sometimes|required|string|max:255',
            'project_id'        => 'sometimes|required',
            'new_project_name'  => 'sometimes|nullable|string|max:255',
            'engineer_id'       => 'sometimes|nullable|exists:users,id',
            'engineer_ids'      => 'sometimes|nullable|array|min:1',
            'engineer_ids.*'    => 'exists:users,id',
            'priority'          => 'sometimes|required|in:High,Medium,Low',
            'start_date'        => 'sometimes|nullable|date',
            'end_date'          => 'sometimes|nullable|date',
            'deadline'          => 'sometimes|nullable|date',
            'deadline_time'     => 'sometimes|nullable|string',
            'description'       => 'sometimes|nullable|string',
            'status'            => 'sometimes|required|in:Assigned,In Progress,Waiting Review,Completed',
            'progress'          => 'sometimes|nullable|integer|min:0|max:100',
            'doc_file'          => \App\Helpers\FileUploadHelper::fileValidationRule(51200),
        ];
        if ($request->has('project_id') && $request->input('project_id') !== 'other') {
            $rules['project_id'] .= '|exists:projects,id';
        }
        $validated = $request->validate($rules);

        // Kelola project 'other' / project baru jika diedit
        if ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
            $projectName = trim($request->input('new_project_name'));
            if (!empty($projectName)) {
                $project = Project::firstOrCreate(
                    ['name' => $projectName],
                    [
                        'client'       => 'Internal / Lainnya',
                        'location'     => 'On-Site / Lapangan',
                        'start_date'   => now()->toDateString(),
                        'deadline'     => $request->filled('deadline') ? substr($request->input('deadline'), 0, 10) : now()->addMonth()->toDateString(),
                        'status'       => 'Planning',
                        'project_type' => 'One-Time Project',
                        'created_by'   => auth()->id(),
                    ]
                );
                $validated['project_id'] = $project->id;
            }
        }
        unset($validated['new_project_name']);

        // Kelola rentang tanggal (start_date & deadline/end_date)
        if ($request->has('start_date') || $request->has('end_date') || $request->has('deadline')) {
            $startDateStr = $request->filled('start_date') ? substr($request->input('start_date'), 0, 10) : ($task->start_date ? $task->start_date->format('Y-m-d') : null);
            $endDateStr   = $request->filled('end_date') 
                ? substr($request->input('end_date'), 0, 10) 
                : ($request->filled('deadline') ? substr($request->input('deadline'), 0, 10) : ($task->deadline ? $task->deadline->format('Y-m-d') : null));

            if (!$startDateStr && $endDateStr) $startDateStr = $endDateStr;
            if (!$endDateStr && $startDateStr) $endDateStr = $startDateStr;
            if ($startDateStr) $validated['start_date'] = $startDateStr;
            if ($endDateStr) $validated['deadline'] = $endDateStr;
        }

        // Kelola deadline & opsional jam
        if (isset($validated['deadline'])) {
            $deadlineTime = $request->filled('deadline_time') ? trim($request->input('deadline_time')) : null;
            if (!empty($deadlineTime)) {
                if (strlen($deadlineTime) === 5) {
                    $deadlineTime .= ':00';
                }
                $datePart = substr($validated['deadline'], 0, 10);
                $validated['deadline'] = $datePart . ' ' . $deadlineTime;
                $validated['deadline_time'] = $deadlineTime;
            } else {
                $validated['deadline'] = substr($validated['deadline'], 0, 10) . ' 00:00:00';
                $validated['deadline_time'] = null;
            }
        }
        if (isset($validated['deadline_time']) && !Schema::hasColumn('tasks', 'deadline_time')) {
            unset($validated['deadline_time']);
        }
        unset($validated['end_date']);

        // Multi-assignee sync
        if ($request->has('engineer_ids') || $request->has('engineer_id')) {
            $rawEngIds = $request->input('engineer_ids');
            if (!empty($rawEngIds)) {
                $engineerIds = array_values(array_unique(array_filter(array_map('intval', (array) $rawEngIds))));
            } elseif ($request->filled('engineer_id')) {
                $engineerIds = [(int) $request->input('engineer_id')];
            } else {
                $engineerIds = [];
            }
            if (!empty($engineerIds)) {
                $task->engineer_id = $engineerIds[0];
                if (Schema::hasTable('task_user')) {
                    $task->engineers()->sync($engineerIds);
                }
            }
        }
        unset($validated['engineer_ids']);

        // Handle file upload dokumentasi
        if ($request->hasFile('doc_file')) {
            if ($task->doc_file) {
                FileUploadHelper::delete($task->doc_file);
            }
            $path = FileUploadHelper::storePublicly($request->file('doc_file'), 'task-docs');
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
        $oldTaskTitle = $task->getOriginal('title');
        $statusChanged = $task->isDirty('status');
        $progressChanged = $task->isDirty('progress');
        $engineerChanged = $task->isDirty('engineer_id');

        $task->save();
        $task = $task->load(['project', 'engineer', 'engineers', 'creator']);

        // Multi-assignee list
        $allAssigneeIds = [];
        if ($task->relationLoaded('engineers') && $task->engineers->isNotEmpty()) {
            $allAssigneeIds = $task->engineers->pluck('id')->toArray();
        } elseif ($task->engineer_id) {
            $allAssigneeIds = [$task->engineer_id];
        }

        // Sinkronkan perubahan judul, tanggal/jam task, dan engineer ke Jadwal untuk setiap hari dalam rentang
        if ($task->deadline) {
            $schedCategory = (ScopeHelper::isMaintenance(auth()->user()) || preg_match('/^\[(PM|CM|INC|REQ|CR|TCK)-[0-9\-]+\]/', $task->title)) 
                ? 'Preventive Maintenance' 
                : 'Task';
            
            $startTime = $task->deadline_time ? substr($task->deadline_time, 0, 5) : ($task->deadline->format('H:i') !== '00:00' ? $task->deadline->format('H:i') : null);
            $taskStartDate = $task->start_date ? $task->start_date->format('Y-m-d') : $task->deadline->format('Y-m-d');
            $taskEndDate   = $task->deadline->format('Y-m-d');

            $curDate = \Carbon\Carbon::parse($taskStartDate);
            $endDate = \Carbon\Carbon::parse($taskEndDate);
            if ($curDate->gt($endDate)) {
                $temp = $curDate;
                $curDate = $endDate;
                $endDate = $temp;
            }

            // Update title lama jika di-rename
            if (!empty($oldTaskTitle) && $oldTaskTitle !== $task->title) {
                \App\Models\Schedule::where('title', $oldTaskTitle)->update(['title' => $task->title]);
            }

            while ($curDate->lte($endDate)) {
                if ($taskStartDate === $taskEndDate || !$curDate->isSunday()) {
                    $curStr = $curDate->toDateString();
                    $sched = \App\Models\Schedule::updateOrCreate(
                        [
                            'title' => $task->title,
                            'date'  => $curStr,
                        ],
                        [
                            'project_id'  => $task->project_id,
                            'category'    => $schedCategory,
                            'engineer_id' => $task->engineer_id,
                            'start_time'  => $startTime ? $startTime . ':00' : null,
                            'end_time'    => $startTime ? date('H:i:s', strtotime($startTime . ' +3 hours')) : null,
                            'location'    => $task->project ? ($task->project->location ?? 'On-Site Client') : 'On-Site Client',
                            'description' => $task->description ?? ('Pengerjaan task: ' . $task->title),
                            'created_by'  => $task->created_by ?? auth()->id(),
                        ]
                    );

                    if (Schema::hasTable('schedule_user') && !empty($allAssigneeIds)) {
                        $sched->engineers()->sync($allAssigneeIds);
                    }
                }
                $curDate->addDay();
            }
        }

        // Sinkronkan kembali ke Tiket SLA jika task berasal dari tiket (termasuk PM dan CM)
        if (preg_match('/^\[(PM|CM|INC|REQ|CR|TCK)-[0-9\-]+\]/', $task->title, $matches)) {
            $ticketNum = trim($matches[0], '[]');
            $ticket = \App\Models\ManagedServiceTicket::where('ticket_number', $ticketNum)->first();
            if ($ticket) {
                $ticketStatus = match($task->status) {
                    'Assigned' => 'Open',
                    'In Progress' => 'In Progress',
                    'Waiting Review' => 'Resolved',
                    'Completed' => 'Closed',
                    default => $ticket->status,
                };
                $ticketUpdates = ['status' => $ticketStatus];
                if ($task->engineer_id) {
                    $ticketUpdates['assigned_to'] = $task->engineer_id;
                }
                if ($ticketStatus === 'Closed' && !$ticket->resolved_at) {
                    $ticketUpdates['resolved_at'] = now();
                }
                $ticket->update($ticketUpdates);
            }
        }

        // Kirim notifikasi jika penugasan engineer diubah atau diperbarui
        if ($engineerChanged || $request->has('engineer_ids') || $request->has('engineer_id')) {
            $assigner = auth()->user();
            $assignerName = $assigner ? $assigner->name : 'Team Leader';
            foreach ($allAssigneeIds as $engId) {
                \App\Models\Notification::create([
                    'user_id' => (int) $engId,
                    'title'   => 'Penugasan Tugas: ' . $task->title,
                    'message' => 'Anda ditugaskan oleh ' . $assignerName . ' pada tugas: "' . $task->title . '" untuk proyek ' . ($task->project?->name ?? 'Project'),
                    'url'     => route('tasks.index'),
                    'is_read' => false,
                ]);
            }
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

        // Kirim notifikasi ke seluruh Engineer terkait jika manajerial mengubah status task
        if ($user && ScopeHelper::isManagerial($user) && $statusChanged) {
            foreach ($allAssigneeIds as $engId) {
                \App\Models\Notification::create([
                    'user_id' => (int) $engId,
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

        \App\Models\Schedule::where('title', $task->title)
            ->where('project_id', $task->project_id)
            ->delete();

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