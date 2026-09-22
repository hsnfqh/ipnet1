{{-- Kanban Project Card --}}
<div class="bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-xs hover:shadow-md hover:border-slate-300 transition duration-150 flex flex-col justify-between gap-2.5">
    <div>
        {{-- Project Name --}}
        <h4 class="text-xs font-bold text-slate-900 leading-snug line-clamp-2">
            <a href="{{ route('projects.show', $project->id) }}" class="hover:text-red-700 transition">
                {{ $project->name }}
            </a>
        </h4>

        {{-- Client Name --}}
        <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-500 font-medium">
            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="truncate">{{ $project->client }}</span>
        </div>
    </div>

    {{-- Value & Meta --}}
    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
        <div>
            @if($project->contract_value > 0)
                <span class="font-bold text-slate-800">
                    Rp {{ number_format($project->contract_value / 1000000, 1, ',', '.') }} Jt
                </span>
            @else
                <span class="text-slate-400 italic">Nilai TBA</span>
            @endif
        </div>

        @if($project->division)
            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 truncate max-w-[90px]" title="{{ $project->division->name }}">
                {{ $project->division->name }}
            </span>
        @elseif($project->sales_name)
            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-red-50 text-red-700 truncate max-w-[90px]">
                {{ $project->sales_name }}
            </span>
        @endif
    </div>

    {{-- Action link --}}
    <div class="flex items-center justify-between pt-1 text-[10.5px]">
        <span class="text-slate-400">
            {{ \Carbon\Carbon::parse($project->updated_at)->diffForHumans() }}
        </span>
        <a href="{{ route('projects.show', $project->id) }}" class="text-slate-600 hover:text-red-700 font-bold flex items-center gap-0.5 transition">
            <span>Detail</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
