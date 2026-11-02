<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Serah Terima - {{ $project->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-page { box-shadow: none !important; border: 1px solid black !important; margin: 0 !important; max-width: 100% !important; padding: 20px !important; }
        }
        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 p-3 sm:p-8 antialiased">

    @php
        // Cek apakah mode eksplisit PM ke Managed Service (hanya via parameter query ?type=pm_to_ms)
        $isPmToMs = (request('type') === 'pm_to_ms');
        
        // Cek apakah serah terima Commercial ke Managed Service (murni kontrak Managed Service)
        $isManagedService = (!$isPmToMs && $project->handover_target === 'managed_service');
        
        $savedStatus = $handoverData['checklist_status'] ?? [];
        $savedNa = $handoverData['checklist_na'] ?? [];
        $savedNotes = $handoverData['checklist_notes'] ?? [];

        $hoStatus = $handoverData['handover_status_selected'] ?? ($project->handover_status ?: ($project->commercial_handover_status === 'Approved' ? 'approved' : 'submitted'));
    @endphp

    {{-- Toolbar Aksi (Hidden Saat Cetak) --}}
    <div class="max-w-5xl mx-auto mb-4 no-print flex flex-wrap items-center justify-between gap-3">
        <button type="button" onclick="window.history.back()"
                class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-xs transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </button>

        @if($project->handover_target === 'both')
            <div class="inline-flex rounded-xl bg-slate-200/80 p-1 border border-slate-300 text-xs font-bold gap-1 shadow-inner">
                <a href="{{ route('sales.handover.document', $project->id) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ !$isPmToMs ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    1. Serah Terima Sales ke PMO
                </a>
                <a href="{{ route('sales.handover.document', ['project' => $project->id, 'type' => 'pm_to_ms']) }}" 
                   class="px-3 py-1.5 rounded-lg transition {{ $isPmToMs ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    2. Serah Terima PM ke Managed Service
                </a>
            </div>
        @endif

        <div class="flex items-center gap-2.5">
            <button type="button" onclick="window.print()"
                    class="px-5 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Dokumen Serah Terima (Print / PDF)</span>
            </button>
        </div>
    </div>

    {{-- Kertas Dokumen Resmi Standar IPNET --}}
    <div class="max-w-5xl mx-auto bg-white border-2 border-[#CBD5E1] rounded-2xl shadow-xl p-6 sm:p-10 print-page space-y-5 text-[#0F172A] font-sans relative">
        
        {{-- Kop Dokumen Persis PDF --}}
        <div class="flex items-start justify-between gap-4">
            <div class="shrink-0">
                <img src="{{ asset('images/ipnet1.png') }}" onerror="this.src='/images/ipnet.png'" alt="Logo IP Network Solusindo" class="h-12 sm:h-14 w-auto object-contain">
            </div>
            <div class="text-right shrink-0">
                <div class="text-[#8F0A0D] font-black text-lg sm:text-xl uppercase tracking-tight leading-none">
                    CHECKLIST SERAH TERIMA
                </div>
                <div class="text-black font-black text-xs sm:text-sm uppercase tracking-normal mt-1">
                    IPNET#1
                </div>
                <div class="text-[10px] text-slate-500 italic font-medium mt-0.5">
                    Hal 1
                </div>
            </div>
        </div>

        {{-- ═══ TEMPLATE 1: SALES / COMMERCIAL KE PROJECT MANAGER (IMPLEMENTASI) ═══ --}}
        @if(!$isManagedService && !$isPmToMs)
            {{-- Judul Utama Dokumen Persis PDF --}}
            <div class="text-center my-3 sm:my-4">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">
                    BERITA ACARA SERAH TERIMA PROYEK
                </div>
                <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                    SERAH TERIMA SALES / COMMERCIAL KE PROJECT MANAGER
                </h1>
                <p class="text-xs italic text-slate-600 mt-1 font-serif">
                    Commercial-to-Project Handover Checklist
                </p>
            </div>

            {{-- Grid Metadata Form --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                            <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-PM-01</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                            <td class="py-1.5 px-3 border-r border-black font-semibold text-slate-900">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : date('d/m/Y') }}</td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                {{ $project->so_code ?: ('HND-PM/' . date('Y') . '/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Deskripsi Penggunaan PMO Persis PDF --}}
            <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg italic">
                Digunakan untuk pekerjaan yang mencakup implementasi/proyek dan memerlukan pengendalian PMO. Sales mengisi dan melampirkan dokumen; PMO memeriksa kesiapan. Item yang belum tersedia tidak otomatis dianggap diterima.
            </div>

            {{-- 1. TUJUAN DAN CARA PAKAI (Banner Hitam & Border Hitam Persis PDF) --}}
            <div class="border border-black overflow-hidden">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    TUJUAN DAN CARA PAKAI
                </div>
                <div class="p-0 text-xs">
                    <table class="w-full border-collapse border border-black">
                        <tbody>
                            <tr class="border-b border-black">
                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama customer</td>
                                <td class="w-3/10 py-1.5 px-2.5 border-r border-black font-bold text-[#0F172A]">{{ $project->client ?: '-' }}</td>
                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama proyek</td>
                                <td class="w-3/10 py-1.5 px-2.5 font-black text-[#8F0A0D]">{{ $project->name ?: '-' }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Kode proyek / Sales Order</td>
                                <td class="py-1.5 px-2.5 border-r border-black font-mono font-bold">{{ $project->so_code ?: ($project->so_number ?: '-') }}</td>
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">No. kontrak / PO / SPK</td>
                                <td class="py-1.5 px-2.5 font-mono font-bold">{{ $project->po_spk_number ?: ($project->po_number ?: '-') }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Tanggal handover</td>
                                <td class="py-1.5 px-2.5 border-r border-black font-semibold">{{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d M Y') : date('d M Y') }}</td>
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Target mulai / milestone</td>
                                <td class="py-1.5 px-2.5 font-semibold">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}</td>
                            </tr>
                            <tr class="border-b border-black">
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Sales / Account Manager</td>
                                <td class="py-1.5 px-2.5 border-r border-black font-bold text-[#0F172A]">{{ $project->sales_name ?: ($project->commercialHandoverBy?->name ?: '-') }}</td>
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">PM penerima</td>
                                <td class="py-1.5 px-2.5 font-bold text-blue-900">{{ $project->pm?->name ?: 'Tim Delivery PMO' }}</td>
                            </tr>
                            <tr>
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nilai Kontrak (Deal)</td>
                                <td class="py-1.5 px-2.5 border-r border-black font-bold text-[#8F0A0D]">
                                    Rp {{ number_format((float) ($project->contract_value ?: ($project->quotation_amount ?: 0)), 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Jenis proyek</td>
                                <td class="py-1.5 px-2.5 font-bold">
                                    {{ $project->project_type ?: 'Implementasi & Deployment' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. CHECKLIST KELENGKAPAN (14 Butir Sesuai PDF IPN-FRM-PM-01) --}}
            <div class="border border-black overflow-hidden">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                    <span>CHECKLIST KELENGKAPAN</span>
                    <span class="text-[10.5px] font-medium text-slate-200">Total 14 Butir Standar PMO</span>
                </div>

                <div class="overflow-x-auto text-xs">
                    <table class="w-full border-collapse border border-black text-left">
                        <thead>
                            <tr class="bg-[#F8FAFC] text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                                <th class="py-2 px-2 w-10 text-center border-r border-black">NO.</th>
                                <th class="py-2 px-3 w-64 sm:w-72 border-r border-black">KATEGORI / ITEM PEMERIKSAAN</th>
                                <th class="py-2 px-2 text-center w-24 border-r border-black">ADA / SIAP</th>
                                <th class="py-2 px-2 text-center w-12 border-r border-black">N/A</th>
                                <th class="py-2 px-3">CATATAN / REFERENSI DOKUMEN</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($handoverFormStructure as $item)
                                @php
                                    $k = $item['key'];
                                    $isNa = !empty($savedNa[$k]);
                                    $st = $savedStatus[$k] ?? ($isNa ? 'N/A' : 'Ada');
                                    $nt = $savedNotes[$k] ?? $item['default_note'];
                                @endphp
                                <tr class="border-b border-black hover:bg-slate-50/50 transition">
                                    <td class="py-2 px-2 text-center font-bold text-black align-middle bg-[#F8FAFC] border-r border-black">
                                        {{ $item['number'] }}
                                    </td>
                                    <td class="py-2 px-3 align-middle border-r border-black">
                                        <div class="text-[9px] font-bold text-[#8F0A0D] uppercase tracking-wider">
                                            {{ $item['category'] }}
                                        </div>
                                        <div class="font-bold text-black text-[11px] leading-tight mt-0.5">
                                            {{ $item['name'] }}
                                        </div>
                                    </td>
                                    <td class="py-2 px-2 text-center align-middle whitespace-nowrap border-r border-black font-bold">
                                        @if($isNa)
                                            <span class="text-slate-400 font-normal">N/A</span>
                                        @elseif($st === 'Tidak')
                                            <span class="text-rose-600 font-bold">Tidak</span>
                                        @else
                                            <span class="text-emerald-700 font-bold">&check; Ya</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-2 text-center align-middle border-r border-black">
                                        <input type="checkbox" disabled {{ $isNa ? 'checked' : '' }} class="w-3.5 h-3.5 text-[#8F0A0D] rounded border-slate-400">
                                    </td>
                                    <td class="py-2 px-2.5 align-middle text-slate-800">
                                        {{ $nt }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. CATATAN KHUSUS & KETENTUAN KOMERSIAL --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS &amp; KETENTUAN KOMERSIAL
                </div>
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/4 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Termin Pembayaran</td>
                            <td class="py-2 px-3">{{ $project->billing_terms ?: 'DP 30% setelah PO, 50% delivery barang, 20% pelunasan setelah BAST selesai.' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Komitmen Layanan &amp; SLA</td>
                            <td class="py-2 px-3">{{ $project->sla_commitment ?: 'Standar SLA Jam Kerja (8x5) Response Time 4 Jam.' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Batasan Scope (Exclusions)</td>
                            <td class="py-2 px-3">{{ $project->exclusions ?: 'Pengadaan di luar BoQ/BOM terlampir dikenakan PO terpisah.' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Catatan Khusus Sales</td>
                            <td class="py-2 px-3 italic text-slate-800">{{ $project->notes ?: ($project->special_notes ?: 'Pekerjaan implementasi siap dilaksanakan sesuai spesifikasi PO/SPK.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 4. REVIEW HANDOVER --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    STATUS REVIEW HANDOVER
                </div>
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Keputusan Status:</td>
                            <td class="py-2 px-3">
                                <div class="flex items-center gap-6">
                                    <label class="inline-flex items-center gap-1.5 font-bold text-xs {{ ($hoStatus === 'approved' || $hoStatus === 'Approved') ? 'text-emerald-800' : 'text-slate-400' }}">
                                        <input type="radio" disabled {{ ($hoStatus === 'approved' || $hoStatus === 'Approved') ? 'checked' : '' }} class="w-4 h-4 text-emerald-600">
                                        <span>Diterima</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 font-bold text-xs {{ ($hoStatus === 'conditional' || $hoStatus === 'Conditional') ? 'text-amber-800' : 'text-slate-400' }}">
                                        <input type="radio" disabled {{ ($hoStatus === 'conditional' || $hoStatus === 'Conditional') ? 'checked' : '' }} class="w-4 h-4 text-amber-600">
                                        <span>Diterima bersyarat</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 font-bold text-xs {{ ($hoStatus === 'rejected' || $hoStatus === 'Rejected') ? 'text-rose-800' : 'text-slate-400' }}">
                                        <input type="radio" disabled {{ ($hoStatus === 'rejected' || $hoStatus === 'Rejected') ? 'checked' : '' }} class="w-4 h-4 text-rose-600">
                                        <span>Ditolak / dikembalikan</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- ATURAN STATUS --}}
            <div class="space-y-1">
                <h3 class="font-black text-xs uppercase text-[#0F172A] tracking-wider">ATURAN STATUS</h3>
                <div class="text-xs text-slate-800 leading-relaxed border border-black p-3 bg-white">
                    <strong class="font-bold text-black">Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. 
                    <strong class="font-bold text-black ml-2">Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian. 
                    <strong class="font-bold text-black ml-2">Ditolak:</strong> scope/kontrak belum jelas; jangan memulai pekerjaan.
                </div>
            </div>

            {{-- 5. TABEL TANDA TANGAN (4 Kolom Sesuai PDF Standar) --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diterima / Disahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">PMO / Project Manager</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Validasi Teknis</div>
                                <div class="font-black text-slate-900 mt-0.5">Technical / Solution Lead</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Mengetahui</div>
                                <div class="font-black text-slate-900 mt-0.5">Head of Operations / PMO</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- 1. Sales / Commercial --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-slate-900">{{ $salesSignature['user_name'] ?? ($project->sales_name ?: 'Sales PIC') }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    @if(!empty($salesSignature) || $project->commercial_handover_status === 'Approved' || $project->commercial_handover_status === 'Submitted')
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            @if(!empty($salesQrSvg))
                                                <div class="w-12 h-12 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                    {!! $salesQrSvg !!}
                                                </div>
                                            @endif
                                            <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[120px] max-w-[150px] min-h-[50px] flex flex-col justify-between shadow-2xs">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                    @if(!empty($salesSignature['signature']))
                                                        <img src="{{ $salesSignature['signature'] }}" alt="TTD" class="max-h-7 max-w-[90px] object-contain">
                                                    @else
                                                        <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $salesSignature['user_name'] ?? ($project->sales_name ?: 'Sales PIC') }}</div>
                                                    @endif
                                                </div>
                                                <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                    dsign.ipnet.id/{{ $salesSignature['qr_token'] ?? 'SALES-SIGNED' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono text-[10.5px]">
                                            &check; Handover Diajukan
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                    &check; Signed by Sales PIC
                                </div>
                            </td>

                            {{-- 2. Project Manager / PMO --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ $pmSignature['user_name'] ?? ($project->pm?->name ?: 'Project Manager') }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->handover_approved_at ? $project->handover_approved_at->format('d/m/Y') : ($project->handover_status === 'Approved' ? date('d/m/Y') : '[Menunggu Approval]') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    @if($project->handover_status === 'Approved' || !empty($pmSignature))
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            @if(!empty($pmQrSvg))
                                                <div class="w-12 h-12 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                    {!! $pmQrSvg !!}
                                                </div>
                                            @endif
                                            <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[120px] max-w-[150px] min-h-[50px] flex flex-col justify-between shadow-2xs">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                    @if(!empty($pmSignature['signature']))
                                                        <img src="{{ $pmSignature['signature'] }}" alt="TTD" class="max-h-7 max-w-[90px] object-contain">
                                                    @else
                                                        <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $pmSignature['user_name'] ?? ($project->pm?->name ?: 'Project Manager') }}</div>
                                                    @endif
                                                </div>
                                                <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                    dsign.ipnet.id/{{ $pmSignature['qr_token'] ?? 'PMO-APPROVED' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-3 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                                Menunggu Approval PMO
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-slate-500">
                                    {{ $project->handover_status === 'Approved' ? '✓ Disahkan oleh PM' : 'Menunggu Pengesahan' }}
                                </div>
                            </td>

                            {{-- 3. Solution Lead --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Solution Lead</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                    <span class="text-[9px] text-slate-400 font-mono">(Solution Architect)</span>
                                </div>
                            </td>

                            {{-- 4. Head of Operations --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Susanto</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                    <span class="text-[9px] text-slate-400 font-mono">(Division Head)</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        {{-- ═══ TEMPLATE 2: SALES / COMMERCIAL KE MANAGED SERVICE (MAINTENANCE) ═══ --}}
        @elseif($isManagedService)
            {{-- Judul Utama Dokumen Persis PDF --}}
            <div class="text-center my-3 sm:my-4">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">
                    BERITA ACARA SERAH TERIMA PROYEK
                </div>
                <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                    SERAH TERIMA SALES / COMMERCIAL KE MANAGED SERVICE<br>
                    UNTUK KONTRAK MAINTENANCE
                </h1>
                <p class="text-xs italic text-slate-600 mt-1 font-serif">
                    Commercial-to-Project Handover Checklist
                </p>
            </div>

            {{-- Grid Metadata Form --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                            <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-MS-01</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                            <td class="py-1.5 px-3 border-r border-black font-semibold text-slate-900">{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') : date('d/m/Y') }}</td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                {{ $project->so_code ?: ('HND-MS/' . date('Y') . '/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Petunjuk Pengisian Persis PDF --}}
            <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                <strong class="font-bold text-black">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
            </div>

            {{-- 1. REFERENSI KONTRAK --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    REFERENSI KONTRAK
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-white border-r border-black">Pelanggan</td>
                            <td class="py-2 px-3 font-black text-slate-900">{{ $project->client ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor kontrak / PO</td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $project->po_spk_number ?: ($project->so_number ?: '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal efektif dan masa kontrak</td>
                            <td class="py-2 px-3 font-medium text-slate-800">
                                {{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') : date('d/m/Y') }} s/d {{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') : '-' }} (1 Tahun Kontrak)
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal berakhir</td>
                            <td class="py-2 px-3 font-semibold text-slate-800">{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC Commercial</td>
                            <td class="py-2 px-3 font-bold text-slate-900">{{ $project->sales_name ?: ($project->commercialHandoverBy?->name ?: '-') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC Managed Service</td>
                            <td class="py-2 px-3 font-bold text-[#8F0A0D]">Tim Managed Service &amp; Maintenance</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. CHECKLIST (9 Butir Sesuai PDF IPN-FRM-MS-01) --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CHECKLIST (9 BUTIR MANAGED SERVICE)
                </div>
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-white text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                            <th class="py-2 px-2.5 w-12 text-center border-r border-black">NO.</th>
                            <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                            <th class="py-2 px-2 text-center w-36 sm:w-44 border-r border-black">STATUS / ISIAN</th>
                            <th class="py-2 px-3 w-1/3 sm:w-2/5">CATATAN</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-black">
                        @foreach($msChecklistItems as $item)
                            @php
                                $k = $item['key'];
                                $st = $savedStatus[$k] ?? 'Ada';
                                $nt = $savedNotes[$k] ?? $item['default_note'];
                            @endphp
                            <tr>
                                <td class="py-2 px-2.5 text-center font-bold border-r border-black">{{ $item['number'] }}</td>
                                <td class="py-2 px-3 font-semibold text-slate-900 border-r border-black">{{ $item['name'] }}</td>
                                <td class="py-1.5 px-2 text-center border-r border-black font-bold">
                                    <span class="text-emerald-700 font-bold">&check; {{ $st }}</span>
                                </td>
                                <td class="py-1.5 px-3 text-slate-800">
                                    {{ $nt }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. CATATAN KHUSUS --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS
                </div>
                <div class="p-3 bg-white italic text-slate-800">
                    {{ $project->notes ?: ($project->special_notes ?: 'Kontrak maintenance berkala berjalan dengan cakupan SLA ' . ($project->sla_tier ?: 'Gold') . ' dan frekuensi ' . ($project->maintenance_frequency ?: 'Bulanan') . '.') }}
                </div>
            </div>

            {{-- 4. STATUS HANDOVER --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    STATUS HANDOVER
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black bg-white">
                            <td class="w-1/3 sm:w-1/4 py-2.5 px-3 font-bold border-r border-black">Status handover:</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 font-bold text-xs">
                                    &check; Diterima / Aktif Operasional
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Cakupan Layanan / SLA</td>
                            <td class="py-2 px-3 font-bold text-[#8F0A0D]">
                                {{ $project->sla_tier ?: 'Gold' }} &bull; {{ $project->sla_coverage_hours ?: '24x7 Non-Stop' }} &bull; Frekuensi: {{ $project->maintenance_frequency ?: 'Bulanan (Monthly)' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. TABEL TANDA TANGAN (4 Kolom Sesuai PDF Standar) --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Disahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">PMO / Project Manager</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diterima oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Managed Service Lead</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Mengetahui</div>
                                <div class="font-black text-slate-900 mt-0.5">Head of Operations</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- 1. Sales / Commercial --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-slate-900">{{ $project->sales_name ?: ($salesSignature['user_name'] ?? 'Sales PIC') }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($salesQrSvg))
                                            <div class="w-12 h-12 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $salesQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[120px] max-w-[150px] min-h-[50px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                @if(!empty($salesSignature['signature']))
                                                    <img src="{{ $salesSignature['signature'] }}" alt="TTD" class="max-h-7 max-w-[90px] object-contain">
                                                @else
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $salesSignature['user_name'] ?? ($project->sales_name ?: 'Sales PIC') }}</div>
                                                @endif
                                            </div>
                                            <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                dsign.ipnet.id/{{ $salesSignature['qr_token'] ?? 'SALES-SIGNED' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                    &check; Signed by Sales PIC
                                </div>
                            </td>

                            {{-- 2. PMO / Project Manager --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> {{ $project->pm?->name ?: 'PMO Delivery' }}</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono text-[10px]">
                                        &check; Terverifikasi
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Managed Service Lead --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Tim Managed Service</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($msQrSvg))
                                            <div class="w-12 h-12 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $msQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[120px] max-w-[150px] min-h-[50px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                @if(!empty($msSignature['signature']))
                                                    <img src="{{ $msSignature['signature'] }}" alt="TTD" class="max-h-7 max-w-[90px] object-contain">
                                                @else
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">Managed Service Lead</div>
                                                @endif
                                            </div>
                                            <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                dsign.ipnet.id/{{ $msSignature['qr_token'] ?? 'MS-ACCEPTED' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                    &check; Accepted by MS
                                </div>
                            </td>

                            {{-- 4. Head of Operations --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Susanto</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                    <span class="text-[9px] text-slate-400 font-mono">(Division Head)</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        {{-- ═══ TEMPLATE 3: PROJECT MANAGER KE MANAGED SERVICE (PASCA IMPLEMENTASI) ═══ --}}
        @else
            {{-- Judul Utama Dokumen Persis PDF --}}
            <div class="text-center my-3 sm:my-4">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">
                    BERITA ACARA SERAH TERIMA PROYEK
                </div>
                <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                    SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE<br>
                    UNTUK OPERASIONAL &amp; PEMELIHARAAN SISTEM
                </h1>
                <p class="text-xs italic text-slate-600 mt-1 font-serif">
                    Project Manager-to-Managed Service Handover Checklist
                </p>
            </div>

            {{-- Grid Metadata Form --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                            <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-MS-02</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                            <td class="py-1.5 px-3 border-r border-black font-mono text-slate-900">01/10/2026</td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Berita Acara</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                BA-MS/{{ date('Y') }}/{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Petunjuk Pengisian Persis PDF --}}
            <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                <strong class="font-bold text-black">Petunjuk:</strong> Formulir checklist serah terima operasional hasil implementasi proyek dari Project Manager (PMO) kepada Divisi Managed Service. Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
            </div>

            {{-- 1. REFERENSI KONTRAK & PROYEK IMPLEMENTASI --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    1. REFERENSI KONTRAK &amp; PROYEK IMPLEMENTASI
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-white border-r border-black">Pelanggan / Klien</td>
                            <td class="py-2 px-3 font-black text-slate-900">{{ $project->client ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Nama Proyek Implementasi</td>
                            <td class="py-2 px-3 font-black text-[#8F0A0D]">{{ $project->name ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor Kontrak / PO / SPK</td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $project->po_spk_number ?: ($project->so_number ?: '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor Sales Order (SO)</td>
                            <td class="py-2 px-3 font-mono font-bold text-[#8F0A0D]">{{ $project->so_code ?: ($project->so_number ?: '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal Efektif &amp; Masa Operasional</td>
                            <td class="py-2 px-3 font-medium text-slate-800">
                                {{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') : date('d/m/Y') }} s/d {{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') : '-' }} (Masa Pemeliharaan Sistem)
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Diserahkan oleh (Project Manager)</td>
                            <td class="py-2 px-3 font-bold text-[#8F0A0D]">{{ $project->pm?->name ?: 'Project Manager' }} (PMO)</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-white border-r border-black">Diterima oleh (Managed Service)</td>
                            <td class="py-2 px-3 font-bold text-slate-900">Tim Managed Service &amp; Maintenance Support</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. CHECKLIST (9 Butir Sesuai PDF IPN-FRM-MS-02) --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CHECKLIST KELENGKAPAN OPERASIONAL
                </div>
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="bg-white text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                            <th class="py-2 px-2.5 w-12 text-center border-r border-black">NO.</th>
                            <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                            <th class="py-2 px-2 text-center w-36 sm:w-44 border-r border-black">STATUS / ISIAN</th>
                            <th class="py-2 px-3 w-1/3 sm:w-2/5">CATATAN</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-black">
                        @foreach($msChecklistItems as $item)
                            @php
                                $k = $item['key'];
                                $st = $savedStatus[$k] ?? 'Ada';
                                $nt = $savedNotes[$k] ?? $item['default_note'];
                            @endphp
                            <tr>
                                <td class="py-2 px-2.5 text-center font-bold border-r border-black">{{ $item['number'] }}</td>
                                <td class="py-2 px-3 font-semibold text-slate-900 border-r border-black">{{ $item['name'] }}</td>
                                <td class="py-1.5 px-2 text-center border-r border-black font-bold">
                                    <span class="text-emerald-700 font-bold">&check; {{ $st }}</span>
                                </td>
                                <td class="py-1.5 px-3 text-slate-800">
                                    {{ $nt }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. CATATAN KHUSUS --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS TRANSISI
                </div>
                <div class="p-3 bg-white italic text-slate-800">
                    {{ $project->notes ?: ($project->special_notes ?: 'Pekerjaan implementasi telah selesai dan siap dialihkan ke tim Managed Service.') }}
                </div>
            </div>

            {{-- 4. TABEL TANDA TANGAN (4 Kolom Sesuai PDF Standar) --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Project Manager / PMO</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diterima oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Managed Service Lead</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Validasi Teknis</div>
                                <div class="font-black text-slate-900 mt-0.5">Technical / Solution Lead</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Mengetahui</div>
                                <div class="font-black text-slate-900 mt-0.5">Head of Operations / PMO</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- 1. Project Manager --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ $project->pm?->name ?: ($pmSignature['user_name'] ?? 'Project Manager') }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($pmQrSvg))
                                            <div class="w-12 h-12 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $pmQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[120px] max-w-[150px] min-h-[50px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                @if(!empty($pmSignature['signature']))
                                                    <img src="{{ $pmSignature['signature'] }}" alt="TTD" class="max-h-7 max-w-[90px] object-contain">
                                                @else
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $project->pm?->name ?: 'Project Manager' }}</div>
                                                @endif
                                            </div>
                                            <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                dsign.ipnet.id/{{ $pmSignature['qr_token'] ?? 'PMO-SIGNED' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                    &check; Signed by Project Manager
                                </div>
                            </td>

                            {{-- 2. Managed Service Lead --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Tim Managed Service</p>
                                    <p><strong class="font-bold">Tanggal:</strong> [Saat Verifikasi]</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 font-mono text-[9.5px]">
                                        Menunggu Acceptance MS
                                    </div>
                                </div>
                            </td>

                            {{-- 3. Solution Lead --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Solution Lead</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                    <span class="text-[9px] text-slate-400 font-mono">(Solution Architect)</span>
                                </div>
                            </td>

                            {{-- 4. Operations Head --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> Susanto</p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-4 text-center">
                                    <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                    <span class="text-[9px] text-slate-400 font-mono">(Division Head)</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Catatan Pengendalian Persis PDF --}}
        <p class="text-[11px] text-slate-800 leading-relaxed pt-2">
            <strong class="font-bold text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
        </p>

        {{-- Footer Cetak --}}
        <div class="border-t border-slate-200 pt-3 text-[10px] text-slate-400 flex items-center justify-between no-print">
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
