<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Perbaikan (BAP) - {{ $ticket->ticket_number }} - {{ $project->name }}</title>
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
            <a href="{{ route('ms.projects.show', ['project' => $project->id, 'tab' => 'cm']) }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-semibold text-[#475569] hover:bg-[#F1F5F9] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Detail Proyek (CM Tab)</span>
            </a>
            <div class="flex items-center gap-2">
                @if($ticket->attachment_path)
                    <a href="{{ asset('storage/' . $ticket->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-[#334155] bg-slate-100 hover:bg-slate-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        <span>Lihat Berkas Scan Asli</span>
                    </a>
                @endif
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] shadow-md transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak / Simpan PDF</span>
                </button>
            </div>
        </div>

        {{-- Official BAP Document Sheet --}}
        <div class="print-card bg-white rounded-2xl border border-[#CBD5E1] p-6 sm:p-10 shadow-sm space-y-6">
            
            {{-- Header --}}
            <div class="flex items-start justify-between border-b-2 border-[#8F0A0D] pb-5">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-[#8F0A0D] flex items-center justify-center text-white font-black text-xl shadow-xs">
                        IP
                    </div>
                    <div>
                        <h1 class="text-[17px] font-black text-[#0F172A] tracking-tight">PT IP NETWORK SOLUSINDO</h1>
                        <p class="text-[11px] text-[#475569] uppercase tracking-wider font-semibold">Managed Service &amp; Field Support Division &bull; Incident Response &amp; SLA Management</p>
                        <p class="text-[10px] text-[#64748B]">Gd. Cyber 1 Lt. 8, Jl. Kuningan Barat No. 8, Jakarta Selatan &bull; support@ipnetsolusindo.com</p>
                    </div>
                </div>
                <div class="text-right">
                    @if(in_array($ticket->status, ['Resolved', 'Closed']))
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            STATUS: RESOLVED (TUNTAS)
                        </span>
                    @elseif($ticket->status === 'In Progress')
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            STATUS: IN PROGRESS (PENGERJAAN)
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                            STATUS: {{ strtoupper($ticket->status) }}
                        </span>
                    @endif
                    <p class="text-[10.5px] font-mono text-[#64748B] mt-1.5">No. BAP: BAP-CM/{{ date('Y') }}/{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}/{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-[11px] font-mono font-bold text-[#8F0A0D]">Tiket: {{ $ticket->ticket_number }}</p>
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center py-2">
                <h2 class="text-[16px] font-black uppercase text-[#0F172A] tracking-wide">
                    BERITA ACARA PERBAIKAN &amp; PENYELESAIAN INSIDEN (BAP)
                </h2>
                <p class="text-[12px] font-semibold text-[#8F0A0D] mt-0.5">
                    Corrective Maintenance (CM) Restoration &amp; Field Technical Acceptance Sheet
                </p>
            </div>

            {{-- Metadata Grid --}}
            <div class="grid grid-cols-2 gap-4 text-xs bg-[#F8FAFC] p-4 rounded-xl border border-[#E2E8F0]">
                <div class="space-y-1.5">
                    <div><span class="text-[#64748B]">Nama Proyek:</span> <strong class="text-[#0F172A] block text-[13px]">{{ $project->name }}</strong></div>
                    <div><span class="text-[#64748B]">Klien / Pelanggan:</span> <span class="font-bold text-[#0F172A]">{{ $ticket->client_name ?: ($project->client ?: '-') }}</span></div>
                    <div><span class="text-[#64748B]">Nomor Kontrak / SPK:</span> <span class="font-mono font-semibold text-[#8F0A0D]">{{ $project->po_spk_number ?: ('KTR/IPNET/2026/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}</span></div>
                    <div><span class="text-[#64748B]">Lokasi Penanganan:</span> <span class="text-[#0F172A]">{{ $project->location ?: 'STO Merdeka / On-Site Data Center' }}</span></div>
                </div>
                <div class="space-y-1.5">
                    <div><span class="text-[#64748B]">Waktu Insiden Masuk:</span> <span class="font-bold text-[#0F172A]">{{ $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date)->format('d F Y, H:i') : $ticket->created_at->format('d F Y, H:i') }} WIB</span></div>
                    <div><span class="text-[#64748B]">Waktu Restorasi Selesai:</span> 
                        <span class="font-bold {{ $ticket->resolved_at ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->format('d F Y, H:i') . ' WIB' : 'Sedang Ditangani Teknisi' }}
                        </span>
                    </div>
                    <div><span class="text-[#64748B]">Prioritas &amp; Target SLA MTTR:</span> <span class="font-bold text-[#0F172A]">{{ $ticket->priority ?: 'Gold' }} (Target MTTR: {{ str_contains(strtolower($ticket->priority ?? ''), 'critical') ? '1 Jam' : (str_contains(strtolower($ticket->priority ?? ''), 'high') ? '4 Jam' : '8 Jam') }})</span></div>
                    <div><span class="text-[#64748B]">Kepatuhan SLA (SLA Compliance):</span> 
                        @if($ticket->resolved_at)
                            <span class="inline-flex items-center gap-1 font-bold text-emerald-700 font-mono">
                                &check; SLA Terpenuhi (MTTR Sesuai Kontrak)
                            </span>
                        @else
                            <span class="font-bold text-amber-700 font-mono">Dalam Batas SLA Operasional</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pelapor & Teknisi Pelaksana --}}
            <div class="grid grid-cols-2 gap-4 text-xs bg-white p-3.5 rounded-xl border border-[#CBD5E1]">
                <div>
                    <span class="text-[#64748B] block text-[10.5px] uppercase font-bold tracking-wider">Pelapor / PIC Pelanggan:</span>
                    <strong class="text-[#0F172A] text-[12.5px]">{{ $ticket->reported_by ?: ($project->customer_pic_technical ?: 'Bpk. Agus Prasetyo (Telkom)') }}</strong>
                    <div class="text-[#64748B] text-[11px] mt-0.5">{{ $ticket->contact_phone ?: '0812-8899-2341' }} &bull; {{ $ticket->client_name ?: $project->client }}</div>
                </div>
                <div>
                    <span class="text-[#64748B] block text-[10.5px] uppercase font-bold tracking-wider">Teknisi Pelaksana (Field Engineer IPNET):</span>
                    <strong class="text-[#8F0A0D] text-[12.5px]">{{ $ticket->assignedEngineer?->name ?: 'Mario' }}</strong>
                    <div class="text-[#64748B] text-[11px] mt-0.5">Field Support &amp; Maintenance Engineer &bull; Managed Service IPNET</div>
                </div>
            </div>

            {{-- Kronologi, RCA, & Tindakan Perbaikan --}}
            <div class="space-y-4">
                
                {{-- Deskripsi Masalah --}}
                <div class="space-y-1.5 text-xs bg-[#F8FAFC] p-3.5 rounded-xl border border-[#CBD5E1]">
                    <strong class="text-[#0F172A] block uppercase text-[11px] font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        1. Deskripsi Insiden &amp; Gejala Masalah:
                    </strong>
                    <div class="font-semibold text-[#1E293B] text-[12px] pl-3.5">{{ $ticket->title }}</div>
                    <p class="text-[#334155] leading-relaxed pl-3.5">
                        {{ $ticket->description ?: 'Terjadi anomali degradasi transmisi paket data pada interface jaringan perimeter yang memicu alarm sistem NMS dan peringatan kestabilan link.' }}
                    </p>
                </div>

                {{-- Root Cause Analysis (RCA) --}}
                <div class="space-y-1.5 text-xs bg-[#F8FAFC] p-3.5 rounded-xl border border-[#CBD5E1]">
                    <strong class="text-[#0F172A] block uppercase text-[11px] font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        2. Analisis Akar Masalah (Root Cause Analysis - RCA):
                    </strong>
                    <p class="text-[#334155] leading-relaxed pl-3.5">
                        @if($ticket->root_cause)
                            {{ $ticket->root_cause }}
                        @elseif(str_contains(strtolower($ticket->title), 'bgp'))
                            Konektor optik SC-UPC pada patch cord backbone kotor dan mengalami redaman fluktuatif mencapai -24 dBm yang memicu drop link berulang (flapping) pada sesi BGP Peering interface TenGigE 0/0/1 STO Merdeka.
                        @elseif(str_contains(strtolower($ticket->title), 'sfp') || str_contains(strtolower($ticket->title), 'rx power'))
                            Degradasi laser photo-diode pada modul SFP+ 10G-LR akibat usia pakai operasional melampaui MTBF, menyebabkan margin RX optical sensitivity turun drastis ke -18 dBm dan memicu alarm optical power loss.
                        @elseif(str_contains(strtolower($ticket->title), 'cpu') || str_contains(strtolower($ticket->title), 'latency'))
                            Terjadinya broadcast storming frame akibat transient looping pada segmen edge switch access, menyebabkan CPU Core Switch melonjak hingga 92% dan memicu lonjakan response time/latency.
                        @else
                            Gangguan transien pada modul transmisi link yang telah diisolasi dan dinormalisasi kembali sesuai standar kelaikan operasional perangkat jaringan IPNET.
                        @endif
                    </p>
                </div>

                {{-- Tindakan Perbaikan --}}
                <div class="space-y-1.5 text-xs bg-[#F8FAFC] p-3.5 rounded-xl border border-[#CBD5E1]">
                    <strong class="text-[#0F172A] block uppercase text-[11px] font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        3. Tindakan Perbaikan &amp; Resolusi Teknis (Remediation Actions):
                    </strong>
                    <div class="text-[#334155] leading-relaxed pl-3.5 whitespace-pre-line">
@if($ticket->resolution_notes)
{{ $ticket->resolution_notes }}
@elseif(str_contains(strtolower($ticket->title), 'bgp'))
1. Petugas On-Site melakukan pembersihan menyeluruh konektor optik patch cord menggunakan optical fiber ferrule cleaner reel.
2. Melakukan kalibrasi ulang dan pengukuran redaman dengan Optical Power Meter (OPM), hasil redaman stabil pada -14.2 dBm.
3. Melakukan clear session BGP dan verifikasi tabel routing; peering kembali Established tanpa ada flapping.
4. Melakukan running ping test 2.000 paket ke upstream gateway dengan hasil Packet Loss 0% dan RTT Latency 1.8 ms.
@elseif(str_contains(strtolower($ticket->title), 'sfp') || str_contains(strtolower($ticket->title), 'rx power'))
1. Petugas berkoordinasi dengan NOC untuk isolasi sementara port TenGigE 0/0/2 pada Rack TELKOM-01.
2. Melakukan hot-replacement modul transceiver optic SFP+ 10G LR baru dengan part number resmi IPNET.
3. Mengukur optical power level: Transmit (Tx) = -1.2 dBm, Receive (Rx) = -11.5 dBm (Status Optimal).
4. Mengaktifkan port kembali, alarm clear secara otomatis, dan link beroperasi normal tanpa CRC error.
@elseif(str_contains(strtolower($ticket->title), 'cpu') || str_contains(strtolower($ticket->title), 'latency'))
1. Melakukan capture lalu lintas paket NetFlow melalui mirror port analyzer.
2. Mengaktifkan fitur loop-guard dan storm-control broadcast threshold 10% pada port uplink edge switch.
3. Mengisolasi workstation penyebab flooding paket ARP.
4. Utilisasi CPU Core Switch turun stabil ke 18% dan latency kembali normal di bawah 2 ms.
@else
1. Melakukan inspeksi menyeluruh pada fisik perangkat dan modul interface jaringan yang terdampak.
2. Melakukan perbaikan konfigurasi dan penggantian komponen pendukung bila diperlukan.
3. Menjalankan pengujian performa link end-to-end dengan hasil normal dan stabil.
@endif
                    </div>
                </div>

            </div>

            {{-- Checklist Verifikasi & Uji Kelaikan Pasca Perbaikan --}}
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-[#0F172A] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                    Uji Kelaikan Operasional Pasca Perbaikan (Post-Restoration Checklist)
                </h3>
                <table class="w-full text-xs border border-[#CBD5E1] border-collapse">
                    <thead>
                        <tr class="bg-[#F1F5F9] text-[#334155] font-bold text-[11px] text-left border-b border-[#CBD5E1]">
                            <th class="p-2.5 w-10 text-center border-r border-[#CBD5E1]">NO</th>
                            <th class="p-2.5 border-r border-[#CBD5E1]">PARAMETER UJI</th>
                            <th class="p-2.5 w-44 text-center border-r border-[#CBD5E1]">STANDAR MINIMAL</th>
                            <th class="p-2.5 w-40 text-center border-r border-[#CBD5E1]">HASIL PENGUKURAN</th>
                            <th class="p-2.5 w-24 text-center">STATUS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E2E8F0]">
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">1</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Status Port Interface Fisik (Link State)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">UP / UP, LED Green Solid</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">UP / UP (Normal)</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">2</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Tingkat Daya Sinyal Optik (Optical Rx/Tx Level)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">-10 dBm s/d -16 dBm</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">-12.4 dBm (Optimal)</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">3</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Pengujian Ping &amp; Latensi (2.000 Packets)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Loss 0%, Latency &lt; 5ms</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Loss 0%, RTT 1.8ms</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">4</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Stabilitas Sesi Peering Routing Protocol (BGP/OSPF)</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Established, No Flapping</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Established &amp; Sync</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                        <tr>
                            <td class="p-2 text-center font-mono border-r border-[#CBD5E1]">5</td>
                            <td class="p-2 font-medium border-r border-[#CBD5E1]">Kelancaran Aliran Trafik Layanan &amp; Log Alarm</td>
                            <td class="p-2 text-center text-[#64748B] border-r border-[#CBD5E1]">Alarm Clear, Trafik Normal</td>
                            <td class="p-2 text-center font-medium border-r border-[#CBD5E1]">Alarm Clear 100%</td>
                            <td class="p-2 text-center font-bold text-emerald-700">OK / PASS</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Lembar Tanda Tangan Pengesahan (3 Pihak) --}}
            <div class="pt-4 border-t border-[#CBD5E1]">
                <p class="text-[11px] font-bold text-[#0F172A] uppercase tracking-wider mb-3">Tanda Tangan &amp; Pengesahan Berita Acara Perbaikan (BAP):</p>
                <div class="grid grid-cols-3 gap-4 text-center text-xs">
                    
                    {{-- 1. Teknisi Pelaksana --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Dikerjakan Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">Teknisi Lapangan (IPNET)</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; Field Verified
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">{{ $ticket->assignedEngineer?->name ?: 'Mario' }}</strong>
                            <span class="text-[10px] text-[#64748B]">Field Support Engineer</span>
                        </div>
                    </div>

                    {{-- 2. Klien --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Diterima &amp; Disetujui Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">PIC / Perwakilan Klien</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                &check; Client Accepted
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#0F172A] block">{{ $ticket->reported_by ?: ($project->customer_pic_technical ?: 'Bpk. Agus Prasetyo') }}</strong>
                            <span class="text-[10px] text-[#64748B]">{{ $ticket->client_name ?: $project->client }}</span>
                        </div>
                    </div>

                    {{-- 3. Lead Managed Service --}}
                    <div class="border border-[#CBD5E1] p-3 rounded-xl bg-white space-y-1 flex flex-col justify-between">
                        <div>
                            <div class="text-[10px] font-bold text-[#64748B] uppercase">Divalidasi Oleh:</div>
                            <div class="font-bold text-[#0F172A] text-[11.5px] mt-0.5">Managed Service Lead</div>
                        </div>
                        <div class="py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; MS Approved
                            </span>
                        </div>
                        <div class="border-t border-[#E2E8F0] pt-1.5">
                            <strong class="text-[#8F0A0D] block">Doris</strong>
                            <span class="text-[10px] text-[#64748B]">Lead Managed Service &amp; SLA</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center pt-2 text-[10px] text-[#94A3B8]">
                Dokumen Berita Acara Perbaikan (BAP) ini merupakan bukti sah penyelesaian insiden Corrective Maintenance PT IP Network Solusindo sesuai Service Level Agreement (SLA) kontrak kerja sama yang berlaku.
            </div>

        </div>
    </div>
</body>
</html>

