{{-- Kanban Project Card (Matching Screenshot 1) --}}
<div class="bg-white rounded-xl p-4 border border-[#E2E8F0] shadow-2xs hover:shadow-md hover:border-[#CBD5E1] transition-all duration-200 flex flex-col justify-between gap-3.5 group">
    <div>
        {{-- Project Name --}}
        <h4 class="text-[13.5px] font-bold text-[#1E293B] leading-snug group-hover:text-[#8F0A0D] transition-colors">
            <a href="{{ route('projects.show', $project->id) }}">
                {{ $project->name }}
            </a>
        </h4>

        {{-- Milestone Status Badge --}}
        <div class="mt-2.5">
            @if($project->tasks && $project->tasks->count() > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} Milestones
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold text-white shadow-xs"
                      style="background: linear-gradient(135deg, #EF4444 0%, #B81525 55%, #8F0A0D 100%); box-shadow: 0 2px 6px rgba(184, 21, 37, 0.35);">
                    No milestones found
                </span>
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
    </div>
</div>
