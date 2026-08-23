@extends('layouts.app')

@section('title', 'Jadwal Kerja')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Jadwal Kerja'])

        <div class="jkw p-[26px] animate-fade-in" x-data="schedulesManager()" x-init="init()">
            

            {{-- HEADER --}}
            <div class="jkw-header">
                <div class="jkw-tabs" role="tablist">
                    <template x-for="mode in [
                        {key:'day', label:'Daily', sub:'Harian'},
                        {key:'week', label:'Weekly', sub:'Mingguan'},
                        {key:'month', label:'Monthly', sub:'Bulanan'}
                    ]" :key="mode.key">
                        <button type="button"
                                class="jkw-tab"
                                :class="{ 'is-active': viewMode === mode.key }"
                                @click="viewMode = mode.key">
                            <span class="jkw-tab-main" x-text="mode.label"></span>
                            <span class="jkw-tab-sub" x-text="'(' + mode.sub + ')'"></span>
                        </button>
                    </template>
                </div>

                <div class="jkw-actions">
                    @if($isLead)
                    <div class="jkw-select-wrap">
                        <svg class="jkw-select-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                        </svg>
                        <select class="jkw-select" x-model="engineerFilter">
                            <option value="">Semua Engineer</option>
                            <template x-for="engineer in engineers" :key="engineer.id">
                                <option :value="engineer.id" x-text="engineer.name"></option>
                            </template>
                        </select>
                    </div>
                    @endif

                    <a :href="'{{ route('schedules.export') }}' + (engineerFilter ? '?engineer_id=' + engineerFilter : '')"
                       class="jkw-btn jkw-btn--ghost jkw-btn--excel"
                       title="Export Excel Data Jadwal">
                        <svg class="jkw-icon jkw-icon--excel" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export Excel</span>
                    </a>

                    <a :href="'{{ route('schedules.export.pdf') }}' + (engineerFilter ? '?engineer_id=' + engineerFilter : '')"
                       target="_blank"
                       class="jkw-btn jkw-btn--ghost jkw-btn--pdf"
                       title="Export PDF Laporan Resmi Jadwal Kerja">
                        <svg class="jkw-icon jkw-icon--pdf" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h1m-1 4h6m-6 4h4"/>
                        </svg>
                        <span>Export PDF</span>
                    </a>
                </div>
            </div>

            {{-- PANEL KETERSEDIAAN ENGINEER — Hanya Managerial/Lead --}}
            @if($isLead)
            <div class="jkw-card jkw-avail"
                 x-transition:enter="jkw-fade-enter" x-transition:enter-start="jkw-fade-start" x-transition:enter-end="jkw-fade-end">
                <div class="jkw-avail-head">
                    <span class="jkw-eyebrow">Ketersediaan Engineer — <span x-text="periodLabel"></span></span>
                    <label class="jkw-check">
                        <input type="checkbox" x-model="showOnlyAvailable">
                        <span>Hanya yang tersedia</span>
                    </label>
                </div>
                <div class="jkw-avail-body">
                    <template x-for="eng in filteredEngineerAvailability" :key="eng.id">
                        <button type="button" class="jkw-eng-chip" :class="eng.available ? 'is-free' : 'is-busy'" @click="engineerFilter = (engineerFilter == eng.id ? '' : eng.id)">
                            <span class="jkw-avatar" :style="'background:' + colorFromName(eng.name)" x-text="initials(eng.name)"></span>
                            <span class="jkw-eng-info">
                                <span class="jkw-eng-name" x-text="eng.name"></span>
                                <span class="jkw-eng-status" x-text="eng.available ? 'Tersedia' : eng.scheduleCount + ' jadwal'"></span>
                            </span>
                            <span class="jkw-dot" :class="eng.available ? 'is-free' : 'is-busy'"></span>
                        </button>
                    </template>
                    <div class="jkw-empty-inline" x-show="filteredEngineerAvailability.length === 0">
                        Tidak ada engineer pada periode ini.
                    </div>
                </div>
            </div>
            @endif

            {{-- LEGEND -- hanya tampil di view selain rekap --}}
            <template x-if="viewMode !== 'table'">
                <div style="display:flex; align-items:center; gap:14px; flex-wrap:nowrap; margin-bottom:12px; padding:8px 16px; background:var(--jkw-surface); border:1px solid var(--jkw-line); border-radius:10px; overflow-x:auto;">
                    <span style="font-size:11px; font-weight:700; color:var(--jkw-muted); text-transform:uppercase; letter-spacing:.3px; white-space:nowrap; flex-shrink:0;">Keterangan:</span>
                    <span style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                        <span style="width:10px; height:10px; border-radius:2px; background:#2563EB; flex-shrink:0; display:inline-block;"></span>
                        <span style="font-size:12px; color:var(--jkw-ink-2); white-space:nowrap;">Jadwal Kerja</span>
                    </span>
                    <span style="color:var(--jkw-line); flex-shrink:0;">|</span>
                    <span style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                        <span style="width:10px; height:10px; border-radius:2px; background:#C81E2C; flex-shrink:0; display:inline-block;"></span>
                        <span style="font-size:12px; color:var(--jkw-ink-2); white-space:nowrap;">Deadline Task</span>
                    </span>
                    <span style="color:var(--jkw-line); flex-shrink:0;">|</span>
                    <span style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                        <span style="width:10px; height:10px; border-radius:2px; background:#991B1B; flex-shrink:0; display:inline-block;"></span>
                        <span style="font-size:12px; color:var(--jkw-ink-2); white-space:nowrap;">Deadline Project</span>
                    </span>
                </div>
            </template>

            {{-- DAY VIEW --}}
            <div x-show="viewMode === 'day'" x-transition:enter="jkw-fade-enter" x-transition:enter-start="jkw-fade-start" x-transition:enter-end="jkw-fade-end">
                <div class="jkw-card">
                    <div class="jkw-nav">
                        <button type="button" class="jkw-nav-btn" @click="changeDay(-1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div class="jkw-nav-center">
                            <span class="jkw-nav-title" x-text="dayLabel"></span>
                            <button type="button" class="jkw-today-btn" @click="goToday()">Hari ini</button>
                        </div>
                        <button type="button" class="jkw-nav-btn" @click="changeDay(1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="jkw-day-list">
                        <template x-for="schedule in paginatedDaySchedules" :key="schedule._uid">
                            <div class="jkw-day-item" :style="'border-left: 4px solid ' + schedule._color + '; padding-left: 14px;'">
                                <div class="jkw-time" x-text="schedule._type === 'schedule' ? (schedule.start_time + ' – ' + schedule.end_time) : schedule._timeLabel"></div>
                                <div class="jkw-day-main">
                                    <div class="jkw-day-title" x-text="schedule._displayTitle"></div>
                                    <div class="jkw-day-meta">
                                        <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(schedule.engineer?.name || '-')" x-text="initials(schedule.engineer?.name || '-')"></span>
                                        <span x-text="schedule.engineer?.name || '-'"></span>
                                        <span class="jkw-sep">·</span>
                                        <span x-text="schedule.project?.name || '-'"></span>
                                        <template x-if="schedule._type === 'schedule' && schedule.location">
                                            <span>
                                                <span class="jkw-sep">·</span>
                                                <span x-text="schedule.location"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="jkw-empty" x-show="daySchedules.length === 0">
                            <div class="jkw-empty-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p>Tidak ada jadwal atau deadline pada hari ini.</p>
                        </div>
                    </div>

                    <!-- Pagination Day View -->
                    <div x-show="daySchedules.length > perPage"
                         style="padding: 12px 18px; border-top: 1px solid #EFEDEB; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; font-size: 12.5px; color: #75727C;">
                        <div>
                            Menampilkan <span style="font-weight: 600; color: #17151C;" x-text="(dayPage - 1) * perPage + 1"></span> &ndash; <span style="font-weight: 600; color: #17151C;" x-text="Math.min(dayPage * perPage, daySchedules.length)"></span> dari <span style="font-weight: 600; color: #17151C;" x-text="daySchedules.length"></span> jadwal
                        </div>
                        <div style="display: flex; align-items: center; gap: 4px;">
                            <button type="button"
                                    @click="prevDayPage()"
                                    :disabled="dayPage === 1"
                                    title="Sebelumnya"
                                    style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #E7E5E3; background: #FFFFFF; color: #3D3A44; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;"
                                    :style="dayPage === 1 ? 'opacity: 0.35; cursor: not-allowed;' : ''">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            <template x-for="p in totalDayPages" :key="p">
                                <button type="button"
                                        @click="goToDayPage(p)"
                                        style="width: 28px; height: 28px; border-radius: 6px; font-size: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;"
                                        :style="dayPage === p ? 'background: #AF1424; color: #FFFFFF; border: 1px solid #AF1424; font-weight: 700;' : 'background: #FFFFFF; color: #17151C; border: 1px solid #E7E5E3; font-weight: 600;'"
                                        x-text="p">
                                </button>
                            </template>

                            <button type="button"
                                    @click="nextDayPage()"
                                    :disabled="dayPage === totalDayPages"
                                    title="Berikutnya"
                                    style="width: 28px; height: 28px; border-radius: 6px; border: 1px solid #E7E5E3; background: #FFFFFF; color: #3D3A44; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;"
                                    :style="dayPage === totalDayPages ? 'opacity: 0.35; cursor: not-allowed;' : ''">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WEEK VIEW --}}
            <div x-show="viewMode === 'week'" x-transition:enter="jkw-fade-enter" x-transition:enter-start="jkw-fade-start" x-transition:enter-end="jkw-fade-end">
                <div class="jkw-card">
                    <div class="jkw-nav">
                        <button type="button" class="jkw-nav-btn" @click="changeWeek(-1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div class="jkw-nav-center">
                            <span class="jkw-nav-title" x-text="weekRange"></span>
                            <button type="button" class="jkw-today-btn" @click="goToday()">Hari ini</button>
                        </div>
                        <button type="button" class="jkw-nav-btn" @click="changeWeek(1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="jkw-week-scroll">
                        <div class="jkw-week-grid">
                            <template x-for="day in weekDays" :key="day.fullDate">
                                <div class="jkw-week-col" :class="{ 'is-today': day.fullDate === todayStr }">
                                    <div class="jkw-week-col-head">
                                        <span x-text="day.name"></span>
                                        <span class="jkw-mono" x-text="day.date"></span>
                                    </div>
                                    <div class="jkw-week-col-body">
                                        <template x-for="event in getAllEventsForDay(day.fullDate)" :key="event._uid">
                                            <div class="jkw-mini-card" :style="'border-left: 3px solid ' + event._color + ';'">
                                                <div class="jkw-mini-time" x-text="event._timeLabel" style="font-size:10px; opacity:0.7;"></div>
                                                <div class="jkw-mini-title" x-text="event._displayTitle"></div>
                                                <div class="jkw-mini-eng" x-text="event._subLabel"></div>
                                            </div>
                                        </template>
                                        <div class="jkw-mini-empty" x-show="getAllEventsForDay(day.fullDate).length === 0">—</div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MONTH VIEW --}}
            <div x-show="viewMode === 'month'" x-transition:enter="jkw-fade-enter" x-transition:enter-start="jkw-fade-start" x-transition:enter-end="jkw-fade-end">
                <div class="jkw-card">
                    <div class="jkw-nav">
                        <button type="button" class="jkw-nav-btn" @click="changeMonth(-1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <div class="jkw-nav-center">
                            <span class="jkw-nav-title" x-text="monthLabel"></span>
                            <button type="button" class="jkw-today-btn" @click="goToday()">Hari ini</button>
                        </div>
                        <button type="button" class="jkw-nav-btn" @click="changeMonth(1)">
                            <svg class="jkw-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div class="jkw-month-scroll">
                        <div class="jkw-month-dow">
                            <template x-for="name in ['Min','Sen','Sel','Rab','Kam','Jum','Sab']" :key="name">
                                <div class="jkw-month-dow-cell" x-text="name"></div>
                            </template>
                        </div>

                        <template x-for="(week, wi) in monthWeeks" :key="wi">
                            <div class="jkw-month-row">
                                <template x-for="(day, di) in week" :key="day.fullDate">
                                    <div class="jkw-month-cell" :class="{ 'is-out': !day.inMonth, 'is-today': day.fullDate === todayStr }">
                                        <div class="jkw-month-daynum" x-text="day.dayNum"></div>
                                        <div class="jkw-month-events">
                                            <template x-for="event in getAllEventsForDay(day.fullDate).slice(0, 3)" :key="event._uid">
                                                <div class="jkw-month-event" :style="'background:' + event._color + '; color:#fff;'" x-text="event._displayTitle"></div>
                                            </template>
                                            <div class="jkw-month-more" x-show="getAllEventsForDay(day.fullDate).length > 3" x-text="'+' + (getAllEventsForDay(day.fullDate).length - 3) + ' lagi'"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- TABLE VIEW --}}
            <div x-show="viewMode === 'table'" x-transition:enter="jkw-fade-enter" x-transition:enter-start="jkw-fade-start" x-transition:enter-end="jkw-fade-end">
                <div class="jkw-card">
                    <div class="jkw-table-scroll">
                        <table class="jkw-table">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Project</th>
                                    <th>Engineer</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Lokasi</th>
                                    @if($isLead)
                                    <th class="jkw-th-right">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="schedule in filteredSchedules" :key="schedule.id">
                                    <tr>
                                        <td class="jkw-td-strong jkw-td-wrap" x-text="schedule.title"></td>
                                        <td class="jkw-td-wrap" x-text="schedule.project?.name"></td>
                                        <td>
                                            <span class="jkw-eng-cell">
                                                <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(schedule.engineer?.name || '-')" x-text="initials(schedule.engineer?.name || '-')"></span>
                                                <span x-text="schedule.engineer?.name"></span>
                                            </span>
                                        </td>
                                        <td class="jkw-mono" x-text="schedule.date.split('T')[0]"></td>
                                        <td class="jkw-mono" x-text="schedule.start_time + ' – ' + schedule.end_time"></td>
                                        <td class="jkw-td-wrap" x-text="schedule.location"></td>
                                        @if($isLead)
                                        <td>
                                            <div style="display:flex; justify-content:flex-end; gap:4px;">
                                                <button type="button"
                                                        @click="editSchedule(schedule)"
                                                        style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:7px; transition:all 0.15s ease;"
                                                        onmouseover="this.style.background='#F1F0EE'; this.style.color='#17151C'"
                                                        onmouseout="this.style.background='transparent'; this.style.color='#75727C'">
                                                    <svg style="width:15px; height:15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        @click="confirmDelete(schedule)"
                                                        style="background:none; border:none; cursor:pointer; color:#C81E2C; padding:6px; border-radius:7px; transition:all 0.15s ease;"
                                                        onmouseover="this.style.background='#FDF1F2'"
                                                        onmouseout="this.style.background='transparent'">
                                                    <svg style="width:15px; height:15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="jkw-empty" x-show="filteredSchedules.length === 0">
                        <div class="jkw-empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p>Belum ada jadwal</p>
                    </div>
                </div>
            </div>

            {{-- CONFIRM DELETE MODAL --}}
            <template x-teleport="body">
                <div x-show="confirmOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:999999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="confirmOpen = false">

                    <div style="background:white; border-radius:16px; width:420px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(14,13,18,0.2); margin:auto; position:relative; animation:jkwFadeUp 0.18s ease;">

                        <div style="padding:20px 24px;">
                            <div style="display:flex; justify-content:center mb-4;">
                                <div style="width:56px; height:56px; border-radius:50%; background:#FEF2F2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                                    <svg style="width:28px; height:28px; color:#C81E2C;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </div>

                            <h3 style="text-align:center; font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; color:#17151C; margin-bottom:8px;">
                                Yakin hapus data?
                            </h3>

                            <p style="text-align:center; font-size:14px; color:#75727C; margin-bottom:24px; word-break:break-word;">
                                Jadwal "<span x-text="confirmData?.title" style="font-weight:600; color:#17151C;"></span>" akan dihapus permanen.
                                <br>Apakah Anda yakin?
                            </p>

                            <div style="display:flex; gap:12px;">
                                <button @click="confirmDeleteAction()"
                                        style="flex:1; padding:10px 16px; border-radius:8px; background:#C81E2C; color:white; border:none; font-weight:600; font-size:14px; cursor:pointer; transition:all 0.15s ease;"
                                        onmouseover="this.style.filter='brightness(1.05)'"
                                        onmouseout="this.style.filter='brightness(1)'">
                                    Yakin
                                </button>
                                <button @click="confirmOpen = false"
                                        style="flex:1; padding:10px 16px; border-radius:8px; background:white; color:#3D3A44; border:1px solid #E7E5E3; font-weight:600; font-size:14px; cursor:pointer; transition:all 0.15s ease;"
                                        onmouseover="this.style.background='#F8F7F6'"
                                        onmouseout="this.style.background='white'">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- MODAL FORM --}}
            <template x-teleport="body">
                <div x-show="modalOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="modalOpen = false">

                    <div style="background:white; border-radius:16px; width:640px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 16px 40px rgba(14,13,18,0.16); margin:auto; position:relative; animation:jkwFadeUp 0.18s ease;">

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 18px; position:sticky; top:0; background:white; border-bottom:1px solid #E7E5E3; z-index:1; border-radius:16px 16px 0 0; gap:12px;">
                            <h3 style="margin:0; font-family:'Space Grotesk', sans-serif; font-size:17px; font-weight:700; color:#17151C; word-break:break-word;" x-text="modalTitle"></h3>
                            <button type="button"
                                    @click="modalOpen = false"
                                    style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:7px; transition:all 0.15s ease; flex-shrink:0;"
                                    onmouseover="this.style.background='#F1F0EE'"
                                    onmouseout="this.style.background='transparent'">
                                <svg style="width:20px; height:20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div style="padding:18px;">
                            <form @submit.prevent="saveSchedule">
                                <div style="display:flex; flex-direction:column; gap:14px;">
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Judul Jadwal</label>
                                        <input type="text" x-model="form.title" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Project</label>
                                        <select x-model="form.project_id" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                            <option value="">Pilih Project</option>
                                            <template x-for="project in projects" :key="project.id">
                                                <option :value="project.id" x-text="project.name"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Engineer</label>
                                        <select x-model="form.engineer_id" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                            <option value="">Pilih Engineer</option>
                                            <template x-for="engineer in engineers" :key="engineer.id">
                                                <option :value="engineer.id" x-text="engineer.name + (isEngineerBusyOnDate(engineer.id, form.date) ? ' (sudah ada jadwal)' : '')"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Tanggal</label>
                                        <input type="date" x-model="form.date" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                    </div>
                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                        <div>
                                            <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Jam Mulai</label>
                                            <input type="time" x-model="form.start_time" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Jam Selesai</label>
                                            <input type="time" x-model="form.end_time" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                        </div>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Lokasi</label>
                                        <input type="text" x-model="form.location" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease;" required>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11.5px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:.3px;">Deskripsi</label>
                                        <textarea x-model="form.description" style="width:100%; padding:10px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; min-height:80px; transition:border 0.15s ease;" rows="3"></textarea>
                                    </div>
                                </div>
                                <div style="display:flex; gap:10px; margin-top:18px; padding-top:16px; border-top:1px solid #EFEDEB;">
                                    <button type="submit" style="flex:1; justify-content:center; background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:10px 17px; border-radius:8px; border:none; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
                                            onmouseover="this.style.filter='brightness(1.05)'"
                                            onmouseout="this.style.filter='brightness(1)'">
                                        Simpan Jadwal
                                    </button>
                                    <button type="button"
                                            @click="modalOpen = false"
                                            style="flex:1; justify-content:center; background:white; color:#3D3A44; border:1px solid #E7E5E3; padding:10px 17px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
                                            onmouseover="this.style.background='#F8F7F6'"
                                            onmouseout="this.style.background='white'">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

