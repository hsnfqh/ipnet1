@extends('layouts.app')

@section('title', 'Reset Password - Field System Management')

@section('content')
<div class="min-h-screen bg-[#F7F6F5] flex items-center justify-center p-4 sm:p-5">
    <div class="flex flex-col lg:flex-row w-full max-w-[900px] rounded-xl sm:rounded-2xl overflow-hidden border border-[#E7E5E3] shadow-[0_16px_40px_rgba(14,13,18,0.12)]">

        {{-- ── Left Panel - Branding ── --}}
        <div class="flex-1 p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[500px] bg-gradient-to-br from-[#AF1424] via-[#96101F] to-[#5C0A13] relative overflow-hidden">
            <div class="absolute inset-0 wms-signature opacity-50"></div>
            <div class="absolute -top-[60px] -right-[60px] w-[220px] h-[220px] rounded-full bg-[radial-gradient(circle,#AF1424_0%,transparent_70%)] opacity-50"></div>

            <div class="relative z-10 text-center">
                <div class="flex flex-col items-center justify-center gap-2.5 sm:gap-3">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0" style="background:transparent;">
                        <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" style="width:100%; height:100%; object-fit:contain;">
                    </div>
                    <div>
                        <h1 class="font-display font-bold text-[19px] sm:text-[24px] text-white leading-tight tracking-[-0.3px] text-center">
                            IP Network Solusindo
                        </h1>
                        <p class="text-white/50 text-[10px] sm:text-[11px] font-medium tracking-[2px] uppercase text-center">
                            Workforce Management System
                        </p>
                    </div>
                </div>

                <div class="hidden sm:block mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-white/10">
                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/70 text-[12px] leading-relaxed text-center">
                        Buat password baru yang kuat untuk mengamankan akun Anda. Password akan dienkripsi secara aman.
                    </p>
                    <div class="mt-4 space-y-2 text-left">
                        <div class="flex items-center gap-2 text-white/60 text-[11px]">
                            <svg class="w-3.5 h-3.5 text-white/50 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Minimal 8 karakter
                        </div>
                        <div class="flex items-center gap-2 text-white/60 text-[11px]">
                            <svg class="w-3.5 h-3.5 text-white/50 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Token reset hanya berlaku 60 menit
                        </div>
                        <div class="flex items-center gap-2 text-white/60 text-[11px]">
                            <svg class="w-3.5 h-3.5 text-white/50 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Password disimpan secara terenkripsi
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Right Panel - Reset Form ── --}}
        <div class="flex-1 bg-white p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[500px]"
             x-data="{
                showPass: false,
                showConfirm: false,
                password: '',
                strength: 0,
                loading: false,
                getStrength() {
                    let s = 0;
                    if (this.password.length >= 8) s++;
                    if (/[A-Z]/.test(this.password)) s++;
                    if (/[0-9]/.test(this.password)) s++;
                    if (/[^A-Za-z0-9]/.test(this.password)) s++;
                    return s;
                },
                strengthLabel() {
                    const s = this.getStrength();
                    return ['', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'][s] || '';
                },
                strengthColor() {
                    return ['#E7E5E3','#C81E2C','#9A6206','#1B7A46','#1B7A46'][this.getStrength()];
                }
             }">

            <div class="mb-6">
                <h2 class="font-display text-[19px] sm:text-[22px] font-semibold text-[#17151C] mb-1.5 tracking-[-0.2px]">Buat Password Baru</h2>
                <p class="text-[12.5px] sm:text-[13px] text-[#75727C]">Masukkan password baru untuk akun Anda.</p>
            </div>

            @if ($errors->any())
                <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:12px 16px; margin-bottom:20px; display:flex; align-items:flex-start; gap:10px;">
                    <svg style="width:18px; height:18px; color:#C81E2C; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p style="font-size:12.5px; color:#C81E2C; margin:0 0 2px;">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" @submit="loading = true">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Email (readonly display) --}}
                <div class="mb-4">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Email</label>
                    <div class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#75727C] bg-[#F8F7F6] flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#B7B3BB] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $email }}
                    </div>
                </div>

                {{-- New Password --}}
                <div class="mb-3">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Password Baru</label>
                    <div class="relative">
                        <input
                            :type="showPass ? 'text' : 'password'"
                            name="password"
                            id="password"
                            x-model="password"
                            class="w-full px-[11px] py-[9px] pr-10 rounded-lg border text-[14px] text-[#17151C] outline-none transition-all @error('password') border-red-400 bg-red-50 @else border-[#E7E5E3] focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] @enderror"
                            placeholder="Min. 8 karakter"
                            autocomplete="new-password"
                            required>
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#B7B3BB] hover:text-[#75727C] transition-colors">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Password strength indicator --}}
                    <div x-show="password.length > 0" class="mt-2" style="display:none;">
                        <div class="flex gap-1 mb-1">
                            <template x-for="i in 4" :key="i">
                                <div class="h-1 flex-1 rounded-full transition-all duration-300"
                                     :style="{ background: i <= getStrength() ? strengthColor() : '#E7E5E3' }"></div>
                            </template>
                        </div>
                        <p class="text-[11px] font-medium" :style="{ color: strengthColor() }" x-text="'Kekuatan: ' + strengthLabel()"></p>
                    </div>

                    @error('password')
                        <p class="text-[12px] text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-5">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Konfirmasi Password</label>
                    <div class="relative">
                        <input
                            :type="showConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="w-full px-[11px] py-[9px] pr-10 rounded-lg border text-[14px] text-[#17151C] outline-none transition-all @error('password_confirmation') border-red-400 bg-red-50 @else border-[#E7E5E3] focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] @enderror"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                            required>
                        <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#B7B3BB] hover:text-[#75727C] transition-colors">
                            <svg x-show="!showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="text-[12px] text-red-500 mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full py-[10px] px-[17px] rounded-lg bg-[#C81E2C] text-white font-semibold text-[13.5px] sm:text-[14px] shadow-[0_8px_20px_rgba(200,30,44,0.24)] hover:brightness-105 active:translate-y-[1px] transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed mb-4">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                            Reset Password
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Menyimpan...
                        </span>
                    </template>
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-[13px] text-[#75727C] hover:text-[#C81E2C] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                        Kembali ke halaman Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
