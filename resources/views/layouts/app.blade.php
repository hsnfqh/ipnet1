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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'wms-red': {
                            950: '#5C0A13',
                            900: '#7A0D18',
                            800: '#96101F',
                            700: '#AF1424',
                            600: '#C81E2C',
                            550: '#D62E3C',
                            500: '#E14B54',
                            300: '#EE9299',
                            200: '#F5BEC2',
                            100: '#FBDEE0',
                            50: '#FDF1F2',
                        },
                        'wms-ink': {
                            950: '#0E0D12',
                            900: '#17151C',
                            800: '#26232C',
                            700: '#3D3A44',
                            600: '#57545F',
                            500: '#75727C',
                            400: '#948F99',
                            300: '#B7B3BB',
                            200: '#DCDADF',
                        },
                        'wms-paper': '#F8F7F6',
                        'wms-paper-dim': '#F1F0EE',
                        'wms-line': '#E7E5E3',
                        'wms-line2': '#EFEDEB',
                        'wms-green': '#1B7A46',
                        'wms-green-bg': '#E4F3EA',
                        'wms-amber': '#9A6206',
                        'wms-amber-bg': '#FAF0D9',
                        'wms-blue': '#25538C',
                        'wms-blue-bg': '#E8F0F9',
                        'wms-gray': '#75727C',
                        'wms-gray-bg': '#EFEDEC',
                    },
                    fontFamily: {
                        sans: ['"Inter"', 'sans-serif'],
                        display: ['"Inter"', 'sans-serif'],
                        body: ['"Inter"', 'sans-serif'],
                        mono: ['"IBM Plex Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'wms-sm': '0 1px 3px rgba(14,13,18,0.04), 0 1px 2px rgba(14,13,18,0.02)',
                        'wms-md': '0 4px 20px -2px rgba(14,13,18,0.06), 0 2px 6px -1px rgba(14,13,18,0.03)',
                        'wms-lg': '0 16px 36px -4px rgba(14,13,18,0.10), 0 6px 12px -2px rgba(14,13,18,0.04)',
                        'wms-red': '0 8px 24px -2px rgba(200,30,44,0.22)',
                        'wms-card': '0 2px 8px -2px rgba(14,13,18,0.05), 0 1px 3px rgba(14,13,18,0.03)',
                        'wms-card-hover': '0 12px 28px -6px rgba(14,13,18,0.09), 0 4px 10px -2px rgba(14,13,18,0.04)',
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
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
            background: #F8FAFC; 
            background-image: radial-gradient(at 0% 0%, rgba(200, 30, 44, 0.015) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(37, 83, 140, 0.015) 0px, transparent 50%);
            font-family: 'Inter', sans-serif; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            color: #17151C;
        }
        
        /* Modern Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #D5D2CD;
            border-radius: 999px;
            transition: background 0.2s ease;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #A3A09A;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        
        /* Cards & Surfaces */
        .wms-card { 
            background: #FFFFFF; 
            border: 1px solid #EAE8E5; 
            border-radius: 14px; 
            box-shadow: 0 1px 3px rgba(14,13,18,0.04), 0 1px 2px rgba(14,13,18,0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .wms-card-hover {
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .wms-card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -6px rgba(14,13,18,0.09), 0 4px 12px -2px rgba(14,13,18,0.04);
            border-color: #DDD9D5;
        }

        .wms-glass {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .wms-input { 
            width: 100%; 
            padding: 9px 12px; 
            border-radius: 10px; 
            border: 1px solid #E4E1DD; 
            font-size: 13.5px; 
            color: #17151C; 
            outline: none; 
            background: #FFFFFF;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .wms-input:focus { 
            border-color: #C81E2C !important; 
            box-shadow: 0 0 0 3.5px rgba(200, 30, 44, 0.1) !important; 
            background: #FFFFFF;
        }
        
        .wms-btn { 
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); 
            cursor: pointer; 
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .wms-btn:hover { 
            filter: brightness(1.05); 
            transform: translateY(-1px);
        }
        .wms-btn:active { 
            transform: translateY(1px) scale(0.99); 
        }

        /* Official IPNET Red Gradient Buttons */
        .btn-ipnet-primary, .btn-ipnet-gradient {
            background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 4px 14px rgba(143, 10, 13, 0.28) !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-ipnet-primary:hover, .btn-ipnet-gradient:hover {
            background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(143, 10, 13, 0.38) !important;
        }
        .btn-ipnet-primary:active, .btn-ipnet-gradient:active {
            transform: translateY(0) !important;
            filter: brightness(0.95) !important;
        }
        
        /* Modern Keyframe Animations */
        @keyframes popInSpring {
            0% {
                opacity: 0;
                transform: scale(0.95) translateY(14px);
            }
            70% {
                opacity: 1;
                transform: scale(1.01) translateY(-2px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes fadeInUpSmooth {
            0% {
                opacity: 0;
                transform: translateY(16px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInSmooth {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes pulseGlow {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
        }

        @keyframes radarPing {
            0% {
                transform: scale(0.9);
                opacity: 0.8;
            }
            80%, 100% {
                transform: scale(2.2);
                opacity: 0;
            }
        }

        @keyframes shimmerSweep {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .animate-pop-in {
            animation: popInSpring 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-fade-in {
            animation: fadeInSmooth 0.35s ease-out both;
        }

        .animate-fade-in-up {
            animation: fadeInUpSmooth 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Staggered Delays for Cascade Pop In */
        .stagger-1 { animation-delay: 0.04s !important; }
        .stagger-2 { animation-delay: 0.08s !important; }
        .stagger-3 { animation-delay: 0.12s !important; }
        .stagger-4 { animation-delay: 0.16s !important; }
        .stagger-5 { animation-delay: 0.20s !important; }
        .stagger-6 { animation-delay: 0.24s !important; }
        .stagger-7 { animation-delay: 0.28s !important; }
        .stagger-8 { animation-delay: 0.32s !important; }

        /* Modern Micro-interactions */
        .hover-lift {
            transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-lift:hover {
            transform: translateY(-2.5px);
            box-shadow: 0 10px 24px -4px rgba(14,13,18,0.08);
        }

        .hover-accent-row {
            position: relative;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-accent-row::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: #C81E2C;
            border-radius: 0 3px 3px 0;
            opacity: 0;
            transform: scaleY(0.4);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hover-accent-row:hover::before {
            opacity: 1;
            transform: scaleY(1);
        }

        .pulse-badge {
            animation: pulseGlow 2s infinite ease-in-out;
        }

        .ping-ring {
            position: absolute;
            inset: -2px;
            border-radius: 9999px;
            background: rgba(200, 30, 44, 0.4);
            animation: radarPing 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
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

        // Global Chart.js Configuration for Modern Aesthetics
        if (window.Chart) {
            Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
            Chart.defaults.color = '#75727C';
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(23, 21, 28, 0.94)';
            Chart.defaults.plugins.tooltip.titleFont = { family: "'Plus Jakarta Sans', sans-serif", weight: '700', size: 12 };
            Chart.defaults.plugins.tooltip.bodyFont = { family: "'Plus Jakarta Sans', sans-serif", weight: '500', size: 11.5 };
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.cornerRadius = 8;
            Chart.defaults.plugins.tooltip.boxPadding = 4;
            Chart.defaults.plugins.tooltip.usePointStyle = true;
            Chart.defaults.animation = {
                duration: 950,
                easing: 'easeOutQuart'
            };
        }

        // Smooth Rolling Number Counter Animation
        function initNumberCounters() {
            const counterElements = document.querySelectorAll('.metric-counter, [data-counter]');
            counterElements.forEach((el) => {
                const targetText = el.getAttribute('data-target') || el.innerText.trim();
                const match = targetText.match(/^([^\d]*)([\d,.]+)([^\d]*)$/);
                if (!match) return;

                const prefix = match[1] || '';
                const numStr = match[2].replace(/,/g, '');
                const suffix = match[3] || '';
                const targetNum = parseFloat(numStr);

                if (isNaN(targetNum)) return;

                const isDecimal = numStr.includes('.');
                const decimals = isDecimal ? numStr.split('.')[1].length : 0;
                const duration = 1100; // ms
                const startTime = performance.now();

                function update(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    // Ease out quartic curve for ultra-smooth deceleration
                    const easeProgress = 1 - Math.pow(1 - progress, 4);
                    const currentVal = targetNum * easeProgress;

                    const formattedNumber = isDecimal 
                        ? currentVal.toFixed(decimals) 
                        : Math.floor(currentVal).toLocaleString();

                    el.textContent = `${prefix}${formattedNumber}${suffix}`;

                    if (progress < 1) {
                        requestAnimationFrame(update);
                    } else {
                        el.textContent = targetText;
                    }
                }

                requestAnimationFrame(update);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initNumberCounters();
        });

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