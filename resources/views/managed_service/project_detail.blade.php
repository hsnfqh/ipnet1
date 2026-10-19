@extends('layouts.app')

@section('title', 'Detail Proyek - ' . $project->name)

@section('content')
<div class="space-y-6" x-data="{ isDocModalOpen: false, selectedDocType: 'spk', selectedDocTitle: '', isSlaModalOpen: false }">
    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('ms.projects.index') }}" class="hover:text-slate-800 transition">Daftar Proyek</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#8F0A0D]">Detail Proyek & Dokumen</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $project->client }}</h1>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-[#8F0A0D]/10 text-[#8F0A0D] border border-[#8F0A0D]/20">
                    {{ $project->project_type ?: 'Managed Service SLA' }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ $project->name }}</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button type="button" @click="isSlaModalOpen = true" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs cursor-pointer">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Parameter SLA</span>
            </button>
            <a href="{{ route('ms.monitoring.index', ['project_id' => $project->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Monitoring PM / CM</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    <!-- BAGIAN 1: INFORMASI CUSTOMER & DOKUMEN PROYEK (SPK, KONTRAK, BOQ, BA, DOKUMEN PROYEK) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Customer & Sales PIC Card -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Data Customer & PIC</h2>
                </div>
                <span class="text-[11px] font-bold text-slate-500">ID: #PROJ-{{ $project->id }}</span>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Nama Customer / Perusahaan</label>
                    <p class="text-sm font-black text-slate-900 mt-0.5">{{ $project->client }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <label class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Sales PIC</label>
                        <p class="text-xs font-black text-slate-900 mt-1">{{ $project->sales_name ?: ($project->creator?->name ?: 'Raiza') }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <label class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Pre-Sales / SA</label>
                        <p class="text-xs font-black text-slate-900 mt-1">{{ $project->presales_name ?: 'Akbar' }}</p>
                    </div>
                </div>

                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-[11px] text-slate-400 font-medium">Customer PIC (Technical):</span>
                        <p class="text-xs font-semibold text-slate-800">{{ $project->customer_pic_technical ?: 'Bpk. Budi Santoso (0812-3456-7890)' }}</p>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 font-medium">Customer PIC (Procurement / Business):</span>
                        <p class="text-xs font-semibold text-slate-800">{{ $project->customer_pic_business ?: 'Ibu Diana Putri' }}</p>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 font-medium">Customer PIC (Finance):</span>
                        <p class="text-xs font-semibold text-slate-800">{{ $project->customer_pic_finance ?: 'Bpk. Hendra Gunawan' }}</p>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-400 font-medium">Lokasi Operasional / Site:</span>
                        <p class="text-xs font-semibold text-slate-800">{{ $project->location ?: 'On-Site Klien (Data Center / Head Office)' }}</p>
                    </div>
                    @if($project->contract_value)
                        <div>
                            <span class="text-[11px] text-slate-400 font-medium">Nilai Kontrak:</span>
                            <p class="text-xs font-extrabold text-[#8F0A0D]">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 5 Dokumen Legal & Proyek: SPK, Kontrak, BOQ, BA, Dokumen Project -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-slate-900"></span>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Berkas & Dokumen Kontrak</h2>
                </div>
                <span class="text-xs text-slate-500 font-medium">SPK, Kontrak, BOQ, BA, Dokumen Project</span>
            </div>

            <div class="space-y-3">
                @foreach($documents as $docKey => $doc)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white transition shadow-2xs">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 mt-0.5 border border-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xs font-bold text-slate-900 truncate">{{ $doc['title'] }}</h3>
                                    @if($doc['file_path'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Belum Ada</span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $doc['description'] }}</p>
                                @if($doc['number'])
                                    <p class="text-[10.5px] font-mono text-slate-600 mt-0.5 font-semibold">No: {{ $doc['number'] }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                            @if($doc['file_path'])
                                <a href="{{ asset('storage/' . $doc['file_path']) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-bold transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Lihat File</span>
                                </a>
                            @endif
                            <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-[#8F0A0D] text-[11px] font-bold transition flex items-center gap-1.5 border border-rose-200 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>{{ $doc['file_path'] ? 'Ganti File' : 'Upload' }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: PARAMETER KONTRAK & SLA MANAGED SERVICE (SESUAI GAMBAR 3) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Parameter Layanan Managed Service & SLA</h2>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Spesifikasi komitmen SLA, kuota Corrective Maintenance (CM), dan frekuensi Preventive Maintenance (PM).</p>
            </div>
            <button type="button" @click="isSlaModalOpen = true" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-50 transition cursor-pointer self-start sm:self-auto">
                Ubah Parameter SLA
            </button>
        </div>

        <!-- 6 Metrics Parameter SLA (Sesuai Sketsa: Durasi, SLA %, MTTR, Coverage Support, Call Response, Problem Resolution) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Durasi Maintenance</span>
                <p class="text-sm font-black text-slate-900">
                    @if($project->service_start_date && $project->service_end_date)
                        {{ \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') }}
                    @else
                        1 Tahun Kontrak
                    @endif
                </p>
                <span class="text-[10px] text-slate-400">Periode Kontrak</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">SLA Commitment</span>
                <p class="text-lg font-black text-[#8F0A0D]">{{ $project->sla_target_percentage ?: '99.5' }}%</p>
                <span class="text-[10px] text-slate-400">Target Availability</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">MTTR</span>
                <p class="text-lg font-black text-slate-900">{{ $project->mttr_hours ?: 4 }} Jam</p>
                <span class="text-[10px] text-slate-400">Mean Time to Repair</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Coverage Support</span>
                <p class="text-lg font-black text-slate-900">{{ $project->sla_coverage_hours ?: '24x7' }}</p>
                <span class="text-[10px] text-slate-400">Standby Support</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Call Response</span>
                <p class="text-lg font-black text-slate-900">{{ $project->call_response_time ?: '15 menit' }}</p>
                <span class="text-[10px] text-slate-400">Respon Panggilan</span>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1">
                <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Target Resolution</span>
                <p class="text-lg font-black text-slate-900">{{ $project->incident_resolution_hours ?: 4 }} Jam</p>
                <span class="text-[10px] text-slate-400">Resolusi Masalah</span>
            </div>
        </div>

        <!-- CM & PM Quota Breakdown (Sesuai Sketsa User: CM 4,6,8,12,18 tiket & PM per bulan, 2 bulan, 3 bulan) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
            <!-- CM Quota Card -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50/50 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Corrective Maintenance</span>
                        <h3 class="text-sm font-bold text-slate-900 mt-0.5">Kuota Tiket CM (1 Tahun)</h3>
                    </div>
                    <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-rose-50 text-[#8F0A0D] border border-rose-200">
                        {{ $cmQuota >= 999 ? 'Unlimited Tiket' : $cmQuota . ' Tiket' }}
                    </span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                        <span>Tiket Terpakai: <strong class="text-slate-900">{{ $cmUsed }} Tiket</strong></span>
                        <span>Sisa Kuota: <strong class="text-emerald-700">{{ $cmRemaining }} Tiket</strong></span>
                    </div>
                    <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-[#8F0A0D] h-2.5 rounded-full transition-all" style="width: {{ $cmQuota >= 999 ? '15%' : min(100, round(($cmUsed / max(1, $cmQuota)) * 100)) }}%"></div>
                    </div>
                </div>

                <p class="text-[11.5px] text-slate-500">Pilihan kuota kontrak: <strong>4, 6, 8, 12, 18 tiket</strong>, atau unlimited per periode.</p>
            </div>

            <!-- PM Frequency Card -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50/50 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Preventive Maintenance</span>
                        <h3 class="text-sm font-bold text-slate-900 mt-0.5">Jadwal Siklus PM (1 Tahun)</h3>
                    </div>
                    <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $pmTotal }} Kali ({{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }})
                    </span>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                        <span>Siklus Selesai: <strong class="text-slate-900">{{ $pmCompleted }} / {{ $pmTotal }} PM</strong></span>
                        <span>Progres: <strong class="text-[#8F0A0D]">{{ $pmProgress }}%</strong></span>
                    </div>
                    <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-2.5 rounded-full transition-all" style="width: {{ min(100, $pmProgress) }}%"></div>
                    </div>
                </div>

                <p class="text-[11.5px] text-slate-500">Frekuensi inspeksi berkala: <strong>Per Bulan, Per 2 Bulan, Per 3 Bulan (Quarterly)</strong>.</p>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD / GANTI DOKUMEN -->
    <template x-teleport="body">
        <div x-show="isDocModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="isDocModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Upload Berkas Dokumen</p>
                        <h3 class="text-base font-bold text-slate-900" x-text="selectedDocTitle"></h3>
                    </div>
                    <button @click="isDocModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">&times;</button>
                </div>

                <form action="{{ route('ms.projects.documents.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="doc_type" :value="selectedDocType">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor Dokumen (Opsional)</label>
                        <input type="text" name="doc_number" placeholder="Contoh: SPK/2026/001 atau KTR-014" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Dokumen (Opsional)</label>
                        <input type="date" name="doc_date" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Berkas File (PDF / Word / Gambar / ZIP) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="file" name="doc_file" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none cursor-pointer">
                        <p class="text-[11px] text-slate-400 mt-1">Maksimal ukuran file: 50MB.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="isDocModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition cursor-pointer">Upload Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL EDIT PARAMETER SLA & KUOTA -->
    <template x-teleport="body">
        <div x-show="isSlaModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto" @click.away="isSlaModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Konfigurasi Kontrak</p>
                        <h3 class="text-base font-bold text-slate-900">Ubah Parameter SLA & Kuota Tiket</h3>
                    </div>
                    <button @click="isSlaModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">&times;</button>
                </div>

                <form action="{{ route('ms.projects.sla.update', $project->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Kuota Corrective Maintenance (CM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="cm_quota" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="4" {{ ($project->cm_quota ?? 18) == 4 ? 'selected' : '' }}>4 Tiket / Tahun</option>
                                <option value="6" {{ ($project->cm_quota ?? 18) == 6 ? 'selected' : '' }}>6 Tiket / Tahun</option>
                                <option value="8" {{ ($project->cm_quota ?? 18) == 8 ? 'selected' : '' }}>8 Tiket / Tahun</option>
                                <option value="12" {{ ($project->cm_quota ?? 18) == 12 ? 'selected' : '' }}>12 Tiket / Tahun</option>
                                <option value="18" {{ ($project->cm_quota ?? 18) == 18 ? 'selected' : '' }}>18 Tiket / Tahun (Standar)</option>
                                <option value="999" {{ ($project->cm_quota ?? 18) == 999 ? 'selected' : '' }}>Unlimited Tiket</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Frekuensi Preventive Maintenance (PM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="pm_frequency_type" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="per_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_bulan' ? 'selected' : '' }}>Per Bulan (12 Kali/Tahun)</option>
                                <option value="per_2_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_2_bulan' ? 'selected' : '' }}>Per 2 Bulan (6 Kali/Tahun)</option>
                                <option value="per_3_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_3_bulan' ? 'selected' : '' }}>Per 3 Bulan / Quarterly (4 Kali/Tahun)</option>
                                <option value="per_4_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_4_bulan' ? 'selected' : '' }}>Per 4 Bulan (3 Kali/Tahun)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Total Siklus PM <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="pm_total_cycles" value="{{ $project->pm_total_cycles ?: 4 }}" min="1" max="24" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">SLA Target (%) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" step="0.01" name="sla_target_percentage" value="{{ $project->sla_target_percentage ?: 99.50 }}" min="80" max="100" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">MTTR (Jam) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="mttr_hours" value="{{ $project->mttr_hours ?: 4 }}" min="1" max="72" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Coverage Support <span class="text-[#8F0A0D]">*</span></label>
                            <select name="sla_coverage_hours" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="24x7" {{ ($project->sla_coverage_hours ?? '24x7') == '24x7' ? 'selected' : '' }}>24x7 Standby</option>
                                <option value="24x8" {{ ($project->sla_coverage_hours ?? '24x7') == '24x8' ? 'selected' : '' }}>24x8</option>
                                <option value="16x7" {{ ($project->sla_coverage_hours ?? '24x7') == '16x7' ? 'selected' : '' }}>16x7</option>
                                <option value="8x7" {{ ($project->sla_coverage_hours ?? '24x7') == '8x7' ? 'selected' : '' }}>8x7</option>
                                <option value="8x5" {{ ($project->sla_coverage_hours ?? '24x7') == '8x5' ? 'selected' : '' }}>8x5 (Office Hours)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Call Response <span class="text-[#8F0A0D]">*</span></label>
                            <select name="call_response_time" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="3 menit" {{ ($project->call_response_time ?? '15 menit') == '3 menit' ? 'selected' : '' }}>3 Menit</option>
                                <option value="5 menit" {{ ($project->call_response_time ?? '15 menit') == '5 menit' ? 'selected' : '' }}>5 Menit</option>
                                <option value="10 menit" {{ ($project->call_response_time ?? '15 menit') == '10 menit' ? 'selected' : '' }}>10 Menit</option>
                                <option value="15 menit" {{ ($project->call_response_time ?? '15 menit') == '15 menit' ? 'selected' : '' }}>15 Menit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Target Resolusi (Jam) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="incident_resolution_hours" value="{{ $project->incident_resolution_hours ?: 4 }}" min="1" max="72" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Layanan</label>
                            <input type="date" name="service_start_date" value="{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('Y-m-d') : '' }}" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Akhir Layanan</label>
                            <input type="date" name="service_end_date" value="{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('Y-m-d') : '' }}" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sales PIC</label>
                            <input type="text" name="sales_name" value="{{ $project->sales_name ?: 'Raiza' }}" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pre-Sales / SA</label>
                            <input type="text" name="presales_name" value="{{ $project->presales_name ?: 'Akbar' }}" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="isSlaModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
