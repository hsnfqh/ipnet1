<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Tanda Tangan Digital – PT IP Network Solusindo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #F8FAFC; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .btn-ipnet-gradient {
            background: linear-gradient(135deg, #8F0A0D 0%, #B91C1C 100%);
            color: #FFFFFF;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-[#0F172A] p-4 sm:p-6">

    <div class="max-w-xl w-full mx-auto my-auto pt-6 pb-10">

        {{-- Header Logo & Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-white border border-[#E2E8F0] shadow-xs mb-4">
                <span class="w-3 h-3 rounded-full bg-[#8F0A0D]"></span>
                <span class="text-xs font-black tracking-wider text-[#0F172A] uppercase">PT IP NETWORK SOLUSINDO</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight">Portal Verifikasi Tanda Tangan Digital</h1>
            <p class="text-xs sm:text-[13px] text-[#64748B] mt-1">Sistem Otentikasi Integritas Dokumen &amp; Penandatangan Resmi</p>
        </div>

        @if($user)
        {{-- Card Valid --}}
        <div class="bg-white rounded-3xl border border-[#E2E8F0] p-6 sm:p-8 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

            {{-- Status Pill --}}
            <div class="flex items-center justify-center mb-6">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>TANDA TANGAN SAH &amp; TERVERIFIKASI RESMI</span>
                </div>
            </div>

            {{-- Signer Info --}}
            <div class="text-center pb-6 border-b border-[#F1F5F9]">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#8F0A0D] to-[#B91C1C] text-white text-2xl font-black flex items-center justify-center mx-auto mb-3 shadow-lg shadow-red-900/20">
                    {{ $user->initials }}
                </div>
                <h2 class="text-xl font-black text-[#0F172A]">{{ $user->name }}</h2>
                <p class="text-xs font-bold text-[#8F0A0D] mt-0.5">{{ $user->position ?? $user->role_label }}</p>
                <p class="text-[11px] text-[#64748B] font-semibold mt-0.5">PT IP Network Solusindo • {{ $user->division?->name ?? 'Field Operations' }}</p>
            </div>

            {{-- Signature & QR Stamp Preview --}}
            <div class="my-6 p-4 rounded-2xl bg-[#F8FAFC] border border-[#EEF2F6]">
                <div class="text-center text-[10px] font-extrabold text-[#64748B] uppercase tracking-wider mb-3">
                    STEMPEL OTENTIKASI RESMI
                </div>

                <div class="flex items-center justify-center gap-6">
                    @if($user->signature_data)
                    <div class="flex flex-col items-center">
                        <img src="{{ $user->signature_data }}" alt="Tanda Tangan" class="max-h-20 max-w-[140px] object-contain filter drop-shadow-xs">
                        <span class="text-[9px] text-[#64748B] font-bold mt-1">Goresan Digital</span>
                    </div>
                    @endif

                    <div class="flex flex-col items-center pl-4 border-l border-gray-200">
                        <div class="w-16 h-16 p-1 bg-white border border-gray-200 rounded-lg shadow-2xs flex items-center justify-center">
                            {!! $user->signature_qr_svg !!}
                        </div>
                        <span class="text-[8.5px] font-black text-[#8F0A0D] tracking-wider mt-1 uppercase">VERIFIED QR</span>
                    </div>
                </div>
            </div>

            {{-- Metadata Table --}}
            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                    <span class="text-[#64748B] font-medium">Token Integritas:</span>
                    <span class="font-mono font-bold text-[#0F172A]">{{ $token }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                    <span class="text-[#64748B] font-medium">Status Akun:</span>
                    <span class="font-bold text-emerald-700">Pegawai Aktif / Terdaftar</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                    <span class="text-[#64748B] font-medium">Tanggal Diperbarui:</span>
                    <span class="font-bold text-[#0F172A]">{{ $user->signature_updated_at ? $user->signature_updated_at->format('d M Y, H:i') . ' WIB' : '-' }}</span>
                </div>
            </div>

            {{-- Footer Security Note --}}
            <div class="mt-6 pt-4 border-t border-[#F1F5F9] text-center">
                <p class="text-[10.5px] text-[#94A3B8] leading-relaxed">
                    Stempel tanda tangan ini diterbitkan dan diverifikasi secara kriptografis oleh Field System Management PT IP Network Solusindo. Segala bentuk pemalsuan dapat ditelusuri melalui log audit sistem.
                </p>
            </div>
        </div>
        @else
        {{-- Card Invalid --}}
        <div class="bg-white rounded-3xl border border-red-200 p-8 shadow-xl text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h2 class="text-xl font-black text-red-900 mb-2">Tanda Tangan Tidak Ditemukan</h2>
            <p class="text-xs text-gray-600 max-w-sm mx-auto mb-6">
                Token tanda tangan digital (<span class="font-mono font-bold text-red-600">{{ $token }}</span>) tidak terdaftar atau telah kedaluwarsa dalam sistem IP Network Solusindo.
            </p>
            <a href="{{ route('login') }}" class="btn-ipnet-gradient inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold shadow-md">
                Kembali ke Portal Login
            </a>
        </div>
        @endif

    </div>

    {{-- Footer --}}
    <footer class="text-center py-4 text-[11px] text-[#94A3B8]">
        &copy; {{ date('Y') }} PT IP Network Solusindo. All rights reserved.
    </footer>

</body>
</html>

