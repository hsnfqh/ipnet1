@php
    $styles = [
        'Planning' => ['bg' => '#E8EAF0', 'fg' => '#2D3748', 'dot' => '#718096'],
        'On Progress' => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B'],
        'Completed' => ['bg' => '#D1FAE5', 'fg' => '#065F46', 'dot' => '#10B981'],
        'Assigned' => ['bg' => '#DBEAFE', 'fg' => '#1E40AF', 'dot' => '#3B82F6'],
        'In Progress' => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B'],
        'Waiting Review' => ['bg' => '#EDE9FE', 'fg' => '#5B21B6', 'dot' => '#8B5CF6'],
        'Active' => ['bg' => '#D1FAE5', 'fg' => '#065F46', 'dot' => '#10B981'],
        'Inactive' => ['bg' => '#FEE2E2', 'fg' => '#991B1B', 'dot' => '#EF4444'],
        'High' => ['bg' => '#FEE2E2', 'fg' => '#991B1B', 'dot' => '#EF4444'],
        'Medium' => ['bg' => '#FEF3C7', 'fg' => '#92400E', 'dot' => '#F59E0B'],
        'Low' => ['bg' => '#E2E8F0', 'fg' => '#475569', 'dot' => '#94A3B8'],
        'Meeting' => ['bg' => '#EFF6FF', 'fg' => '#1E40AF', 'dot' => '#3B82F6'],
        'Kegiatan' => ['bg' => '#FDF1F2', 'fg' => '#991B1B', 'dot' => '#C81E2C'],
        'Task' => ['bg' => '#FDF1F2', 'fg' => '#991B1B', 'dot' => '#C81E2C'],
        'Day Off' => ['bg' => '#F1F5F9', 'fg' => '#334155', 'dot' => '#64748B'],
    ];
    $s = $styles[$status] ?? ['bg' => '#E2E8F0', 'fg' => '#475569', 'dot' => '#94A3B8'];
@endphp

<span style="background: {{ $s['bg'] }}; color: {{ $s['fg'] }}; font-size: 11.5px; font-weight: 700; padding: 4px 10px 4px 8px; border-radius: 20px; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.1px; border: 1px solid {{ $s['dot'] }}33;">
    <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $s['dot'] }}; flex-shrink: 0; box-shadow: 0 0 6px {{ $s['dot'] }}66;"></span>
    {{ $status }}
</span>