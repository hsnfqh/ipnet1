@php
    $progress = min(100, max(0, $value));
    $gradient = $progress === 100 
        ? 'linear-gradient(90deg, #10B981, #059669)' 
        : 'linear-gradient(90deg, #C81E2C, #E14B54)';
@endphp

<div class="w-full bg-[#EAE8E5] rounded-full h-1.5 sm:h-2 overflow-hidden relative shadow-inner">
    <div class="h-full rounded-full transition-all duration-1000 ease-out"
         style="width: {{ $progress }}%; background: {{ $gradient }}; transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);"></div>
</div>