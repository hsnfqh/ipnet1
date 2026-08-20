@extends('layouts.app')

@section('title', 'Presensi')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Presensi Harian'])

        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-4" x-data="attendanceManager()" x-init="init()">

            {{-- ── 1. STATUS CARDS (Clean, Professional White Cards) ────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 sm:gap-4">
                
                {{-- Card 1: Clock In --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow border border-[#E7E5E3]">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">Clock In (Masuk)</span>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                             :class="clockIn ? (clockIn.is_within_range ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600') : 'bg-[#F1F0EE] text-[#75727C]'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="font-display text-[28px] sm:text-[32px] font-bold text-[#17151C] leading-none tracking-tight font-mono" x-text="clockIn ? clockIn.time : '--:--'">--:--</div>
                        <div class="mt-2 flex items-center gap-2">
                            <template x-if="clockIn">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-semibold rounded-md"
                                      :class="clockIn.is_within_range ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="clockIn.is_within_range ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                    <span x-text="clockIn.is_within_range ? 'Hadir (Sesuai Radius)' : 'Luar Jangkauan'"></span>
                                </span>
                            </template>
                            <template x-if="!clockIn">
                                <span class="text-[12px] text-[#948F99]">Belum Clock In hari ini</span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Clock Out --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow border border-[#E7E5E3]">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">Clock Out (Pulang)</span>
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                             :class="clockOut ? 'bg-emerald-50 text-emerald-600' : 'bg-[#F1F0EE] text-[#75727C]'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="font-display text-[28px] sm:text-[32px] font-bold text-[#17151C] leading-none tracking-tight font-mono" x-text="clockOut ? clockOut.time : '--:--'">--:--</div>
                        <div class="mt-2 flex items-center gap-2">
                            <template x-if="clockOut">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-semibold rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span x-text="'Durasi: ' + (duration || '-')"></span>
                                </span>
                            </template>
                            <template x-if="!clockOut && clockIn">
                                <span class="text-[12px] text-[#75727C]">Sedang bertugas (Jam pulang 17:30)</span>
                            </template>
                            <template x-if="!clockOut && !clockIn">
                                <span class="text-[12px] text-[#948F99]">Belum Clock In</span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Lokasi & GPS --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow border border-[#E7E5E3]">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">Lokasi & GPS Real-Time</span>
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-2.5">
                        <div class="font-display text-[22px] sm:text-[24px] font-bold text-[#17151C] leading-tight truncate">
                            <template x-if="currentDistance !== null">
                                <span><span x-text="currentDistance" class="font-mono">0</span> <span class="text-[13px] font-semibold text-[#75727C]">m dari kantor</span></span>
                            </template>
                            <template x-if="currentDistance === null">
                                <span class="text-[14px] text-[#75727C] font-normal" x-text="gpsStatus">Mendeteksi...</span>
                            </template>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <template x-if="currentLat">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[11px] font-semibold rounded-md"
                                      :class="withinRange ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="withinRange ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                    <span x-text="withinRange ? 'Dalam Radius (Maks 100m)' : 'Luar Radius Kantor'"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── 2. ACTION CONTROLS & CAMERA PANEL ──────────────────────────────── --}}
            <div class="wms-card p-5 bg-white border border-[#E7E5E3] shadow-sm space-y-4">
                
                {{-- Tombol Aksi Utama --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    
                    <div class="flex flex-wrap items-center gap-3">
                        {{-- Tombol Clock In --}}
                        <template x-if="!clockIn">
                            <button type="button" 
                                    @click="handleAction('clock_in')"
                                    :disabled="loading || !currentLat"
                                    class="px-5 py-3 rounded-xl bg-[#C81E2C] hover:bg-[#A31622] text-white font-semibold text-[13.5px] transition shadow-[0_4px_14px_rgba(200,30,44,0.25)] flex items-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <template x-if="loading && actionType === 'clock_in'">
                                    <svg class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!(loading && actionType === 'clock_in')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </template>
                                <span x-text="loading && actionType === 'clock_in' ? 'Memproses Clock In...' : 'Clock In Sekarang'"></span>
                            </button>
                        </template>

                        {{-- Tombol Clock Out --}}
                        <template x-if="clockIn && !clockOut">
                            <button type="button" 
                                    @click="handleAction('clock_out')"
                                    :disabled="loading || !currentLat"
                                    class="px-5 py-3 rounded-xl bg-[#17151C] hover:bg-[#2E2C34] text-white font-semibold text-[13.5px] transition shadow-md flex items-center gap-2.5 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <template x-if="loading && actionType === 'clock_out'">
                                    <svg class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                </template>
                                <template x-if="!(loading && actionType === 'clock_out')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </template>
                                <span x-text="loading && actionType === 'clock_out' ? 'Memproses Clock Out...' : 'Clock Out Sekarang'"></span>
                            </button>
                        </template>

                        {{-- Status Selesai --}}
                        <template x-if="clockIn && clockOut">
                            <div class="px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-semibold flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Presensi hari ini sudah lengkap & selesai.
                            </div>
                        </template>

                        {{-- Tombol Toggle Selfie Camera --}}
                        <template x-if="!(clockIn && clockOut)">
                            <button type="button" 
                                    @click="toggleCamera()"
                                    class="px-4 py-3 rounded-xl border border-[#E7E5E3] bg-white hover:bg-[#F8F7F6] text-[#17151C] font-semibold text-[13px] transition flex items-center gap-2 shadow-sm cursor-pointer"
                                    :class="cameraOpen ? 'border-[#C81E2C] text-[#C81E2C] bg-[#FDF1F2]' : ''">
                                <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="cameraOpen ? 'Tutup Kamera' : (capturedPhoto ? 'Foto Terambil ✓' : 'Tambah Foto Selfie')"></span>
                            </button>
                        </template>
                    </div>

                    {{-- GPS Status Warning / Hint --}}
                    <div class="text-[12px] text-[#75727C]">
                        <div x-show="!currentLat && !gpsError" class="flex items-center gap-2 text-amber-600">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Mencari koordinat GPS...
                        </div>
                        <div x-show="gpsError" x-cloak class="text-[#C81E2C] font-medium" x-text="gpsError"></div>
                    </div>
                </div>

                {{-- Viewfinder Kamera Selfie & Note (Expandable) --}}
                <div x-show="cameraOpen" x-cloak class="pt-4 border-t border-[#EFEDEB] space-y-3.5 animate-fade-in">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Video / Photo Box --}}
                        <div class="bg-[#17151C] rounded-2xl p-2.5 overflow-hidden flex flex-col items-center justify-center max-w-sm">
                            <video id="att-video" autoplay playsinline class="w-full rounded-xl aspect-[4/3] object-cover bg-black" x-ref="video" x-show="!capturedPhoto"></video>
                            <canvas id="att-canvas" x-ref="canvas" class="hidden"></canvas>
                            <img :src="capturedPhoto" x-show="capturedPhoto" class="w-full rounded-xl aspect-[4/3] object-cover border-2 border-emerald-500" alt="Selfie preview">
                            
                            <div class="flex items-center gap-2.5 mt-2.5 w-full justify-center">
                                <button type="button" 
                                        @click="capturePhoto()" 
                                        x-show="!capturedPhoto"
                                        class="px-4 py-1.5 rounded-lg bg-[#C81E2C] text-white text-[12px] font-semibold hover:bg-[#A31622] transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Ambil Foto
                                </button>
                                <button type="button" 
                                        @click="retakePhoto()" 
                                        x-show="capturedPhoto"
                                        class="px-4 py-1.5 rounded-lg bg-white/20 text-white text-[12px] font-semibold hover:bg-white/30 transition cursor-pointer">
                                    Ulangi Foto
                                </button>
                            </div>
                        </div>

                        {{-- Catatan Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider">Catatan Lokasi / Pekerjaan (Opsional)</label>
                            <textarea x-model="note" 
                                      rows="4" 
                                      placeholder="Contoh: Kunjungan on-site maintenance router di gedung klien..."
                                      class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── 3. RIWAYAT PRESENSI 7 HARI TERAKHIR ────────────────────────────── --}}
            <div class="wms-card overflow-hidden bg-white shadow-sm border border-[#E7E5E3]">
                <div class="px-5 py-3.5 border-b border-[#EFEDEB] bg-[#F8F7F6] flex items-center justify-between">
                    <span class="text-[11.5px] font-bold text-[#75727C] uppercase tracking-wider">Riwayat Presensi (7 Hari Terakhir)</span>
                    <span class="text-[12px] font-medium text-[#948F99]">Otomatis tersimpan</span>
                </div>

                @if($history->isEmpty())
                    <div class="py-12 text-center text-[#75727C]">
                        <div class="w-10 h-10 rounded-xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-2.5 text-[#75727C]">
                            <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-[13.5px] font-semibold text-[#17151C]">Belum ada riwayat presensi</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[13px]">
                            <thead>
                                <tr class="border-b border-[#EFEDEB] bg-[#FAF9F8] text-[11px] font-bold text-[#75727C] uppercase tracking-wider">
                                    <th class="py-3 px-4 w-40">Tanggal & Hari</th>
                                    <th class="py-3 px-4 text-center w-36">Clock In</th>
                                    <th class="py-3 px-4 text-center w-36">Clock Out</th>
                                    <th class="py-3 px-4 text-center w-28">Durasi</th>
                                    <th class="py-3 px-4 text-center w-40">Status Presensi</th>
                                    <th class="py-3 px-4 min-w-[200px]">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#EFEDEB]">
                                @php
                                    $daysMap = [
                                        'Sunday'    => 'Minggu',
                                        'Monday'    => 'Senin',
                                        'Tuesday'   => 'Selasa',
                                        'Wednesday' => 'Rabu',
                                        'Thursday'  => 'Kamis',
                                        'Friday'    => 'Jumat',
                                        'Saturday'  => 'Sabtu',
                                    ];
                                @endphp

                                @foreach($history as $date => $records)
                                    @php
                                        $ci = $records->firstWhere('type', 'clock_in');
                                        $co = $records->firstWhere('type', 'clock_out');
                                        $isToday = $date === $today;
                                        $parsedDate = \Carbon\Carbon::parse($date);
                                        $dayIndo = $daysMap[$parsedDate->format('l')] ?? $parsedDate->format('l');

                                        $durationStr = '—';
                                        if ($ci && $co) {
                                            $mins = \Carbon\Carbon::parse($ci->created_at)->diffInMinutes(\Carbon\Carbon::parse($co->created_at));
                                            $h = intdiv($mins, 60);
                                            $m = $mins % 60;
                                            $durationStr = "{$h}j {$m}m";
                                        }
                                    @endphp
                                    <tr class="hover:bg-[#FBFBFA] transition-colors {{ $isToday ? 'bg-red-50/20' : '' }}">
                                        {{-- Tanggal & Hari --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div>
                                                    <div class="font-semibold text-[#17151C] text-[13.5px]">{{ $parsedDate->format('d M Y') }}</div>
                                                    <div class="text-[11.5px] font-medium text-[#75727C]">{{ $dayIndo }}</div>
                                                </div>
                                                @if($isToday)
                                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-[#C81E2C] text-white">Hari Ini</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Clock In --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($ci)
                                                <span class="inline-flex items-center font-mono font-medium text-[12.5px] text-[#17151C] bg-[#F1F0EE] px-2.5 py-1 rounded-lg">
                                                    {{ \Carbon\Carbon::parse($ci->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-[#948F99] font-mono text-[12px]">—</span>
                                            @endif
                                        </td>

                                        {{-- Clock Out --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($co)
                                                <span class="inline-flex items-center font-mono font-medium text-[12.5px] text-[#17151C] bg-[#F1F0EE] px-2.5 py-1 rounded-lg">
                                                    {{ \Carbon\Carbon::parse($co->created_at)->setTimezone('Asia/Jakarta')->format('H:i') }}
                                                </span>
                                            @else
                                                <span class="text-[#948F99] font-mono text-[12px]">—</span>
                                            @endif
                                        </td>


                                        {{-- Durasi --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="font-bold text-[#17151C] text-[13px]">{{ $durationStr }}</span>
                                        </td>

                                        {{-- Status Presensi --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($ci)
                                                @if($ci->is_within_range)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Hadir (Sesuai Radius)
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                        Luar Jangkauan ({{ $ci->distance_meters ?? $ci->distance }}m)
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11px] font-semibold rounded-lg bg-[#F1F0EE] text-[#75727C]">
                                                    Tidak Hadir
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Catatan --}}
                                        <td class="py-3.5 px-4">
                                            <span class="text-[#3D3A44] text-[12.5px] leading-snug">
                                                {{ ($ci && $ci->notes) ? $ci->notes : (($co && $co->notes) ? $co->notes : '—') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ── 4. MODAL KONFIRMASI LUAR JANGKAUAN ────────────────────────────── --}}
            <template x-teleport="body">
                <div x-show="outOfRangeModal"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/65 z-[99999] flex items-center justify-center p-4 backdrop-blur-sm"
                     @click.self="outOfRangeModal = false"
                     style="display: none;">

                    <div class="bg-white rounded-2xl w-[480px] max-w-full overflow-hidden shadow-[0_24px_64px_rgba(14,13,18,0.35)] border border-[#E7E5E3] text-left animate-fade-in-up">
                        
                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-[#EFEDEB] flex items-center justify-between bg-[#FBFBFA]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <h3 class="font-display text-[15.5px] font-bold text-[#17151C]">Presensi di Luar Radius Kantor</h3>
                            </div>
                            <button type="button" @click="outOfRangeModal = false" class="text-[#75727C] hover:text-[#17151C] p-1 rounded-lg hover:bg-[#F1F0EE] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-3.5 bg-amber-50/80 border border-amber-200 rounded-xl text-[13px] text-amber-900 font-semibold">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Jarak Anda saat ini:</span>
                                </div>
                                <div class="font-mono text-[14px] text-amber-950 font-bold" x-text="currentDistance + ' meter'"></div>
                            </div>

                            <p class="text-[13.5px] text-[#3D3A44] leading-relaxed text-center">
                                Posisi GPS Anda terdeteksi melebihi batas radius kantor (<strong>100 meter</strong>). Absensi tetap dapat disimpan dan otomatis diberi catatan status <strong class="text-amber-800">Luar Jangkauan</strong>.
                            </p>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 border-t border-[#EFEDEB] bg-[#FAF9F8] flex items-center justify-end gap-3">
                            <button type="button" 
                                    @click="outOfRangeModal = false"
                                    :disabled="loading"
                                    class="px-4 py-2 rounded-xl bg-white border border-[#E7E5E3] text-[#3D3A44] font-semibold text-[13px] hover:bg-[#F8F7F6] transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="confirmProceed()"
                                    :disabled="loading"
                                    class="px-5 py-2 rounded-xl bg-[#C81E2C] hover:bg-[#A31622] text-white font-semibold text-[13px] transition shadow-[0_4px_12px_rgba(200,30,44,0.25)] flex items-center gap-2 cursor-pointer">
                                <span x-text="loading ? 'Memproses...' : 'Ya, Tetap Absen'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- ── 5. MODAL PERINGATAN PULANG LEBIH AWAL (< 17:30) ────────────────── --}}
            <template x-teleport="body">
                <div x-show="earlyClockOutModal"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/65 z-[99999] flex items-center justify-center p-4 backdrop-blur-sm"
                     @click.self="earlyClockOutModal = false"
                     style="display: none;">

                    <div class="bg-white rounded-2xl w-[480px] max-w-full overflow-hidden shadow-[0_24px_64px_rgba(14,13,18,0.35)] border border-[#E7E5E3] text-left animate-fade-in-up">
                        
                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-[#EFEDEB] flex items-center justify-between bg-[#FBFBFA]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="font-display text-[15.5px] font-bold text-[#17151C]">Konfirmasi Clock Out Lebih Awal</h3>
                            </div>
                            <button type="button" @click="earlyClockOutModal = false" class="text-[#75727C] hover:text-[#17151C] p-1 rounded-lg hover:bg-[#F1F0EE] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-4">
                            <div class="flex items-center justify-between p-3.5 bg-blue-50/80 border border-blue-200 rounded-xl text-[13px] text-blue-900 font-semibold">
                                <span>Waktu Sekarang: <strong class="font-mono text-blue-950 ml-1 text-[14px]" x-text="currentTimeStr"></strong></span>
                                <span class="text-blue-300">|</span>
                                <span>Jam Pulang Normal: <strong class="font-mono text-blue-950 ml-1 text-[14px]">17:30</strong></span>
                            </div>

                            <p class="text-[13.5px] text-[#3D3A44] leading-relaxed text-center">
                                Jam kerja operasional resmi selesai pada pukul <strong>17:30</strong>. Apakah Anda yakin ingin mengakhiri presensi kerja dan melakukan Clock Out saat ini?
                            </p>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-6 py-4 border-t border-[#EFEDEB] bg-[#FAF9F8] flex items-center justify-end gap-3">
                            <button type="button" 
                                    @click="earlyClockOutModal = false"
                                    :disabled="loading"
                                    class="px-4 py-2 rounded-xl bg-white border border-[#E7E5E3] text-[#3D3A44] font-semibold text-[13px] hover:bg-[#F8F7F6] transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" 
                                    @click="confirmEarlyClockOut()"
                                    :disabled="loading"
                                    class="px-5 py-2 rounded-xl bg-[#17151C] hover:bg-[#2E2C34] text-white font-semibold text-[13px] transition shadow-md flex items-center gap-2 cursor-pointer">
                                <span x-text="loading ? 'Memproses...' : 'Ya, Clock Out Sekarang'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>



        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('attendanceManager', () => ({
        // State GPS
        currentLat:      null,
        currentLon:      null,
        currentDistance: null,
        withinRange:     false,
        gpsStatus:       'Mendeteksi lokasi...',
        gpsError:        null,

        // State presensi
        clockIn:   @json($clockIn ? ['time' => \Carbon\Carbon::parse($clockIn->created_at)->setTimezone('Asia/Jakarta')->format('H:i'), 'is_within_range' => $clockIn->is_within_range, 'distance' => $clockIn->distance_meters ?? $clockIn->distance] : null),
        clockOut:  @json($clockOut ? ['time' => \Carbon\Carbon::parse($clockOut->created_at)->setTimezone('Asia/Jakarta')->format('H:i')] : null),
        duration:  @json($clockIn && $clockOut ? (function() use ($clockIn, $clockOut) { $m = \Carbon\Carbon::parse($clockIn->created_at)->diffInMinutes(\Carbon\Carbon::parse($clockOut->created_at)); return intdiv($m,60).'j '.($m%60).'m'; })() : null),

        // UI state
        cameraOpen:         false,
        capturedPhoto:      null,
        note:               '',
        loading:            false,
        actionType:         null,
        videoStream:        null,
        outOfRangeModal:    false,
        earlyClockOutModal: false,
        currentTimeStr:     '',
        pendingAction:      null,

        init() {
            this.getLocation();
        },

        isBefore1730() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            return (hours < 17) || (hours === 17 && minutes < 30);
        },

        getLocation() {
            if (!navigator.geolocation) {
                this.gpsError = 'Browser tidak mendukung GPS.';
                return;
            }
            navigator.geolocation.watchPosition(
                (pos) => {
                    this.currentLat = pos.coords.latitude;
                    this.currentLon = pos.coords.longitude;
                    this.currentDistance = this.haversine(
                        -6.1664, 106.8148,
                        this.currentLat, this.currentLon
                    );
                    this.withinRange = this.currentDistance <= 100;
                    this.gpsStatus = this.currentDistance + ' m dari kantor';
                    this.gpsError = null;
                },
                (err) => {
                    this.gpsError = 'GPS tidak dapat diakses. Pastikan izin lokasi diaktifkan.';
                    this.gpsStatus = 'GPS tidak tersedia';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },

        haversine(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2)**2 +
                      Math.cos(lat1*Math.PI/180) * Math.cos(lat2*Math.PI/180) * Math.sin(dLon/2)**2;
            return Math.round(R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)));
        },

        async toggleCamera() {
            this.cameraOpen = !this.cameraOpen;
            if (this.cameraOpen) {
                await this.$nextTick();
                this.startCamera();
            } else {
                this.stopCamera();
            }
        },

        async startCamera() {
            try {
                this.videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' }
                });
                const video = document.getElementById('att-video');
                if (video) video.srcObject = this.videoStream;
            } catch (e) {
                console.warn('Kamera tidak dapat diakses:', e);
            }
        },

        stopCamera() {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
            }
        },

        capturePhoto() {
            const video  = document.getElementById('att-video');
            const canvas = document.getElementById('att-canvas');
            if (!video || !canvas) return;
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.8);
            this.stopCamera();
        },

        retakePhoto() {
            this.capturedPhoto = null;
            this.startCamera();
        },

        async handleAction(type) {
            if (!this.currentLat) {
                alert('Lokasi GPS belum terdeteksi. Harap tunggu beberapa saat.');
                return;
            }

            if (type === 'clock_out' && this.isBefore1730()) {
                this.currentTimeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                this.earlyClockOutModal = true;
                return;
            }

            if (!this.withinRange) {
                this.pendingAction = type;
                this.outOfRangeModal = true;
                return;
            }

            await this.doSubmit(type);
        },

        async confirmEarlyClockOut() {
            this.earlyClockOutModal = false;
            if (!this.withinRange) {
                this.pendingAction = 'clock_out';
                this.outOfRangeModal = true;
                return;
            }
            await this.doSubmit('clock_out');
        },

        async confirmProceed() {
            this.outOfRangeModal = false;
            if (this.pendingAction) {
                await this.doSubmit(this.pendingAction);
                this.pendingAction = null;
            }
        },

        async doSubmit(type) {
            this.loading    = true;
            this.actionType = type;

            try {
                const resp = await fetch(`/attendance/${type === 'clock_in' ? 'clock-in' : 'clock-out'}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        latitude:  this.currentLat,
                        longitude: this.currentLon,
                        photo:     this.capturedPhoto || null,
                        note:      this.note,
                    })
                });

                const data = await resp.json();

                if (resp.ok) {
                    if (type === 'clock_in') {
                        this.clockIn = {
                            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                            is_within_range: data.within_range,
                            distance: data.distance,
                        };
                    } else {
                        this.clockOut = {
                            time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                        };
                        this.duration = data.duration || '';
                    }
                    this.cameraOpen    = false;
                    this.capturedPhoto = null;
                    this.note          = '';
                    this.stopCamera();
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message || 'Terjadi kesalahan.', 'error');
                }
            } catch (e) {
                this.showToast('Gagal menghubungi server.', 'error');
            } finally {
                this.loading    = false;
                this.actionType = null;
            }
        },

        showToast(msg, type = 'success') {
            const t = document.createElement('div');
            t.style.cssText = `position:fixed;bottom:20px;right:20px;z-index:99999;
                padding:12px 20px;border-radius:10px;font-size:13.5px;font-weight:600;
                font-family:'Inter',sans-serif;box-shadow:0 8px 24px rgba(14,13,18,.16);
                background:${type === 'success' ? '#17151C' : '#C81E2C'};color:white;
                animation:fadeInUp .18s ease;`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .3s'; setTimeout(() => t.remove(), 300); }, 3000);
        }
    }));
});
</script>
@endpush
@endsection
