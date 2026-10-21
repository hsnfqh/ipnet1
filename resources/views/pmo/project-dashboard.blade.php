@extends('layouts.app')

@section('title', 'Project Dashboard - ' . $project->name)

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Project Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            {{-- Flash Alert Message --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. PROJECT HEADER & NAVIGATION                             -->
            <!-- ========================================================== -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('pmo.implementations.index') }}" 
                               class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] inline-flex items-center gap-1 transition-colors">
                                <span>&larr; Projects</span>
                            </a>
                            <span class="text-slate-300">&bull;</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
                                {{ $daysSpan }} Days Span
                            </span>
                            @if($healthStatus === 'Healthy')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    On-Track
                                </span>
                            @elseif($healthStatus === 'At-Risk')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    At-Risk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Delayed
                                </span>
                            @endif
                        </div>
                        <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-snug">
                            {{ $project->name }}
                        </h2>
                        <div class="flex items-center gap-2 sm:gap-3 text-xs text-slate-500 flex-wrap">
                            <span class="font-bold text-slate-700">{{ $project->client ?: 'Client IPNET' }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>{{ $project->division ? $project->division->name : 'Divisi Belum Didelegasikan' }}</span>
                            <span class="text-slate-300">&bull;</span>
                            <span>PM: <strong class="text-slate-800">{{ $project->pm ? $project->pm->name : 'Rizki' }}</strong></span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <a href="{{ route('projects.show', $project->id) }}" 
                           class="px-4 py-2 rounded-xl border border-slate-200/80 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Detail Proyek Lengkap</span>
                        </a>
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition shadow-2xs flex items-center gap-1.5">
                            <span>Daftar Projects</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. 4 PROJECT METRIC CARDS                                  -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                
                {{-- Card 1: Total Budget Spend --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Budget Spend</span>
                            <h3 class="text-xs font-semibold text-slate-600">Realisasi Biaya PM</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 tracking-tight">
                                Rp {{ number_format($totalSpend, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>Serapan Anggaran</span>
                            <span class="font-semibold text-slate-700">{{ $spendPercentage }}% Terpakai</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Contract Value & Sisa Anggaran --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Project Budget</span>
                            <h3 class="text-xs font-semibold text-slate-600">Nilai Kontrak Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 tracking-tight">
                                Rp {{ number_format($contractValue, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>Sisa Anggaran:</span>
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Total Task List & Progress EV --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Task List</span>
                            <h3 class="text-xs font-semibold text-slate-600">Workload Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 tracking-tight">{{ $totalTasks }}</span>
                            <span class="text-xs font-semibold text-slate-500">Tasks</span>
                            <span class="text-xs font-bold text-slate-700 ml-auto">Actual EV: {{ $actualEV }}%</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100">
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full bg-[#8F0A0D] rounded-full transition-all duration-300" style="width: {{ $actualEV }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Portfolio / Project Status --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Project Status</span>
                            <h3 class="text-xs font-semibold text-slate-600">Kesehatan S-Curve</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $healthStatus === 'Healthy' ? 'bg-emerald-500' : ($healthStatus === 'At-Risk' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                            <span class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 tracking-tight">{{ $healthStatus }}</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>S-Curve SLA:</span>
                            <span class="font-semibold {{ $healthStatus === 'Healthy' ? 'text-emerald-700' : ($healthStatus === 'At-Risk' ? 'text-amber-700' : 'text-rose-700') }}">
                                {{ $healthStatusText }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. WORKSPACE: TASK SHARE (LEFT) & LOG EXPENSE PM (RIGHT)    -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT COLUMN: Task Share by Engineer & Sub-Scope --}}
                <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Task Share by Engineer</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Alokasi beban tugas tim teknis di proyek ini</p>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200/60">
                            {{ $totalTasks }} Tasks
                        </span>
                    </div>

                    @if($totalTasks > 0)
                        <div style="height: 190px;" class="flex items-center justify-center relative">
                            <canvas id="projectTaskShareChart"></canvas>
                        </div>
                    @else
                        <div class="h-[180px] flex flex-col items-center justify-center text-center p-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <p class="text-xs font-semibold text-slate-700">Belum Ada Task Terdaftar</p>
                            <p class="text-[11px] text-slate-400 max-w-[220px] mt-0.5 leading-relaxed">
                                Tambahkan tugas teknis di detail proyek untuk memantau porsi alokasi kerja.
                            </p>
                        </div>
                    @endif

                    {{-- Breakdown List --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        @forelse($taskShareList as $item)
                            <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-xl hover:bg-slate-50 transition">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <span class="font-semibold text-slate-800 truncate">{{ $item['name'] }}</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-700">{{ $item['count'] }} Tasks</span>
                                    <span class="text-[11px] text-slate-400 block font-medium">({{ $item['percentage'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs">
                                Belum ada pembagian tugas engineer.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT COLUMN: 3. LOG CATEGORIZED EXPENSE (SEPERTI GAMBAR 3) --}}
                <div class="lg:col-span-7 space-y-4">
                    
                    {{-- Form Box "Log Categorized Expense" --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-base">💳</span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Log Categorized Expense</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Catat realisasi biaya operasional &amp; belanja proyek oleh PM</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-700">
                                Total: Rp {{ number_format($totalSpend, 0, ',', '.') }}
                            </span>
                        </div>

                        <form action="{{ route('pmo.projects.expenses.store', $project->id) }}" method="POST" class="space-y-3.5">
                            @csrf
                            
                            {{-- Field 1: Category --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                                <select name="category" required 
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none cursor-pointer">
                                    <option value="Hardware">Hardware &amp; Perangkat</option>
                                    <option value="Transport">Transport &amp; Akomodasi Lapangan</option>
                                    <option value="Meals & Operational">Konsumsi &amp; Operasional Tim</option>
                                    <option value="Subcontractor">Jasa Subkontraktor &amp; Vendor</option>
                                    <option value="Permits & Legal">Perizinan &amp; Legal</option>
                                    <option value="Others" selected>Others</option>
                                </select>
                            </div>

                            {{-- Field 2: Amount (IDR) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Amount (IDR)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" 
                                           name="amount" 
                                           min="1" 
                                           step="1000"
                                           placeholder="Contoh: 900000" 
                                           required 
                                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                                </div>
                            </div>

                            {{-- Field 3: Description / Receipt Note --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / No. Nota</label>
                                    <input type="text" 
                                           name="description" 
                                           placeholder="Keterangan pengeluaran..." 
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
                                    <input type="date" 
                                           name="expense_date" 
                                           value="{{ date('Y-m-d') }}"
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none cursor-pointer">
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-1">
                                <button type="submit" 
                                        class="w-full py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-xs flex items-center justify-center gap-2 cursor-pointer">
                                    <span>Add Expense</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Riwayat Pengeluaran (Expense Logs) --}}
                    <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Riwayat Pengeluaran Proyek</h4>
                            <span class="text-xs font-semibold text-slate-400">{{ $expenses->count() }} Transaksi</span>
                        </div>

                        <div class="space-y-2.5">
                            @forelse($expenses as $exp)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/70 border border-slate-200/60 hover:bg-slate-50 transition">
                                    <div class="min-w-0 pr-3">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-amber-100 text-amber-900">
                                                {{ $exp['category'] }}
                                            </span>
                                            <span class="text-[11px] text-slate-400">{{ $exp['expense_date'] }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-slate-800 mt-1 truncate">
                                            {{ $exp['description'] }}
                                        </p>
                                        <span class="text-[10.5px] text-slate-400 block">Oleh: {{ $exp['creator_name'] ?? 'PM' }}</span>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-xs font-extrabold text-slate-900">
                                            {{ $exp['formatted'] }}
                                        </span>
                                        <form action="{{ route('pmo.projects.expenses.destroy', [$project->id, $exp['id']]) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                                    title="Hapus Pengeluaran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs font-medium border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                                    Belum ada catatan pengeluaran untuk proyek ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shareData = @json($taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []]);
        const ctx = document.getElementById('projectTaskShareChart');
        if (ctx && shareData.labels && shareData.labels.length > 0) {
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: shareData.labels,
                    datasets: [{
                        data: shareData.counts,
                        backgroundColor: shareData.colors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ` ${context.label}: ${val} Tasks (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
