<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WMS - IPN Field Workforce')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js - PASTIKAN INI -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { 
            margin: 0 !important; 
            padding: 0 !important; 
            height: 100%; 
            width: 100%; 
            background: #F7F6F5; 
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
        }
        
        .wms-card { background: white; border: 1px solid #E7E5E3; border-radius: 12px; box-shadow: 0 1px 2px rgba(14,13,18,0.05); }

        .wms-input { width: 100%; padding: 9px 11px; border-radius: 8px; border: 1px solid #E7E5E3; font-size: 14px; color: #17151C; outline: none; background: white; }
        .wms-input:focus { border-color: #C81E2C !important; box-shadow: 0 0 0 3px #FDF1F2 !important; }
        .wms-btn { transition: all 0.12s ease; cursor: pointer; }
        .wms-btn:hover { filter: brightness(1.06); }
        .wms-btn:active { transform: translateY(1px); }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.2s ease; }
        .animate-fade-in-up { animation: fadeInUp 0.18s ease; }
    </style>
    
    @stack('styles')
</head>
<body>
    {{-- Alpine root --}}
    <div x-data="app()" x-init="init()">
        @yield('content')

        {{-- ======= WHAT'S NEW MODAL (VERSIONED) ======= --}}
        <div
            id="whats-new-modal"
            x-data="whatsNewModal()"
            x-show="$store.changelog.open"
            x-cloak
            style="position:fixed; inset:0; z-index:999999; background:rgba(14,13,18,0.55);"
            @click.self="$store.changelog.hide()">

            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:560px; max-width:calc(100vw - 32px); max-height:90vh; border-radius:20px; box-shadow:0 28px 70px rgba(14,13,18,0.28); background:white; display:flex; flex-direction:column; overflow:hidden;"
                x-show="$store.changelog.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                {{-- Header --}}
                <div style="background:linear-gradient(135deg, #83101D 0%, #5C0A13 100%); padding:20px 24px 0; position:relative; flex-shrink:0;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
                        <div>
                            <div style="font-size:10px; font-weight:700; color:rgba(255,255,255,0.55); letter-spacing:1.4px; text-transform:uppercase; margin-bottom:4px;">Riwayat Update</div>
                            <h2 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; color:white;">Yang Baru di WMS</h2>
                        </div>
                        <button @click="$store.changelog.hide()" style="width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.15s; margin-top:2px;" onmouseover="this.style.background='rgba(255,255,255,0.28)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                            <svg style="width:14px; height:14px; color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Version Tabs --}}
                    <div style="display:flex; gap:4px; overflow-x:auto; scrollbar-width:none;">
                        <template x-for="(ver, idx) in versions" :key="idx">
                            <button
                                @click="activeVersion = idx"
                                :style="activeVersion === idx
                                    ? 'background:white; color:#83101D; font-weight:700; border-radius:10px 10px 0 0; padding:8px 16px; border:none; cursor:pointer; font-size:12px; font-family:\'Inter\',sans-serif; white-space:nowrap; transition:all 0.15s;'
                                    : 'background:rgba(255,255,255,0.12); color:rgba(255,255,255,0.75); font-weight:500; border-radius:10px 10px 0 0; padding:8px 16px; border:none; cursor:pointer; font-size:12px; font-family:\'Inter\',sans-serif; white-space:nowrap; transition:all 0.15s;'"
                                x-text="ver.label">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Body --}}
                <div style="overflow-y:auto; flex:1; min-height:0;">
                    <template x-if="versions[activeVersion]">
                        <div x-key="activeVersion">
                            {{-- Version Info Bar --}}
                            <div style="padding:14px 24px 10px; background:#FAFAF9; border-bottom:1px solid #F0EFED; display:flex; align-items:center; gap:10px;">
                                <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#83101D,#5C0A13); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <div style="font-size:13px; font-weight:700; color:#17151C;" x-text="versions[activeVersion].label"></div>
                                    <div style="font-size:11px; color:#75727C;" x-text="versions[activeVersion].date"></div>
                                </div>
                                <div style="margin-left:auto;">
                                    <span style="background:#FEF1F2; color:#C81E2C; font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px;" x-text="versions[activeVersion].fixes.length + ' Perbaikan'"></span>
                                </div>
                            </div>

                            {{-- Fixes List --}}
                            <div style="padding:16px 24px 20px; display:flex; flex-direction:column; gap:10px;">
                                <template x-for="(fix, i) in versions[activeVersion].fixes" :key="i">
                                    <div style="display:flex; gap:13px; align-items:flex-start; padding:13px 14px; border-radius:10px; background:white; border:1px solid #EDEBE9; transition:box-shadow 0.15s;" onmouseover="this.style.boxShadow='0 2px 8px rgba(14,13,18,0.07)'" onmouseout="this.style.boxShadow='none'">
                                        <div style="width:24px; height:24px; border-radius:50%; background:#FEF1F2; color:#C81E2C; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;" x-text="i + 1"></div>
                                        <div>
                                            <div style="font-size:13px; font-weight:600; color:#17151C; margin-bottom:3px;" x-text="fix.title"></div>
                                            <div style="font-size:12px; color:#75727C; line-height:1.55;" x-text="fix.desc"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Footer --}}
                <div style="padding:14px 24px; border-top:1px solid #F0EFED; flex-shrink:0; display:flex; align-items:center; gap:10px;">
                    <div style="font-size:12px; color:#A5A1AA;">
                        <span x-text="'v' + (activeVersion > 0 ? versions[activeVersion-1]?.label : '')"></span>
                    </div>
                    <button @click="$store.changelog.hide()" style="margin-left:auto; padding:10px 24px; border-radius:10px; background:#C81E2C; color:white; border:none; font-weight:600; font-size:13px; cursor:pointer; transition:all 0.15s ease; font-family:'Inter',sans-serif;" onmouseover="this.style.filter='brightness(1.08)'" onmouseout="this.style.filter='brightness(1)'">
                        Oke, Mengerti
                    </button>
                </div>

            </div>
        </div>
        {{-- ======= END WHAT'S NEW MODAL (VERSIONED) ======= --}}
    </div>
    
    <script>
        // Register Service Worker for Desktop Push Notifications
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then((reg) => {
                    window.swRegistration = reg;
                }).catch((err) => {
                    console.warn('ServiceWorker registration failed: ', err);
                });
            });
        }

        // Notification permission diminta saat user interaksi dengan tombol notifikasi
        // (tidak auto-prompt saat klik pertama agar tidak memblokir UI)

        document.addEventListener('alpine:init', () => {
            // Global store untuk changelog modal
            Alpine.store('changelog', {
                open: false,
                show() { this.open = true; },
                hide() { this.open = false; }
            });

            Alpine.data('app', () => ({
                sidebarCollapsed: false,
                init() {
                    // Load from localStorage
                    const saved = localStorage.getItem('sidebarCollapsed');
                    if (saved !== null) {
                        this.sidebarCollapsed = JSON.parse(saved);
                    }
                },
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    localStorage.setItem('sidebarCollapsed', JSON.stringify(this.sidebarCollapsed));
                }
            }));

            // What's New Modal
            Alpine.data('whatsNewModal', () => ({
                open: false,
                activeVersion: 0,
                versions: [
                    {
                        label: 'v1.0',
                        date: '1–5 Agustus 2026',
                        fixes: [
                            { title: 'Rilis awal WMS', desc: 'Sistem manajemen workforce pertama kali diluncurkan dengan fitur dasar manajemen project dan task.' },
                            { title: 'Autentikasi pengguna', desc: 'Sistem login, register, dan manajemen role (Admin, Lead Engineer, Engineer) tersedia.' },
                            { title: 'Dashboard utama', desc: 'Halaman dashboard dengan ringkasan data project, task, dan jadwal kerja.' },
                            { title: 'Manajemen project & task', desc: 'Fitur CRUD project dan task beserta assignment ke engineer.' },
                        ]
                    },
                    {
                        label: 'v1.1',
                        date: '6–10 Agustus 2026',
                        fixes: [
                            { title: 'Fitur export data ke Excel', desc: 'Penambahan fitur export data pengguna dan jadwal kerja ke format Excel (.xlsx).' },
                            { title: 'Perbaikan sidebar tidak ikut scroll', desc: 'Sidebar kini tetap diam saat konten halaman di-scroll, diterapkan di seluruh halaman aplikasi.' },
                            { title: 'Pembatasan hak akses status task', desc: 'Perubahan status task hanya dapat dilakukan oleh Lead Engineer. Engineer hanya bisa memperbarui progress dan upload dokumentasi.' },
                            { title: 'Upload dokumentasi task', desc: 'Engineer dapat mengunggah file dokumentasi (foto/PDF) sebagai bukti pekerjaan pada setiap task.' },
                        ]
                    },
                    {
                        label: 'v1.2',
                        date: '11–12 Agustus 2026',
                        fixes: [
                            { title: 'Peningkatan tampilan sidebar', desc: 'Efek hover dan animasi pada sidebar diperhalus.' },
                        ]
                    },
                ],
                init() {
                    // Set ke versi terbaru saat dibuka
                    this.activeVersion = this.versions.length - 1;
                },
                close() {
                    this.$store.changelog.open = false;
                }
            }));
        });
    </script>
    
    @stack('scripts')
</body>
</html>