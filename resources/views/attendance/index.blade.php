@extends('layouts.app')

@section('title', 'Presensi')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Presensi'])

        <div class="att p-4 sm:p-[26px] animate-fade-in" x-data="attendanceManager()" x-init="init()">

            {{-- ── STATUS CARD ────────────────────────────────────────── --}}
            <div class="att-grid-top">

                {{-- Clock In Card --}}
                <div class="att-card att-card--ci" :class="clockIn ? 'is-done' : ''">
                    <div class="att-card-header">
                        <div class="att-card-icon" :class="clockIn ? 'is-done' : ''">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="att-card-label">Clock In</div>
                            <div class="att-card-time" x-text="clockIn ? clockIn.time : '--:--'"></div>
                        </div>
                        <template x-if="clockIn">
                            <span class="att-badge" :class="clockIn.is_within_range ? 'att-badge--green' : 'att-badge--orange'">
                                <span class="att-badge-dot"></span>
                                <span x-text="clockIn.is_within_range ? 'Hadir' : 'Luar Jangkauan'"></span>
                            </span>
                        </template>
                    </div>
                    <template x-if="clockIn">
                        <div class="att-card-detail">
                            <div class="att-card-dist">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:12px;height:12px;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="clockIn.distance + ' m dari kantor'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Clock Out Card --}}
                <div class="att-card att-card--co" :class="clockOut ? 'is-done' : ''">
                    <div class="att-card-header">
                        <div class="att-card-icon att-card-icon--co" :class="clockOut ? 'is-done' : ''">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="att-card-label">Clock Out</div>
                            <div class="att-card-time" x-text="clockOut ? clockOut.time : '--:--'"></div>
                        </div>
                        <template x-if="clockOut">
                            <span class="att-badge att-badge--gray">
                                <span class="att-badge-dot" style="background:#75727C;"></span>
                                <span>Selesai</span>
                            </span>
                        </template>
                    </div>
                    <template x-if="clockOut && clockIn">
                        <div class="att-card-detail">
                            <div class="att-card-dist">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:12px;height:12px;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="'Durasi: ' + duration"></span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Lokasi Real-Time --}}
                <div class="att-card att-card--loc">
                    <div class="att-card-header">
                        <div class="att-card-icon att-card-icon--loc">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div style="min-width:0;">
                            <div class="att-card-label">Lokasi Saat Ini</div>
                            <div class="att-card-time" style="font-size:14px;" x-text="gpsStatus"></div>
                        </div>
                    </div>
                    <template x-if="currentLat">
                        <div class="att-card-detail">
                            <div class="att-card-dist" :class="withinRange ? 'att-dist--green' : 'att-dist--orange'">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:12px;height:12px;flex-shrink:0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                <span x-text="currentDistance + ' m dari kantor'"></span>
                                <span x-text="withinRange ? '✓ Dalam jangkauan' : '⚠ Luar jangkauan'"></span>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            {{-- ── TOMBOL CLOCK IN / OUT + KAMERA ─────────────────────────── --}}
            <div class="att-action-section">

                {{-- Kamera Preview --}}
                <div class="att-cam-wrap" x-show="cameraOpen" x-cloak>
                    <div class="att-cam-label">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Foto Selfie (Opsional)
                    </div>
                    <video id="att-video" autoplay playsinline class="att-video" x-ref="video"></video>
                    <canvas id="att-canvas" x-ref="canvas" style="display:none;"></canvas>
                    <template x-if="capturedPhoto">
                        <img :src="capturedPhoto" class="att-photo-preview" alt="Foto selfie">
                    </template>
                    <div class="att-cam-actions">
                        <button type="button" @click="capturePhoto()"
                            class="att-btn att-btn--secondary" x-show="!capturedPhoto">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Ambil Foto
                        </button>
                        <button type="button" @click="retakePhoto()" x-show="capturedPhoto"
                            class="att-btn att-btn--ghost">
                            Ulangi Foto
                        </button>
                    </div>
                </div>

                {{-- Note Input --}}
                <div class="att-note-wrap" x-show="cameraOpen" x-cloak>
                    <label class="att-label">Catatan (Opsional)</label>
                    <input type="text" x-model="note" placeholder="Misal: Kerja lapangan, kunjungan klien..."
                        class="att-input">
                </div>

                {{-- Tombol Aksi --}}
                <div class="att-btns">
                    <template x-if="!clockIn">
                        <button type="button" @click="handleAction('clock_in')"
                            :disabled="loading || !currentLat"
                            class="att-btn att-btn--clockin" id="btn-clockin">
                            <template x-if="loading && actionType === 'clock_in'">
                                <svg class="att-spin" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <template x-if="!(loading && actionType === 'clock_in')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:18px;height:18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                            </template>
                            <span x-text="loading && actionType === 'clock_in' ? 'Memproses...' : 'Clock In Sekarang'"></span>
                        </button>
                    </template>

                    <template x-if="clockIn && !clockOut">
                        <button type="button" @click="handleAction('clock_out')"
                            :disabled="loading || !currentLat"
                            class="att-btn att-btn--clockout" id="btn-clockout">
                            <template x-if="loading && actionType === 'clock_out'">
                                <svg class="att-spin" fill="none" viewBox="0 0 24 24">
                                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <template x-if="!(loading && actionType === 'clock_out')">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:18px;height:18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </template>
                            <span x-text="loading && actionType === 'clock_out' ? 'Memproses...' : 'Clock Out Sekarang'"></span>
                        </button>
                    </template>

                    <template x-if="clockIn && clockOut">
                        <div class="att-done-msg">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:22px;height:22px;color:#1B7A46;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Presensi hari ini sudah selesai. Terima kasih!</span>
                        </div>
                    </template>

                    <template x-if="!cameraOpen && !(clockIn && clockOut)">
                        <button type="button" @click="toggleCamera()"
                            class="att-btn att-btn--ghost" style="font-size:12.5px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Tambah Foto Selfie
                        </button>
                    </template>
                </div>

                {{-- GPS loading indicator --}}
                <div class="att-gps-hint" x-show="!currentLat && !gpsError">
                    <svg class="att-spin" fill="none" viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Mendeteksi lokasi GPS...
                </div>
                <div class="att-gps-error" x-show="gpsError" x-cloak x-text="gpsError"></div>

            </div>

            {{-- ── RIWAYAT 7 HARI ──────────────────────────────────────────── --}}
            <div class="att-history-section">
                <div class="att-section-title">Riwayat Presensi (7 Hari Terakhir)</div>

                @if($history->isEmpty())
                <div class="att-empty">
                    <div class="att-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p>Belum ada riwayat presensi.</p>
                </div>
                @else
                <div class="att-history-list">
                    @foreach($history as $date => $records)
                    @php
                        $ci = $records->firstWhere('type', 'clock_in');
                        $co = $records->firstWhere('type', 'clock_out');
                        $isToday = $date === $today;
                    @endphp
                    <div class="att-history-row" style="{{ $isToday ? 'border-left:3px solid #C81E2C;' : '' }}">
                        <div class="att-history-date">
                            <div class="att-history-day">{{ \Carbon\Carbon::parse($date)->translatedFormat('D') }}</div>
                            <div class="att-history-dnum">{{ \Carbon\Carbon::parse($date)->format('d M') }}</div>
                            @if($isToday)<span class="att-today-badge">Hari ini</span>@endif
                        </div>
                        <div class="att-history-times">
                            <div class="att-history-time-item">
                                <span class="att-history-type att-history-type--in">IN</span>
                                <span class="att-history-val">{{ $ci ? \Carbon\Carbon::parse($ci->created_at)->format('H:i') : '—' }}</span>
                                @if($ci)
                                <span class="att-history-badge" style="{{ $ci->is_within_range ? 'background:#E4F3EA;color:#1B7A46;' : 'background:#FEF3E2;color:#9A6206;' }}">
                                    {{ $ci->is_within_range ? 'Hadir' : 'Luar Jangkauan' }}
                                </span>
                                @endif
                            </div>
                            <div class="att-history-time-item">
                                <span class="att-history-type att-history-type--out">OUT</span>
                                <span class="att-history-val">{{ $co ? \Carbon\Carbon::parse($co->created_at)->format('H:i') : '—' }}</span>
                                @if($ci && $co)
                                @php
                                    $mins = \Carbon\Carbon::parse($ci->created_at)->diffInMinutes(\Carbon\Carbon::parse($co->created_at));
                                    $h = intdiv($mins, 60); $m = $mins % 60;
                                @endphp
                                <span class="att-history-badge" style="background:#F1F0EE;color:#3D3A44;">{{ $h }}j {{ $m }}m</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ── MODAL KONFIRMASI LUAR JANGKAUAN (di dalam x-data scope) ── --}}
            <div x-show="outOfRangeModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4"
                 @click.self="outOfRangeModal = false">

                <div class="att-oor-modal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    {{-- Icon Warning --}}
                    <div class="att-oor-icon-wrap">
                        <div class="att-oor-icon-ring">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Judul --}}
                    <h3 class="att-oor-title">Anda Berada di Luar Jangkauan</h3>

                    {{-- Info Jarak --}}
                    <div class="att-oor-distance-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Jarak Anda: <strong x-text="currentDistance + ' meter'" style="font-family:'IBM Plex Mono',monospace;"></strong></span>
                        <span style="color:#D4D0CC;">|</span>
                        <span>Batas: <strong style="font-family:'IBM Plex Mono',monospace;">100 meter</strong></span>
                    </div>

                    {{-- Pesan --}}
                    <p class="att-oor-desc">
                        Anda berada di luar radius kantor. Absensi tetap dapat dilakukan,
                        namun akan dicatat sebagai <strong>luar jangkauan</strong> dalam sistem.
                    </p>

                    <div style="width:100%;height:1px;background:#F0EFED;margin:6px 0 18px;"></div>

                    {{-- Tombol --}}
                    <div class="att-oor-btns">
                        <button @click="confirmProceed()"
                                :disabled="loading"
                                class="att-btn att-btn--clockin" style="flex:1;justify-content:center;padding:9px 16px;font-size:13px;">
                            <template x-if="loading">
                                <svg class="att-spin" fill="none" viewBox="0 0 24 24" style="width:14px;height:14px;">
                                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Memproses...' : 'Ya, Tetap Absen'"></span>
                        </button>
                        <button @click="outOfRangeModal = false"
                                :disabled="loading"
                                class="att-btn att-btn--ghost" style="flex:1;justify-content:center;padding:9px 16px;font-size:13px;">
                            Batal
                        </button>
                    </div>

                </div>
            </div>

            {{-- ── MODAL PERINGATAN CLOCK OUT LEBIH AWAL (SEBELUM 17:30) ── --}}
            <div x-show="earlyClockOutModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4"
                 @click.self="earlyClockOutModal = false">

                <div class="att-oor-modal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    {{-- Icon Clock Warning --}}
                    <div class="att-oor-icon-wrap">
                        <div class="att-oor-icon-ring">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Judul --}}
                    <h3 class="att-oor-title">Clock Out Lebih Awal?</h3>

                    {{-- Info Waktu --}}
                    <div class="att-oor-distance-badge">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Waktu Sekarang: <strong x-text="currentTimeStr" style="font-family:'IBM Plex Mono',monospace;"></strong></span>
                        <span style="color:#D4D0CC;">|</span>
                        <span>Jam Pulang: <strong style="font-family:'IBM Plex Mono',monospace;">17:30</strong></span>
                    </div>

                    {{-- Pesan --}}
                    <p class="att-oor-desc">
                        Jam kerja normal selesai pukul <strong>17:30</strong>. Apakah Anda yakin ingin melakukan Clock Out sekarang?
                    </p>

                    <div style="width:100%;height:1px;background:#F0EFED;margin:6px 0 18px;"></div>

                    {{-- Tombol --}}
                    <div class="att-oor-btns">
                        <button @click="confirmEarlyClockOut()"
                                :disabled="loading"
                                class="att-btn att-btn--clockin" style="flex:1;justify-content:center;padding:9px 16px;font-size:13px;">
                            <template x-if="loading">
                                <svg class="att-spin" fill="none" viewBox="0 0 24 24" style="width:14px;height:14px;">
                                    <circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Memproses...' : 'Ya, Tetap Clock Out'"></span>
                        </button>
                        <button @click="earlyClockOutModal = false"
                                :disabled="loading"
                                class="att-btn att-btn--ghost" style="flex:1;justify-content:center;padding:9px 16px;font-size:13px;">
                            Batal
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- STYLES --}}
<style>
.att, .att * { box-sizing: border-box; }

