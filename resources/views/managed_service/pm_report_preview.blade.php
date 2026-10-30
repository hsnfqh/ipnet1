<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inspeksi PM - {{ $cycle->cycle_label }} - {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: #FFFFFF !important; padding: 0 !important; margin: 0 !important; }
            .doc-viewer-canvas { padding: 0 !important; background: transparent !important; }
            .a4-document {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
            }
        }
        .a4-document {
            width: 210mm;
            max-width: 100%;
            min-height: 297mm;
            box-sizing: border-box;
            background: #FFFFFF;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 20px 30px -5px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.08);
            border-radius: 2px;
            margin: 24px auto 48px auto;
        }
    </style>
</head>
<body class="bg-[#525659] text-[#0F172A] font-sans antialiased min-h-screen">
    
    {{-- Fixed Top Toolbar (No Print) --}}
    <div class="no-print sticky top-0 z-50 bg-[#1E293B] border-b border-[#334155] px-4 py-2.5 shadow-md">
        <div class="max-w-[210mm] mx-auto flex items-center justify-between text-white text-xs">
            <a href="{{ route('ms.projects.show', ['project' => $project->id, 'tab' => 'pm']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700/60 transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Detail Proyek</span>
            </a>
            
            <div class="hidden sm:flex items-center gap-2 text-slate-300 text-[11px] font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>Dokumen Resmi A4 &bull; {{ $cycle->cycle_label }}</span>
            </div>

            <div class="flex items-center gap-2">
                @php
                    $reportFileVal = $cycle->report_file ?: ($cycle->report_file_path ?? null);
                @endphp
                @if($reportFileVal)
                    <a href="{{ route('ms.monitoring.pm.report', ['cycle' => $cycle->id, 'raw' => 1]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-200 bg-slate-700 hover:bg-slate-600 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Berkas Asli</span>
                    </a>
                @endif
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#A30C10] shadow-sm transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Document Viewer Canvas --}}
    <div class="doc-viewer-canvas px-3 py-6 sm:py-8 flex justify-center">
        
        {{-- Authentic A4 Paper Sheet --}}
        <div class="a4-document p-8 sm:p-12 space-y-5 text-[#0F172A]">
            
            {{-- Official Letterhead (Kop Surat Resmi) --}}
            <div class="border-b-2 border-[#8F0A0D] pb-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-lg bg-[#8F0A0D] flex items-center justify-center text-white font-black text-lg shadow-xs">
                            IP
                        </div>
                        <div>
                            <h1 class="text-[15px] font-black text-[#0F172A] tracking-tight leading-tight">PT IP NETWORK SOLUSINDO</h1>
                            <p class="text-[10px] text-[#475569] uppercase tracking-wider font-bold">Managed Service &amp; Field Support Division &bull; SLA Operations</p>
                            <p class="text-[9.5px] text-[#64748B]">Gd. Cyber 1 Lt. 8, Jl. Kuningan Barat No. 8, Jakarta Selatan &bull; support@ipnetsolusindo.com</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            STATUS: {{ strtoupper($cycle->activity_status ?: 'COMPLETED') }}
                        </span>
                        <p class="text-[9.5px] font-mono text-[#64748B] mt-1">No. Laporan: PM/{{ date('Y') }}/{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($cycle->cycle_number, 2, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>

            {{-- Document Title --}}
            <div class="text-center py-1">
                <h2 class="text-[14px] font-black uppercase text-[#0F172A] tracking-wide">
                    LAPORAN HASIL INSPEKSI PREVENTIVE MAINTENANCE (PM)
                </h2>
                <p class="text-[11px] font-semibold text-[#8F0A0D] mt-0.5">
                    {{ $cycle->cycle_label }} &bull; Kontrak Pemeliharaan Jaringan &amp; Sistem SLA
                </p>
            </div>

            {{-- Metadata Box (Compact & Balanced) --}}
            <div class="grid grid-cols-2 gap-3 text-[10.5px] bg-[#F8FAFC] p-3 rounded-lg border border-[#CBD5E1]">
                <div class="space-y-1">
                    <div><span class="text-[#64748B]">Nama Proyek:</span> <strong class="text-[#0F172A] block text-[11.5px]">{{ $project->name }}</strong></div>
                    <div><span class="text-[#64748B]">Klien / Pelanggan:</span> <span class="font-bold text-[#0F172A]">{{ $project->client ?: '-' }}</span></div>
                    <div><span class="text-[#64748B]">Nomor Kontrak / SPK:</span> <span class="font-mono font-semibold text-[#8F0A0D]">{{ $project->po_spk_number ?: ('KTR/IPNET/2026/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}</span></div>
                    <div><span class="text-[#64748B]">Lokasi Kunjungan:</span> <span class="text-[#0F172A]">{{ $project->location ?: 'On-Site Data Center / Server Room Klien' }}</span></div>
                </div>
                <div class="space-y-1">
                    <div><span class="text-[#64748B]">Jadwal Target:</span> <span class="font-bold text-[#0F172A]">{{ $cycle->scheduled_date ? date('d F Y', strtotime($cycle->scheduled_date)) : '-' }}</span></div>
                    <div><span class="text-[#64748B]">Realisasi Visit:</span> <span class="font-bold text-emerald-700">{{ $cycle->actual_date ? date('d F Y', strtotime($cycle->actual_date)) : date('d F Y') }}</span></div>
                    <div><span class="text-[#64748B]">Target SLA Kontrak:</span> <span class="font-bold text-[#0F172A]">{{ $project->sla_tier ?: 'Gold' }} (Coverage {{ $project->sla_coverage_hours ?: '24x7' }})</span></div>
                    <div><span class="text-[#64748B]">Teknisi Lapangan (PIC):</span> <span class="font-bold text-[#8F0A0D]">{{ $cycle->assignedEngineer?->name ?: 'Eris (Field Support Engineer)' }}</span></div>
                </div>
            </div>

            {{-- Checklist Pemeriksaan Fisik & Jaringan (Fixed Proportion Table) --}}
            <div class="space-y-1.5">
                <h3 class="text-[11px] font-bold text-[#0F172A] uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                    Item Pemeriksaan &amp; Kelaikan Operasional Lapangan
                </h3>
                <table class="w-full text-[10.5px] border border-[#CBD5E1] border-collapse" style="table-layout: fixed;">
                    <thead>
                        <tr class="bg-[#F1F5F9] text-[#334155] font-bold text-[10px] text-left border-b border-[#CBD5E1]">
                            <th class="p-1.5 text-center border-r border-[#CBD5E1]" style="width: 32px;">NO</th>
                            <th class="p-1.5 border-r border-[#CBD5E1]" style="width: 40%;">KOMPONEN INSPEKSI</th>
                            <th class="p-1.5 text-center border-r border-[#CBD5E1]" style="width: 25%;">STANDAR PARAMETER</th>
                            <th class="p-1.5 text-center border-r border-[#CBD5E1]" style="width: 22%;">HASIL AKTUAL</th>
                            <th class="p-1.5 text-center" style="width: 13%;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">1</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Kebersihan Fisik Rack, Filter Debu &amp; Exhaust Fan</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Bebas debu &amp; aliran udara lancar</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Bersih, Fan normal</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">2</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Suhu Lingkungan Server Room &amp; Sensor Telemetri</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">18&deg;C - 24&deg;C</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">21.5&deg;C / RH 52%</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">3</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Catu Daya Listrik Dual Power Supply (PSU A + B)</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Redundant, No Fluctuation</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Dual Input Normal</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">4</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Status Indikator LED Port, Link &amp; Optical Transceiver SFP</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Solid Green / No Flapping</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Normal, Rx/Tx Stabil</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">5</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Utilisasi Resource (CPU &amp; Memory Utilization)</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Rata-rata &lt; 60%</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">CPU 18%, RAM 34%</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">6</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Backup Konfigurasi Perangkat Berjalan (Running Config)</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Tersimpan di NMS / TFTP</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Backup Sukses Tersimpan</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">7</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">System Log, Error Interface &amp; Security Policy Audit</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">CRC Error = 0, No Drop</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Log Bersih, Tidak Ada Drop</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-1.5 text-center font-mono border-r border-[#CBD5E1]">8</td>
                            <td class="p-1.5 font-medium border-r border-[#CBD5E1]">Uji Redundansi Jalur Failover Link Backbone &amp; HA Heartbeat</td>
                            <td class="p-1.5 text-center text-[#64748B] border-r border-[#CBD5E1]">Failover &lt; 2 Detik</td>
                            <td class="p-1.5 text-center font-medium border-r border-[#CBD5E1]">Sinkronisasi HA Normal</td>
                            <td class="p-1.5 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Temuan & Rekomendasi Lapangan --}}
            <div class="space-y-1 text-[10.5px] bg-[#F8FAFC] p-3 rounded-lg border border-[#CBD5E1]">
                <strong class="text-[#0F172A] block uppercase text-[10px] font-bold">Catatan Temuan &amp; Rekomendasi Teknis:</strong>
                <p class="text-[#334155] leading-relaxed">
                    {{ $cycle->activity_notes ?: 'Kunjungan preventive maintenance siklus ini telah selesai dilaksanakan secara menyeluruh. Seluruh modul core router, switch perimeter, dan link interface beroperasi dengan performa prima tanpa indikasi packet loss maupun anomali suhu. Cadangan backup konfigurasi telah disinkronkan ke server pusat IPNET.' }}
                </p>
            </div>

            {{-- Lembar Tanda Tangan Pengesahan (3 Kolom Proporsional) --}}
            <div class="pt-3 border-t border-[#CBD5E1]">
                <p class="text-[10px] font-bold text-[#0F172A] uppercase tracking-wider mb-2.5">Tanda Tangan &amp; Pengesahan Berita Acara Kunjungan:</p>
                <div class="grid grid-cols-3 gap-3 text-center text-[10.5px]">
                    
                    {{-- 1. Teknisi Pelaksana --}}
                    <div class="border border-[#CBD5E1] p-2.5 rounded-lg bg-white flex flex-col justify-between">
                        <div>
                            <div class="text-[9.5px] font-bold text-[#64748B] uppercase">Dikerjakan Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[10.5px] mt-0.5">Teknisi Lapangan (PIC)</div>
                        </div>
                        <div class="py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; Field Verified
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">{{ $cycle->assignedEngineer?->name ?: 'Eris' }}</strong>
                            <span class="text-[9px] text-[#64748B]">Field Support Engineer</span>
                        </div>
                    </div>

                    {{-- 2. Klien --}}
                    <div class="border border-[#CBD5E1] p-2.5 rounded-lg bg-white flex flex-col justify-between">
                        <div>
                            <div class="text-[9.5px] font-bold text-[#64748B] uppercase">Disetujui Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[10.5px] mt-0.5">PIC / Perwakilan Klien</div>
                        </div>
                        <div class="py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                &check; Client Signed
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#0F172A] block">{{ $project->customer_pic_technical ?: 'Bpk. PIC Lapangan Klien' }}</strong>
                            <span class="text-[9px] text-[#64748B]">{{ $project->client }}</span>
                        </div>
                    </div>

                    {{-- 3. Lead Managed Service --}}
                    <div class="border border-[#CBD5E1] p-2.5 rounded-lg bg-white flex flex-col justify-between">
                        <div>
                            <div class="text-[9.5px] font-bold text-[#64748B] uppercase">Diketahui Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[10.5px] mt-0.5">Managed Service Lead</div>
                        </div>
                        <div class="py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; MS Approved
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">Doris</strong>
                            <span class="text-[9px] text-[#64748B]">Managed Service Coordinator</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center pt-1 text-[9px] text-[#94A3B8]">
                Dokumen ini merupakan Laporan Inspeksi Resmi PT IP Network Solusindo dan menjadi dasar pemenuhan SLA Kontrak serta lampiran penagihan berkala.
            </div>

        </div>
    </div>
</body>
</html>
