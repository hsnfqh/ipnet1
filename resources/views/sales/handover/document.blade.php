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
        // Resolusi tipe dokumen yang sedang ditampilkan (sales_to_pm, sales_to_ms, atau pm_to_ms)
        $typeParam = request('type');
        if ($typeParam === 'pm_to_ms') {
            $activeDocType = 'pm_to_ms';
        } elseif ($typeParam === 'sales_to_ms' || ($project->handover_target === 'managed_service' && empty($typeParam))) {
            $activeDocType = 'sales_to_ms';
        } else {
            $activeDocType = 'sales_to_pm';
        }

        $isSalesToPm = ($activeDocType === 'sales_to_pm');
        $isSalesToMs = ($activeDocType === 'sales_to_ms');
        $isPmToMs    = ($activeDocType === 'pm_to_ms');

        $isManagedService = $isSalesToMs;

        $savedStatus = $handoverData['checklist_status'] ?? [];
        $savedNa = $handoverData['checklist_na'] ?? [];
        $savedNotes = $handoverData['checklist_notes'] ?? [];

        $hoStatus = $handoverData['handover_status_selected'] ?? ($project->handover_status ?: ($project->commercial_handover_status === 'Approved' ? 'approved' : 'submitted'));

        // Nama PM resmi dari sistem (relasi project->pm atau fallback user role PM)
        $assignedPmName = $project->pm?->name 
            ?: ($handoverData['pm_name'] 
                ?? ($handoverData['assigned_pm_name'] 
                    ?? ($project->pm_id ? \App\Models\User::find($project->pm_id)?->name : 'Rizki')
                )
            );

        // Nama Sales PIC resmi dari sistem
        $salesPicName = $project->sales_name ?: ($project->commercialHandoverBy?->name ?: ($salesSignature['user_name'] ?? 'Raiza'));
    @endphp

    {{-- Toolbar Aksi (Hidden Saat Cetak) --}}
    <div class="max-w-5xl mx-auto mb-4 no-print flex items-center justify-between gap-3">
        <a href="{{ route('sales.handover.index') }}" onclick="event.preventDefault(); handleGoBack();"
           class="px-4 py-2 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 shadow-xs transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>

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

        {{-- ========================================================================= --}}
        {{-- TEMPLATE 1: SERAH TERIMA SALES / COMMERCIAL KE PROJECT MANAGER (IPN-FRM-PM-01) --}}
        {{-- ========================================================================= --}}
        @if($isSalesToPm)
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

            {{-- Grid Metadata Form Persis Scan PDF --}}
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
                            <td class="py-1.5 px-3 border-r border-black font-mono text-slate-900">
                                {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}
                            </td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                {{ $project->handover_no ?: ('HND-PM/' . date('Y') . '/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Petunjuk Pengisian Persis Scan PDF --}}
            <div class="text-[11px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                Digunakan untuk pekerjaan yang mencakup implementasi/proyek dan memerlukan pengendalian PMO. Sales mengisi dan melampirkan dokumen; PMO memeriksa kesiapan. Item yang belum tersedia tidak otomatis dianggap diterima.
            </div>

            {{-- 1. TUJUAN DAN CARA PAKAI Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    TUJUAN DAN CARA PAKAI
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama customer</td>
                            <td class="w-3/10 py-1.5 px-2.5 border-r border-black font-semibold text-slate-900">{{ $project->client ?: '-' }}</td>
                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama proyek</td>
                            <td class="w-3/10 py-1.5 px-2.5 font-bold text-[#8F0A0D]">{{ $project->name ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Kode proyek / Sales Order</td>
                            <td class="py-1.5 px-2.5 border-r border-black font-mono font-bold">{{ $project->so_code ?: ($project->so_number ?: '-') }}</td>
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">No. kontrak / PO / SPK</td>
                            <td class="py-1.5 px-2.5 font-mono font-bold">{{ $project->po_spk_number ?: ($project->po_number ?: '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Tanggal handover</td>
                            <td class="py-1.5 px-2.5 border-r border-black font-semibold">{{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}</td>
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Target mulai / milestone</td>
                            <td class="py-1.5 px-2.5 font-semibold">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Sales / Account Manager</td>
                            <td class="py-1.5 px-2.5 border-r border-black font-bold text-[#0F172A]">{{ $salesPicName }}</td>
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">PM penerima</td>
                            <td class="py-1.5 px-2.5 font-bold text-[#8F0A0D]">{{ $assignedPmName }}</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Folder dokumen / lokasi penyimpanan</td>
                            <td class="py-1.5 px-2.5 border-r border-black font-mono text-slate-700">
                                {{ $handoverData['doc_storage_location'] ?? ($project->doc_storage_location ?? 'Google Drive / Server IPNET') }}
                            </td>
                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Jenis proyek</td>
                            <td class="py-1.5 px-2.5">
                                @php
                                    $selectedProjType = $handoverData['project_type_category'] ?? 'Implementasi';
                                    $otherProjType = $handoverData['project_type_other'] ?? '';
                                @endphp
                                <span class="inline-flex items-center gap-1 font-mono font-bold text-[11px]">
                                    [{{ $selectedProjType === 'Implementasi' ? '✓' : ' ' }}] Implementasi &nbsp;&nbsp; 
                                    [{{ $selectedProjType === 'Upgrade' ? '✓' : ' ' }}] Upgrade &nbsp;&nbsp; 
                                    [{{ $selectedProjType === 'Relokasi' ? '✓' : ' ' }}] Relokasi &nbsp;&nbsp; 
                                    [{{ $selectedProjType === 'Lainnya' ? '✓' : ' ' }}] Lainnya{{ $selectedProjType === 'Lainnya' && $otherProjType ? ': ' . $otherProjType : ': ______' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-[10.5px] italic text-slate-600 px-1">
                Tandai status setiap item. Jika tidak relevan, pilih N/A dan tuliskan alasannya pada kolom catatan.
            </div>

            {{-- 2. CHECKLIST KELENGKAPAN (14 Butir Persis Scan PDF) --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                    <span>CHECKLIST KELENGKAPAN</span>
                    <span class="text-[10.5px] font-medium text-slate-200">Total 14 Butir Standar PMO</span>
                </div>

                <div class="overflow-x-auto text-xs">
                    <table class="w-full border-collapse border border-black text-left">
                        <thead>
                            <tr class="bg-[#F1F5F9] border-b border-black text-[11px] font-bold text-black uppercase">
                                <th class="w-8 py-2 px-2 text-center border-r border-black">NO.</th>
                                <th class="py-2 px-3 border-r border-black">KATEGORI / ITEM PEMERIKSAAN</th>
                                <th class="w-28 py-2 px-2 text-center border-r border-black">ADA / SIAP</th>
                                <th class="w-12 py-2 px-2 text-center border-r border-black">N/A</th>
                                <th class="py-2 px-3">CATATAN / REFERENSI DOKUMEN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black font-normal text-[11.5px]">
                            @foreach($handoverFormStructure as $idx => $item)
                                @php
                                    $itemKey = $item['key'] ?? ('item_' . $item['number']);
                                    $altKey  = $item['alt_key'] ?? '';
                                    $itemStatus = $savedStatus[$itemKey] 
                                        ?? ($altKey ? ($savedStatus[$altKey] ?? null) : null) 
                                        ?? ($savedStatus[$item['number']] ?? null);

                                    $itemNa = !empty($savedNa[$itemKey]) 
                                        || ($altKey && !empty($savedNa[$altKey])) 
                                        || !empty($savedNa[$item['number']]);

                                    $itemNote = $savedNotes[$itemKey] 
                                        ?? ($altKey ? ($savedNotes[$altKey] ?? null) : null) 
                                        ?? ($savedNotes[$item['number']] ?? ($item['default_note'] ?? ''));

                                    // Default checked ada / siap jika belum diisi eksplisit
                                    $isAda = ($itemStatus === 'yes' || $itemStatus === 'ada' || $itemStatus === 'Ada' || $itemStatus === 'valid' || $itemStatus === true || !isset($itemStatus)) && !$itemNa;
                                    $isTidak = ($itemStatus === 'no' || $itemStatus === 'tidak' || $itemStatus === 'Tidak') && !$itemNa;
                                @endphp
                                <tr class="hover:bg-slate-50 transition border-b border-black">
                                    <td class="py-1.5 px-2 text-center font-bold border-r border-black align-top">{{ $item['number'] }}</td>
                                    <td class="py-1.5 px-3 border-r border-black align-top">
                                        <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">{{ $item['category'] }}</div>
                                        <div class="font-semibold text-black leading-snug">{{ $item['name'] }}</div>
                                    </td>
                                    <td class="py-1.5 px-2 border-r border-black text-center align-top whitespace-nowrap">
                                        <span class="inline-flex items-center gap-0.5 font-mono {{ $isAda ? 'font-bold text-black' : 'text-slate-400' }}">
                                            [{{ $isAda ? '✓' : ' ' }}] Ya
                                        </span>
                                        &nbsp;
                                        <span class="inline-flex items-center gap-0.5 font-mono {{ $isTidak ? 'font-bold text-black' : 'text-slate-400' }}">
                                            [{{ $isTidak ? '✓' : ' ' }}] Tidak
                                        </span>
                                    </td>
                                    <td class="py-1.5 px-2 border-r border-black text-center align-top whitespace-nowrap font-mono {{ $itemNa ? 'font-bold text-black' : 'text-slate-400' }}">
                                        [{{ $itemNa ? '✓' : ' ' }}]
                                    </td>
                                    <td class="py-1.5 px-3 align-top text-slate-700">
                                        {{ $itemNote ?: ($item['default_note'] ?? '-') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. CATATAN KHUSUS Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS
                </div>
                <div class="p-3 bg-white text-slate-800 leading-relaxed min-h-[50px]">
                    {{ $project->special_notes ?: ($handoverData['special_notes'] ?? '[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]') }}
                </div>
            </div>

            {{-- 4. REVIEW HANDOVER Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    REVIEW HANDOVER
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Isu scope/komersial yang perlu klarifikasi</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_clarification_issues'] ?? ($handoverData['review_scope_clarification'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Asumsi yang harus dikonfirmasi</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_assumptions'] ?? ($handoverData['review_assumptions'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kebutuhan validasi Solution/Technical</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_tech_validation'] ?? ($handoverData['review_technical_validation'] ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Keputusan yang masih memerlukan persetujuan</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_pending_decisions'] ?? ($handoverData['review_decision_approval'] ?? '-') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. HASIL PEMERIKSAAN DAN KEPUTUSAN Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    HASIL PEMERIKSAAN DAN KEPUTUSAN
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black bg-white">
                            <td class="w-1/3 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Status handover:</td>
                            <td class="py-2 px-3 font-mono font-bold">
                                @php
                                    $selectedHoStatus = $handoverData['handover_status_selected'] ?? (strtolower($project->handover_status) === 'conditional' ? 'conditional' : (in_array(strtolower($project->handover_status), ['approved', 'submitted']) ? 'approved' : 'approved'));
                                @endphp
                                <span class="{{ $selectedHoStatus === 'approved' ? 'text-emerald-700' : 'text-slate-500' }}">
                                    [{{ $selectedHoStatus === 'approved' ? '✓' : ' ' }}] Diterima
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $selectedHoStatus === 'conditional' ? 'text-amber-700' : 'text-slate-500' }}">
                                    [{{ $selectedHoStatus === 'conditional' ? '✓' : ' ' }}] Diterima bersyarat
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $selectedHoStatus === 'rejected' ? 'text-rose-700' : 'text-slate-500' }}">
                                    [{{ $selectedHoStatus === 'rejected' ? '✓' : ' ' }}] Ditolak / dikembalikan
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kekurangan / risiko yang masih terbuka</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_risks'] ?? ($project->handover_conditional_notes ?: ($handoverData['open_risks'] ?? '-')) }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">PIC tindak lanjut</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_pic_target'] ?? ($handoverData['followup_pic'] ?? $assignedPmName) }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Target penyelesaian</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['followup_target'] ?? ($project->handover_conditional_deadline ? $project->handover_conditional_deadline->format('d/m/Y') : '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Batasan pekerjaan yang boleh berjalan</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_work_limitations'] ?? ($handoverData['work_limitations'] ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Nomor tiket / WO / dokumen terkait</td>
                            <td class="py-2 px-3 font-mono text-slate-800">{{ $handoverData['handover_related_docs'] ?? ($handoverData['ticket_wo_number'] ?? ($project->so_code ?: ($project->so_number ?: '-'))) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 6. ATURAN STATUS Persis Scan PDF --}}
            <div class="border border-black p-3 bg-slate-50 text-[11px] leading-relaxed">
                <div class="font-bold text-black uppercase mb-1">ATURAN STATUS</div>
                <p class="text-slate-700">
                    <strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. <strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. <strong>Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak.
                </p>
            </div>

            {{-- 7. TABEL TANDA TANGAN 4 KOLOM PERSIS SCAN PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diterima oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">PMO / Project Manager</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Validasi teknis oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">TL / Solution</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Persetujuan / eskalasi pihak berwenang</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- Col 1: Sales / Commercial --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-slate-900">{{ $salesPicName }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    @if(!empty($salesSignature) || $project->commercial_handover_status === 'Approved' || $project->commercial_handover_status === 'Submitted')
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            @if(!empty($salesQrSvg))
                                                <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                    {!! $salesQrSvg !!}
                                                </div>
                                            @endif
                                            <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $salesPicName }}</div>
                                                </div>
                                                <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                    dsign.ipnet.id/{{ $salesSignature['qr_token'] ?? 'SALES-SIGNED' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-6 text-center border-b border-slate-400 w-3/4 mx-auto"></div>
                                    @endif
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                    &check; Signed by Sales PIC
                                </div>
                            </td>

                            {{-- Col 2: PMO / Project Manager --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ $assignedPmName }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->handover_approved_at ? $project->handover_approved_at->format('d/m/Y') : ($project->handover_status === 'Approved' ? date('d/m/Y') : '-') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    @if($project->handover_status === 'Approved' || !empty($pmSignature))
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            @if(!empty($pmQrSvg))
                                                <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                    {!! $pmQrSvg !!}
                                                </div>
                                            @endif
                                            <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $assignedPmName }}</div>
                                                </div>
                                                <div class="text-[6.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                    dsign.ipnet.id/{{ $pmSignature['qr_token'] ?? 'PMO-APPROVED' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="py-4 text-center">
                                            <span class="inline-block px-2.5 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 text-[10px] font-bold">
                                                Menunggu Approval PM
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[9.5px] text-center font-mono text-slate-500">
                                    {{ $project->handover_status === 'Approved' ? '✓ Disahkan oleh PM' : 'Menunggu Pengesahan' }}
                                </div>
                            </td>

                            {{-- Col 3: Validasi Teknis TL / Solution (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>

                            {{-- Col 4: Persetujuan / Eskalasi Pihak Berwenang (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Pengendalian Persis Scan PDF --}}
            <div class="text-[10.5px] text-slate-600 leading-snug pt-1">
                <strong class="text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
            </div>

        {{-- ========================================================================= --}}
        {{-- TEMPLATE 2: SERAH TERIMA SALES / COMMERCIAL KE MANAGED SERVICE (IPN-FRM-MS-01) --}}
        {{-- ========================================================================= --}}
        @elseif($isSalesToMs)
            {{-- Judul Utama Dokumen Persis Scan PDF --}}
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

            {{-- Grid Metadata Form Persis Scan PDF --}}
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
                            <td class="py-1.5 px-3 border-r border-black font-mono text-slate-900">
                                {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}
                            </td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                {{ $project->handover_no ?: ('HND-MS/' . date('Y') . '/' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Petunjuk Pengisian Persis Scan PDF --}}
            <div class="text-[11px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                <strong class="font-bold text-black">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
            </div>

            {{-- 1. REFERENSI KONTRAK Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    REFERENSI KONTRAK
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Pelanggan</td>
                            <td class="py-2 px-3 font-bold text-slate-900">{{ $project->client ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Nomor kontrak / PO</td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-900">{{ $project->po_spk_number ?: ($project->po_number ?: '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal efektif dan masa kontrak</td>
                            <td class="py-2 px-3 font-medium text-slate-800">
                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }} (Masa Kontrak: {{ $project->contract_duration ?: '12 Bulan' }})
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal berakhir</td>
                            <td class="py-2 px-3 font-medium text-slate-800">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">PIC Commercial</td>
                            <td class="py-2 px-3 font-bold text-[#0F172A]">{{ $salesPicName }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">PIC Managed Service</td>
                            <td class="py-2 px-3 font-bold text-[#8F0A0D]">{{ $project->msAcceptedBy?->name ?: 'Tim Managed Service' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. CHECKLIST (8 Butir Persis Scan PDF) --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                    <span>CHECKLIST</span>
                    <span class="text-[10.5px] font-medium text-slate-200">Total 8 Butir Standar Managed Service</span>
                </div>
                <table class="w-full border-collapse border border-black text-left">
                    <thead>
                        <tr class="bg-[#F1F5F9] border-b border-black text-[11px] font-bold text-black uppercase">
                            <th class="w-10 py-2 px-2 text-center border-r border-black">NO.</th>
                            <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                            <th class="w-48 py-2 px-3 border-r border-black">STATUS / ISIAN</th>
                            <th class="py-2 px-3">CATATAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black font-normal text-[11.5px]">
                        @foreach($msMaintenanceChecklist as $item)
                            @php
                                $msKey = $item['key'] ?? ('item_' . $item['number']);
                                $hasSaved = isset($savedStatus[$msKey]) || isset($savedStatus[$item['number']]);
                                if ($hasSaved) {
                                    $itemStatus = $savedStatus[$msKey] ?? $savedStatus[$item['number']];
                                    $itemNote = $savedNotes[$msKey] ?? ($savedNotes[$item['number']] ?? '');
                                } elseif ($project->ms_handover_status === 'Accepted') {
                                    $itemStatus = $item['status'];
                                    $itemNote = $item['default_note'];
                                } else {
                                    $itemStatus = 'Belum Diperiksa';
                                    $itemNote = 'Menunggu verifikasi kesiapan oleh PIC Managed Service';
                                }
                                $itemNa = !empty($savedNa[$msKey]) || !empty($savedNa[$item['number']]);
                                if ($itemNa) $itemStatus = 'Tidak Berlaku';
                            @endphp
                            <tr class="hover:bg-slate-50 transition border-b border-black">
                                <td class="py-2 px-2 text-center font-bold border-r border-black align-top">{{ $item['number'] }}</td>
                                <td class="py-2 px-3 font-semibold text-black border-r border-black align-top">{{ $item['name'] }}</td>
                                <td class="py-2 px-3 border-r border-black align-top font-medium {{ $itemStatus === 'Belum Diperiksa' ? 'text-amber-700 italic font-semibold' : 'text-slate-800' }}">{{ $itemStatus }}</td>
                                <td class="py-2 px-3 align-top {{ $itemStatus === 'Belum Diperiksa' ? 'text-slate-400 italic' : 'text-slate-600' }}">{{ $itemNote }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. CATATAN KHUSUS Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS
                </div>
                <div class="p-3 bg-white text-slate-800 leading-relaxed min-h-[50px]">
                    {{ $project->special_notes ?: ($handoverData['special_notes'] ?? '[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]') }}
                </div>
            </div>

            {{-- 4. STATUS HANDOVER Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    STATUS HANDOVER
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black bg-white">
                            <td class="w-1/3 sm:w-1/4 py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Status handover:</td>
                            <td class="py-2.5 px-3 font-mono font-bold">
                                @php
                                    $selectedMsHoStatus = $handoverData['handover_status_selected'] ?? ($project->ms_handover_status === 'Accepted' ? 'approved' : null);
                                @endphp
                                <span class="{{ $selectedMsHoStatus === 'approved' ? 'text-emerald-700 font-bold' : 'text-slate-500' }}">
                                    [{{ $selectedMsHoStatus === 'approved' ? '✓' : ' ' }}] Diterima
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $selectedMsHoStatus === 'conditional' ? 'text-amber-700 font-bold' : 'text-slate-500' }}">
                                    [{{ $selectedMsHoStatus === 'conditional' ? '✓' : ' ' }}] Diterima bersyarat
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $selectedMsHoStatus === 'rejected' ? 'text-rose-700 font-bold' : 'text-slate-500' }}">
                                    [{{ $selectedMsHoStatus === 'rejected' ? '✓' : ' ' }}] Ditolak / dikembalikan
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kekurangan / risiko yang masih terbuka</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_risks'] ?? ($handoverData['open_risks'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Informasi yang belum tersedia</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_missing_info'] ?? ($handoverData['missing_info'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">PIC dan target penyelesaian</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_pic_target'] ?? ($handoverData['resolution_pic_target'] ?? '-') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Konfirmasi Commercial dan Managed Service</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_confirmation'] ?? ($handoverData['confirmation_notes'] ?? 'Telah dikonfirmasi dan diselaraskan antara Sales PIC dan Tim Managed Service.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Batasan pekerjaan yang boleh berjalan</td>
                            <td class="py-2 px-3 text-slate-800">{{ $handoverData['handover_work_limitations'] ?? ($handoverData['work_limitations'] ?? '-') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. ATURAN STATUS Persis Scan PDF --}}
            <div class="border border-black p-3 bg-slate-50 text-[11px] leading-relaxed">
                <div class="font-bold text-black uppercase mb-1">ATURAN STATUS</div>
                <p class="text-slate-700">
                    <strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. <strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. <strong>Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak.
                </p>
            </div>

            {{-- 6. TABEL TANDA TANGAN 4 KOLOM PERSIS SCAN PDF HAL 2 --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diperiksa / diterima oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Managed Service</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Validasi teknis oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">TL / Solution</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Persetujuan / eskalasi pihak berwenang</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- Col 1: Sales / Commercial --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-slate-900">{{ $salesPicName }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->commercial_handover_at ? $project->commercial_handover_at->format('d/m/Y') : date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($salesQrSvg))
                                            <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $salesQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $salesPicName }}</div>
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

                            {{-- Col 2: Managed Service --}}
                            <td class="p-2.5 border-r border-black align-top flex flex-col justify-between min-h-[160px]">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ $project->msAcceptedBy?->name ?: ($project->ms_handover_status === 'Accepted' ? 'Tim Managed Service' : '—') }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ $project->ms_accepted_at ? $project->ms_accepted_at->format('d/m/Y') : '—' }}</p>
                                </div>
                                @if($project->ms_handover_status === 'Accepted' || !empty($msSignature['signed']))
                                    <div class="py-3 flex items-center justify-center">
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            @if(!empty($msQrSvg))
                                                <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                    {!! $msQrSvg !!}
                                                </div>
                                            @endif
                                            <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                    <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $project->msAcceptedBy?->name ?: ($msSignature['user_name'] ?? 'Managed Service Lead') }}</div>
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
                                @else
                                    <div class="py-3 my-2 flex flex-col items-center justify-center text-center p-2 border border-dashed border-amber-300 rounded-lg bg-amber-50/60">
                                        <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider">Menunggu Pemeriksaan</span>
                                        <span class="text-[9px] text-amber-600 mt-0.5">Belum diverifikasi oleh tim Managed Service</span>
                                    </div>
                                    <div class="text-[9px] text-center font-mono text-amber-700 font-semibold">
                                        ⏳ Pending MS Acceptance
                                    </div>
                                @endif
                            </td>

                            {{-- Col 3: Validasi Teknis TL / Solution (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>

                            {{-- Col 4: Persetujuan / Eskalasi Pihak Berwenang (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Pengendalian Persis Scan PDF --}}
            <div class="text-[10.5px] text-slate-600 leading-snug pt-1">
                <strong class="text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
            </div>

        {{-- ========================================================================= --}}
        {{-- TEMPLATE 3: SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE (IPN-FRM-DEL-01) --}}
        {{-- ========================================================================= --}}
        @else
            {{-- Judul Utama Dokumen Persis Scan PDF --}}
            <div class="text-center my-3 sm:my-4">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">
                    BERITA ACARA SERAH TERIMA PROYEK
                </div>
                <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                    SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE
                </h1>
                <p class="text-xs italic text-slate-600 mt-1 font-serif">
                    Project Handover to Managed Service Checklist – Tahap proses: DELIVER
                </p>
            </div>

            {{-- Grid Metadata Form Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse border border-black">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                            <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-DEL-01</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                            <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                            <td class="py-1.5 px-3 border-r border-black font-mono text-slate-900">{{ date('d/m/Y') }}</td>
                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                            <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]">
                                HND-PMMS/{{ date('Y') }}/{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Petunjuk Pengisian Persis Scan PDF --}}
            <div class="text-[11px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                <strong class="font-bold text-black">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
            </div>

            {{-- 1. REFERENSI KONTRAK Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    REFERENSI KONTRAK
                </div>
                <table class="w-full border-collapse">
                    <tbody>
                        <tr class="border-b border-black">
                            <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Pelanggan</td>
                            <td class="py-2 px-3 font-bold text-slate-900">{{ $project->client ?: '-' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Nomor kontrak / SPK / Nama Project</td>
                            <td class="py-2 px-3 font-mono font-bold text-slate-900">
                                {{ ($project->po_spk_number ?: ($project->po_number ?: '-')) }} / {{ $project->name }}
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Handover</td>
                            <td class="py-2 px-3 font-semibold text-slate-800">{{ date('d/m/Y') }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Diserahkan oleh PMO / PM</td>
                            <td class="py-2 px-3 font-bold text-[#8F0A0D]">{{ $assignedPmName }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Diterima oleh Managed Service</td>
                            <td class="py-2 px-3 font-bold text-blue-900">{{ $project->msAcceptedBy?->name ?: 'Tim Managed Service' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 2. CHECKLIST (9 Butir Persis Scan PDF) --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                    <span>CHECKLIST</span>
                    <span class="text-[10.5px] font-medium text-slate-200">Total 9 Butir Standar Transisi Deliver ke Operate</span>
                </div>
                <table class="w-full border-collapse border border-black text-left">
                    <thead>
                        <tr class="bg-[#F1F5F9] border-b border-black text-[11px] font-bold text-black uppercase">
                            <th class="w-10 py-2 px-2 text-center border-r border-black">No.</th>
                            <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                            <th class="w-48 py-2 px-3 border-r border-black">STATUS / ISIAN</th>
                            <th class="py-2 px-3">CATATAN</th>
                        </tr>
                    </thead>
                    @php
                        $delHandover = $handoverData['ms_handover'] ?? [];
                        $delStatusMap = $delHandover['checklist_status'] ?? [];
                        $delNaMap = $delHandover['checklist_na'] ?? [];
                        $delNotesMap = $delHandover['checklist_notes'] ?? [];
                    @endphp
                    <tbody class="divide-y divide-black font-normal text-[11.5px]">
                        @foreach($msChecklistItems as $item)
                            @php
                                $itemKey = $item['key'] ?? ('item_' . $item['number']);
                                $itemStatus = $delStatusMap[$itemKey] ?? ($delStatusMap[$item['number']] ?? $item['status']);
                                $itemNa = !empty($delNaMap[$itemKey]) || !empty($delNaMap[$item['number']]);
                                $itemNote = $delNotesMap[$itemKey] ?? ($delNotesMap[$item['number']] ?? $item['default_note']);
                                if ($itemNa) $itemStatus = 'Tidak Berlaku';
                            @endphp
                            <tr class="hover:bg-slate-50 transition border-b border-black">
                                <td class="py-2 px-2 text-center font-bold border-r border-black align-top">{{ $item['number'] }}</td>
                                <td class="py-2 px-3 font-semibold text-black border-r border-black align-top">{{ $item['name'] }}</td>
                                <td class="py-2 px-3 border-r border-black align-top font-medium text-slate-800">{{ $itemStatus }}</td>
                                <td class="py-2 px-3 align-top text-slate-600">{{ $itemNote }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 3. CATATAN KHUSUS Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    CATATAN KHUSUS
                </div>
                <div class="p-3 bg-white text-slate-800 leading-relaxed min-h-[50px]">
                    {{ $project->notes ?: ($project->special_notes ?: '[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]') }}
                </div>
            </div>

            {{-- 4. STATUS HANDOVER Persis Scan PDF --}}
            <div class="border border-black overflow-hidden text-xs">
                <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                    STATUS HANDOVER
                </div>
                <table class="w-full border-collapse">
                                        <tbody>
                        <tr class="border-b border-black bg-white">
                            <td class="w-1/3 sm:w-1/4 py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Status handover:</td>
                            <td class="py-2.5 px-3 font-mono font-bold">
                                @php
                                    $delSelectedStatus = $delHandover['handover_status_selected'] ?? ($project->ms_handover_status === 'Accepted' ? 'approved' : 'approved');
                                @endphp
                                <span class="{{ $delSelectedStatus === 'approved' ? 'text-emerald-700' : 'text-slate-500' }}">
                                    [{{ $delSelectedStatus === 'approved' ? '✓' : ' ' }}] Diterima
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $delSelectedStatus === 'conditional' ? 'text-amber-700' : 'text-slate-500' }}">
                                    [{{ $delSelectedStatus === 'conditional' ? '✓' : ' ' }}] Diterima bersyarat
                                </span>
                                &nbsp;&nbsp;&nbsp;
                                <span class="{{ $delSelectedStatus === 'rejected' ? 'text-rose-700' : 'text-slate-500' }}">
                                    [{{ $delSelectedStatus === 'rejected' ? '✓' : ' ' }}] Ditolak / dikembalikan
                                </span>
                            </td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Penerimaan layanan</td>
                            <td class="py-2 px-3 text-slate-800">{{ $delHandover['service_acceptance'] ?? 'Layanan implementasi fisik dan konfigurasi jaringan telah siap dialihkan ke tim Managed Service.' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kekurangan / risiko dan PIC</td>
                            <td class="py-2 px-3 text-slate-800">{{ $delHandover['risks_and_pic'] ?? 'Tidak ada isu kritis / residual risiko telah dipetakan ke PIC terkait.' }}</td>
                        </tr>
                        <tr class="border-b border-black">
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal tindak lanjut</td>
                            <td class="py-2 px-3 text-slate-800">{{ $delHandover['follow_up_date'] ?? date('d/m/Y', strtotime('+7 days')) }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-bold bg-[#F8FAFC] border-r border-black">Konfirmasi penyerah dan penerima</td>
                            <td class="py-2 px-3 text-slate-800">{{ $delHandover['confirmation_notes'] ?? 'Penyerahan tanggung jawab operasional dari Project Manager kepada Managed Service.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- 5. ATURAN STATUS Persis Scan PDF --}}
            <div class="border border-black p-3 bg-slate-50 text-[11px] leading-relaxed">
                <div class="font-bold text-black uppercase mb-1">ATURAN STATUS</div>
                <p class="text-slate-700">
                    <strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. <strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. <strong>Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak.
                </p>
            </div>

            {{-- 6. TABEL TANDA TANGAN 4 KOLOM PERSIS SCAN PDF HAL 2 --}}
            <div class="border border-black overflow-hidden text-xs">
                <table class="w-full border-collapse text-xs">
                    <thead>
                        <tr class="bg-white border-b border-black text-center font-bold">
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Project Manager</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Diperiksa / diterima oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Managed Service</div>
                            </th>
                            <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Validasi teknis oleh</div>
                                <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                            <th class="py-2.5 px-3 w-1/4 align-top">
                                <div class="text-[11px] text-slate-600 font-medium">Persetujuan / eskalasi pihak berwenang</div>
                                <div class="text-[10px] font-normal text-slate-500">(bila perlu)</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="min-h-[160px]">
                            {{-- Col 1: Project Manager --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ $assignedPmName }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($pmQrSvg))
                                            <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $pmQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $assignedPmName }}</div>
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

                            {{-- Col 2: Managed Service --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-blue-900">{{ $project->msAcceptedBy?->name ?: 'Tim Managed Service' }}</span></p>
                                    <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                </div>
                                <div class="py-3 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                        @if(!empty($msQrSvg))
                                            <div class="w-11 h-11 shrink-0 p-0.5 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                {!! $msQrSvg !!}
                                            </div>
                                        @endif
                                        <div class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded min-w-[110px] max-w-[140px] min-h-[46px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-0.5 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[22px]">
                                                <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ $project->msAcceptedBy?->name ?: 'Managed Service Lead' }}</div>
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

                            {{-- Col 3: Validasi Teknis Sales / Commercial (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>

                            {{-- Col 4: Persetujuan / Eskalasi Pihak Berwenang (Bila Perlu) -> STRIP --}}
                            <td class="p-2.5 align-top flex-col justify-between">
                                <div class="space-y-0.5">
                                    <p><strong class="font-bold">Nama:</strong> -</p>
                                    <p><strong class="font-bold">Tanggal:</strong> -</p>
                                    <p><strong class="font-bold">Tanda tangan:</strong> -</p>
                                </div>
                                <div class="py-6 text-center text-slate-400 font-mono text-[11px]">
                                    (Tidak Diperlukan)
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Pengendalian Persis Scan PDF --}}
            <div class="text-[10.5px] text-slate-600 leading-snug pt-1">
                <strong class="text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
            </div>
        @endif

    </div>

    <script>
        function handleGoBack() {
            try {
                if (window.opener && !window.opener.closed) {
                    window.close();
                    return;
                }
            } catch (e) {}

            if (document.referrer && document.referrer !== window.location.href && document.referrer.indexOf(window.location.host) !== -1) {
                window.location.href = document.referrer;
            } else if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "{{ route('sales.handover.index') }}";
            }
        }
    </script>
</body>
</html>
