@php
    $accent = $accent ?? false;
    $href = $href ?? null;
    $iconColor = $accent ? '#C81E2C' : '#3D3A44';
    $iconBg = $accent ? '#FDF1F2' : '#F4F2EF';
@endphp

@if($href)
    <a href="{{ $href }}" class="block group text-decoration-none focus:outline-none">
@else
    <div class="group">
@endif

<div class="wms-card wms-card-hover relative overflow-hidden p-4 sm:p-[18px] bg-white border border-[#EAE8E5] group-hover:border-[#C81E2C]/40 group-hover:shadow-[0_12px_28px_-6px_rgba(200,30,44,0.12)] transition-all duration-300 {{ $href ? 'cursor: pointer' : '' }}">
    {{-- Top Accent Bar --}}
    @if($accent)
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-[#C81E2C] via-[#E14B54] to-[#7A0D18]"></div>
    @else
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-transparent group-hover:bg-[#EAE8E5] transition-colors duration-300"></div>
    @endif

    {{-- Subtle Ambient Glow on Hover --}}
    <div class="absolute -right-8 -top-8 w-24 h-24 rounded-full bg-red-500/5 blur-xl group-hover:bg-red-500/10 transition-all duration-500 pointer-events-none"></div>

    <div class="flex items-center justify-between relative z-10">
        <span class="text-[11.5px] font-bold text-[#75727C] group-hover:text-[#17151C] uppercase tracking-[0.4px] transition-colors duration-200 truncate pr-2">
            {{ $label }}
        </span>
        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 transition-all duration-300 group-hover:scale-110 group-hover:rotate-6 shadow-xs" style="background: {{ $iconBg }};">
            <svg class="lucide lucide-{{ $icon }} transition-colors duration-200" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                {!! $slot !!}
            </svg>
        </div>
    </div>

    <div class="relative z-10 mt-2.5">
        <div class="metric-counter font-display text-[26px] sm:text-[30px] font-bold text-[#17151C] group-hover:text-[#C81E2C] tracking-[-0.5px] leading-tight transition-colors duration-200" data-target="{{ $value }}">
            {{ $value }}
        </div>
    </div>
</div>

@if($href)
    </a>
@else
    </div>
@endif