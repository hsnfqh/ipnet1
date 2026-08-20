@extends('layouts.app')

@section('title', 'Login - WMS')

@section('content')
<div class="min-h-screen bg-[#F7F6F5] flex items-center justify-center p-4 sm:p-5">
    <div class="flex flex-col lg:flex-row w-full max-w-[900px] rounded-xl sm:rounded-2xl overflow-hidden border border-[#E7E5E3] shadow-[0_16px_40px_rgba(14,13,18,0.12)]">
        <!-- Left Panel - Branding -->
        <div class="flex-1 p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[500px] bg-gradient-to-br from-[#AF1424] via-[#96101F] to-[#5C0A13] relative overflow-hidden">
            <div class="absolute inset-0 wms-signature opacity-50"></div>
            <div class="absolute -top-[60px] -right-[60px] w-[220px] h-[220px] rounded-full bg-[radial-gradient(circle,#AF1424_0%,transparent_70%)] opacity-50"></div>
            
            <div class="relative z-10 text-center">
                <!-- LOGO + TEKS DI TENGAH -->
                <div class="flex flex-col items-center justify-center gap-2.5 sm:gap-3">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0" style="background:transparent;">
                        <img src="{{ asset('images/ipnet1.png') }}" 
                             alt="IP Network Solusindo" 
                             style="width:100%; height:100%; object-fit:contain;">
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
                    <p class="text-white/70 text-[12px] leading-relaxed" style="text-align:center;">
                        Selamat datang di sistem manajemen tenaga kerja IP NET 1. 
                        Kelola proyek, jadwal, dan penugasan teknisi lapangan secara terpusat 
                        dalam satu platform terintegrasi.
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="flex-1 bg-white p-7 sm:p-10 lg:p-[46px] flex flex-col justify-center lg:min-h-[500px]">
            <h2 class="font-display text-[19px] sm:text-[22px] font-semibold text-[#17151C] mb-1.5 tracking-[-0.2px]">Masuk ke Akun</h2>
            <p class="text-[12.5px] sm:text-[13px] text-[#75727C] mb-5 sm:mb-[22px]">Pilih peran untuk mengakses dashboard</p>

            @if (session('status'))
                <div style="background:#E4F3EA; border:1px solid #A3D9B5; border-radius:10px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:flex-start; gap:10px;">
                    <svg style="width:18px; height:18px; color:#1B7A46; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size:13px; color:#1B7A46; margin:0; line-height:1.5;">{{ session('status') }}</p>
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST" x-data="loginForm()">
                @csrf
                
                <!-- Role Selection -->
                <div class="flex gap-1 sm:gap-1.5 mb-5 sm:mb-[22px] bg-[#F1F0EE] p-1 rounded-xl">
                    <template x-for="(label, key) in roleLabels" :key="key">
                        <button type="button" 
                                @click="selectRole(key)"
                                class="flex-1 py-2 px-1 sm:px-1.5 rounded-lg border-none cursor-pointer text-[11px] sm:text-[12.5px] font-semibold transition-all whitespace-nowrap"
                                :class="selectedRole === key ? 'bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)]' : 'bg-transparent text-[#3D3A44]'"
                                x-text="label">
                        </button>
                    </template>
                </div>
                
                <input type="hidden" name="role" id="role-selected" value="lead">
                
                <div class="mb-3.5">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Email</label>
                    <input type="email" 
                           name="email" 
                           x-model="email"
                           class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all @error('email') border-red-500 @enderror"
                           placeholder="Masukkan email">
                    @error('email')
                    <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-3.5">
                    <label class="block text-[11.5px] sm:text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Kata Sandi</label>
                    <input type="password" 
                           name="password" 
                           class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password">
                    @error('password')
                    <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <label class="flex items-center gap-2 text-[12.5px] sm:text-[13px] text-[#3D3A44] cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#E7E5E3] text-[#C81E2C] focus:ring-[#C81E2C]" checked>
                        Ingat saya
                    </label>
                    <a href="{{ route('password.request') }}" class="text-[12.5px] sm:text-[13px] text-[#C81E2C] hover:underline">Lupa password?</a>
                </div>
                
                <button type="submit" 
                        class="w-full py-[10px] px-[17px] rounded-lg bg-[#C81E2C] text-white font-semibold text-[13.5px] sm:text-[14px] shadow-[0_8px_20px_rgba(200,30,44,0.24)] hover:brightness-105 active:translate-y-[1px] transition-all flex items-center justify-center gap-1.5 wms-btn">
                    Masuk sebagai <span id="role-label" x-text="roleLabels[selectedRole]"></span>
                </button>
                
             
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loginForm', () => ({
            selectedRole: 'lead',
            email: 'rangga.saputra@ipnetwork.co.id',
            roleLabels: {
                lead: 'Lead Engineer',
                l1: 'Engineer L1',
                l2: 'Engineer L2'
            },
            roleEmails: {
                lead: 'rangga.saputra@ipnetwork.co.id',
                l1: 'fajar.n@ipnetwork.co.id',
                l2: 'dimas.p@ipnetwork.co.id'
            },
            selectRole(role) {
                this.selectedRole = role;
                document.getElementById('role-selected').value = role;
                this.email = this.roleEmails[role] || '';
                
                // Update email input
                const emailInput = document.querySelector('input[name="email"]');
                if (emailInput) {
                    emailInput.value = this.email;
                }
            }
        }));
    });
</script>
@endpush
@endsection