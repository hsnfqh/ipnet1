<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Aduan & Layanan Tiket Klien - PT IP Network Solusindo</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ipnet1.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & IBM Plex Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=IBM+Plex+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    },
                    colors: {
                        ipnet: {
                            50: '#FDF2F2',
                            100: '#FDE8E8',
                            200: '#FBD5D5',
                            500: '#B81525',
                            600: '#8F0A0D',
                            700: '#73080A',
                            800: '#580608',
                            900: '#3D0405',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --ipnet-primary: #8F0A0D;
            --ipnet-primary-hover: #73080A;
        }
        body {
            background-color: #F8FAFC;
            color: #0F172A;
        }
        .btn-ipnet-primary {
            background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
            color: #FFFFFF;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 6px rgba(143, 10, 13, 0.2);
        }
        .btn-ipnet-primary:hover {
            background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
            box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3);
            transform: translateY(-1px);
        }
        .ipnet-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }
        .ipnet-card:hover {
            border-color: #CBD5E1;
        }
        .form-input-focus {
            transition: all 0.15s ease-in-out;
        }
        .form-input-focus:focus {
            border-color: #8F0A0D;
            box-shadow: 0 0 0 3px rgba(143, 10, 13, 0.12);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col font-sans antialiased text-slate-800 bg-[#F8FAFC]" x-data="clientPortal()" x-cloak>

    <!-- ======================================================== -->
    <!-- TOP NAVIGATION BAR (FULL, CLEAN & MODERN)               -->
    <!-- ======================================================== -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Logo & Brand Header -->
            <div class="flex items-center gap-3">
                <a href="{{ route('public.helpdesk.index') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/ipnet1.png') }}" alt="IP-NET Logo" class="h-9 sm:h-10 w-auto object-contain transition-transform group-hover:scale-102" onerror="this.src='{{ asset('images/ipnet.png') }}'">
                    <div class="border-l border-slate-200 pl-3.5 flex flex-col justify-center">
                        <span class="text-xs sm:text-sm font-extrabold tracking-tight text-slate-900 leading-tight">PT IP NETWORK SOLUSINDO</span>
                        <span class="text-[11px] text-slate-500 font-medium leading-tight">Helpdesk &amp; Portal Pengaduan Klien</span>
                    </div>
                </a>
            </div>

            <!-- Tab Switcher -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Navigation Pill Switcher -->
                <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 border border-slate-200/80">
                    <button type="button" @click="activeTab = 'create'" 
                            :class="activeTab === 'create' ? 'bg-white text-[#8F0A0D] font-extrabold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                            class="px-3 sm:px-4 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Tiket</span>
                    </button>

                    <button type="button" @click="activeTab = 'track'" 
                            :class="activeTab === 'track' ? 'bg-white text-[#8F0A0D] font-extrabold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                            class="px-3 sm:px-4 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Lacak Tiket</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- ======================================================== -->
    <!-- MAIN CONTENT CONTAINER (FULL WIDTH ERGONOMIC MAX-W-7XL) -->
    <!-- ======================================================== -->
    <main class="flex-1 py-6 sm:py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">

        <!-- ======================================================== -->
        <!-- EXECUTIVE HERO BANNER (IDENTIK DASHBOARD RESMI IP-NET)    -->
        <!-- ======================================================== -->
        <div class="rounded-2xl px-6 py-6 sm:px-8 sm:py-7 text-white shadow-lg shadow-red-950/10 relative overflow-hidden mb-8">
            <!-- Layered Geometric Faceted Red Planes (SVG Asli IP-NET) -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="redGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#C61828" />
                            <stop offset="100%" stop-color="#9E0E1D" />
                        </linearGradient>
                        <linearGradient id="redGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" stop-color="#B01423" />
                            <stop offset="100%" stop-color="#7A0813" />
                        </linearGradient>
                        <linearGradient id="redGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                            <stop offset="0%" stop-color="#940E1B" />
                            <stop offset="100%" stop-color="#5A040C" />
                        </linearGradient>
                        <linearGradient id="redGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                            <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.22" />
                            <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                        </linearGradient>
                        <filter id="facetDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                            <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                        </filter>
                    </defs>

                    <!-- Base Background -->
                    <rect width="1440" height="200" fill="url(#redGrad1)" />

                    <!-- Top-Left Large Diagonal Angled Plane -->
                    <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1)" />

                    <!-- Intersecting Broad Diagonal Facet Strip -->
                    <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2)" filter="url(#facetDropShadow)" />

                    <!-- Crossing Foreground Diagonal Bright Red Plane -->
                    <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1)" opacity="0.9" filter="url(#facetDropShadow)" />

                    <!-- Right Edge Deeper Contrast Facet -->
                    <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3)" filter="url(#facetDropShadow)" />

                    <!-- Soft Angular Ambient Highlight Overlays -->
                    <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlight)" />
                </svg>
            </div>

            <div class="relative z-10">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center mb-2 px-3 py-1 text-[11px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                        <span class="w-1.5 h-1.5 mr-2 bg-emerald-400 rounded-full inline-block animate-pulse"></span>
                        PORTAL RESMI LAYANAN KLIEN &bull; PT IP NETWORK SOLUSINDO
                    </div>

                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">
                        Pusat Layanan &amp; Pengaduan Pelanggan
                    </h1>
                    <p class="mt-1.5 text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                        Selamat datang di portal bantuan resmi PT IP Network Solusindo. Sampaikan kendala atau kebutuhan layanan Anda untuk segera ditindaklanjuti dan ditangani langsung oleh tim teknis kami.
                    </p>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- NOTIFICATIONS / ALERTS                                   -->
        <!-- ======================================================== -->
        @if(session('success'))
            <div class="mb-6 p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-2xs">
                <div class="flex items-start gap-3.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-emerald-950">Tiket Berhasil Diterbitkan!</h3>
                        <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                        @if(session('ticket_number'))
                            <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
                                <div class="px-3.5 py-1.5 rounded-xl bg-white border border-emerald-300 font-mono font-extrabold text-emerald-900 text-xs sm:text-sm tracking-wide shadow-2xs">
                                    {{ session('ticket_number') }}
                                </div>
                                <button type="button" @click="navigator.clipboard.writeText('{{ session('ticket_number') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="px-3 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-1.5 transition cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span x-text="copied ? 'Tersalin ke Clipboard! ✓' : 'Salin Nomor Tiket'"></span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-2xs">
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-lg bg-rose-500 text-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Mohon periksa isian formulir:</h4>
                        <ul class="mt-1 text-xs list-disc list-inside text-rose-700 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- TAB 1: FORM BUAT TIKET ADUAN BARU (12-COL ERGONOMIC LAYOUT)     -->
        <!-- ============================================================== -->
        <div x-show="activeTab === 'create'">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                <!-- Main Form Column (8 of 12 cols - Spacious & Clean) -->
                <div class="lg:col-span-8">
                    <form method="POST" action="{{ route('public.helpdesk.store') }}" enctype="multipart/form-data" 
                          class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_2px_14px_rgba(0,0,0,0.03)] overflow-hidden">
                        @csrf

                        <!-- Card Header -->
                        <div class="px-6 py-5 sm:px-7 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                            <div>
                                <h2 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Formulir Pengaduan Gangguan Layanan</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Silakan isi informasi lengkap kendala teknis yang dialami</p>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs">
                                3 Langkah Pengisian
                            </span>
                        </div>

                        <div class="p-6 sm:p-7 space-y-7">
                            <!-- ========================================== -->
                            <!-- SECTION 1: DATA IDENTITAS KLIEN & PELAPOR  -->
                            <!-- ========================================== -->
                            <div>
                                <div class="flex items-center gap-3.5 mb-5 pb-3 border-b border-slate-100">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs sm:text-[13px] flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                        01
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h3 class="text-xs sm:text-[13.5px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Identitas Klien &amp; Pelapor</h3>
                                        <p class="text-[11.5px] text-slate-500 leading-snug mt-0.5">Informasi instansi perusahaan dan kontak yang dapat dihubungi</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Nama Perusahaan / Klien -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Nama Perusahaan / Klien (PT / Instansi) <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="text" name="client_name" value="{{ old('client_name') }}" required
                                               placeholder="Masukkan nama perusahaan Anda"
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs">
                                    </div>

                                    <!-- Nama PIC / Pelapor -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Nama PIC / Pelapor <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="text" name="reported_by" value="{{ old('reported_by') }}" required
                                               placeholder="Masukkan nama lengkap Anda"
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs">
                                    </div>

                                    <!-- Email Kontak Resmi -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Email Kontak Resmi <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="email" name="contact_email" value="{{ old('contact_email') }}" required
                                               placeholder="Masukkan alamat email aktif"
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs">
                                    </div>

                                    <!-- Nomor WhatsApp / Telepon -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Nomor WhatsApp / Telepon <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required
                                               placeholder="Masukkan nomor telepon / WhatsApp"
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs">
                                    </div>

                                    <!-- Tanggal & Waktu Gangguan -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Tanggal &amp; Waktu Gangguan <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="datetime-local" name="incident_date" value="{{ old('incident_date') }}" required
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs cursor-pointer">
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- SECTION 2: KATEGORI & TINGKAT URGENSI      -->
                            <!-- ========================================== -->
                            <div>
                                <div class="flex items-center gap-3.5 mb-5 pb-3 border-b border-slate-100">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs sm:text-[13px] flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                        02
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h3 class="text-xs sm:text-[13.5px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Kategori &amp; Tingkat Urgensi (Severity)</h3>
                                        <p class="text-[11.5px] text-slate-500 leading-snug mt-0.5">Pilih jenis layanan terdampak dan tingkat keparahan gangguan</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- Kategori Kendala Grid -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">
                                            Kategori Layanan Terdampak <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                            @php
                                                $categories = [
                                                    ['val' => 'Jaringan & Internet', 'label' => 'Jaringan & Internet', 'desc' => 'Koneksi lambat / putus'],
                                                    ['val' => 'Firewall & Security', 'label' => 'Firewall & Security', 'desc' => 'Blokir / VPN / IPS'],
                                                    ['val' => 'Server & Cloud', 'label' => 'Server & Cloud', 'desc' => 'Server down / Storage'],
                                                    ['val' => 'Hardware Switch/Router', 'label' => 'Switch / Router', 'desc' => 'Perangkat error / mati'],
                                                    ['val' => 'Access Point & WiFi', 'label' => 'WiFi & Hotspot', 'desc' => 'SSID / Login portal'],
                                                    ['val' => 'Permintaan Teknis Lainnya', 'label' => 'Layanan Lainnya', 'desc' => 'Bantuan teknis umum'],
                                                ];
                                            @endphp

                                            @foreach($categories as $cat)
                                                <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150 select-none"
                                                       :class="selectedCategory === '{{ $cat['val'] }}' ? 'border-[#8F0A0D] bg-[#FEF2F2] ring-2 ring-[#8F0A0D]/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                                    <input type="radio" name="category" value="{{ $cat['val'] }}" x-model="selectedCategory" class="sr-only" required>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-bold text-slate-900 leading-tight">{{ $cat['label'] }}</span>
                                                        <span class="w-2 h-2 rounded-full" :class="selectedCategory === '{{ $cat['val'] }}' ? 'bg-[#8F0A0D]' : 'bg-slate-200'"></span>
                                                    </div>
                                                    <span class="text-[11px] text-slate-500 leading-tight mt-0.5">{{ $cat['desc'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Tingkat Urgensi (Low, Medium, High, Critical) -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-2">
                                            Tingkat Urgensi Kendala (Severity) <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                            <!-- Low -->
                                            <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                                   :class="selectedPriority === 'Low' ? 'border-blue-500 bg-blue-50/70 ring-2 ring-blue-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                                <input type="radio" name="priority" value="Low" x-model="selectedPriority" class="sr-only" required>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-extrabold" :class="selectedPriority === 'Low' ? 'text-blue-700' : 'text-slate-700'">LOW</span>
                                                    <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Low' ? 'bg-blue-500' : 'bg-slate-300'"></span>
                                                </div>
                                                <p class="text-[10.5px] text-slate-600 leading-tight">Gangguan minor / tidak berdampak sistem.</p>
                                            </label>

                                            <!-- Medium -->
                                            <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                                   :class="selectedPriority === 'Medium' ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                                <input type="radio" name="priority" value="Medium" x-model="selectedPriority" class="sr-only" required>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-extrabold" :class="selectedPriority === 'Medium' ? 'text-amber-700' : 'text-slate-700'">MEDIUM</span>
                                                    <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Medium' ? 'bg-amber-500' : 'bg-slate-300'"></span>
                                                </div>
                                                <p class="text-[10.5px] text-slate-600 leading-tight">Fungsi sebagian lambat / terganggu parsial.</p>
                                            </label>

                                            <!-- High -->
                                            <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                                   :class="selectedPriority === 'High' ? 'border-orange-500 bg-orange-50/70 ring-2 ring-orange-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                                <input type="radio" name="priority" value="High" x-model="selectedPriority" class="sr-only" required>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-extrabold" :class="selectedPriority === 'High' ? 'text-orange-700' : 'text-slate-700'">HIGH</span>
                                                    <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'High' ? 'bg-orange-500' : 'bg-slate-300'"></span>
                                                </div>
                                                <p class="text-[10.5px] text-slate-600 leading-tight">Layanan utama terhenti pada banyak user.</p>
                                            </label>

                                            <!-- Critical -->
                                            <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                                   :class="selectedPriority === 'Critical' ? 'border-[#8F0A0D] bg-[#FEF2F2] ring-2 ring-[#8F0A0D]/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                                <input type="radio" name="priority" value="Critical" x-model="selectedPriority" class="sr-only" required>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs font-extrabold" :class="selectedPriority === 'Critical' ? 'text-[#8F0A0D]' : 'text-slate-700'">CRITICAL</span>
                                                    <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Critical' ? 'bg-[#8F0A0D]' : 'bg-slate-300'"></span>
                                                </div>
                                                <p class="text-[10.5px] text-slate-600 leading-tight">Total Down! Operasional mati total.</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- SECTION 3: RINCIAN GANGGUAN & LAMPIRAN     -->
                            <!-- ========================================== -->
                            <div>
                                <div class="flex items-center gap-3.5 mb-5 pb-3 border-b border-slate-100">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs sm:text-[13px] flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                        03
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h3 class="text-xs sm:text-[13.5px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Rincian Gangguan &amp; Bukti Lampiran</h3>
                                        <p class="text-[11.5px] text-slate-500 leading-snug mt-0.5">Deskripsikan permasalahan secara detail dan sertakan file bukti jika ada</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- Judul Kendala -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Subjek / Judul Kendala Singkat <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="text" name="title" value="{{ old('title') }}" required
                                               placeholder="Masukkan judul atau ringkasan kendala yang dialami"
                                               class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs">
                                    </div>

                                    <!-- Deskripsi Kendala -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Deskripsi Lengkap Masalah &amp; Kronologi <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <textarea name="description" rows="4" required
                                                  placeholder="Jelaskan secara rinci kendala teknis yang dialami, perangkat atau lokasi yang terdampak, serta kronologi kejadian..."
                                                  class="w-full px-4 py-3 text-xs sm:text-[13px] font-medium text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs leading-relaxed">{{ old('description') }}</textarea>
                                    </div>

                                    <!-- Modern File Upload Zone -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Lampiran Screenshot / Foto / Dokumen <span class="text-slate-400 font-normal">(Opsional)</span>
                                        </label>
                                        <div class="border-2 border-dashed border-slate-300 hover:border-[#8F0A0D] rounded-xl p-5 text-center transition-all bg-slate-50/60 hover:bg-white cursor-pointer group"
                                             @click="$refs.fileInput.click()">
                                            <input type="file" name="attachment" x-ref="fileInput" @change="fileName = $refs.fileInput.files[0]?.name || ''" class="hidden" accept=".jpg,.jpeg,.png,.pdf,.docx,.zip">
                                            
                                            <div class="flex flex-col items-center justify-center gap-1.5">
                                                <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-red-50 text-slate-500 group-hover:text-[#8F0A0D] flex items-center justify-center transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                    </svg>
                                                </div>
                                                <span class="text-xs font-bold text-slate-700 group-hover:text-slate-900" 
                                                      x-text="fileName ? fileName : 'Klik untuk memilih file lampiran pendukung'"></span>
                                                <p class="text-[11px] text-slate-400">Format yang didukung: JPG, PNG, PDF, DOCX (Maksimal 10MB)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Submit Footer -->
                        <div class="px-6 py-4 sm:px-7 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-2 text-[11.5px] text-slate-500">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Data terlindungi aman dan langsung masuk ke antrean tim NOC &amp; Engineer IP-NET.</span>
                            </div>

                            <button type="submit" 
                                    class="btn-ipnet-primary px-7 py-3 rounded-xl font-bold text-xs sm:text-[13px] flex items-center justify-center gap-2 cursor-pointer whitespace-nowrap">
                                <span>Kirim Tiket Aduan</span>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Side Information Column (4 of 12 cols - Scrolls naturally with form) -->
                <div class="lg:col-span-4 space-y-5">


                    <!-- Card 1: Alur Penanganan Tiket (Clean & Professional) -->
                    <div class="ipnet-card p-5 sm:p-6">
                        <div class="flex items-center gap-2.5 mb-3.5 pb-2.5 border-b border-slate-100">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Alur Penanganan Aduan</h4>
                                <p class="text-[11px] text-slate-400">Proses tindak lanjut dari laporan Anda</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs text-slate-600">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">1</div>
                                <div>
                                    <div class="font-bold text-slate-800 text-[12px] leading-tight">Tiket Diterbitkan</div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">Sistem menghasilkan nomor unik tiket dan mengirim notifikasi langsung ke tim teknis.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">2</div>
                                <div>
                                    <div class="font-bold text-slate-800 text-[12px] leading-tight">Disposisi &amp; Diagnosa</div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">Teknisi ditugaskan untuk analisis akar masalah (root cause) sesuai prioritas.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">3</div>
                                <div>
                                    <div class="font-bold text-slate-800 text-[12px] leading-tight">Tindakan &amp; Penyelesaian</div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">Perbaikan dilakukan secara remote atau on-site hingga layanan normal kembali.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-bold text-[11px] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">4</div>
                                <div>
                                    <div class="font-bold text-slate-800 text-[12px] leading-tight">Konfirmasi Selesai (Resolved)</div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed mt-1">Laporan penyelesaian dicatat dan tiket ditutup setelah diverifikasi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- TAB 2: PELACAKAN TIKET (TRACK TICKET) - LEBAR & KONSISTEN       -->
        <!-- ============================================================== -->
        <div x-show="activeTab === 'track'" class="space-y-6">
            
            <!-- Search Box Card (Lebar & Proporsional Sesuai Container Utama) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_2px_14px_rgba(0,0,0,0.03)] p-6 sm:p-8">
                <div class="max-w-2xl mx-auto text-center mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-3 border border-red-100 shadow-2xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight">Lacak Status &amp; Progres Tiket Aduan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Pantau perkembangan tindak lanjut dan diagnosa teknisi atas kendala Anda secara real-time.</p>
                </div>

                <form method="GET" action="{{ route('public.helpdesk.index') }}" class="max-w-2xl mx-auto flex flex-col sm:flex-row gap-2.5">
                    <input type="text" name="track" value="{{ request('track') }}" required
                           placeholder="Masukkan nomor tiket Anda (contoh: TCK-202610-0001)"
                           class="flex-1 px-4 py-3 text-xs sm:text-sm font-mono font-bold text-slate-800 bg-white border border-[#CBD5E1] rounded-xl outline-none form-input-focus shadow-2xs uppercase tracking-wider">
                    <button type="submit" 
                            class="btn-ipnet-primary px-8 py-3 rounded-xl font-bold text-xs sm:text-sm flex items-center justify-center gap-2 cursor-pointer shadow-sm shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari Tiket</span>
                    </button>
                </form>
            </div>

            <!-- Tracking Result Section (Full Width 12-Col Ergonomic Grid) -->
            @if($trackingTicket)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_2px_14px_rgba(0,0,0,0.03)] overflow-hidden">
                    
                    <!-- Header Info Bar (Lebar & Jelas) -->
                    <div class="p-6 sm:p-7 bg-slate-50/70 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono text-base sm:text-xl font-black text-slate-900 tracking-tight">{{ $trackingTicket->ticket_number }}</span>
                                @if($trackingTicket->status === 'Resolved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                        Resolved (Selesai Ditangani)
                                    </span>
                                @elseif($trackingTicket->status === 'Closed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                        Closed (Ditutup)
                                    </span>
                                @elseif($trackingTicket->status === 'In Progress')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800 border border-blue-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                        Sedang Dikerjakan (In Progress)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200 shadow-2xs">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        Open (Menunggu Antrean NOC)
                                    </span>
                                @endif
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded-lg bg-slate-200/80 text-slate-700 border border-slate-300">
                                    {{ $trackingTicket->category ?? 'Gangguan Teknis' }}
                                </span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 leading-snug">{{ $trackingTicket->title }}</h2>
                            <p class="text-xs text-slate-500 flex flex-wrap items-center gap-x-2.5 gap-y-1">
                                <span>Klien: <strong class="text-slate-800 font-semibold">{{ $trackingTicket->client_name }}</strong></span>
                                <span class="text-slate-300">•</span>
                                <span>Pelapor: <span class="text-slate-700 font-medium">{{ $trackingTicket->reported_by }}</span></span>
                                @if($trackingTicket->contact_phone)
                                    <span class="text-slate-300">•</span>
                                    <span>Kontak / WA: <span class="font-mono text-slate-700 font-semibold">{{ $trackingTicket->contact_phone }}</span></span>
                                @endif
                            </p>
                        </div>

                        <!-- Tanggal Pengajuan Tiket -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs text-xs text-slate-500 shrink-0 self-start md:self-center">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Didaftarkan: <strong class="text-slate-800 font-semibold font-mono">{{ $trackingTicket->created_at->translatedFormat('d M Y, H:i') }} WIB</strong></span>
                        </div>
                    </div>

                    <!-- Main Grid Content (8 Cols Progres & 4 Cols Detail Ringkasan) -->
                    <div class="p-6 sm:p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                            
                            <!-- Left Column: Stepper & Deskripsi (8 Kolom - Lapang & Bersih) -->
                            <div class="lg:col-span-8 space-y-6">
                                
                                <!-- Timeline Stepper Penanganan -->
                                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 sm:p-6">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-5 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <span>Tahapan Progres Penanganan</span>
                                    </h4>

                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-2 relative">
                                        <!-- Step 1: Diterima -->
                                        <div class="text-center">
                                            <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto mb-2 text-sm font-black shadow-sm shadow-emerald-500/20">✓</div>
                                            <div class="text-xs font-extrabold text-slate-900">Tiket Diterima</div>
                                            <div class="text-[11px] text-slate-400 mt-0.5 font-mono">{{ $trackingTicket->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-[10.5px] text-slate-500 mt-0.5">Tercatat di Portal NOC</div>
                                        </div>

                                        <!-- Step 2: Penugasan -->
                                        <div class="text-center">
                                            @if($trackingTicket->assigned_to)
                                                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto mb-2 text-sm font-black shadow-sm shadow-emerald-500/20">✓</div>
                                                <div class="text-xs font-extrabold text-slate-900">Ditugaskan</div>
                                                <div class="text-[11px] text-slate-700 font-bold truncate mt-0.5">{{ $trackingTicket->assignedEngineer->name ?? 'Teknisi' }}</div>
                                                <div class="text-[10.5px] text-slate-500 mt-0.5">PIC Maintenance</div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-2 text-sm font-bold">2</div>
                                                <div class="text-xs font-bold text-slate-600">Disposisi Teknisi</div>
                                                <div class="text-[11px] text-amber-600 font-semibold mt-0.5">Antrean Tim NOC</div>
                                                <div class="text-[10.5px] text-slate-400 mt-0.5">Menunggu PIC</div>
                                            @endif
                                        </div>

                                        <!-- Step 3: Pengerjaan -->
                                        <div class="text-center">
                                            @if(in_array($trackingTicket->status, ['In Progress', 'Resolved', 'Closed']))
                                                <div class="w-10 h-10 rounded-full {{ in_array($trackingTicket->status, ['Resolved', 'Closed']) ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white animate-pulse' }} flex items-center justify-center mx-auto mb-2 text-sm font-black shadow-sm">
                                                    {{ in_array($trackingTicket->status, ['Resolved', 'Closed']) ? '✓' : '3' }}
                                                </div>
                                                <div class="text-xs font-extrabold text-slate-900">Pengerjaan</div>
                                                <div class="text-[11px] text-blue-600 font-bold mt-0.5">Sedang Ditangani</div>
                                                <div class="text-[10.5px] text-slate-500 mt-0.5">Investigasi Masalah</div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-2 text-sm font-bold">3</div>
                                                <div class="text-xs font-medium text-slate-500">Pengerjaan</div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">-</div>
                                                <div class="text-[10.5px] text-slate-400 mt-0.5">Investigasi Masalah</div>
                                            @endif
                                        </div>

                                        <!-- Step 4: Selesai -->
                                        <div class="text-center">
                                            @if(in_array($trackingTicket->status, ['Resolved', 'Closed']))
                                                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto mb-2 text-sm font-black shadow-sm shadow-emerald-500/20">✓</div>
                                                <div class="text-xs font-extrabold text-emerald-800">Selesai</div>
                                                <div class="text-[11px] text-emerald-600 font-mono font-bold mt-0.5">{{ $trackingTicket->resolved_at ? $trackingTicket->resolved_at->format('d/m/Y H:i') : 'Terverifikasi' }}</div>
                                                <div class="text-[10.5px] text-emerald-700 mt-0.5">Solusi Berhasil</div>
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-2 text-sm font-bold">4</div>
                                                <div class="text-xs font-medium text-slate-500">Selesai</div>
                                                <div class="text-[11px] text-slate-400 mt-0.5">-</div>
                                                <div class="text-[10.5px] text-slate-400 mt-0.5">Konfirmasi Akhir</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Deskripsi Kendala -->
                                <div class="space-y-2">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                        <span>Rincian Kendala yang Dilaporkan</span>
                                    </h4>
                                    <div class="p-4 sm:p-5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed font-medium">
                                        {{ $trackingTicket->description }}
                                    </div>
                                </div>

                                <!-- Catatan Tindakan / Resolusi Teknisi -->
                                @if($trackingTicket->resolution_notes)
                                    <div class="p-5 sm:p-6 rounded-2xl bg-emerald-50/80 border border-emerald-200 space-y-2.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                            <h4 class="text-xs sm:text-sm font-extrabold text-emerald-950">Catatan Tindakan &amp; Solusi Tim Teknisi IP-NET:</h4>
                                        </div>
                                        <p class="text-xs sm:text-sm text-emerald-800 whitespace-pre-line leading-relaxed font-medium">
                                            {{ $trackingTicket->resolution_notes }}
                                        </p>
                                        @if($trackingTicket->root_cause)
                                            <div class="pt-3 border-t border-emerald-200/80 text-xs text-emerald-900 font-semibold flex items-center gap-1.5">
                                                <span class="text-emerald-700">Akar Masalah (Root Cause):</span>
                                                <span class="font-bold text-emerald-950">{{ $trackingTicket->root_cause }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- File Lampiran Bukti -->
                                @if($trackingTicket->attachment_path)
                                    <div class="pt-2">
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">Dokumen / Bukti Lampiran:</h4>
                                        <a href="{{ asset('storage/' . $trackingTicket->attachment_path) }}" target="_blank"
                                           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:border-[#8F0A0D] hover:text-[#8F0A0D] transition shadow-2xs">
                                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span>Buka / Download File Lampiran Bukti</span>
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column: Ringkasan Spesifikasi & Kontak (4 Kolom - Rapi & Konsisten) -->
                            <div class="lg:col-span-4 space-y-5">
                                
                                <!-- Card Detail Spesifikasi Tiket -->
                                <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2 border-b border-slate-200 flex items-center justify-between">
                                        <span>Informasi Tiket</span>
                                        <span class="text-[10.5px] font-medium text-slate-400">Detail Ringkasan</span>
                                    </h4>

                                    <div class="space-y-3.5 text-xs">
                                        <div>
                                            <span class="text-slate-400 font-medium block text-[11px]">Prioritas Kendala:</span>
                                            <span class="font-bold text-slate-900 inline-block mt-0.5 px-2.5 py-0.5 rounded-md 
                                                {{ $trackingTicket->priority === 'P1 - Critical' ? 'bg-red-100 text-red-800' : ($trackingTicket->priority === 'P2 - Major' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200/80 text-slate-800') }}">
                                                {{ $trackingTicket->priority }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 font-medium block text-[11px]">Kategori Layanan:</span>
                                            <span class="font-bold text-slate-900">{{ $trackingTicket->category ?? 'Gangguan Teknis' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 font-medium block text-[11px]">Waktu Kejadian:</span>
                                            <span class="font-bold text-slate-900 font-mono">{{ $trackingTicket->incident_date ? $trackingTicket->incident_date->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 font-medium block text-[11px]">Teknisi Penanggung Jawab (PIC):</span>
                                            <span class="font-bold text-slate-900">{{ $trackingTicket->assignedEngineer->name ?? 'Tim NOC / Antrean Maintenance' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Hubungi Tim NOC Cepat -->
                                <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-3">
                                    <div>
                                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Butuh Eskalasi Segera?</h4>
                                        <p class="text-[11px] text-slate-500 leading-relaxed mt-1">Jika kendala berdampak kritis pada operasional utama, hubungi dispatcher NOC kami:</p>
                                    </div>
                                    <div class="space-y-2 pt-1">
                                        <a href="https://wa.me/6281234567890?text=Halo%20NOC%20IP-NET,%20mohon%20bantu%20eskalasi%20tiket%20{{ $trackingTicket->ticket_number }}" target="_blank"
                                           class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-xs">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                            <span>WhatsApp NOC Dispatcher</span>
                                        </a>
                                        <a href="tel:02112345678" 
                                           class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-2 transition">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            <span>Hotline (021) 1234-5678</span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @elseif(request('track'))
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-[0_2px_14px_rgba(0,0,0,0.03)] p-10 text-center max-w-xl mx-auto">
                    <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3.5">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h4 class="text-base font-extrabold text-slate-900">Tiket Tidak Ditemukan</h4>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">Nomor tiket "<span class="font-mono font-bold text-slate-800">{{ request('track') }}</span>" tidak ditemukan di sistem. Pastikan Anda memasukkan nomor tiket dengan format yang benar (contoh: <strong>TCK-202610-0001</strong>).</p>
                    <a href="{{ route('public.helpdesk.index') }}?tab=track" class="inline-block mt-4 px-5 py-2 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition">
                        Coba Nomor Tiket Lain
                    </a>
                </div>
            @endif
        </div>

    </main>

    <!-- ======================================================== -->
    <!-- FOOTER (FULL WIDTH & CLEAN)                              -->
    <!-- ======================================================== -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500 space-y-1">
            <p class="font-bold text-slate-800">© 2026 PT IP Network Solusindo. All rights reserved.</p>
            <p class="text-[11.5px] text-slate-400">Integrated Managed Service &amp; Technical Helpdesk Support Portal</p>
        </div>
    </footer>

    <script>
        function clientPortal() {
            return {
                activeTab: '{{ request("track") ? "track" : "create" }}',
                selectedCategory: '{{ old("category", "") }}',
                selectedPriority: '{{ old("priority", "") }}',
                fileName: '',
                copied: false
            }
        }
    </script>
</body>
</html>
