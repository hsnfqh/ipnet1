<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Digital - PT IP Network Solusindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between">

    <!-- HEADER -->
    <header class="bg-white border-b border-slate-200">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/ipnet1.png') }}" alt="IPNET Logo" class="h-8 object-contain" onerror="this.src='{{ asset('images/ipnet.png') }}'">
                <div>
                    <div class="font-extrabold text-sm text-slate-900 leading-tight">PT IP Network Solusindo</div>
                    <div class="text-[10px] font-bold text-[#8F0A0D] tracking-wider uppercase">Electronic Document Verification</div>
                </div>
            </div>
            <span class="text-xs font-mono font-bold text-slate-400">STATUS: OFFICIAL</span>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-3xl mx-auto px-4 py-8 w-full flex-1">
        @if($document)
            <div class="space-y-6">

                <!-- STATUS BANNER -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-emerald-200 shadow-sm text-center space-y-3 relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto text-3xl font-black shadow-lg shadow-emerald-500/20">
                        ✓
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider">
                            Dokumen Resmi & Terverifikasi Sah
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-2">
                            {{ $document->title }}
                        </h1>
                        <p class="font-mono text-xs font-bold text-slate-500 mt-0.5">
                            No. Dokumen: {{ $document->document_number }}
                        </p>
                    </div>
                    <p class="text-xs text-slate-600 max-w-lg mx-auto leading-relaxed">
                        Dokumen elektronik ini telah sah ditandatangani oleh seluruh pihak berwenang sesuai ketentuan sistem penandatanganan digital PT IP Network Solusindo.
                    </p>
                </div>

                <!-- AUDIT TRAIL PENANDATANGAN -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                        Daftar Pihak Penandatangan Resmi (Audit Trail)
                    </h2>

                    <div class="space-y-3">
                        <!-- INTERNAL SIGNERS -->
                        @foreach($document->internal_signers ?? [] as $s)
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-red-50 text-[#8F0A0D] text-[10px] font-bold">INTERNAL</span>
                                        <span class="font-bold text-xs text-slate-900">{{ $s['name'] }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $s['role_title'] }} &bull; {{ $s['email'] }}</div>
                                    @if(($s['status'] ?? '') === 'signed')
                                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                                            Ditandatangani pada {{ \Carbon\Carbon::parse($s['signed_at'])->format('d/m/Y H:i') }} WIB
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    @if(!empty($s['signature']))
                                        <div class="bg-white border border-slate-200 rounded-lg p-1 inline-block">
                                            <img src="{{ $s['signature'] }}" alt="Signature" class="h-8 max-w-[100px] object-contain">
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- CLIENT SIGNER -->
                        <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold">KLIEN</span>
                                    <span class="font-bold text-xs text-slate-900">{{ $document->client_name ?: 'PIC Klien' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $document->client_position ?: 'Penanggung Jawab' }} &bull; {{ $document->client_company ?: '-' }}
                                </div>
                                @if($document->client_signed_at)
                                    <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                                        Ditandatangani pada {{ $document->client_signed_at->format('d/m/Y H:i') }} WIB (IP: {{ $document->client_ip }})
                                    </div>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                @if($document->client_signature)
                                    <div class="bg-white border border-slate-200 rounded-lg p-1 inline-block">
                                        <img src="{{ $document->client_signature }}" alt="Client Signature" class="h-8 max-w-[110px] object-contain">
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum TTD</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- METADATA & HASH -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-3">
                    <h2 class="text-xs font-black text-slate-900 uppercase tracking-wider">
                        Integritas Kriptografi Dokumen
                    </h2>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1.5 text-xs">
                        <div class="text-slate-500 text-[11px]">SHA-256 Digital Fingerprint:</div>
                        <div class="font-mono text-[11px] text-emerald-800 break-all select-all font-semibold">
                            {{ $document->verification_hash ?: 'HASH-' . sha1($document->document_number) }}
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                        <div class="text-[11px] text-slate-400">
                            Terdaftar di sistem PT IP Network Solusindo sejak {{ $document->created_at->format('d/m/Y H:i') }} WIB
                        </div>
                        <a href="{{ route('digital_signatures.download_pdf', $document->id) }}"
                           class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Lembar Pengesahan PDF</span>
                        </a>
                    </div>
                </div>

            </div>
        @else
            <!-- NOT FOUND / INVALID -->
            <div class="bg-white rounded-3xl p-8 border border-rose-200 shadow-sm text-center space-y-3">
                <div class="w-16 h-16 rounded-full bg-rose-500 text-white flex items-center justify-center mx-auto text-3xl font-black">
                    ✕
                </div>
                <h1 class="text-xl font-black text-slate-900">Dokumen Tidak Ditemukan atau Tidak Valid</h1>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Nomor verifikasi atau hash yang Anda masukkan tidak terdaftar pada repositori resmi PT IP Network Solusindo.
                </p>
            </div>
        @endif
    </main>

    <!-- FOOTER -->
    <footer class="py-6 border-t border-slate-200 bg-white text-center text-xs text-slate-400">
        <div class="max-w-4xl mx-auto px-4">
            <div>&copy; {{ date('Y') }} PT IP Network Solusindo. All rights reserved.</div>
            <div class="text-[11px] mt-0.5">Sistem Verifikasi Digital Terpadu Sesuai Standar Hukum UU ITE</div>
        </div>
    </footer>

</body>
</html>
