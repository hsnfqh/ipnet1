{{-- Kanban Project Card (Matching Screenshot 1) --}}
<div class="bg-white rounded-xl p-4 border border-[#E2E8F0] shadow-2xs hover:shadow-md hover:border-[#CBD5E1] transition-all duration-200 flex flex-col justify-between gap-3.5 group">
    <div>
        {{-- Project Name --}}
        <h4 class="text-[13.5px] font-bold text-[#1E293B] leading-snug group-hover:text-[#8F0A0D] transition-colors">
            <a href="{{ route('projects.show', $project->id) }}">
                {{ $project->name }}
            </a>
        </h4>

        {{-- Milestone & Proposal Status Badges --}}
        <div class="mt-2.5 flex items-center flex-wrap gap-1.5">
            @if($project->tasks && $project->tasks->count() > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} Milestones
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold text-white shadow-xs"
                      style="background: linear-gradient(135deg, #EF4444 0%, #B81525 55%, #8F0A0D 100%);">
                    No milestones
                </span>
            @endif

            @php
                $hd = is_array($project->handover_data) ? $project->handover_data : [];
                $hasProposal = !empty($project->proposal_file) || !empty($hd['technical_assignments']['presales']['document_path']) || !empty($hd['technical_assignments']['architect']['document_path']);
            @endphp
            @if($hasProposal)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Proposal Ready</span>
                </span>
            @else
                <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 transition" title="Upload proposal teknis di detail project">
                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span>Upload Proposal</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Footer Info --}}
    <div class="pt-3 border-t border-[#F1F5F9] flex flex-col gap-2.5">
        <div class="flex items-center justify-between text-[11px]">
            {{-- Client Dept / Team Badge (Standalone Sales, no engineer division) --}}
            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $project->client_department ?: 'IPNET 01' }}
            </span>

            {{-- Icon Counters --}}
            <div class="flex items-center gap-3 text-slate-400 font-semibold text-[11px]">
                <div class="flex items-center gap-1" title="Milestones / Tasks">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>{{ $project->tasks ? $project->tasks->count() : 0 }}</span>
                </div>
                <div class="flex items-center gap-1" title="Attachments">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <span>{{ $project->relationLoaded('projectDocuments') ? $project->projectDocuments->count() : 0 }}</span>
                </div>
            </div>
        </div>

        {{-- Created By --}}
        <div class="flex items-center justify-between text-[10.5px] text-slate-400">
            <span>Created by</span>
            <span class="font-bold text-slate-700 truncate max-w-[150px]">
                {{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}
            </span>
        </div>

        @php
            $authCardUser = auth()->user();
            $authCardUserRoles = $authCardUser && method_exists($authCardUser, 'roles') ? $authCardUser->roles->pluck('name')->toArray() : [];
            $isPresalesUser = $authCardUser && (
                !empty(array_intersect(['Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect', 'SA'], $authCardUserRoles))
                || in_array(strtolower($authCardUser->position ?? ''), ['presales', 'pre-sales', 'solution architect', 'sa', 'pre sales'])
                || str_contains(strtolower($authCardUser->email ?? ''), 'akbar')
                || str_contains(strtolower($authCardUser->email ?? ''), 'aris')
            );
            $canMarkComplete = $authCardUser && !$isPresalesUser && (
                $project->created_by === $authCardUser->id
                || $project->sales_name === $authCardUser->name
                || ($project->sales_id && $project->sales_id === $authCardUser->id)
                || !empty(array_intersect(['Sales', 'Account Manager', 'PMO', 'Project Manager', 'Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader Commercial & Solution', 'Super Admin', 'Admin'], $authCardUserRoles))
            );
        @endphp

        @if($canMarkComplete && (($column ?? '') === 'In Progress' || $project->status === 'In Progress'))
            <form action="{{ route('projects.stage_update', $project->id) }}" method="POST" class="pt-2 border-t border-[#F1F5F9]" onclick="event.stopPropagation();">
                @csrf
                <input type="hidden" name="status" value="Completed">
                <button type="submit" onclick="return confirm('Tandai proyek {{ addslashes($project->name) }} sebagai Selesai (Completed)?')"
                        class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-2.5 rounded-lg bg-gradient-to-r from-[#8F0A0D] to-[#B81525] hover:from-[#7A080A] hover:to-[#A0121F] text-white text-[11px] font-bold transition shadow-2xs cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Tandai Selesai</span>
                </button>
            </form>
        @endif
    </div>
</div>
