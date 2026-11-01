@extends('layouts.app')

@section('title', 'Pengaturan Akun & Tanda Tangan')

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Pengaturan'])

        <div class="p-4 sm:p-7 max-w-7xl mx-auto space-y-6" x-data="settingsManager()">

            {{-- ══ HEADER SECTION ══ --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#8F0A0D] to-[#B91C1C] flex items-center justify-center text-white shadow-lg shadow-red-900/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span class="text-[11px] font-bold text-[#8F0A0D] tracking-wider uppercase">PENGATURAN SISTEM</span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight">Pengaturan Akun & Tanda Tangan</h1>
                        <p class="text-xs sm:text-[13px] text-[#64748B] mt-0.5">Kelola data profil, keamanan akun, tanda tangan digital tersimpan, dan preferensi aplikasi Anda.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Akun Aktif • PT IP Network Solusindo</span>
                    </span>
                </div>
            </div>

            {{-- ══ NOTIFICATION ALERTS ══ --}}
            @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-[13px] font-semibold flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-[#8F0A0D] text-xs sm:text-[13px] font-semibold flex items-center gap-2.5 shadow-xs">
                <svg class="w-5 h-5 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-[#8F0A0D] text-xs sm:text-[13px]">
                <ul class="list-disc pl-5 space-y-1 font-semibold">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ══ MAIN 2-COLUMN GRID ══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- ══════════════════════════════════════════════════════════════
                     KOLOM KIRI (7 SPAN): PROFIL & TANDA TANGAN DIGITAL
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- ── KARTU 1: PROFIL PENGGUNA ── --}}
                    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-[#F1F5F9] mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-[15px] font-bold text-[#0F172A]">Profil Pengguna</h2>
                                    <p class="text-[11.5px] text-[#64748B]">Kelola data identitas dan informasi kontak akun Anda.</p>
                                </div>
                            </div>
                            <button type="button" @click="isEditingProfile = !isEditingProfile"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold border border-[#CBD5E1] text-[#334155] hover:text-[#8F0A0D] hover:bg-red-50 hover:border-red-200 transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span x-text="isEditingProfile ? 'Batal' : 'Ubah Profil'"></span>
                            </button>
                        </div>

                        {{-- Mode Tampilan Profil --}}
                        <div x-show="!isEditingProfile">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pb-5 mb-5 border-b border-[#F1F5F9]">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#8F0A0D] to-[#B91C1C] text-white text-xl font-black flex items-center justify-center shrink-0 shadow-md">
                                    {{ $user->initials }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-black text-[#0F172A] truncate">{{ $user->name }}</h3>
                                    <p class="text-xs font-semibold text-[#64748B]">{{ $user->position ?? $user->role_label }}</p>
                                    <p class="text-[11px] text-[#94A3B8] mt-0.5 font-medium">PT IP Network Solusindo</p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-[#F1F5F9] text-[#334155] uppercase tracking-wider">
                                            {{ $user->role_label }}
                                        </span>
                                        @if($user->division)
                                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $user->division->name }}
                                        </span>
                                        @endif
                                        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $user->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                                    <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block mb-1">Alamat Email</span>
                                    <span class="font-bold text-[#0F172A] break-all">{{ $user->email }}</span>
                                </div>
                                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                                    <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block mb-1">No. WhatsApp / Telepon</span>
                                    <span class="font-bold text-[#0F172A]">{{ $user->phone ?: '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Form Edit Profil (Inline) --}}
                        <form x-show="isEditingProfile" x-cloak action="{{ route('settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">No. WhatsApp / Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none">
                            </div>

                            <div class="pt-2 flex justify-end gap-2">
                                <button type="button" @click="isEditingProfile = false"
                                        class="px-4 py-2 text-xs font-bold text-[#64748B] bg-white border border-[#CBD5E1] rounded-xl hover:bg-[#F8FAFC] cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="btn-ipnet-gradient px-5 py-2 text-xs font-bold rounded-xl shadow-md cursor-pointer">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ── KARTU 2: TANDA TANGAN DIGITAL TERSIMPAN (UTAMA) ── --}}
                    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-xs relative overflow-hidden">
                        {{-- Accent Bar --}}
                        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-[#8F0A0D] via-[#B91C1C] to-red-400"></div>

                        <div class="flex items-center justify-between pb-4 border-b border-[#F1F5F9] mb-5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-[15px] font-bold text-[#0F172A]">Tanda Tangan Digital Saya</h2>
                                        @if($user->has_signature)
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✓ TERSIMPAN &amp; AKTIF
                                        </span>
                                        @else
                                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                            BELUM DIATUR
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-[11.5px] text-[#64748B]">Tanda tangan ini akan otomatis ditempelkan saat Anda menekan tombol "Tandatangani" pada dokumen.</p>
                                </div>
                            </div>

                            @if($user->has_signature)
                            <form action="{{ route('settings.signature.delete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tanda tangan tersimpan? Anda perlu menggambar atau mengunggah ulang nantinya.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition cursor-pointer flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus TTD</span>
                                </button>
                            </form>
                            @endif
                        </div>

                        {{-- Pilihan Format Stempel --}}
                        <div class="mb-5">
                            <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-2">Tampilan Format Stempel Dokumen</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                       :class="signatureType === 'signature_qr' ? 'bg-red-50/50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#334155]'">
                                    <input type="radio" name="signature_type_choice" value="signature_qr" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold">TTD + QR Barcode</div>
                                        <div class="text-[10px] opacity-75">Rekomendasi Resmi</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                       :class="signatureType === 'signature_only' ? 'bg-red-50/50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#334155]'">
                                    <input type="radio" name="signature_type_choice" value="signature_only" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold">Tanda Tangan Saja</div>
                                        <div class="text-[10px] opacity-75">Goresan Digital</div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                       :class="signatureType === 'qr_only' ? 'bg-red-50/50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#334155]'">
                                    <input type="radio" name="signature_type_choice" value="qr_only" x-model="signatureType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold">QR Barcode Saja</div>
                                        <div class="text-[10px] opacity-75">E-Sign Token</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Mode Input TTD: Canvas vs Upload File --}}
                        <div class="mb-5">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider">Metode Input Tanda Tangan</label>
                                <div class="flex rounded-lg bg-[#F1F5F9] p-0.5 border border-[#E2E8F0]">
                                    <button type="button" @click="signMethod = 'draw'"
                                            class="px-2.5 py-1 text-[11px] font-bold rounded-md transition cursor-pointer"
                                            :class="signMethod === 'draw' ? 'bg-white text-[#8F0A0D] shadow-xs' : 'text-[#64748B] hover:text-[#0F172A]'">
                                        ✍️ Coret di Kanvas
                                    </button>
                                    <button type="button" @click="signMethod = 'upload'"
                                            class="px-2.5 py-1 text-[11px] font-bold rounded-md transition cursor-pointer"
                                            :class="signMethod === 'upload' ? 'bg-white text-[#8F0A0D] shadow-xs' : 'text-[#64748B] hover:text-[#0F172A]'">
                                        📁 Unggah Gambar (PNG)
                                    </button>
                                </div>
                            </div>

                            {{-- Area 1: Coret di Kanvas --}}
                            <div x-show="signMethod === 'draw'" class="space-y-2">
                                <div class="relative bg-[#F8FAFC] border-2 border-dashed border-[#CBD5E1] rounded-2xl overflow-hidden shadow-inner flex items-center justify-center">
                                    <canvas id="settings-signature-canvas" width="560" height="190" class="w-full h-48 cursor-crosshair touch-none bg-transparent"></canvas>
                                    
                                    <div class="absolute bottom-5 left-10 right-10 border-b border-gray-300 pointer-events-none text-center">
                                        <span class="text-[9.5px] text-gray-400 font-bold tracking-widest uppercase">Garis Penandatanganan Resmi</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-1">
                                    <button type="button" @click="clearCanvas()"
                                            class="px-3.5 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Hapus &amp; Gambar Ulang
                                    </button>
                                    <span class="text-[11px] text-gray-400 font-medium italic">Gunakan mouse atau sentuhan stylus / jari Anda</span>
                                </div>
                            </div>

                            {{-- Area 2: Unggah Berkas --}}
                            <div x-show="signMethod === 'upload'" x-cloak class="space-y-2">
                                <label class="border-2 border-dashed border-[#CBD5E1] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-red-50/30 rounded-2xl p-6 text-center block cursor-pointer transition">
                                    <input type="file" accept="image/png,image/jpeg,image/webp" class="hidden" @change="handleFileUpload($event)">
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-[#8F0A0D] flex items-center justify-center mx-auto mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-[#1E293B]">Klik untuk memilih berkas gambar tanda tangan</p>
                                    <p class="text-[10.5px] text-[#64748B] mt-1">Disarankan berkas PNG dengan latar belakang transparan (Maks. 3MB)</p>
                                </label>
                            </div>
                        </div>

                        {{-- ══ PRATINJAU STEMPEL RESMI (LIVE PREVIEW) ══ --}}
                        <div class="mb-5 p-4 rounded-2xl bg-gradient-to-br from-[#FAFAF9] to-[#F1F5F9] border border-[#CBD5E1] shadow-2xs">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[10.5px] font-extrabold text-[#8F0A0D] uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    PRATINJAU STEMPEL TTD DI DOKUMEN (OFFICIAL STAMP)
                                </span>
                                <span class="text-[10px] text-gray-500 font-semibold">Tampilan otomatis di BA / SPK / Laporan</span>
                            </div>

                            {{-- Box Stempel Interaktif --}}
                            <div class="bg-white rounded-xl border border-slate-300 p-4 shadow-xs">
                                <div class="flex items-center justify-center gap-5 flex-wrap sm:flex-nowrap">
                                    
                                    {{-- Kolom Goresan TTD --}}
                                    <div class="flex-1 flex flex-col items-center justify-center text-center min-h-[90px] border-r sm:border-r border-dashed border-slate-200 pr-3"
                                         x-show="signatureType !== 'qr_only'">
                                        <template x-if="previewSignatureData">
                                            <img :src="previewSignatureData" alt="Tanda Tangan" class="max-h-20 max-w-full object-contain filter drop-shadow-xs">
                                        </template>
                                        <template x-if="!previewSignatureData">
                                            <div class="text-[11px] text-slate-400 font-medium italic py-5">
                                                Belum ada goresan tanda tangan
                                            </div>
                                        </template>
                                        <div class="text-[10px] font-extrabold text-slate-700 mt-1 uppercase tracking-tight">{{ $user->name }}</div>
                                        <div class="text-[9px] text-slate-500 font-semibold">{{ $user->position ?? $user->role_label }}</div>
                                    </div>

                                    {{-- Kolom QR Barcode Verifikasi --}}
                                    <div class="flex flex-col items-center justify-center text-center shrink-0 pl-2"
                                         x-show="signatureType !== 'signature_only'">
                                        <div class="w-16 h-16 p-1 bg-white border border-slate-200 rounded-lg shadow-2xs flex items-center justify-center">
                                            {!! $qrSvg !!}
                                        </div>
                                        <span class="text-[8.5px] font-black text-[#8F0A0D] tracking-wider mt-1 uppercase">VERIFIED E-SIGN</span>
                                        <span class="text-[8px] text-slate-400 font-mono">{{ $user->signature_qr_token }}</span>
                                    </div>
                                </div>

                                <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[9.5px] text-slate-400 font-medium">
                                    <span>Tervalidasi Digital • IP Network Solusindo</span>
                                    <span>Token: <strong class="text-slate-600 font-mono">{{ $user->signature_qr_token }}</strong></span>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Simpan TTD --}}
                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] text-[#64748B]">
                                @if($user->signature_updated_at)
                                Terakhir disimpan: <strong>{{ $user->signature_updated_at->format('d M Y H:i') }} WIB</strong>
                                @else
                                Tanda tangan belum pernah disimpan
                                @endif
                            </span>

                            <button type="button" @click="saveSignatureSettings()" :disabled="isSavingSignature"
                                    class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-xs shadow-md flex items-center gap-2 cursor-pointer disabled:opacity-50">
                                <svg x-show="isSavingSignature" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSavingSignature ? 'Menyimpan...' : 'Simpan Tanda Tangan & QR'"></span>
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     KOLOM KANAN (5 SPAN): KEAMANAN, NOTIFIKASI & PREFERENSI
                     ══════════════════════════════════════════════════════════════ --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- ── KARTU 3: KEAMANAN & AKSES KATA SANDI ── --}}
                    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-xs">
                        <div class="flex items-center justify-between pb-3 border-b border-[#F1F5F9] mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-[14.5px] font-bold text-[#0F172A]">Keamanan & Kata Sandi</h2>
                                    <p class="text-[11px] text-[#64748B]">Perbarui kata sandi akun Anda secara berkala.</p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('settings.password') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                                <input type="password" name="current_password" required placeholder="Masukkan kata sandi saat ini"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi baru"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none">
                            </div>

                            <div class="pt-1 flex justify-end">
                                <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-[#0F172A] bg-[#F1F5F9] hover:bg-[#E2E8F0] border border-[#CBD5E1] transition cursor-pointer">
                                    Perbarui Kata Sandi
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ── KARTU 4: NOTIFIKASI SISTEM ── --}}
                    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-xs">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-[#F1F5F9] mb-4">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-[14.5px] font-bold text-[#0F172A]">Notifikasi Sistem</h2>
                                <p class="text-[11px] text-[#64748B]">Atur kanal pemberitahuan persetujuan dokumen &amp; tugas.</p>
                            </div>
                        </div>

                        <form action="{{ route('settings.preferences') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="theme_preference" :value="themePreference">
                            <input type="hidden" name="timezone_preference" :value="timezonePreference">

                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                                <div>
                                    <h4 class="text-xs font-bold text-[#1E293B]">Notifikasi Web In-App</h4>
                                    <p class="text-[10.5px] text-[#64748B]">Pemberitahuan lonceng saat ada laporan atau tugas baru.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="notification_web" value="1" {{ $user->notification_web ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8F0A0D]"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6]">
                                <div>
                                    <h4 class="text-xs font-bold text-[#1E293B]">Notifikasi Email</h4>
                                    <p class="text-[10.5px] text-[#64748B]">Pengingat email saat dokumen menunggu tanda tangan Anda.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="notification_email" value="1" {{ $user->notification_email ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#8F0A0D]"></div>
                                </label>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-white btn-ipnet-gradient shadow-xs cursor-pointer">
                                Simpan Pengaturan Notifikasi
                            </button>
                        </form>
                    </div>

                    {{-- ── KARTU 5: PREFERENSI APLIKASI ── --}}
                    <div class="bg-white rounded-2xl border border-[#E2E8F0] p-6 shadow-xs">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-[#F1F5F9] mb-4">
                            <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-[14.5px] font-bold text-[#0F172A]">Preferensi Tampilan &amp; Waktu</h2>
                                <p class="text-[11px] text-[#64748B]">Sesuaikan bahasa dan zona waktu kerja Anda.</p>
                            </div>
                        </div>

                        <form action="{{ route('settings.preferences') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="notification_web" value="{{ $user->notification_web ? '1' : '0' }}">
                            <input type="hidden" name="notification_email" value="{{ $user->notification_email ? '1' : '0' }}">

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-2">Tema Aplikasi</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <label class="p-2.5 rounded-xl border text-center cursor-pointer transition text-xs font-bold"
                                           :class="themePreference === 'light' ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#64748B]'">
                                        <input type="radio" name="theme_preference" value="light" x-model="themePreference" class="hidden">
                                        ☀️ Terang
                                    </label>
                                    <label class="p-2.5 rounded-xl border text-center cursor-pointer transition text-xs font-bold"
                                           :class="themePreference === 'dark' ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#64748B]'">
                                        <input type="radio" name="theme_preference" value="dark" x-model="themePreference" class="hidden">
                                        🌙 Gelap
                                    </label>
                                    <label class="p-2.5 rounded-xl border text-center cursor-pointer transition text-xs font-bold"
                                           :class="themePreference === 'system' ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D]' : 'bg-[#F8FAFC] border-[#E2E8F0] text-[#64748B]'">
                                        <input type="radio" name="theme_preference" value="system" x-model="themePreference" class="hidden">
                                        💻 Sistem
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Zona Waktu Kerja</label>
                                <select name="timezone_preference" x-model="timezonePreference"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#0F172A] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] outline-none bg-white">
                                    <option value="Asia/Jakarta">WIB – Waktu Indonesia Barat (GMT+7)</option>
                                    <option value="Asia/Makassar">WITA – Waktu Indonesia Tengah (GMT+8)</option>
                                    <option value="Asia/Jayapura">WIT – Waktu Indonesia Timur (GMT+9)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Bahasa Sistem</label>
                                <div class="px-3.5 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#EEF2F6] text-xs font-bold text-[#0F172A] flex items-center justify-between">
                                    <span>🇮🇩 Bahasa Indonesia (Resmi)</span>
                                    <span class="text-[10px] text-emerald-600 font-extrabold">Aktif</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold text-[#0F172A] bg-[#F1F5F9] hover:bg-[#E2E8F0] border border-[#CBD5E1] transition cursor-pointer">
                                Simpan Preferensi
                            </button>
                        </form>
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
        isEditingProfile: false,
        signatureType: '{{ $user->signature_type ?: "signature_qr" }}',
        signMethod: 'draw',
        previewSignatureData: '{!! addslashes($user->signature_data ?: "") !!}',
        themePreference: '{{ $user->theme_preference ?: "system" }}',
        timezonePreference: '{{ $user->timezone_preference ?: "Asia/Jakarta" }}',
        isSavingSignature: false,

        // Canvas state
        canvas: null,
        ctx: null,
        isDrawing: false,

        init() {
            this.$nextTick(() => {
                this.initCanvas();
            });
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
                // Update live preview
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
                    alert('✓ ' + data.message);
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
