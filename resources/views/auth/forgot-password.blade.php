@extends('layouts.app')

@section('title', 'Lupa Password - Field Service Management - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       Smooth Fluid Entrance & Micro-Animations
       ======================================================== */
    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(24px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(18px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-7px) rotate(0.5deg); }
    }

    @keyframes pulseGlow {
        0%, 100% { opacity: 0.35; transform: scale(1); }
        50% { opacity: 0.65; transform: scale(1.1); }
    }

    @keyframes cardEntrance {
        0% { opacity: 0; transform: translateY(20px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Flowing Red Rim Beam Animation at Top of Card */
    @keyframes rimShimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .card-top-rim {
        background: linear-gradient(90deg, transparent 0%, #8F0A0D 20%, #C61828 45%, #FFA8B2 50%, #C61828 55%, #8F0A0D 80%, transparent 100%);
        background-size: 200% 100%;
        animation: rimShimmer 3.5s linear infinite;
    }

    .anim-hero-reveal {
        animation: heroReveal 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-card-enter {
        animation: cardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    }

    .anim-delay-1 { animation-delay: 0.08s !important; }
    .anim-delay-2 { animation-delay: 0.16s !important; }
    .anim-delay-3 { animation-delay: 0.24s !important; }
    .anim-delay-4 { animation-delay: 0.32s !important; }

    .animate-logo-float {
        animation: logoFloat 4.5s ease-in-out infinite;
    }

    .animate-pulse-glow {
        animation: pulseGlow 6s ease-in-out infinite;
    }

    /* Primary Action Button Animation */
    .ipnet-login-btn {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);
        background-size: 200% auto;
        box-shadow: 0 4px 16px rgba(143, 10, 13, 0.28);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-login-btn:hover {
        background-position: right center;
        box-shadow: 0 8px 26px rgba(143, 10, 13, 0.38);
        transform: translateY(-2px);
    }
    .ipnet-login-btn:active {
        transform: translateY(0) scale(0.99);
    }

    /* Form Input Micro-Interactions */
    .ipnet-input {
        background-color: #FFFFFF;
        border: 1.5px solid #CBD5E1;
        border-radius: 14px;
        color: #1E293B;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }
    .ipnet-input:hover {
        border-color: #94A3B8;
    }
    .ipnet-input:focus {
        border-color: #8F0A0D;
        box-shadow: 0 0 0 3.5px rgba(143, 10, 13, 0.12);
        background-color: #FFFFFF;
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen w-full flex flex-col lg:flex-row bg-[#F8FAFC] overflow-x-hidden font-sans">

    {{-- ======================================================== --}}
    {{-- Left Half - Faceted Brand Panel (Matching Dashboard Style)--}}
    {{-- ======================================================== --}}
    <div id="brand-left-panel" class="lg:w-1/2 min-h-[480px] lg:min-h-screen relative flex flex-col justify-between p-8 sm:p-12 lg:p-16 text-white overflow-hidden select-none bg-[#750608]">
        
        {{-- Layered Geometric Faceted Red Planes (Identical to Dashboard Hero Banner) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none z-0">
            <svg class="w-full h-full object-cover" viewBox="0 0 1000 1000" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="facetGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#C61828" />
                        <stop offset="50%" stop-color="#9E0E1D" />
                        <stop offset="100%" stop-color="#7A0813" />
                    </linearGradient>
                    <linearGradient id="facetGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#B01423" />
                        <stop offset="100%" stop-color="#5A040C" />
                    </linearGradient>
                    <linearGradient id="facetGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#830B17" />
                        <stop offset="100%" stop-color="#420207" />
                    </linearGradient>
                    <linearGradient id="facetHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                        <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.25" />
                        <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                    </linearGradient>
                    <filter id="loginFacetShadow" x="-10%" y="-10%" width="130%" height="130%">
                        <feDropShadow dx="-8" dy="12" stdDeviation="16" flood-color="#2A0205" flood-opacity="0.5" />
                    </filter>
                    <pattern id="login-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                        <path d="M 32 0 L 0 0 0 32" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                    </pattern>
                </defs>

                <!-- Base Geometric Background -->
                <rect width="1000" height="1000" fill="url(#facetGrad1)" />

                <!-- Grid Texture Overlay -->
                <rect width="1000" height="1000" fill="url(#login-grid)" />

                <!-- Large Diagonal Angled Planes -->
                <polygon points="0,0 600,0 200,1000 0,1000" fill="url(#facetGrad2)" opacity="0.95" />
                <polygon points="180,0 820,0 1000,1000 450,1000" fill="url(#facetGrad1)" filter="url(#loginFacetShadow)" />
                <polygon points="520,0 1000,0 1000,1000 780,1000" fill="url(#facetGrad3)" filter="url(#loginFacetShadow)" />

                <!-- Ambient Angular Highlights -->
                <polygon points="0,0 520,0 900,1000 280,1000" fill="url(#facetHighlight)" />
            </svg>
        </div>

        {{-- Dynamic Interactive Mouse Spotlight Follower --}}
        <div id="spotlight-glow" class="absolute w-[520px] h-[520px] rounded-full bg-white/10 blur-3xl pointer-events-none transition-all duration-300 -translate-x-1/2 -translate-y-1/2 opacity-60 hidden lg:block" style="top: 50%; left: 50%;"></div>

        {{-- Dynamic Ambient Light Orbs --}}
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl pointer-events-none animate-pulse-glow"></div>
        <div class="absolute bottom-10 right-0 w-80 h-80 rounded-full bg-black/40 blur-3xl pointer-events-none"></div>

        {{-- Top Brand Indicator --}}
        <div class="relative z-10 flex items-center anim-fade-up">
            <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-[11px] font-bold tracking-wider uppercase shadow-sm transition-transform hover:scale-105">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>Enterprise Platform</span>
            </div>
        </div>

        {{-- Center Branding Content --}}
        <div class="relative z-10 my-auto py-8 flex flex-col items-center text-center max-w-md mx-auto">
            
            {{-- Floating Logo in Premium Glass Ring --}}
            <div class="relative mb-6 anim-hero-reveal">
                <div class="absolute inset-0 bg-white/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-white/10 backdrop-blur-md border border-white/25 flex items-center justify-center shadow-[0_16px_36px_rgba(0,0,0,0.3)] animate-logo-float transition-transform hover:scale-110 cursor-pointer">
                    <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" class="h-16 sm:h-20 w-auto object-contain drop-shadow-md">
                </div>
            </div>

            {{-- Title & Subtitle --}}
            <h1 class="font-display font-black text-[28px] sm:text-[36px] text-white tracking-tight leading-tight drop-shadow-sm anim-fade-up anim-delay-1">
                IP Network Solusindo
            </h1>
            <div class="inline-flex items-center gap-2 mt-2 mb-4 anim-fade-up anim-delay-2">
                <span class="h-[1px] w-6 bg-white/40"></span>
                <p class="text-rose-100 text-[11.5px] sm:text-[12.5px] font-bold tracking-[2.5px] uppercase">
                    Field Service Management
                </p>
                <span class="h-[1px] w-6 bg-white/40"></span>
            </div>

            <p class="text-white/85 text-[13.5px] sm:text-[14.5px] leading-relaxed font-normal max-w-sm drop-shadow-xs anim-fade-up anim-delay-3">
                Masukkan email akun Anda dan sistem akan mengirimkan tautan verifikasi resmi untuk mereset kata sandi Anda dengan aman.
            </p>
        </div>

        {{-- Bottom Copyright Note --}}
        <div class="relative z-10 text-center text-white/60 text-[11.5px] font-medium tracking-wide anim-fade-up anim-delay-4">
            PT IP Network Solusindo &bull; Field Service Management
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- Right Half - Modern SaaS Forgot Password Card             --}}
    {{-- ======================================================== --}}
    <div class="lg:w-1/2 min-h-[500px] lg:min-h-screen flex items-center justify-center p-6 sm:p-10 lg:p-14 relative bg-[#F8FAFC]">
        
        {{-- Modern Geometric Dot Matrix & Subtle Grid on Right Side --}}
        <div class="absolute inset-0 pointer-events-none select-none opacity-60">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="right-dot-pattern" width="24" height="24" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1" fill="#CBD5E1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#right-dot-pattern)" />
            </svg>
        </div>

        {{-- Soft Ambient Radial Lights --}}
        <div class="absolute top-10 right-10 w-96 h-96 rounded-full bg-red-600/5 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 rounded-full bg-blue-600/5 blur-3xl pointer-events-none"></div>

        {{-- Executive Form Card --}}
        <div class="w-full max-w-[440px] bg-white border border-[#E2E8F0] rounded-[24px] p-7 sm:p-9 shadow-[0_20px_50px_-15px_rgba(15,23,42,0.08),0_1px_3px_rgba(0,0,0,0.03)] relative z-10 anim-card-enter overflow-hidden">
            
            {{-- Flowing Red Shimmer Rim Beam (At Top of Card) --}}
            <div class="absolute top-0 left-0 right-0 h-[3.5px] card-top-rim"></div>

            {{-- Top Badge Indicator --}}
            <div class="flex items-center mb-4">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                    <span>Pemulihan Akun</span>
                </div>
            </div>

            {{-- Status Notification --}}
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3.5 mb-5 flex items-start gap-2.5 anim-fade-up">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[12.5px] text-emerald-800 leading-normal font-medium">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Header --}}
            <div class="mb-6">
                <h2 class="font-display text-[22px] sm:text-[25px] font-bold text-[#1E293B] tracking-tight">
                    Lupa Kata Sandi?
                </h2>
                <p class="text-[12.5px] text-[#64748B] mt-1 leading-normal">
                    Masukkan email akun Anda dan kami akan mengirimkan link untuk mereset password.
                </p>
            </div>

            <form action="{{ route('password.email') }}" method="POST" x-data="{ loading: false }" @submit="loading = true" class="space-y-4">
                @csrf

                {{-- Email Input --}}
                <div>
                    <label class="block text-[11.5px] font-bold text-[#475569] mb-1.5 uppercase tracking-[0.4px]">
                        Email Terdaftar
                    </label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-[#94A3B8] group-focus-within:text-[#8F0A0D] transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email') }}"
                               autofocus
                               class="w-full pl-10 pr-3.5 py-2.5 ipnet-input @error('email') border-red-500 @enderror"
                               placeholder="nama@ipnetwork.co.id"
                               required>
                    </div>
                    @error('email')
                    <p class="text-[11.5px] text-red-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button with Interactive Micro-Animations --}}
                <button type="submit"
                        :disabled="loading"
                        class="w-full py-3 px-5 rounded-xl text-white font-bold text-[13.5px] ipnet-login-btn flex items-center justify-center gap-2 cursor-pointer group disabled:opacity-70 disabled:cursor-not-allowed mt-2">
                    <template x-if="!loading">
                        <span class="flex items-center gap-2">
                            <span>Kirim Link Reset Password</span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengirim Instruksi...</span>
                        </span>
                    </template>
                </button>

                {{-- Back to Login Link --}}
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-[12.5px] font-semibold text-[#64748B] hover:text-[#8F0A0D] transition-colors group">
                        <svg class="w-3.5 h-3.5 transition-transform duration-150 group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                        <span>Kembali ke Halaman Login</span>
                    </a>
                </div>
            </form>

            {{-- Footer --}}
            <div class="mt-6 pt-5 border-t border-[#E2E8F0] text-center text-[11.5px] text-[#94A3B8]">
                <span>&copy; {{ date('Y') }} PT IP Network Solusindo</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Interactive mouse spotlight tracking on left branding panel
    document.addEventListener('DOMContentLoaded', () => {
        const leftPanel = document.getElementById('brand-left-panel');
        const spotlight = document.getElementById('spotlight-glow');

        if (leftPanel && spotlight && window.innerWidth >= 1024) {
            leftPanel.addEventListener('mousemove', (e) => {
                const rect = leftPanel.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                spotlight.style.left = `${x}px`;
                spotlight.style.top = `${y}px`;
            });
        }
    });
</script>
@endpush
@endsection