/* Grid atas */
.att-grid-top {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}

/* Card */
.att-card {
    background: white;
    border: 1px solid #E7E5E3;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 1px 2px rgba(14,13,18,.05);
    transition: box-shadow .2s;
}
.att-card.is-done { border-color: #BFE8C9; background: #F0FAF3; }
.att-card--co.is-done { border-color: #E7E5E3; background: white; }
.att-card-header { display: flex; align-items: flex-start; gap: 12px; }
.att-card-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: #FDF1F2; color: #C81E2C;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.att-card-icon svg { width: 18px; height: 18px; }
.att-card-icon.is-done { background: #E4F3EA; color: #1B7A46; }
.att-card-icon--co { background: #F1F0EE; color: #75727C; }
.att-card-icon--co.is-done { background: #E4F3EA; color: #1B7A46; }
.att-card-icon--loc { background: #EEF2FF; color: #3B5BDB; }
.att-card-label { font-size: 11px; font-weight: 700; color: #75727C; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }
.att-card-time { font-family: 'IBM Plex Mono', monospace; font-size: 20px; font-weight: 700; color: #17151C; }
.att-card-detail { margin-top: 10px; padding-top: 10px; border-top: 1px solid #EFEDEB; }
.att-card-dist { display: flex; align-items: center; gap: 5px; font-size: 12px; color: #75727C; flex-wrap: wrap; }
.att-dist--green { color: #1B7A46; }
.att-dist--orange { color: #9A6206; }

/* Badge */
.att-badge {
    margin-left: auto; flex-shrink: 0;
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; padding: 3px 9px 3px 7px; border-radius: 20px;
}
.att-badge--green { background: #E4F3EA; color: #1B7A46; }
.att-badge--orange { background: #FEF3E2; color: #9A6206; }
.att-badge--gray { background: #F1F0EE; color: #3D3A44; }
.att-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

/* Action section */
.att-action-section {
    background: white; border: 1px solid #E7E5E3; border-radius: 14px;
    padding: 20px; box-shadow: 0 1px 2px rgba(14,13,18,.05); margin-bottom: 18px;
}

/* Camera */
.att-cam-wrap { margin-bottom: 14px; }
.att-cam-label {
    display: flex; align-items: center; gap: 6px;
    font-size: 11.5px; font-weight: 700; color: #75727C;
    text-transform: uppercase; letter-spacing: .3px; margin-bottom: 10px;
}
.att-video {
    width: 100%; max-width: 360px; border-radius: 12px;
    background: #17151C; display: block;
    aspect-ratio: 4/3; object-fit: cover;
}
.att-photo-preview {
    width: 100%; max-width: 360px; border-radius: 12px;
    margin-top: 8px; display: block; border: 2px solid #E4F3EA;
}
.att-cam-actions { display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; }

/* Note */
.att-note-wrap { margin-bottom: 14px; }
.att-label { display: block; font-size: 11.5px; font-weight: 700; color: #75727C; text-transform: uppercase; letter-spacing: .3px; margin-bottom: 6px; }
.att-input {
    width: 100%; padding: 10px 12px; border-radius: 8px;
    border: 1px solid #E7E5E3; font-size: 14px; color: #17151C;
    outline: none; background: white; transition: border .15s, box-shadow .15s;
    font-family: 'Inter', sans-serif;
}
.att-input:focus { border-color: #C81E2C; box-shadow: 0 0 0 3px #FDF1F2; }

/* Buttons */
.att-btns { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.att-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 22px; border-radius: 10px; border: none; cursor: pointer;
    font-size: 14px; font-weight: 700; transition: all .15s ease;
    font-family: 'Inter', sans-serif;
}
.att-btn:disabled { opacity: .55; cursor: not-allowed; }
.att-btn--clockin {
    background: #C81E2C; color: white;
    box-shadow: 0 8px 20px rgba(200,30,44,.28);
}
.att-btn--clockin:not(:disabled):hover { filter: brightness(1.08); }
.att-btn--clockout {
    background: #17151C; color: white;
    box-shadow: 0 8px 20px rgba(14,13,18,.2);
}
.att-btn--clockout:not(:disabled):hover { filter: brightness(1.15); }
.att-btn--secondary {
    background: #FDF1F2; color: #C81E2C;
    border: 1px solid #F3C6CB;
}
.att-btn--secondary:hover { background: #F9E0E2; }
.att-btn--ghost {
    background: white; color: #3D3A44;
    border: 1px solid #E7E5E3;
}
.att-btn--ghost:hover { background: #F8F7F6; }

@keyframes att-spin { to { transform: rotate(360deg); } }
.att-spin { animation: att-spin .7s linear infinite; width: 16px; height: 16px; }

.att-done-msg {
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 600; color: #1B7A46;
    background: #F0FAF3; border: 1px solid #BFE8C9;
    border-radius: 10px; padding: 12px 18px;
}
.att-gps-hint, .att-gps-error {
    display: flex; align-items: center; gap: 6px;
    font-size: 12.5px; color: #75727C; margin-top: 10px;
}
.att-gps-error { color: #C81E2C; }

/* History */
.att-history-section {
    background: white; border: 1px solid #E7E5E3; border-radius: 14px;
    overflow: hidden; box-shadow: 0 1px 2px rgba(14,13,18,.05);
}
.att-section-title {
    font-size: 12px; font-weight: 700; color: #75727C;
    text-transform: uppercase; letter-spacing: .4px;
    padding: 14px 18px; border-bottom: 1px solid #EFEDEB;
    background: #FAFAF9;
}
.att-history-list { display: flex; flex-direction: column; }
.att-history-row {
    display: flex; align-items: center; gap: 16px;
    padding: 13px 18px; border-bottom: 1px solid #EFEDEB;
    transition: background .15s;
}
.att-history-row:last-child { border-bottom: none; }
.att-history-row:hover { background: #FAFAF9; }
.att-history-date { display: flex; flex-direction: column; align-items: center; min-width: 46px; gap: 1px; }
.att-history-day { font-size: 10.5px; font-weight: 700; color: #75727C; text-transform: uppercase; }
.att-history-dnum { font-size: 13.5px; font-weight: 700; color: #17151C; font-family: 'IBM Plex Mono', monospace; }
.att-today-badge { font-size: 9px; font-weight: 700; background: #C81E2C; color: white; border-radius: 4px; padding: 1px 5px; margin-top: 2px; }
.att-history-times { flex: 1; display: flex; flex-direction: column; gap: 5px; }
.att-history-time-item { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.att-history-type {
    font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px;
    letter-spacing: .3px; flex-shrink: 0;
}
.att-history-type--in { background: #FDF1F2; color: #C81E2C; }
.att-history-type--out { background: #F1F0EE; color: #3D3A44; }
.att-history-val { font-family: 'IBM Plex Mono', monospace; font-size: 13px; font-weight: 700; color: #17151C; min-width: 38px; }
.att-history-badge { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }

.att-empty { text-align: center; padding: 40px 16px; color: #75727C; }
.att-empty-icon { width: 44px; height: 44px; border-radius: 10px; background: #F1F0EE; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
.att-empty-icon svg { width: 20px; height: 20px; opacity: .6; }
.att-empty p { font-size: 13.5px; margin: 0; }

/* ── OUT OF RANGE MODAL ── */
.att-oor-modal {
    background: white;
    border-radius: 20px;
    width: 440px;
    max-width: 100%;
    padding: 28px 28px 24px;
    box-shadow: 0 24px 64px rgba(14,13,18,.2);
    text-align: center;
    animation: fadeInUp .18s ease;
}
.att-oor-icon-wrap { display: flex; justify-content: center; margin-bottom: 16px; }
.att-oor-icon-ring {
    width: 60px; height: 60px; border-radius: 50%;
    background: #FEF3E2;
    display: flex; align-items: center; justify-content: center;
}
.att-oor-icon-ring svg { width: 28px; height: 28px; color: #D97706; }
.att-oor-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 18px; font-weight: 700; color: #17151C;
    margin: 0 0 14px;
}
.att-oor-distance-badge {
    display: inline-flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: center;
    background: #FEF3E2; border: 1px solid #FDD6A0; border-radius: 10px;
    padding: 8px 16px; margin-bottom: 16px; font-size: 12.5px; color: #92400E;
}
.att-oor-desc {
    font-size: 13.5px; color: #3D3A44; line-height: 1.65;
    margin: 0 0 8px;
}
.att-oor-tag {
    display: inline-block;
    background: #FEF3E2; color: #9A6206;
    font-size: 11.5px; font-weight: 700;
    padding: 2px 9px; border-radius: 20px;
    white-space: nowrap;
}
.att-oor-sub {
    font-size: 12px; color: #75727C; line-height: 1.6;
    margin: 0 0 4px;
}
.att-oor-btns { display: flex; gap: 10px; flex-wrap: wrap; }
</style>

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
        clockIn:   @json($clockIn ? ['time' => \Carbon\Carbon::parse($clockIn->created_at)->format('H:i'), 'is_within_range' => $clockIn->is_within_range, 'distance' => $clockIn->distance_meters] : null),
        clockOut:  @json($clockOut ? ['time' => \Carbon\Carbon::parse($clockOut->created_at)->format('H:i')] : null),
        duration:  @json($clockIn && $clockOut ? (function() use ($clockIn, $clockOut) { $m = \Carbon\Carbon::parse($clockIn->created_at)->diffInMinutes(\Carbon\Carbon::parse($clockOut->created_at)); return intdiv($m,60).'j '.($m%60).'m'; })() : null),

        // UI state
        cameraOpen:         false,
        capturedPhoto:      null,
        note:               '',
        loading:            false,
        actionType:         null,
        videoStream:        null,
        outOfRangeModal:    false,   // popup konfirmasi luar jangkauan
        earlyClockOutModal: false,   // popup konfirmasi pulang lebih awal (< 17:30)
        currentTimeStr:     '',
        pendingAction:      null,    // simpan action yang tertunda

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

            // Jika Clock Out sebelum 17:30 -> munculkan peringatan pulang lebih awal
            if (type === 'clock_out' && this.isBefore1730()) {
                this.currentTimeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                this.earlyClockOutModal = true;
                return;
            }

            // Jika di luar jangkauan → tampilkan popup konfirmasi luar jangkauan
            if (!this.withinRange) {
                this.pendingAction = type;
                this.outOfRangeModal = true;
                return;
            }

            // Langsung proses
            await this.doSubmit(type);
        },

        // Dipanggil saat klik "Ya, Tetap Clock Out" dari modal pulang lebih awal
        async confirmEarlyClockOut() {
            this.earlyClockOutModal = false;
            // Jika ternyata juga di luar jangkauan, tanyakan lagi konfirmasi lokasi
            if (!this.withinRange) {
                this.pendingAction = 'clock_out';
                this.outOfRangeModal = true;
                return;
            }
            await this.doSubmit('clock_out');
        },

        // Dipanggil dari tombol "Ya, Tetap Absen" di popup luar jangkauan
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
