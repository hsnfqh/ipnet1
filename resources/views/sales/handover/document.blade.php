<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima - {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-page { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: 100% !important; }
        }
        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 p-4 sm:p-8 antialiased">

    {{-- Toolbar Aksi (Hidden Saat Cetak) --}}
    <div class="max-w-4xl mx-auto mb-5 no-print flex items-center justify-between gap-3">
        <button type="button" onclick="window.history.back()"
                class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-xs transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </button>

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Berita Acara (Print / PDF)</span>
            </button>
        </div>
    </div>

    {{-- Kertas Dokumen Resmi (Format Standar IPNET A4) --}}
    <div class="max-w-4xl mx-auto bg-white border border-slate-300 rounded-2xl shadow-xl p-8 sm:p-12 print-page space-y-6">

        {{-- 1. KOP SURAT RESMI PT IP NETWORK SOLUSINDO --}}
        <div class="flex items-center justify-between border-b-2 border-[#8F0A0D] pb-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#8F0A0D] to-[#B91C1C] text-white flex items-center justify-center font-black text-xl shadow-md">
                    IP
                </div>
                <div>
                    <h1 class="text-[19px] font-black tracking-tight text-[#1E293B] uppercase">PT IP Network Solusindo</h1>
                    <p class="text-[11.5px] font-semibold text-[#8F0A0D] tracking-wider uppercase">Field System Management &bull; Technical Delivery &amp; Operation</p>
                    <p class="text-[10px] text-slate-500">Jl. Inpres No. 18, Jakarta Timur &bull; info@ipnetsolusindo.com &bull; www.ipnetsolusindo.com</p>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10.5px] font-extrabold uppercase tracking-wider {{ ($project->handover_status === 'Approved' || $project->ms_handover_status === 'Accepted') ? 'bg-emerald-50 text-emerald-700 border border-emerald-300' : 'bg-amber-50 text-amber-800 border border-amber-300' }}">
                    {{ ($project->handover_status === 'Approved' || $project->ms_handover_status === 'Accepted') ? 'Resmi Disahkan' : 'Menunggu Approval' }}
                </span>
                <p class="text-[10.5px] font-mono text-slate-400 mt-1">
                    BA-HND/{{ date('Y') }}/{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        {{-- 2. JUDUL BERITA ACARA --}}
        <div class="text-center space-y-1 pt-2">
            <h2 class="text-[17px] font-black text-[#1E293B] uppercase tracking-wide border-b border-slate-200 inline-block pb-1">
                BERITA ACARA SERAH TERIMA PROYEK (INTERNAL HANDOVER)
            </h2>
            <p class="text-[11.5px] text-slate-500">
                Pengalihan Tanggung Jawab Operasional &amp; Delivery dari Divisi Komersial (Sales) ke Tim Pelaksana Teknis
            </p>
        </div>

        {{-- 3. PERNYATAAN PEMBUKA --}}
        <div class="text-[12px] text-slate-700 leading-relaxed bg-[#F8FAFC] p-3.5 rounded-xl border border-slate-200">
            Pada hari ini, <strong>{{ $project->commercial_handover_at ? $project->commercial_handover_at->translatedFormat('l, d F Y') : date('d F Y') }}</strong>, 
            telah dilakukan serah terima berkas kontrak komersial, spesifikasi teknis deliverable, dan kewenangan implementasi proyek dengan rincian sebagai berikut:
        </div>

        {{-- 4. TABEL DETAIL PROYEK & LEGALITAS KONTRAK --}}
        <div class="border border-slate-300 rounded-xl overflow-hidden text-xs">
            <table class="w-full border-collapse">
                <tbody class="divide-y divide-slate-200">
                    <tr class="bg-[#F8FAFC]">
                        <td class="w-44 p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Nama Proyek</td>
                        <td class="p-2.5 font-bold text-slate-900 text-[13px]">{{ $project->name }}</td>
                    </tr>
                    <tr>
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Pelanggan / Klien</td>
                        <td class="p-2.5 font-semibold text-slate-800">{{ $project->client ?: '-' }}</td>
                    </tr>
                    <tr class="bg-[#F8FAFC]">
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Nomor PO / SPK / Kontrak</td>
                        <td class="p-2.5 font-mono font-bold text-slate-900">
                            {{ $project->po_spk_number ?: ($project->so_number ?: '-') }}
                            @if($project->po_spk_date)
                                <span class="text-slate-500 font-sans font-normal ml-2">({{ \Carbon\Carbon::parse($project->po_spk_date)->format('d/m/Y') }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Nilai Kontrak (Deal)</td>
                        <td class="p-2.5 font-bold text-[#8F0A0D] text-[13px]">
                            Rp {{ number_format((float) ($project->contract_value ?: ($project->quotation_amount ?: 0)), 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-[#F8FAFC]">
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Target &amp; Karakteristik</td>
                        <td class="p-2.5 font-semibold text-slate-800">
                            @if($project->handover_target === 'managed_service')
                                <span class="px-2 py-0.5 rounded font-bold bg-slate-200 text-slate-800">Operate: Managed Service &amp; Maintenance</span>
                                <span class="text-slate-500 ml-2">(Tier: {{ $project->sla_tier ?: 'Gold' }} &bull; {{ $project->maintenance_frequency ?: 'Monthly' }})</span>
                            @else
                                <span class="px-2 py-0.5 rounded font-bold bg-slate-200 text-slate-800">Deliver: PMO &amp; Delivery Team (Implementasi)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Termin Pembayaran</td>
                        <td class="p-2.5 text-slate-700 leading-snug">{{ $project->billing_terms ?: 'Sesuai dengan ketentuan standar PO/SPK.' }}</td>
                    </tr>
                    <tr class="bg-[#F8FAFC]">
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Komitmen Layanan &amp; SLA</td>
                        <td class="p-2.5 text-slate-700 leading-snug">{{ $project->sla_commitment ?: 'Garansi dan respon teknis standar operasional.' }}</td>
                    </tr>
                    <tr>
                        <td class="p-2.5 font-bold text-slate-600 bg-slate-100 border-r border-slate-200 uppercase text-[10.5px]">Batasan Scope (Exclusions)</td>
                        <td class="p-2.5 text-slate-700 leading-snug">{{ $project->exclusions ?: 'Pengadaan di luar BoQ dikenakan PO terpisah.' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- 5. KOTAK TANDA TANGAN SAH (2 PIHAK) --}}
        <div class="pt-4">
            <h4 class="text-[11.5px] font-bold text-slate-600 uppercase tracking-wider mb-2.5 text-center">
                PENGESAHAN DOKUMEN TANDA TERIMA DIGITAL (VERIFIED)
            </h4>

            <div class="grid grid-cols-2 gap-5 border border-slate-300 rounded-xl p-5 bg-[#F8FAFC]">
                
                {{-- ══ PIHAK 1: YANG MENYERAHKAN (SALES PIC) ══ --}}
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col justify-between h-56 text-center shadow-2xs">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#8F0A0D] block">PIHAK YANG MENYERAHKAN</span>
                        <span class="text-[10px] text-slate-500 font-medium">Divisi Sales &amp; Commercial</span>
                    </div>

                    {{-- Stempel Tanda Tangan & QR --}}
                    <div class="my-auto py-2 flex items-center justify-center gap-3">
                        @if(!empty($salesSignature['signature']))
                            <div class="h-16 flex items-center justify-center">
                                <img src="{{ $salesSignature['signature'] }}" alt="TTD Sales" class="max-h-16 max-w-full object-contain filter drop-shadow-2xs">
                            </div>
                        @else
                            <div class="py-2">
                                <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Diserahkan Secara Digital
                                </span>
                            </div>
                        @endif

                        @if(!empty($salesQrSvg))
                            <div class="w-14 h-14 p-1 bg-white border border-slate-200 rounded shadow-2xs shrink-0 flex items-center justify-center">
                                {!! $salesQrSvg !!}
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 pt-2 text-[10.5px] text-slate-700 space-y-0.5">
                        <p class="font-bold text-slate-900 truncate">
                            {{ $salesSignature['user_name'] ?? ($project->commercialHandoverBy?->name ?? ($project->sales_name ?: 'Sales PIC')) }}
                        </p>
                        <p class="text-[9.5px] text-slate-500">
                            {{ $salesSignature['user_role'] ?? 'Sales PIC & Account Manager' }}
                        </p>
                        <p class="text-[9px] font-mono text-slate-400">
                            {{ $salesSignature['signed_at'] ?? ($project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y H:i') : '-') }}
                            @if(!empty($salesSignature['qr_token']))
                                &bull; <strong class="text-slate-600">{{ $salesSignature['qr_token'] }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- ══ PIHAK 2: YANG MENERIMA (PMO / MANAGED SERVICE) ══ --}}
                <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col justify-between h-56 text-center shadow-2xs">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#8F0A0D] block">PIHAK YANG MENERIMA</span>
                        <span class="text-[10px] text-slate-500 font-medium">
                            {{ $project->handover_target === 'managed_service' ? 'Divisi Managed Service & Operasional' : 'Divisi Project Management Office (PMO)' }}
                        </span>
                    </div>

                    {{-- Stempel Tanda Tangan & QR --}}
                    <div class="my-auto py-2 flex items-center justify-center gap-3">
                        @php
                            $recipientSig = $pmSignature ?: $msSignature;
                            $recipientQr = $pmQrSvg ?: $msQrSvg;
                            $isAccepted = ($project->handover_status === 'Approved' || $project->ms_handover_status === 'Accepted');
                        @endphp

                        @if(!empty($recipientSig['signature']))
                            <div class="h-16 flex items-center justify-center">
                                <img src="{{ $recipientSig['signature'] }}" alt="TTD Penerima" class="max-h-16 max-w-full object-contain filter drop-shadow-2xs">
                            </div>
                        @elseif($isAccepted)
                            <div class="py-2">
                                <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    ✓ Disahkan &amp; Diterima PMO
                                </span>
                            </div>
                        @else
                            <div class="py-2">
                                <span class="px-2.5 py-1 rounded text-[10px] font-medium italic text-slate-400 bg-slate-100 border border-slate-200">
                                    Menunggu Tanda Tangan Penerima
                                </span>
                            </div>
                        @endif

                        @if(!empty($recipientQr) && $isAccepted)
                            <div class="w-14 h-14 p-1 bg-white border border-slate-200 rounded shadow-2xs shrink-0 flex items-center justify-center">
                                {!! $recipientQr !!}
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-200 pt-2 text-[10.5px] text-slate-700 space-y-0.5">
                        <p class="font-bold text-slate-900 truncate">
                            {{ $recipientSig['user_name'] ?? ($project->pm?->name ?? ($project->msAcceptedBy?->name ?? 'Project Manager / PMO')) }}
                        </p>
                        <p class="text-[9.5px] text-slate-500">
                            {{ $recipientSig['user_role'] ?? ($project->handover_target === 'managed_service' ? 'Lead Maintenance / MS' : 'Project Manager / PMO') }}
                        </p>
                        <p class="text-[9px] font-mono text-slate-400">
                            {{ $recipientSig['signed_at'] ?? ($project->handover_approved_at ? $project->handover_approved_at->format('d/m/Y H:i') : ($isAccepted ? date('d/m/Y') : 'Belum Disahkan')) }}
                            @if(!empty($recipientSig['qr_token']) && $isAccepted)
                                &bull; <strong class="text-slate-600">{{ $recipientSig['qr_token'] }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- 6. CATATAN LEGAL & PENUTUP --}}
        <div class="border-t border-slate-200 pt-4 text-[10px] text-slate-400 flex items-center justify-between">
            <div>
                Dokumen resmi digenerate secara otomatis oleh Field System Management PT IP Network Solusindo.
            </div>
            <div class="font-mono">
                Waktu Cetak: {{ date('d/m/Y H:i:s') }}
            </div>
        </div>

    </div>

</body>
</html>
