@php
    $size = $size ?? 32;
    $tone = $tone ?? '#3D3A44';
    $initials = collect(explode(' ', $name))
        ->map(fn($word) => substr($word, 0, 1))
        ->take(2)
        ->join('');
    $initials = strtoupper($initials);
@endphp

<div style="width: {{ $size }}px; height: {{ $size }}px; border-radius: 50%; background: {{ $tone }}; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 2px #FFFFFF, 0 1px 3px rgba(14,13,18,0.18); font-family: 'Inter', sans-serif; font-weight: 600; font-size: {{ $size * 0.36 }}px; flex-shrink: 0;">
    {{ $initials }}
</div>