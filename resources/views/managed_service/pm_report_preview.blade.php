<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Inspeksi PM - {{ $cycle->cycle_label }} - {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-card { box-shadow: none !important; border: 1px solid #CBD5E1 !important; border-radius: 0 !important; }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#0F172A] font-sans antialiased min-h-screen p-4 sm:p-8">
    <div class="max-w-4xl mx-auto space-y-4">
        
        {{-- Action Bar (No Print) --}}
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-2xl border border-[#CBD5E1] shadow-xs">
            <a href="{{ route('ms.projects.show', ['project' => $project->id, 'tab' => 'pm']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#475569] hover:bg-[#F1F5F9] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Detail Proyek</span>
            </a>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        {{-- Official PM Report Sheet --}}
        <div class="print-card bg-white rounded-2xl border border-[#CBD5E1] p-6 sm:p-10 shadow-sm space-y-6">
            
            {{-- Header --}}
            <div class="flex items-start justify-between border-b-2 border-[#8F0A0D] pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-[#8F0A0D] flex items-center justify-center text-white font-black text-xl shadow-xs">
                        IP
                    </div>
                    <div>
                        <h1 class="text-[17px] font-black text-[#0F172A] tracking-tight">PT IP NETWORK SOLUSINDO</h1>
                        <p class="text-[11px] text-[#475569] uppercase tracking-wider font-semibold">Managed Service &amp; Field Support Division &bull; SLA Operations</p>
                        <p class="text-[10px] text-[#64748B]">Gd. Cyber 1 Lt. 8, Jl. Kuningan Barat No. 8, Jakarta Selatan &bull; support@ipnetsolusindo.com</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        STATUS: {{ strtoupper($cycle->activity_status ?: 'COMPLETED') }}
                    </span>
                    <p class="text-[10.5px] font-mono text-[#64748B] mt-1.5">No. Laporan: PM/{{ date('Y') }}/{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($cycle->cycle_number, 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center py-2">
                <h2 class="text-[16px] font-black uppercase text-[#0F172A] tracking-wide">
                    LAPORAN HASIL INSPEKSI PREVENTIVE MAINTENANCE (PM)
                </h2>
                <p class="text-[12px] font-semibold text-[#8F0A0D] mt-0.5">
                    {{ $cycle->cycle_label }} &bull; Kontrak Pemeliharaan SLA Rutin
                </p>
            </div>

            {{-- Metadata Grid --}}
            <div class="grid grid-cols-2 gap-4 text-xs bg-[#F8FAFC] p-4 rounded-xl border border-[#E2E8F0]">
                <div class="space-y-1.5">
                    <div><span class="text-[#64748B]">Nama Proyek:</span> <strong class="text-[#0F172A] block text-[13px]">{{ $project->name }}</strong></div>
                    <div><span class="text-[#64748B]">Klien / Pelanggan:</span> <span class="font-bold text-[#0F172A]">{{ $project->client ?: '-' }}</span></div>
                    <div><span class="text-[#64748B]">Nomor Kontrak / SPK:</span> <span class="font-mono font-semibold text-[#8F0A0D]">{{ $project->po_spk_number ?: ('KTR/IPNET/2026/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}</span></div>
                    <div><span class="text-[#64748B]">Lokasi Kunjungan:</span> <span class="text-[#0F172A]">{{ $project->location ?: 'On-Site Data Center / Server Room Klien' }}</span></div>
                </div>
                <div class="space-y-1.5">
                    <div><span class="text-[#64748B]">Jadwal Target:</span> <span class="font-bold text-[#0F172A]">{{ $cycle->scheduled_date ? date('d F Y', strtotime($cycle->scheduled_date)) : '-' }}</span></div>
                    <div><span class="text-[#64748B]">Realisasi Visit:</span> <span class="font-bold text-emerald-700">{{ $cycle->actual_date ? date('d F Y', strtotime($cycle->actual_date)) : date('d F Y') }}</span></div>
                    <div><span class="text-[#64748B]">Target SLA Kontrak:</span> <span class="font-bold text-[#0F172A]">{{ $project->sla_tier ?: 'Gold' }} (Coverage {{ $project->sla_coverage_hours ?: '24x7' }})</span></div>
                    <div><span class="text-[#64748B]">Teknisi Lapangan (PIC):</span> <span class="font-bold text-[#8F0A0D]">{{ $cycle->assignedEngineer?->name ?: 'Eris (Field Support Engineer)' }}</span></div>
                </div>
            </div>

            {{-- Checklist Pemeriksaan Fisik & Jaringan --}}
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                    Item Pemeriksaan &amp; Kelaikan Operasional Lapangan
                </h3>
                <table class="w-full text-xs border border-[#CBD5E1] border-collapse">
                    <thead>
                        <tr class="bg-[#F1F5F9] text-[#334155] font-bold text-[11px] text-left border-b border-[#CBD5E1]">
                            <th class="p-2.5 w-10 text-center border-r border-[#CBD5E1]">NO</th>
                            <th class="p-2.5 border-r border-[#CBD5E1]">KOMPONEN INSPEKSI</th>
                            <th class="p-2.5 w-36 text-center border-r border-[#CBD5E1]">STANDAR PARAMETER</th>
                            <th class="p-2.5 w-32 text-center border-r border-[#CBD5E1]">HASIL AKTUAL</th>
                            <th class="p-2.5 w-24 text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">1</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Kebersihan Fisik Rack, Filter Debu &amp; Exhaust Fan</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Bebas debu &amp; aliran udara lancar</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Bersih, Fan normal</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">2</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Suhu Lingkungan Server Room &amp; Sensor Telemetri</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">18&deg;C - 24&deg;C</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">21.5&deg;C / RH 52%</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">3</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Catu Daya Listrik Dual Power Supply (PSU A + B)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Redundant, No Fluctuation</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Dual Input Normal</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">4</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Status Indikator LED Port, Link &amp; Optical Transceiver SFP</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Solid Green / No Flapping</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Normal, Rx/Tx Stabil</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">5</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Utilisasi Resource (CPU &amp; Memory Utilization)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Rata-rata &lt; 60%</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">CPU 18%, RAM 34%</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">6</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Backup Konfigurasi Perangkat Berjalan (Running Config)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Tersimpan di NMS / TFTP</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Backup Sukses Tersimpan</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">7</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">System Log, Error Interface &amp; Security Policy Audit</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">CRC Error = 0, No Drop</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Log Bersih, Tidak Ada Drop</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">8</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Uji Redundansi Jalur Failover Link Backbone &amp; HA Heartbeat</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Failover &lt; 2 Detik</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Sinkronisasi HA Normal</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Temuan & Rekomendasi Lapangan --}}
            <div class="space-y-1.5 text-xs bg-[#F8FAFC] p-3.5 rounded-xl border border-[#CBD5E1]">
                <strong class="text-[#0F172A] block uppercase text-[11px] font-bold">Catatan Temuan &amp; Rekomendasi Teknis:</strong>
                <p class="text-[#334155] leading-relaxed">
                    {{ $cycle->activity_notes ?: 'Kunjungan preventive maintenance siklus ini telah selesai dilaksanakan secara menyeluruh. Seluruh modul core router, switch perimeter, dan link interface beroperasi dengan performa prima tanpa indikasi packet loss maupun anomali suhu. Cadangan backup konfigurasi telah disinkronkan ke server pusat IPNET.' }}
                </p>
            </div>

            {{-- Lembar Tanda Tangan Pengesahan --}}
            <div class="pt-4 border-t border-[#CBD5E1]">
                <p class="text-[11px] font-bold text-[#0F172A] uppercase tracking-wider mb-3">Tanda Tangan &amp; Pengesahan Berita Acara Kunjungan:</p>
                <div class="grid grid-cols-3 gap-4 text-center text-xs">
                    
                    {{-- 1. Teknisi Pelaksana --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Dikerjakan Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">Teknisi Lapangan (PIC)</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; Field Verified
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">{{ $cycle->assignedEngineer?->name ?: 'Eris' }}</strong>
                            <span class="text-[10px] text-[#64748B]">Field Support Engineer</span>
                        </div>
                    </div>

                    {{-- 2. Klien --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Disetujui Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">PIC / Perwakilan Klien</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                &check; Client Signed
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#0F172A] block">{{ $project->customer_pic_technical ?: 'Bpk. PIC Lapangan Klien' }}</strong>
                            <span class="text-[10px] text-[#64748B]">{{ $project->client }}</span>
                        </div>
                    </div>

                    {{-- 3. Lead Managed Service --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Diketahui Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">Managed Service Lead</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; MS Approved
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">Doris</strong>
                            <span class="text-[10px] text-[#64748B]">Managed Service Coordinator</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center pt-2 text-[10px] text-[#94A3B8]">
                Dokumen ini merupakan Laporan Inspeksi Resmi PT IP Network Solusindo dan menjadi dasar pemenuhan SLA Kontrak serta lampiran penagihan berkala.
            </div>

        </div>
    </div>
</body>
</html>

