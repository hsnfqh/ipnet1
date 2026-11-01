@extends('layouts.app')

@section('title', 'Settings - PT IP Network Solusindo')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.05s !important; }
    .anim-delay-2 { animation-delay: 0.1s !important; }

    /* Tab Navigasi: Aktif MERAH CRIMSON IPNET, BUKAN HITAM */
    .settings-tab-pill {
        padding: 9px 20px;
        border-radius: 12px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        color: #475569;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8.5px;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .settings-tab-pill:hover:not(.active) {
        background: #FEF2F2;
        border-color: #FECACA;
        color: #8F0A0D;
        transform: translateY(-1px);
    }
    .settings-tab-pill.active {
        background: #8F0A0D !important;
        border-color: #8F0A0D !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(143, 10, 13, 0.28) !important;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Settings'])

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1600px] w-full mx-auto" x-data="settingsManager()" x-init="init()">

            {{-- ══ SECTION 1: HEADER SECTION DENGAN TAB NAVIGASI MERAH ══ --}}
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> PENGATURAN SISTEM &amp; AKUN
                        </p>
                        <h2 class="text-[22px] font-bold text-[#1E293B] tracking-tight mt-0.5">Settings</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola informasi profil pengguna, proteksi keamanan kata sandi, dan tanda tangan digital terverifikasi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-2 shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[#64748B]">Status Akun:</span>
                            <span class="text-emerald-700 font-extrabold">{{ $user->status ?? 'Active' }}</span>
                        </div>

                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-2 shadow-xs">
                            <span class="text-[#64748B]">Role Akses:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $user->role_label }}</span>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigasi: Urutan Baru (Profil -> Keamanan -> TTD), Aktif Merah Crimson --}}
                <div class="flex flex-wrap items-center gap-2.5 pt-1">
                    {{-- Tab 1: Profil Pengguna --}}
                    <button type="button"
                            @click="activeTab = 'profile'"
                            class="settings-tab-pill"
                            :class="activeTab === 'profile' ? 'active' : ''">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profil Pengguna</span>
                    </button>

                    {{-- Tab 2: Keamanan & Kata Sandi --}}
                    <button type="button"
                            @click="activeTab = 'security'"
                            class="settings-tab-pill"
                            :class="activeTab === 'security' ? 'active' : ''">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Keamanan &amp; Kata Sandi</span>
                    </button>

                    {{-- Tab 3: Tanda Tangan Digital --}}
                    <button type="button"
                            @click="activeTab = 'signature'; $nextTick(() => initCanvas());"
                            class="settings-tab-pill"
                            :class="activeTab === 'signature' ? 'active' : ''">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        <span>Tanda Tangan Digital</span>
                        @if($user->has_signature)
                        <span class="w-2 h-2 rounded-full" :class="activeTab === 'signature' ? 'bg-white' : 'bg-emerald-500'"></span>
                        @endif
                    </button>
                </div>
            </div>

            {{-- ══ NOTIFIKASI SUKSES / ERROR ══ --}}
            @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-semibold flex items-center gap-3 shadow-xs anim-fade-up">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-[#8F0A0D] text-[13px] font-semibold flex items-center gap-3 shadow-xs anim-fade-up">
                <svg class="w-5 h-5 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 text-[#8F0A0D] text-[13px] anim-fade-up">
                <ul class="list-disc pl-5 space-y-1 font-semibold">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ══ SECTION 2: 3 SUMMARY CARDS RAPI & SEIMBANG (FULL WIDTH) ══ --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 anim-fade-up anim-delay-1">
                {{-- Card 1: Profil Pengguna --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Identitas Akun</p>
                        <h3 class="text-[17px] font-bold text-[#1E293B] mt-1 truncate">{{ $user->name }}</h3>
                        <p class="text-[12px] text-[#94A3B8] mt-0.5 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] flex items-center justify-center text-[#8F0A0D] shadow-xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 2: Keamanan Akun --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Keamanan &amp; Akses</p>
                        <h3 class="text-[17px] font-bold text-[#2563EB] mt-1">Terproteksi</h3>
                        <p class="text-[12px] text-[#94A3B8] mt-0.5">Enkripsi Kata Sandi Bcrypt Aktif</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-[#EFF6FF] border border-[#BFDBFE] flex items-center justify-center text-[#2563EB] shadow-xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 3: Tanda Tangan Digital --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Tanda Tangan Digital</p>
                        <h3 class="text-[17px] font-bold mt-1 {{ $user->has_signature ? 'text-[#16A34A]' : 'text-[#D97706]' }}">
                            {{ $user->has_signature ? 'Tersimpan' : 'Belum Diatur' }}
                        </h3>
                        <p class="text-[12px] text-[#94A3B8] mt-0.5 truncate">
                            {{ $user->has_signature ? 'Siap digunakan pada BA / SPK' : 'Diperlukan untuk stempel otomatis' }}
                        </p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl {{ $user->has_signature ? 'bg-[#F0FDF4] border-[#BBF7D0] text-[#16A34A]' : 'bg-[#FFFBEB] border-[#FDE68A] text-[#D97706]' }} border flex items-center justify-center shadow-xs shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ══ SECTION 3: KONTEN TAB (FULL SIZE, RAPI, PROPORSIONAL) ══ --}}

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- TAB 1: PROFIL PENGGUNA (FULL WIDTH 2-KOLOM BERSIH)       --}}
            {{-- ───────────────────────────────────────────────────────── --}}
            <div x-show="activeTab === 'profile'" class="space-y-6 anim-fade-up anim-delay-2">
                <div class="ipnet-card p-6 sm:p-8 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-[#E2E8F0]">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="ipnet-badge-dot"></span>
                                <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">INFORMASI PROFIL AKUN</span>
                            </div>
                            <h3 class="text-[19px] font-bold text-[#1E293B] tracking-tight">Data Profil Pengguna</h3>
                            <p class="text-[13px] text-[#64748B] mt-0.5">Kelola data identitas dan informasi kontak yang terdaftar pada sistem PT IP Network Solusindo.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        {{-- Kartu Ringkasan Akun (Kolom Kiri 5 Span) --}}
                        <div class="lg:col-span-5 space-y-5">
                            <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-5 text-center sm:text-left flex flex-col sm:flex-row items-center sm:items-start gap-5">
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#8F0A0D] to-[#B91C1C] text-white text-2xl font-extrabold flex items-center justify-center shrink-0 shadow-md">
                                    {{ $user->initials }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-xl font-bold text-[#1E293B]">{{ $user->name }}</h4>
                                    <p class="text-[13px] text-[#64748B] font-medium mt-0.5">{{ $user->position ?? $user->role_label }}</p>
                                    <p class="text-[12px] text-[#94A3B8]">PT IP Network Solusindo</p>
                                    <div class="flex flex-wrap items-center gap-2 mt-3 justify-center sm:justify-start">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#8F0A0D]/10 text-[#8F0A0D] uppercase tracking-wider border border-[#8F0A0D]/20">
                                            {{ $user->role_label }}
                                        </span>
                                        @if($user->division)
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $user->division->name }}
                                        </span>
                                        @endif
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $user->status ?? 'Active' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 rounded-2xl bg-white border border-[#E2E8F0] space-y-3">
                                <span class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider block">Catatan Akun Sistem</span>
                                <div class="text-[12px] text-[#475569] space-y-2 leading-relaxed">
                                    <p class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Alamat email resmi digunakan sebagai pengenal otentikasi login tunggal.</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Perubahan divisi organisasi dan hak akses dikelola oleh Administrator Sistem.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Formulir Data Profil (Kolom Kanan 7 Span - Full Size) --}}
                        <div class="lg:col-span-7">
                            <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                        Nama Lengkap <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                           class="w-full px-4 py-3 rounded-xl border border-[#CBD5E1] text-[13.5px] font-semibold text-[#1E293B] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none transition">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                            Alamat Email (Login)
                                        </label>
                                        <div class="relative">
                                            <input type="email" value="{{ $user->email }}" readonly
                                                   class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] text-[13.5px] font-semibold text-[#64748B] cursor-not-allowed outline-none">
                                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                            Nomor WhatsApp / Telepon
                                        </label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                                               class="w-full px-4 py-3 rounded-xl border border-[#CBD5E1] text-[13.5px] font-semibold text-[#1E293B] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none transition">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[12px] font-bold text-[#64748B] uppercase tracking-wider mb-1.5">
                                            Jabatan / Posisi
                                        </label>
                                        <input type="text" value="{{ $user->position ?: ($user->role_label) }}" readonly
                                               class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] text-[13px] font-semibold text-[#64748B] cursor-not-allowed outline-none">
                                    </div>

                                    <div>
                                        <label class="block text-[12px] font-bold text-[#64748B] uppercase tracking-wider mb-1.5">
                                            Divisi Kerja
                                        </label>
                                        <input type="text" value="{{ $user->division ? $user->division->name : 'Operasional & Proyek' }}" readonly
                                               class="w-full px-4 py-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] text-[13px] font-semibold text-[#64748B] cursor-not-allowed outline-none">
                                    </div>
                                </div>

                                <div class="pt-3">
                                    <button type="submit"
                                            class="btn-ipnet-gradient w-full sm:w-auto px-7 py-3 rounded-xl font-bold text-[13px] shadow-md flex items-center justify-center gap-2 cursor-pointer transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Simpan Perubahan Profil</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- TAB 2: KEAMANAN & KATA SANDI (FULL SIZE 2-KOLOM PROPORSIONAL) --}}
            {{-- ───────────────────────────────────────────────────────── --}}
            <div x-show="activeTab === 'security'" x-cloak class="space-y-6 anim-fade-up anim-delay-2">
                <div class="ipnet-card p-6 sm:p-8 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-[#E2E8F0]">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="ipnet-badge-dot"></span>
                                <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">PROTEKSI AKUN SISTEM</span>
                            </div>
                            <h3 class="text-[19px] font-bold text-[#1E293B] tracking-tight">Keamanan &amp; Kata Sandi</h3>
                            <p class="text-[13px] text-[#64748B] mt-0.5">Perbarui kata sandi secara berkala untuk menjaga keamanan akses akun Anda di sistem IP Network Solusindo.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                        {{-- Formulir Ubah Kata Sandi (Kolom Kiri 7 Span) --}}
                        <div class="lg:col-span-7">
                            <form action="{{ route('settings.password') }}" method="POST" class="space-y-5">
                                @csrf

                                {{-- Password Saat Ini --}}
                                <div>
                                    <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                        Password Saat Ini <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showCurrent ? 'text' : 'password'" name="current_password" required placeholder="Masukkan kata sandi saat ini"
                                               class="w-full px-4 py-3 pr-11 rounded-xl border border-[#CBD5E1] text-[13.5px] font-semibold text-[#1E293B] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none transition">
                                        <button type="button" @click="showCurrent = !showCurrent"
                                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                            <svg x-show="!showCurrent" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showCurrent" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Password Baru --}}
                                <div>
                                    <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                        Password Baru <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showNew ? 'text' : 'password'" name="password" required placeholder="Minimal 8 karakter"
                                               class="w-full px-4 py-3 pr-11 rounded-xl border border-[#CBD5E1] text-[13.5px] font-semibold text-[#1E293B] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none transition">
                                        <button type="button" @click="showNew = !showNew"
                                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                            <svg x-show="!showNew" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showNew" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Konfirmasi Password Baru --}}
                                <div>
                                    <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                        Konfirmasi Password Baru <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                                               class="w-full px-4 py-3 pr-11 rounded-xl border border-[#CBD5E1] text-[13.5px] font-semibold text-[#1E293B] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none transition">
                                        <button type="button" @click="showConfirm = !showConfirm"
                                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer">
                                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                            class="btn-ipnet-gradient w-full sm:w-auto px-7 py-3 rounded-xl font-bold text-[13px] shadow-md flex items-center justify-center gap-2 cursor-pointer transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span>Simpan Password Baru</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Panduan Keamanan (Kolom Kanan 5 Span) --}}
                        <div class="lg:col-span-5 space-y-4">
                            <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4">
                                <div class="flex items-center gap-2.5 pb-3 border-b border-[#E2E8F0]">
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <h4 class="text-[13.5px] font-bold text-[#1E293B]">Standar Keamanan Kata Sandi</h4>
                                </div>

                                <div class="space-y-3 text-[12.5px] text-[#475569]">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5">1</span>
                                        <span>Minimal <strong>8 karakter</strong> (kombinasi huruf besar, kecil, angka, dan simbol).</span>
                                    </div>
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5">2</span>
                                        <span>Hindari menggunakan tanggal lahir atau informasi pribadi yang mudah ditebak.</span>
                                    </div>
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5">3</span>
                                        <span>Jangan pernah membagikan password akun Anda kepada siapapun.</span>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-[#E2E8F0] text-[11.5px] text-[#64748B] flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Tersimpan dengan enkripsi Bcrypt hashing standar industri.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ───────────────────────────────────────────────────────── --}}
            {{-- TAB 3: TANDA TANGAN DIGITAL (FULL SIZE 2-KOLOM RAPI)      --}}
            {{-- ───────────────────────────────────────────────────────── --}}
            <div x-show="activeTab === 'signature'" x-cloak class="space-y-6 anim-fade-up anim-delay-2">
                <div class="ipnet-card p-6 sm:p-8 w-full">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 mb-6 border-b border-[#E2E8F0]">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="ipnet-badge-dot"></span>
                                <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">KONFIGURASI TANDA TANGAN DIGITAL</span>
                            </div>
                            <h3 class="text-[19px] font-bold text-[#1E293B] tracking-tight">Tanda Tangan &amp; Barcode Verifikasi</h3>
                            <p class="text-[13px] text-[#64748B] mt-0.5">Tanda tangan yang tersimpan di sini akan otomatis disematkan pada dokumen resmi tanpa perlu menggambar ulang.</p>
                        </div>

                        @if($user->has_signature)
                        <form action="{{ route('settings.signature.delete') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanda tangan tersimpan?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-[12.5px] font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition cursor-pointer flex items-center gap-1.5 border border-red-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus Tanda Tangan</span>
                            </button>
                        </form>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        {{-- Area Input & Kanvas (Kolom Kiri 7 Span) --}}
                        <div class="lg:col-span-7 space-y-5">
                            
                            {{-- Format Stempel --}}
                            <div>
                                <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-2">Pilihan Format Stempel Dokumen</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center gap-3"
                                           :class="signatureType === 'signature_qr' ? 'bg-[#FEF2F2] border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#CBD5E1] text-[#334155]'">
                                        <input type="radio" name="signature_type_choice" value="signature_qr" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                        <div class="min-w-0">
                                            <div class="text-[12.5px] font-bold">TTD + QR Barcode</div>
                                            <div class="text-[10.5px] text-[#64748B]">Standar Resmi</div>
                                        </div>
                                    </label>

                                    <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center gap-3"
                                           :class="signatureType === 'signature_only' ? 'bg-[#FEF2F2] border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#CBD5E1] text-[#334155]'">
                                        <input type="radio" name="signature_type_choice" value="signature_only" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                        <div class="min-w-0">
                                            <div class="text-[12.5px] font-bold">Tanda Tangan Saja</div>
                                            <div class="text-[10.5px] text-[#64748B]">Goresan Grafis</div>
                                        </div>
                                    </label>

                                    <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center gap-3"
                                           :class="signatureType === 'qr_only' ? 'bg-[#FEF2F2] border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#CBD5E1] text-[#334155]'">
                                        <input type="radio" name="signature_type_choice" value="qr_only" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                        <div class="min-w-0">
                                            <div class="text-[12.5px] font-bold">QR Barcode Saja</div>
                                            <div class="text-[10.5px] text-[#64748B]">Token Verifikasi</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Pilihan Tab Metode Coret / Unggah --}}
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider">Metode Masukan Tanda Tangan</label>
                                    <div class="inline-flex rounded-xl bg-[#F1F5F9] p-1 border border-[#E2E8F0]">
                                        <button type="button" @click="signMethod = 'draw'"
                                                class="px-3.5 py-1.5 text-[12px] font-bold rounded-lg transition cursor-pointer flex items-center gap-1.5"
                                                :class="signMethod === 'draw' ? 'bg-white text-[#8F0A0D] shadow-xs' : 'text-[#64748B] hover:text-[#0F172A]'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            <span>Gambar di Kanvas</span>
                                        </button>
                                        <button type="button" @click="signMethod = 'upload'"
                                                class="px-3.5 py-1.5 text-[12px] font-bold rounded-lg transition cursor-pointer flex items-center gap-1.5"
                                                :class="signMethod === 'upload' ? 'bg-white text-[#8F0A0D] shadow-xs' : 'text-[#64748B] hover:text-[#0F172A]'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <span>Unggah Berkas Gambar</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Kanvas Interaktif --}}
                                <div x-show="signMethod === 'draw'" class="space-y-2.5">
                                    <div class="relative bg-[#F8FAFC] border-2 border-dashed border-[#CBD5E1] rounded-2xl overflow-hidden shadow-inner flex items-center justify-center">
                                        <canvas id="settings-signature-canvas" width="600" height="200" class="w-full h-48 cursor-crosshair touch-none bg-transparent"></canvas>
                                        
                                        <div class="absolute bottom-5 left-10 right-10 border-b border-gray-300 pointer-events-none text-center">
                                            <span class="text-[9.5px] text-gray-400 font-bold tracking-widest uppercase">Garis Penandatanganan Resmi</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1">
                                        <button type="button" @click="clearCanvas()"
                                                class="px-3.5 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Bersihkan Kanvas
                                        </button>
                                        <span class="text-[11.5px] text-gray-400 font-medium">Sentuhan jari, stylus, atau kursor mouse</span>
                                    </div>
                                </div>

                                {{-- Unggah File Gambar --}}
                                <div x-show="signMethod === 'upload'" x-cloak class="space-y-2.5">
                                    <label class="border-2 border-dashed border-[#CBD5E1] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-red-50/20 rounded-2xl p-7 text-center block cursor-pointer transition">
                                        <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="handleFileUpload($event)">
                                        <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        </div>
                                        <p class="text-[13px] font-bold text-[#1E293B]">Pilih berkas tanda tangan dari perangkat</p>
                                        <p class="text-[11.5px] text-[#64748B] mt-1">Format PNG transparan sangat direkomendasikan (Maks. 3MB)</p>
                                    </label>
                                </div>
                            </div>

                            {{-- Tombol Simpan TTD --}}
                            <div class="pt-2">
                                <button type="button" @click="saveSignatureSettings()" :disabled="isSavingSignature"
                                        class="btn-ipnet-gradient w-full sm:w-auto px-7 py-3 rounded-xl font-bold text-[13px] shadow-md flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 transition">
                                    <svg x-show="isSavingSignature" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="isSavingSignature ? 'Menyimpan...' : 'Simpan Tanda Tangan & QR'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Pratinjau Stempel Resmi Dokumen (Kolom Kanan 5 Span) --}}
                        <div class="lg:col-span-5">
                            <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#CBD5E1] space-y-4">
                                <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                                    <span class="text-[11.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">PRATINJAU RESMI DOKUMEN</span>
                                    <span class="text-[11px] text-[#64748B] font-semibold">Tampilan di BA &amp; SPK</span>
                                </div>

                                {{-- Kartu Stempel Dokumen --}}
                                <div class="bg-white rounded-xl border border-slate-300 p-5 shadow-xs">
                                    <div class="flex items-center justify-center gap-4">
                                        
                                        {{-- Goresan TTD --}}
                                        <div class="flex-1 flex flex-col items-center justify-center text-center min-h-[95px] border-r border-dashed border-slate-200 pr-3"
                                             x-show="signatureType !== 'qr_only'">
                                            <template x-if="previewSignatureData">
                                                <img :src="previewSignatureData" alt="Tanda Tangan" class="max-h-20 max-w-full object-contain filter drop-shadow-xs">
                                            </template>
                                            <template x-if="!previewSignatureData">
                                                <div class="text-[11.5px] text-slate-400 font-medium italic py-5">
                                                    Belum ada goresan tanda tangan
                                                </div>
                                            </template>
                                            <div class="text-[11px] font-bold text-[#0F172A] mt-1 uppercase">{{ $user->name }}</div>
                                            <div class="text-[9.5px] text-slate-500 font-medium">{{ $user->position ?? $user->role_label }}</div>
                                        </div>

                                        {{-- QR Barcode --}}
                                        <div class="flex flex-col items-center justify-center text-center shrink-0 pl-1"
                                             x-show="signatureType !== 'signature_only'">
                                            <div class="w-18 h-18 p-1.5 bg-white border border-slate-200 rounded-lg shadow-2xs flex items-center justify-center">
                                                {!! $qrSvg !!}
                                            </div>
                                            <span class="text-[9px] font-extrabold text-[#8F0A0D] tracking-wider mt-1 uppercase">VERIFIED</span>
                                            <span class="text-[8px] text-slate-400 font-mono">{{ $user->signature_qr_token }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                                        <span>PT IP Network Solusindo</span>
                                        <span>Token: <strong class="text-slate-600 font-mono">{{ $user->signature_qr_token }}</strong></span>
                                    </div>
                                </div>

                                <div class="text-[11.5px] text-[#64748B] space-y-1.5 pt-1">
                                    <p class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Diverifikasi secara kriptografis oleh Field System Management.</span>
                                    </p>
                                    <p class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Dapat dipindai langsung menggunakan kamera smartphone.</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function settingsManager() {
    return {
        activeTab: 'profile',
        signatureType: '{{ $user->signature_type ?: "signature_qr" }}',
        signMethod: 'draw',
        previewSignatureData: '{!! addslashes($user->signature_data ?: "") !!}',
        isSavingSignature: false,

        // Password visibility toggles
        showCurrent: false,
        showNew: false,
        showConfirm: false,

        // Canvas state
        canvas: null,
        ctx: null,
        isDrawing: false,

        init() {
            // Jika ada query param atau anchor
            const hash = window.location.hash.replace('#', '');
            if (['profile', 'security', 'signature'].includes(hash)) {
                this.activeTab = hash;
            }
            if (this.activeTab === 'signature') {
                this.$nextTick(() => {
                    this.initCanvas();
                });
            }
        },

        initCanvas() {
            const canvas = document.getElementById('settings-signature-canvas');
            if (!canvas) return;
            this.canvas = canvas;
            this.ctx = canvas.getContext('2d');
            this.ctx.strokeStyle = '#0F172A';
            this.ctx.lineWidth = 2.8;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';

            const getPos = (e) => {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                if (e.touches && e.touches.length > 0) {
                    return {
                        x: (e.touches[0].clientX - rect.left) * scaleX,
                        y: (e.touches[0].clientY - rect.top) * scaleY
                    };
                }
                return {
                    x: (e.clientX - rect.left) * scaleX,
                    y: (e.clientY - rect.top) * scaleY
                };
            };

            const startDraw = (e) => {
                e.preventDefault();
                this.isDrawing = true;
                const pos = getPos(e);
                this.ctx.beginPath();
                this.ctx.moveTo(pos.x, pos.y);
            };

            const draw = (e) => {
                if (!this.isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                this.ctx.lineTo(pos.x, pos.y);
                this.ctx.stroke();
                this.previewSignatureData = canvas.toDataURL('image/png');
            };

            const stopDraw = (e) => {
                if (this.isDrawing) {
                    e.preventDefault();
                    this.ctx.closePath();
                    this.isDrawing = false;
                    this.previewSignatureData = canvas.toDataURL('image/png');
                }
            };

            canvas.onmousedown = startDraw;
            canvas.onmousemove = draw;
            window.addEventListener('mouseup', stopDraw);

            canvas.ontouchstart = startDraw;
            canvas.ontouchmove = draw;
            canvas.ontouchend = stopDraw;
        },

        clearCanvas() {
            if (this.canvas && this.ctx) {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.previewSignatureData = '{!! addslashes($user->signature_data ?: "") !!}';
            }
        },

        handleFileUpload(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (ev) => {
                this.previewSignatureData = ev.target.result;
            };
            reader.readAsDataURL(file);
        },

        async saveSignatureSettings() {
            if (!this.previewSignatureData) {
                alert('Silakan gambar tanda tangan Anda pada kanvas atau pilih berkas gambar tanda tangan.');
                return;
            }

            this.isSavingSignature = true;
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            try {
                const res = await fetch('{{ route("settings.signature") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        signature_data: this.previewSignatureData,
                        signature_type: this.signatureType
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert('Tanda tangan digital berhasil disimpan!');
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan: ' + (data.message || data.error || 'Terjadi kesalahan sistem.'));
                }
            } catch (err) {
                alert('Terjadi kesalahan: ' + err.message);
            } finally {
                this.isSavingSignature = false;
            }
        }
    };
}
</script>
@endpush

@endsection
