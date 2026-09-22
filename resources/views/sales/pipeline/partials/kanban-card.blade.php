{{-- Kanban Project Card (Official IPNet Style) --}}
<div class="bg-white rounded-xl p-3.5 border border-[#E2E8F0] shadow-xs hover:shadow-md hover:border-[#CBD5E1] transition duration-150 flex flex-col justify-between gap-2.5">
    <div>
        {{-- Project Name --}}
        <h4 class="text-[13px] font-bold text-[#1E293B] leading-snug line-clamp-2">
            <a href="{{ route('projects.show', $project->id) }}" class="hover:text-[#8F0A0D] transition">
                {{ $project->name }}
            </a>
        </h4>

        {{-- Client Name --}}
        <div class="flex items-center gap-1.5 mt-1.5 text-[11.5px] text-[#64748B] font-medium">
            <svg class="w-3.5 h-3.5 text-[#94A3B8] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="truncate">{{ $project->client }}</span>
        </div>
    </div>

    {{-- Value & Division Badge --}}
    <div class="pt-2 border-t border-[#F1F5F9] flex items-center justify-between text-[11.5px]">
        <div>
            @if($project->contract_value > 0)
                <span class="font-extrabold text-[#8F0A0D]">
                    Rp {{ number_format($project->contract_value / 1000000, 1, ',', '.') }} Jt
                </span>
            @else
                <span class="text-[#94A3B8] italic">Nilai TBA</span>
            @endif
        </div>

        @if($project->division)
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569] truncate max-w-[100px]" title="{{ $project->division->name }}">
                {{ $project->division->name }}
            </span>
        @elseif($project->sales_name)
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-50 text-[#8F0A0D] border border-red-100 truncate max-w-[100px]">
                {{ $project->sales_name }}
            </span>
        @endif
    </div>

    {{-- Footer Meta & Action --}}
    <div class="flex items-center justify-between pt-1 text-[10.5px]">
        <span class="text-[#94A3B8]">
            {{ \Carbon\Carbon::parse($project->updated_at)->diffForHumans() }}
        </span>
        <a href="{{ route('projects.show', $project->id) }}" class="text-[#8F0A0D] hover:underline font-bold flex items-center gap-0.5 transition">
            <span>Detail</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>