<style>
/* ============================================================
   JADWAL KERJA — SCOPED STYLES
   ============================================================ */
.jkw, .jkw * { box-sizing: border-box !important; }
.jkw [x-cloak] { display: none !important; }

.jkw {
    --jkw-primary:#C81E2C;
    --jkw-primary-dark:#AF1424;
    --jkw-primary-soft:#FDF1F2;
    --jkw-primary-border:#F3C6CB;
    --jkw-success:#1E8A4C;
    --jkw-success-soft:#F0FAF3;
    --jkw-success-border:#BFE8C9;
    --jkw-ink:#17151C;
    --jkw-ink-2:#3D3A44;
    --jkw-muted:#75727C;
    --jkw-line:#E7E5E3;
    --jkw-line-soft:#EFEDEB;
    --jkw-surface:#FFFFFF;
    --jkw-bg-soft:#FAFAF9;
    color: var(--jkw-ink) !important;
    font-family: 'Inter', system-ui, sans-serif !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

@keyframes jkwFadeUp { from { opacity:0; transform:translateY(14px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes fadeInUp { from { opacity:0; transform:translateY(20px) scale(.95); } to { opacity:1; transform:translateY(0) scale(1); } }
.jkw-fade-enter { transition: opacity .18s ease, transform .18s ease !important; }
.jkw-fade-start { opacity:0 !important; }
.jkw-fade-end { opacity:1 !important; }

/* ---------- header ---------- */
.jkw-header {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    margin-bottom: 16px !important;
}

.jkw-tabs {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    background: #F1F0EE !important;
    padding: 3px !important;
    border-radius: 10px !important;
    border: 1px solid #E7E5E3 !important;
    height: 38px !important;
    box-sizing: border-box !important;
    flex-shrink: 0 !important;
}
.jkw-tab {
    all: unset !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    height: 30px !important;
    padding: 0 12px !important;
    border-radius: 7px !important;
    cursor: pointer !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: var(--jkw-muted) !important;
    transition: all .15s ease !important;
    text-align: center !important;
    white-space: nowrap !important;
    border: 1px solid transparent !important;
    box-sizing: border-box !important;
}
.jkw-tab.is-active {
    background: #FFFFFF !important;
    color: #17151C !important;
    border-color: #E7E5E3 !important;
    box-shadow: 0 1px 3px rgba(14,13,18,0.08) !important;
}
.jkw-tab-main { font-weight: 700 !important; }
.jkw-tab-sub { font-size: 11px !important; font-weight: 500 !important; opacity: 0.75 !important; }
.jkw-tab:hover:not(.is-active) { color: #17151C !important; background: rgba(255,255,255,0.6) !important; }

.jkw-actions {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-shrink: 0 !important;
    justify-content: flex-end !important;
    flex-wrap: nowrap !important;
}

.jkw-select-wrap {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    height: 38px !important;
    width: 175px !important;
    flex: 0 0 175px !important;
    box-sizing: border-box !important;
}
.jkw-select-icon {
    width: 14px !important;
    height: 14px !important;
    position: absolute !important;
    left: 11px !important;
    color: var(--jkw-muted) !important;
    pointer-events: none !important;
}
.jkw-select {
    all: unset !important;
    display: block !important;
    width: 100% !important;
    height: 38px !important;
    line-height: 38px !important;
    background: var(--jkw-surface) !important;
    border: 1px solid var(--jkw-line) !important;
    border-radius: 8px !important;
    padding: 0 10px 0 32px !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: var(--jkw-ink-2) !important;
    cursor: pointer !important;
    text-overflow: ellipsis !important;
    overflow: hidden !important;
    white-space: nowrap !important;
    box-sizing: border-box !important;
    box-shadow: 0 1px 2px rgba(14,13,18,0.04) !important;
}
.jkw-select:focus { border-color: var(--jkw-primary) !important; }

.jkw-btn {
    all: unset !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 38px !important;
    padding: 0 14px !important;
    border-radius: 8px !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    line-height: 1 !important;
    transition: all .15s ease !important;
    box-sizing: border-box !important;
    box-shadow: 0 1px 2px rgba(14,13,18,0.04) !important;
}
.jkw-btn--ghost { background: var(--jkw-surface) !important; color: var(--jkw-ink-2) !important; border: 1px solid var(--jkw-line) !important; }
.jkw-btn--ghost:hover { background: var(--jkw-bg-soft) !important; }
.jkw-btn--ghost.is-on { background: var(--jkw-ink) !important; color: #fff !important; border-color: var(--jkw-ink) !important; }
.jkw-btn--excel:hover { border-color: #10B981 !important; background: #ECFDF5 !important; color: #065F46 !important; }
.jkw-icon--excel { color: #10B981 !important; }
.jkw-btn--pdf:hover { border-color: #C81E2C !important; background: #FDF1F2 !important; color: #991B1B !important; }
.jkw-icon--pdf { color: #C81E2C !important; }
.jkw-btn--primary { background: var(--jkw-primary) !important; color: #fff !important; box-shadow: 0 8px 20px rgba(200,30,44,.24) !important; font-size: 13.5px !important; height: 38px !important; padding: 0 18px !important; }
.jkw-btn--primary:hover { background: var(--jkw-primary-dark) !important; }
.jkw-btn--block { flex: 1 !important; }
.jkw-icon { width: 14px !important; height: 14px !important; flex-shrink: 0 !important; }

/* ---------- card shell ---------- */
.jkw-card { background:var(--jkw-surface) !important; border:1px solid var(--jkw-line) !important; border-radius:14px !important; box-shadow:0 1px 2px rgba(14,13,18,.05) !important; overflow:hidden !important; margin-bottom:16px !important; width:100% !important; }

/* ---------- availability panel ---------- */
.jkw-avail-head { display:flex !important; align-items:center !important; justify-content:space-between !important; flex-wrap:wrap !important; gap:8px !important; padding:14px 16px !important; border-bottom:1px solid var(--jkw-line) !important; }
.jkw-eyebrow { font-size:11px !important; font-weight:700 !important; color:var(--jkw-muted) !important; text-transform:uppercase !important; letter-spacing:.4px !important; word-break:break-word !important; }
.jkw-check { display:flex !important; align-items:center !important; gap:6px !important; font-size:12.5px !important; color:var(--jkw-ink-2) !important; cursor:pointer !important; }
.jkw-avail-body { display:flex !important; flex-wrap:wrap !important; gap:8px !important; padding:14px 16px !important; }

.jkw-eng-chip {
    all: unset !important;
    display:flex !important; align-items:center !important; gap:9px !important; padding:7px 12px 7px 7px !important;
    border-radius:24px !important; border:1px solid !important; cursor:pointer !important; transition:all .15s ease !important;
    flex:1 1 220px !important; max-width:280px !important;
}
.jkw-eng-chip.is-free { background:var(--jkw-success-soft) !important; border-color:var(--jkw-success-border) !important; }
.jkw-eng-chip.is-busy { background:var(--jkw-primary-soft) !important; border-color:var(--jkw-primary-border) !important; }
.jkw-eng-chip:hover { filter:brightness(0.97) !important; }
.jkw-eng-info { display:flex !important; flex-direction:column !important; line-height:1.25 !important; min-width:0 !important; }
.jkw-eng-name { font-size:12.5px !important; font-weight:700 !important; color:var(--jkw-ink) !important; overflow:hidden !important; text-overflow:ellipsis !important; white-space:nowrap !important; }
.jkw-eng-status { font-size:11px !important; color:var(--jkw-muted) !important; }
.jkw-dot { width:7px !important; height:7px !important; border-radius:50% !important; flex-shrink:0 !important; margin-left:auto !important; }
.jkw-dot.is-free { background:var(--jkw-success) !important; }
.jkw-dot.is-busy { background:var(--jkw-primary) !important; }

.jkw-avatar { width:26px !important; height:26px !important; border-radius:50% !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; color:#fff !important; font-size:11px !important; font-weight:700 !important; flex-shrink:0 !important; }
.jkw-avatar--sm { width:20px !important; height:20px !important; font-size:9.5px !important; }

.jkw-empty-inline { font-size:12.5px !important; color:var(--jkw-muted) !important; padding:6px 0 !important; }

/* ---------- nav row (day/week/month) ---------- */
.jkw-nav { display:flex !important; align-items:center !important; justify-content:space-between !important; padding:12px 14px !important; border-bottom:1px solid var(--jkw-line) !important; gap:6px !important; }
.jkw-nav-btn { all:unset !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; cursor:pointer !important; color:var(--jkw-muted) !important; padding:7px !important; border-radius:8px !important; transition:background .15s ease !important; flex-shrink:0 !important; }
.jkw-nav-btn:hover { background:var(--jkw-bg-soft) !important; color:var(--jkw-ink) !important; }
.jkw-nav-center { display:flex !important; flex:1 1 auto !important; min-width:0 !important; align-items:center !important; justify-content:center !important; gap:8px !important; flex-wrap:wrap !important; }
.jkw-nav-title { font-size:clamp(11.5px, 3vw, 14px) !important; font-weight:700 !important; color:var(--jkw-ink) !important; text-transform:capitalize !important; text-align:center !important; overflow:hidden !important; text-overflow:ellipsis !important; white-space:nowrap !important; max-width:100% !important; }
.jkw-today-btn { all:unset !important; font-size:11px !important; font-weight:700 !important; color:var(--jkw-primary) !important; border:1px solid var(--jkw-primary-border) !important; padding:4px 10px !important; border-radius:6px !important; cursor:pointer !important; white-space:nowrap !important; flex-shrink:0 !important; }
.jkw-today-btn:hover { background:var(--jkw-primary-soft) !important; }
.jkw-mono { font-family:'IBM Plex Mono', monospace !important; }

/* ---------- day view ---------- */
.jkw-day-list { padding:14px 14px !important; display:flex !important; flex-direction:column !important; gap:8px !important; }
.jkw-day-item { display:flex !important; align-items:flex-start !important; gap:12px !important; padding:12px !important; background:var(--jkw-primary-soft) !important; border-left:3px solid var(--jkw-primary) !important; border-radius:8px !important; flex-wrap:wrap !important; }
.jkw-time { font-family:'IBM Plex Mono', monospace !important; font-size:12px !important; font-weight:700 !important; color:var(--jkw-primary-dark) !important; white-space:nowrap !important; }
.jkw-day-main { flex:1 1 160px !important; min-width:0 !important; }
.jkw-day-title { font-size:13.5px !important; font-weight:700 !important; color:var(--jkw-ink) !important; margin-bottom:3px !important; word-break:break-word !important; }
.jkw-day-meta { display:flex !important; align-items:center !important; gap:6px !important; font-size:11.5px !important; color:var(--jkw-muted) !important; flex-wrap:wrap !important; }
.jkw-sep { color:var(--jkw-line) !important; }

.jkw-empty { text-align:center !important; padding:40px 16px !important; color:var(--jkw-muted) !important; }
.jkw-empty-icon { width:44px !important; height:44px !important; border-radius:10px !important; background:var(--jkw-bg-soft) !important; display:flex !important; align-items:center !important; justify-content:center !important; margin:0 auto 12px !important; }
.jkw-empty-icon svg { width:20px !important; height:20px !important; opacity:.6 !important; }
.jkw-empty p { font-size:13.5px !important; margin:0 !important; }

/* ---------- week view ---------- */
.jkw-week-scroll { overflow-x:auto !important; -webkit-overflow-scrolling:touch !important; scroll-snap-type:x proximity !important; }
.jkw-week-grid { display:grid !important; grid-template-columns:repeat(7, minmax(120px,1fr)) !important; min-width:840px !important; }
.jkw-week-col { min-height:200px !important; padding:12px !important; border-right:1px solid var(--jkw-line-soft) !important; scroll-snap-align:start !important; }
.jkw-week-col:last-child { border-right:none !important; }
.jkw-week-col.is-today { background:var(--jkw-bg-soft) !important; }
.jkw-week-col-head { font-size:11px !important; color:var(--jkw-muted) !important; font-weight:700 !important; margin-bottom:10px !important; display:flex !important; align-items:center !important; gap:5px !important; }
.jkw-week-col-body { display:flex !important; flex-direction:column !important; gap:6px !important; }
.jkw-mini-card { background:rgba(37,99,235,0.07) !important; border-radius:6px !important; padding:6px 9px !important; }
.jkw-mini-time { font-family:'IBM Plex Mono', monospace !important; font-size:10px !important; font-weight:700 !important; color:var(--jkw-ink-2) !important; }
.jkw-mini-title { font-size:11.5px !important; color:var(--jkw-ink) !important; font-weight:600 !important; line-height:1.3 !important; margin-top:1px !important; word-break:break-word !important; }
.jkw-mini-eng { font-size:9.5px !important; color:var(--jkw-muted) !important; margin-top:1px !important; }
.jkw-mini-empty { font-size:11px !important; color:#C7C4CD !important; padding:8px 0 !important; text-align:center !important; }

/* ---------- month view ---------- */
.jkw-month-scroll { overflow-x:auto !important; -webkit-overflow-scrolling:touch !important; }
.jkw-month-dow { display:grid !important; grid-template-columns:repeat(7, minmax(88px,1fr)) !important; border-bottom:1px solid var(--jkw-line-soft) !important; min-width:700px !important; }
.jkw-month-dow-cell { padding:9px !important; text-align:center !important; font-size:10.5px !important; font-weight:700 !important; color:var(--jkw-muted) !important; text-transform:uppercase !important; letter-spacing:.3px !important; }
.jkw-month-row { display:grid !important; grid-template-columns:repeat(7, minmax(88px,1fr)) !important; border-bottom:1px solid var(--jkw-line-soft) !important; min-width:700px !important; }
.jkw-month-row:last-child { border-bottom:none !important; }
.jkw-month-cell { min-height:90px !important; padding:7px 8px !important; border-right:1px solid var(--jkw-line-soft) !important; }
.jkw-month-cell:last-child { border-right:none !important; }
.jkw-month-cell.is-out { background:var(--jkw-bg-soft) !important; }
.jkw-month-cell.is-today { background:var(--jkw-primary-soft) !important; }
.jkw-month-daynum { font-size:11.5px !important; font-weight:700 !important; color:var(--jkw-ink) !important; margin-bottom:5px !important; }
.jkw-month-cell.is-out .jkw-month-daynum { color:#C7C4CD !important; }
.jkw-month-events { display:flex !important; flex-direction:column !important; gap:2px !important; }
.jkw-month-event { border-radius:4px !important; padding:2px 6px !important; font-size:9.5px !important; font-weight:600 !important; color:#fff !important; overflow:hidden !important; text-overflow:ellipsis !important; white-space:nowrap !important; }
.jkw-month-more { font-size:9.5px !important; color:var(--jkw-muted) !important; padding-left:6px !important; }

/* ---------- table view ---------- */
.jkw-table-scroll { overflow-x:auto !important; -webkit-overflow-scrolling:touch !important; }
.jkw-table { width:100% !important; border-collapse:collapse !important; font-size:13px !important; min-width:720px !important; }
.jkw-table thead tr { background:var(--jkw-bg-soft) !important; }
.jkw-table th { text-align:left !important; padding:10px 14px !important; font-size:11px !important; font-weight:700 !important; color:var(--jkw-muted) !important; text-transform:uppercase !important; letter-spacing:.3px !important; white-space:nowrap !important; border-bottom:1px solid var(--jkw-line) !important; }
.jkw-th-right { text-align:right !important; }
.jkw-table td { padding:11px 14px !important; border-top:1px solid var(--jkw-line-soft) !important; color:var(--jkw-ink-2) !important; }
.jkw-table tbody tr { transition:background .12s ease !important; }
.jkw-table tbody tr:hover { background:var(--jkw-bg-soft) !important; }
.jkw-td-strong { font-weight:600 !important; color:var(--jkw-ink) !important; }
.jkw-td-wrap { max-width:220px !important; white-space:normal !important; word-break:break-word !important; }
.jkw-eng-cell { display:flex !important; align-items:center !important; gap:8px !important; white-space:nowrap !important; }
.jkw-row-actions { display:flex !important; justify-content:flex-end !important; gap:4px !important; }
.jkw-icon-btn { all:unset !important; display:inline-flex !important; align-items:center !important; justify-content:center !important; cursor:pointer !important; color:var(--jkw-muted) !important; padding:6px !important; border-radius:7px !important; transition:background .15s ease !important; flex-shrink:0 !important; }
.jkw-icon-btn svg { width:15px !important; height:15px !important; }
.jkw-icon-btn:hover { background:var(--jkw-bg-soft) !important; color:var(--jkw-ink) !important; }
.jkw-icon-btn--danger { color:var(--jkw-primary) !important; }
.jkw-icon-btn--danger:hover { background:var(--jkw-primary-soft) !important; color:var(--jkw-primary-dark) !important; }

/* ---------- modal ---------- */
.jkw-modal-overlay { position:fixed !important; inset:0 !important; background:rgba(14,13,18,.6) !important; z-index:99999 !important; display:flex !important; align-items:center !important; justify-content:center !important; padding:16px !important; backdrop-filter:blur(2px) !important; }
.jkw-modal { background:var(--jkw-surface) !important; border-radius:16px !important; width:640px !important; max-width:100% !important; max-height:90vh !important; overflow-y:auto !important; animation:jkwFadeUp .18s ease !important; box-shadow:0 16px 40px rgba(14,13,18,.16) !important; margin:auto !important; }
.jkw-modal-head { display:flex !important; align-items:center !important; justify-content:space-between !important; padding:16px 18px !important; position:sticky !important; top:0 !important; background:var(--jkw-surface) !important; border-bottom:1px solid var(--jkw-line) !important; z-index:1 !important; border-radius:16px 16px 0 0 !important; gap:12px !important; }
.jkw-modal-head h3 { margin:0 !important; font-family:'Space Grotesk', sans-serif !important; font-size:17px !important; font-weight:700 !important; color:var(--jkw-ink) !important; word-break:break-word !important; }
.jkw-modal-body { padding:18px !important; }
.jkw-form-grid { display:flex !important; flex-direction:column !important; gap:14px !important; }
.jkw-field-row { display:grid !important; grid-template-columns:1fr 1fr !important; gap:12px !important; }
.jkw-field { display:flex !important; flex-direction:column !important; }
.jkw-field label { display:block !important; font-size:11.5px !important; font-weight:700 !important; color:var(--jkw-muted) !important; margin-bottom:6px !important; text-transform:uppercase !important; letter-spacing:.3px !important; }
.jkw-field input, .jkw-field select, .jkw-field textarea {
    all: unset !important;
    width:100% !important; padding:10px 12px !important; border-radius:8px !important; border:1px solid var(--jkw-line) !important;
    font-size:14px !important; color:var(--jkw-ink) !important; background:var(--jkw-surface) !important; transition:border-color .15s ease !important;
}
.jkw-field textarea { min-height:80px !important; resize:vertical !important; font-family:inherit !important; }
.jkw-field input:focus, .jkw-field select:focus, .jkw-field textarea:focus { border-color:var(--jkw-primary) !important; }
.jkw-modal-actions-row { display:flex !important; flex-direction:row !important; gap:10px !important; margin-top:18px !important; padding-top:16px !important; border-top:1px solid var(--jkw-line-soft) !important; }

/* ===================== RESPONSIVE BREAKPOINTS ===================== */

/* Tablet and below */
@media (max-width: 960px) {
    .jkw-header {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 12px !important;
    }
    .jkw-actions {
        width: 100% !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
    }
    .jkw-select-wrap {
        flex: 1 1 180px !important;
        width: auto !important;
    }
    .jkw-btn {
        flex: 1 1 auto !important;
    }
}

/* Small tablet / large phone */
@media (max-width: 720px) {
    .jkw-nav { flex-wrap:nowrap !important; padding:10px 10px !important; }
    .jkw-field-row { grid-template-columns:1fr !important; }
    .jkw-eng-chip { flex:1 1 100% !important; max-width:100% !important; }
}

/* Phone */
@media (max-width: 480px) {
    .jkw {
        --card-radius: 10px;
    }
    .jkw-card { border-radius:10px !important; }
    .jkw-tabs { padding:3px !important; }
    .jkw-tab { padding:8px 4px !important; font-size:11px !important; }
    .jkw-btn { font-size:11.5px !important; padding:9px 12px !important; }
    .jkw-btn span { white-space:normal !important; }
    .jkw-nav-btn { padding:5px !important; }
    .jkw-today-btn { padding:3px 7px !important; font-size:10px !important; }
    .jkw-avail-head { padding:12px !important; }
    .jkw-avail-body { padding:12px !important; }
    .jkw-eng-chip { padding:6px 10px 6px 6px !important; }
    .jkw-avatar { width:24px !important; height:24px !important; font-size:10px !important; }
    .jkw-day-item { flex-direction:column !important; align-items:flex-start !important; gap:6px !important; padding:10px !important; }
    .jkw-day-main { min-width:0 !important; flex-basis:100% !important; }
    .jkw-td-wrap { max-width:140px !important; }
    .jkw-table th, .jkw-table td { padding:9px 10px !important; }

    /* modal becomes near full-screen on phones */
    .jkw-modal-overlay { padding:0 !important; align-items:flex-end !important; }
    .jkw-modal { width:100% !important; max-width:100% !important; max-height:92vh !important; border-radius:16px 16px 0 0 !important; }
    .jkw-modal-actions-row { flex-direction:column !important; }

    /* prevent iOS Safari auto-zoom on input focus */
    .jkw-field input, .jkw-field select, .jkw-field textarea { font-size:16px !important; }
}

/* Very small phones */
@media (max-width: 360px) {
    .jkw-tab { font-size:10px !important; padding:7px 3px !important; }
    .jkw-nav-title { font-size:11px !important; }
    .jkw-eng-name, .jkw-eng-status { font-size:10.5px !important; }
}
</style>

@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('schedulesManager', function() {
            return {
                schedules: @json($schedules),
                tasks: @json($tasks),
                calendarProjects: @json($calendarProjects),
                projects: @json($projects),
                engineers: @json($engineers),
                viewMode: 'day',
                currentDate: new Date(),
                showAvailability: false,
                showOnlyAvailable: false,
                engineerFilter: '',
                modalOpen: false,
                editing: false,
                confirmOpen: false,
                confirmData: null,
                form: {
                    id: null,
                    title: '',
                    project_id: null,
                    engineer_id: null,
                    date: '',
                    start_time: '08:00',
                    end_time: '17:00',
                    location: '',
                    description: ''
                },

                init: function() {},

                // Warna event
                // Jadwal biasa  = Biru
                // Deadline Task = Merah
                // Deadline Proj = Merah tua

                formatDate: function(d) {
                    var y = d.getFullYear();
                    var m = String(d.getMonth() + 1).padStart(2, '0');
                    var day = String(d.getDate()).padStart(2, '0');
                    return y + '-' + m + '-' + day;
                },
                initials: function(name) {
                    if (!name) return '?';
                    var parts = name.trim().split(/\s+/);
                    return ((parts[0] ? parts[0][0] : '') + (parts[1] ? parts[1][0] : '')).toUpperCase();
                },
                colorFromName: function(name) {
                    var palette = ['#C81E2C', '#1E8A4C', '#B8860B', '#3457D5', '#7A4FBF', '#C2563E', '#0E7C86'];
                    var hash = 0;
                    for (var i = 0; i < (name || '').length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
                    return palette[Math.abs(hash) % palette.length];
                },

                get todayStr() {
                    return this.formatDate(new Date());
                },

                get filteredSchedules() {
                    if (!this.engineerFilter) return this.schedules;
                    return this.schedules.filter(function(s) {
                        return String(s.engineer_id) === String(this.engineerFilter);
                    }.bind(this));
                },

                get currentDateStr() {
                    return this.formatDate(this.currentDate);
                },
                get dayLabel() {
                    return this.currentDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },
                dayPage: 1,
                perPage: 10,

                get daySchedules() {
                    return this.getAllEventsForDay(this.currentDateStr).sort(function(a, b) {
                        return (a._timeLabel || '').localeCompare(b._timeLabel || '');
                    });
                },
                get totalDayPages() {
                    return Math.ceil(this.daySchedules.length / this.perPage) || 1;
                },
                get paginatedDaySchedules() {
                    var start = (this.dayPage - 1) * this.perPage;
                    return this.daySchedules.slice(start, start + this.perPage);
                },
                goToDayPage: function(p) {
                    if (p >= 1 && p <= this.totalDayPages) {
                        this.dayPage = p;
                    }
                },
                prevDayPage: function() {
                    if (this.dayPage > 1) {
                        this.dayPage--;
                    }
                },
                nextDayPage: function() {
                    if (this.dayPage < this.totalDayPages) {
                        this.dayPage++;
                    }
                },
                changeDay: function(delta) {
                    var d = new Date(this.currentDate);
                    d.setDate(d.getDate() + delta);
                    this.currentDate = d;
                    this.dayPage = 1;
                },
                goToday: function() {
                    this.currentDate = new Date();
                    this.dayPage = 1;
                },

                get weekDays() {
                    var start = new Date(this.currentDate);
                    start.setDate(start.getDate() - start.getDay());
                    var days = [];
                    for (var i = 0; i < 7; i++) {
                        var d = new Date(start);
                        d.setDate(d.getDate() + i);
                        days.push({
                            name: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'][d.getDay()],
                            date: d.getDate(),
                            fullDate: this.formatDate(d),
                            index: i
                        });
                    }
                    return days;
                },
                get weekRange() {
                    return this.weekDays[0].fullDate + ' — ' + this.weekDays[6].fullDate;
                },
                changeWeek: function(delta) {
                    var d = new Date(this.currentDate);
                    d.setDate(d.getDate() + (delta * 7));
                    this.currentDate = d;
                },

                get monthLabel() {
                    return this.currentDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
                },
                get monthWeeks() {
                    var year = this.currentDate.getFullYear();
                    var month = this.currentDate.getMonth();
                    var startDate = new Date(year, month, 1);
                    startDate.setDate(startDate.getDate() - startDate.getDay());
                    var weeks = [];
                    var cursor = new Date(startDate);
                    for (var w = 0; w < 6; w++) {
                        var days = [];
                        for (var i = 0; i < 7; i++) {
                            days.push({
                                dayNum: cursor.getDate(),
                                fullDate: this.formatDate(cursor),
                                inMonth: cursor.getMonth() === month
                            });
                            cursor.setDate(cursor.getDate() + 1);
                        }
                        weeks.push(days);
                    }
                    return weeks;
                },
                changeMonth: function(delta) {
                    var d = new Date(this.currentDate);
                    d.setMonth(d.getMonth() + delta);
                    this.currentDate = d;
                },

                goToday: function() {
                    this.currentDate = new Date();
                },

                get periodDates() {
                    if (this.viewMode === 'day') return [this.currentDateStr];
                    if (this.viewMode === 'week') return this.weekDays.map(function(d) { return d.fullDate; });
                    if (this.viewMode === 'month') {
                        var flat = [];
                        this.monthWeeks.forEach(function(w) {
                            w.forEach(function(d) {
                                if (d.inMonth) flat.push(d.fullDate);
                            });
                        });
                        return flat;
                    }
                    return [];
                },
                get periodLabel() {
                    if (this.viewMode === 'day') return this.dayLabel;
                    if (this.viewMode === 'week') return this.weekRange;
                    if (this.viewMode === 'month') return this.monthLabel;
                    return '';
                },
                get engineerAvailability() {
                    var dates = this.periodDates;
                    return this.engineers.map(function(eng) {
                        var count = this.schedules.filter(function(s) {
                            return s.engineer_id === eng.id && dates.indexOf(s.date.split('T')[0]) !== -1;
                        }).length;
                        return { id: eng.id, name: eng.name, scheduleCount: count, available: count === 0 };
                    }.bind(this)).sort(function(a, b) {
                        if (a.available === b.available) return 0;
                        return a.available ? -1 : 1;
                    });
                },
                get filteredEngineerAvailability() {
                    return this.showOnlyAvailable ? this.engineerAvailability.filter(function(e) { return e.available; }) : this.engineerAvailability;
                },
                isEngineerBusyOnDate: function(engineerId, date) {
                    if (!date) return false;
                    return this.schedules.some(function(s) {
                        return s.engineer_id === engineerId && s.date.split('T')[0] === date && s.id !== this.form.id;
                    }.bind(this));
                },

                getSchedulesForDay: function(date) {
                    return this.filteredSchedules.filter(function(s) {
                        var scheduleDate = s.date.split('T')[0];
                        return scheduleDate === date;
                    }).map(function(s) {
                        return {
                            id: s.id,
                            title: s.title,
                            project_id: s.project_id,
                            engineer_id: s.engineer_id,
                            date: s.date,
                            start_time: s.start_time ? s.start_time.substring(0, 5) : '',
                            end_time: s.end_time ? s.end_time.substring(0, 5) : '',
                            location: s.location,
                            description: s.description,
                            project: s.project,
                            engineer: s.engineer
                        };
                    });
                },

                getAllEventsForDay: function(date) {
                    var self = this;
                    var events = [];

                    // --- Jadwal biasa (BIRU) ---
                    var filtered = this.engineerFilter
                        ? this.schedules.filter(function(s) { return String(s.engineer_id) === String(self.engineerFilter); })
                        : this.schedules;
                    filtered.forEach(function(s) {
                        var d = (s.date || '').split('T')[0];
                        if (d === date) {
                            events.push({
                                _uid: 'sch-' + s.id,
                                _type: 'schedule',
                                _color: '#2563EB',
                                _displayTitle: s.title,
                                _timeLabel: s.start_time ? s.start_time.substring(0, 5) : '',
                                _subLabel: (s.engineer && s.engineer.name) ? s.engineer.name : (s.project && s.project.name ? s.project.name : ''),
                                id: s.id,
                                title: s.title,
                                project_id: s.project_id,
                                engineer_id: s.engineer_id,
                                date: s.date,
                                start_time: s.start_time ? s.start_time.substring(0, 5) : '',
                                end_time: s.end_time ? s.end_time.substring(0, 5) : '',
                                location: s.location,
                                description: s.description,
                                project: s.project,
                                engineer: s.engineer
                            });
                        }
                    });

                    // --- Deadline Task (MERAH) ---
                    var filteredTasks = this.engineerFilter
                        ? this.tasks.filter(function(t) { return String(t.engineer_id) === String(self.engineerFilter); })
                        : this.tasks;
                    filteredTasks.forEach(function(t) {
                        if (t.deadline === date) {
                            events.push({
                                _uid: 'task-' + t.id,
                                _type: 'task',
                                _color: '#C81E2C',
                                _displayTitle: 'Deadline: ' + t.title,
                                _timeLabel: 'Deadline',
                                _subLabel: (t.engineer && t.engineer.name) ? t.engineer.name : (t.project && t.project.name ? t.project.name : ''),
                                id: t.id,
                                title: t.title,
                                project: t.project,
                                engineer: t.engineer,
                                priority: t.priority,
                                status: t.status,
                                deadline: t.deadline,
                                start_time: '',
                                end_time: '',
                                location: '',
                                description: ''
                            });
                        }
                    });

                    // --- Deadline Project (MERAH TUA) ---
                    this.calendarProjects.forEach(function(p) {
                        if (p.deadline === date) {
                            events.push({
                                _uid: 'proj-' + p.id,
                                _type: 'project',
                                _color: '#991B1B',
                                _displayTitle: 'Deadline: ' + p.name,
                                _timeLabel: 'Deadline',
                                _subLabel: p.client || '',
                                id: p.id,
                                title: p.name,
                                project: { id: p.id, name: p.name },
                                engineer: null,
                                start_time: '',
                                end_time: '',
                                location: '',
                                description: ''
                            });
                        }
                    });

                    return events;
                },

                get modalTitle() {
                    return this.editing ? 'Edit Jadwal' : 'Buat Jadwal';
                },

                openModal: function(schedule) {
                    if (schedule) {
                        this.editing = true;
                        this.form = {
                            id: schedule.id,
                            title: schedule.title,
                            project_id: schedule.project_id,
                            engineer_id: schedule.engineer_id,
                            date: schedule.date.split('T')[0],
                            start_time: schedule.start_time,
                            end_time: schedule.end_time,
                            location: schedule.location,
                            description: schedule.description || ''
                        };
                    } else {
                        this.editing = false;
                        this.form = {
                            id: null,
                            title: '',
                            project_id: this.projects[0] ? this.projects[0].id : null,
                            engineer_id: this.engineers[0] ? this.engineers[0].id : null,
                            date: this.viewMode === 'day' ? this.currentDateStr : '',
                            start_time: '08:00',
                            end_time: '17:00',
                            location: '',
                            description: ''
                        };
                    }
                    this.modalOpen = true;
                },

                editSchedule: function(schedule) {
                    this.openModal(schedule);
                },

                confirmDelete: function(schedule) {
                    this.confirmData = schedule;
                    this.confirmOpen = true;
                },

                confirmDeleteAction: function() {
                    if (this.confirmData) {
                        this.deleteSchedule(this.confirmData);
                    }
                    this.confirmOpen = false;
                },

                saveSchedule: async function() {
                    try {
                        var url = this.editing ? '/schedules/' + this.form.id : '/schedules';
                        var method = this.editing ? 'PUT' : 'POST';

                        var response = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(this.form)
                        });

                        if (response.ok) {
                            var data = await response.json();
                            if (this.editing) {
                                var index = this.schedules.findIndex(function(s) { return s.id === this.form.id; }.bind(this));
                                this.schedules[index] = data;
                            } else {
                                this.schedules.push(data);
                            }
                            this.modalOpen = false;
                            this.showToast('Jadwal berhasil ' + (this.editing ? 'diperbarui' : 'ditambahkan') + '!');
                        } else {
                            var error = await response.json();
                            this.showToast('Error: ' + (error.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error saving schedule:', error);
                        this.showToast('Terjadi kesalahan saat menyimpan jadwal.');
                    }
                },

                deleteSchedule: async function(schedule) {
                    try {
                        var response = await fetch('/schedules/' + schedule.id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            this.schedules = this.schedules.filter(function(s) {
                                return s.id !== schedule.id;
                            });
                            this.showToast('Jadwal berhasil dihapus!');
                        } else {
                            this.showToast('Gagal menghapus jadwal.');
                        }
                    } catch (error) {
                        console.error('Error deleting schedule:', error);
                        this.showToast('Terjadi kesalahan saat menghapus jadwal.');
                    }
                },

                showToast: function(message) {
                    var oldToast = document.querySelector('.jkw-toast-custom');
                    if (oldToast) oldToast.remove();

                    var toast = document.createElement('div');
                    toast.className = 'jkw-toast-custom';
                    toast.style.cssText = 'position:fixed; bottom:16px; right:16px; left:16px; max-width:340px; margin-left:auto; background:#17151C; color:white; padding:12px 20px; border-radius:8px; box-shadow:0 16px 40px rgba(14,13,18,0.16); font-size:14px; z-index:999999; animation:jkwFadeUp 0.18s ease;';
                    toast.textContent = message;
                    document.body.appendChild(toast);

                    setTimeout(function() {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.3s ease';
                        setTimeout(function() {
                            toast.remove();
                        }, 300);
                    }, 3000);
                }
            };
        });
    });
</script>
@endpush
@endsection