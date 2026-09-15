@php
    $styles = [
        'Planning'       => ['bg' => '#F1F3F9', 'fg' => '#334155', 'dot' => '#64748B', 'pulse' => false],
        'On Progress'    => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B', 'pulse' => true],
        'In Progress'    => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B', 'pulse' => true],
        'Completed'      => ['bg' => '#ECFDF5', 'fg' => '#065F46', 'dot' => '#10B981', 'pulse' => false],
        'Assigned'       => ['bg' => '#EFF6FF', 'fg' => '#1E40AF', 'dot' => '#3B82F6', 'pulse' => true],
        'Waiting Review' => ['bg' => '#F5F3FF', 'fg' => '#5B21B6', 'dot' => '#8B5CF6', 'pulse' => true],
        'Active'         => ['bg' => '#ECFDF5', 'fg' => '#065F46', 'dot' => '#10B981', 'pulse' => true],
        'Inactive'       => ['bg' => '#FEF2F2', 'fg' => '#991B1B', 'dot' => '#EF4444', 'pulse' => false],
        'High'           => ['bg' => '#FEF2F2', 'fg' => '#991B1B', 'dot' => '#EF4444', 'pulse' => false],
        'Medium'         => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B', 'pulse' => false],
        'Low'            => ['bg' => '#F1F5F9', 'fg' => '#475569', 'dot' => '#94A3B8', 'pulse' => false],
        'Meeting'        => ['bg' => '#EFF6FF', 'fg' => '#1E40AF', 'dot' => '#3B82F6', 'pulse' => false],
        'Kegiatan'       => ['bg' => '#FDF1F2', 'fg' => '#991B1B', 'dot' => '#C81E2C', 'pulse' => false],
        'Task'           => ['bg' => '#FDF1F2', 'fg' => '#991B1B', 'dot' => '#C81E2C', 'pulse' => false],
        'Day Off'        => ['bg' => '#F1F5F9', 'fg' => '#334155', 'dot' => '#64748B', 'pulse' => false],
        'Sesi PoC & Lab' => ['bg' => '#ECFDF5', 'fg' => '#065F46', 'dot' => '#10B981', 'pulse' => true],
        'Review Desain & SOW' => ['bg' => '#EFF6FF', 'fg' => '#1E40AF', 'dot' => '#3B82F6', 'pulse' => false],
        'Meeting Klien / Principal' => ['bg' => '#F5F3FF', 'fg' => '#5B21B6', 'dot' => '#8B5CF6', 'pulse' => false],
        'Kalkulasi BoQ'  => ['bg' => '#FFFBEB', 'fg' => '#92400E', 'dot' => '#F59E0B', 'pulse' => false],
    ];
    $s = $styles[$status] ?? ['bg' => '#F1F5F9', 'fg' => '#475569', 'dot' => '#94A3B8', 'pulse' => false];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold tracking-[0.1px] whitespace-nowrap border shadow-2xs transition-all duration-200"
      style="background: {{ $s['bg'] }}; color: {{ $s['fg'] }}; border-color: {{ $s['dot'] }}33;">
    <span class="relative flex h-2 w-2 shrink-0">
        @if($s['pulse'])
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background: {{ $s['dot'] }};"></span>
        @endif
        <span class="relative inline-flex rounded-full h-2 w-2" style="background: {{ $s['dot'] }}; box-shadow: 0 0 4px {{ $s['dot'] }}88;"></span>
    </span>
    {{ $status }}
</span>