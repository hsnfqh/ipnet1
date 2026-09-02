<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Field System Management - IPN')</title>
    
    <!-- Favicon IP Network Solusindo -->
    <link rel="icon" type="image/png" href="{{ asset('images/ipnet1.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/ipnet1.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/ipnet1.png') }}">
    
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
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
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
        });
    </script>
    
    @stack('scripts')
</body>
</html>