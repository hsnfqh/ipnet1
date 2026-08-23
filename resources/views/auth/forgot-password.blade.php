@extends('layouts.app')

@section('title', 'Lupa Password - Field System Management')

@section('content')
<div class="min-h-screen bg-[#F7F6F5] flex items-center justify-center p-4 sm:p-5">
    <div class="flex flex-col lg:flex-row w-full max-w-[900px] rounded-xl sm:rounded-2xl overflow-hidden border border-[#E7E5E3] shadow-[0_16px_40px_rgba(14,13,18,0.12)]">

        {{-- ── Left Panel - Branding ── --}}
        <div class="flex-1 p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[460px] bg-gradient-to-br from-[#AF1424] via-[#96101F] to-[#5C0A13] relative overflow-hidden">
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
                            Field System Management 
                        </p>
                    </div>
                </div>

                <div class="hidden sm:block mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-white/10">
                    {{-- Ikon kunci --}}
                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 rounded-full bg-white/10 flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/70 text-[12px] leading-relaxed text-center">
                        Masukkan email akun Anda dan kami akan mengirimkan link untuk mereset password Anda dengan aman.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Right Panel - Form ── --}}
        <div class="flex-1 bg-white p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[460px]">

            <div class="mb-6">
                <h2 class="font-display text-[19px] sm:text-[22px] font-semibold text-[#17151C] mb-1.5 tracking-[-0.2px]">Lupa Password?</h2>
                <p class="text-[12.5px] sm:text-[13px] text-[#75727C]">Masukkan email akun Anda dan kami akan mengirimkan link reset password.</p>
            </div>

            {{-- Success message --}}
            @if (session('status'))
                <div style="background:#E4F3EA; border:1px solid #A3D9B5; border-radius:10px; padding:12px 16px; margin-bottom:20px; display:flex; align-items:flex-start; gap:10px;">
                    <svg style="width:18px; height:18px; color:#1B7A46; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size:13px; color:#1B7A46; margin:0; line-height:1.5;">{{ session('status') }}</p>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div class="mb-4">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="w-full px-[11px] py-[9px] rounded-lg border text-[14px] text-[#17151C] outline-none transition-all @error('email') border-red-400 bg-red-50 @else border-[#E7E5E3] focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] @enderror"
                        placeholder="email@ipnetwork.co.id"
                        autocomplete="email"
                        required
                        autofocus>
                    @error('email')
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Kirim Link Reset Password
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Mengirim...
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
