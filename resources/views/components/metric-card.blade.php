@php
    $accent = $accent ?? false;
    $href = $href ?? null;
    $iconColor = $accent ? '#C81E2C' : '#3D3A44';
    $iconBg = $accent ? '#FDF1F2' : '#F1F0EE';
@endphp

@if($href)
    <a href="{{ $href }}" class="block group text-decoration-none">
@endif

<div class="wms-card wms-card-hover group-hover:-translate-y-1 group-hover:border-[#C81E2C]/40 group-hover:shadow-md transition-all duration-300" style="padding: 17px 18px; position: relative; overflow: hidden; {{ $href ? 'cursor: pointer;' : '' }}">
    @if($accent)
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #C81E2C, #7A0D18);"></div>
    @endif
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <span style="font-size: 11.5px; color: #75727C; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px;" class="group-hover:text-[#17151C] transition-colors duration-200">{{ $label }}</span>
        <div class="group-hover:scale-110 group-hover:bg-[#FDF1F2] transition-all duration-300" style="width: 30px; height: 30px; border-radius: 8px; background: {{ $iconBg }}; display: flex; align-items: center; justify-content: center;">
            <svg class="lucide lucide-{{ $icon }}" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $slot !!}
            </svg>
        </div>
    </div>
    <div style="font-family: 'Space Grotesk', sans-serif; font-size: 29px; font-weight: 700; color: #17151C; margin-top: 11px; letter-spacing: -0.3px;" class="group-hover:text-[#C81E2C] transition-colors duration-200">{{ $value }}</div>
</div>

@if($href)
    </a>
@endif