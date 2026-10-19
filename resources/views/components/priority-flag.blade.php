@php
    $colors = [
        'High' => '#8F0A0D',
        'Medium' => '#9A6206',
        'Low' => '#75727C',
    ];
    $color = $colors[$level] ?? '#75727C';
@endphp

<span style="display: inline-flex; align-items: center; gap: 5px; font-size: 11.5px; font-weight: 700; color: {{ $color }}; text-transform: uppercase; letter-spacing: 0.3px;">
    <svg class="lucide lucide-flag" width="12" height="12" viewBox="0 0 24 24" fill="{{ $color }}" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 21V3M4 7l16-3v12l-16 3Z"/>
    </svg>
    {{ $level }}
    @if($level === 'High')
        <span class="inline-block w-[14px] h-1.5 rounded" style="background-image: linear-gradient(135deg, #E14B54 0%, #AF1424 55%, #5C0A13 100%); margin-left: 2px;"></span>
    @endif
</span>