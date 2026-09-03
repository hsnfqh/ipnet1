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

                    <button type="button"
                            @click="openModal()"
                            class="jkw-btn jkw-btn--primary"
                            title="Tambah Jadwal Baru">
                        <svg class="jkw-icon" style="color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Jadwal</span>
                    </button>
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
                        <button type="button" class="jkw-eng-chip" :class="eng.available ? 'is-free' : (eng.isDayOff ? 'is-dayoff' : 'is-busy')" @click="engineerFilter = (engineerFilter == eng.id ? '' : eng.id)">
                            <span class="jkw-avatar" :style="'background:' + colorFromName(eng.name)" x-text="initials(eng.name)"></span>
                            <span class="jkw-eng-info">
                                <span class="jkw-eng-name" x-text="eng.name"></span>
                                <span class="jkw-eng-status" x-text="eng.statusLabel"></span>
                            </span>
                            <span class="jkw-dot" :class="eng.available ? 'is-free' : (eng.isDayOff ? 'is-dayoff' : 'is-busy')"></span>
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
                        <span style="font-size:12px; color:var(--jkw-ink-2); white-space:nowrap;">Jadwal Meeting</span>
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
                    <span style="color:var(--jkw-line); flex-shrink:0;">|</span>
                    <span style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                        <span style="width:10px; height:10px; border-radius:2px; background:#64748B; flex-shrink:0; display:inline-block;"></span>
                        <span style="font-size:12px; color:var(--jkw-ink-2); white-space:nowrap;">Day Off / Libur</span>
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
                            <div class="jkw-day-item" :class="'jkw-day-item--' + schedule._type">
                                <div class="jkw-time" style="display:flex; align-items:center; gap:5px;">
                                    <svg style="width:13px; height:13px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                                    <span x-text="schedule._timeLabel"></span>
                                </div>
                                <div class="jkw-day-main">
                                    <div class="jkw-day-title" style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                        <span x-text="schedule._displayTitle"></span>
                                        <template x-if="schedule._type === 'schedule' || schedule._type === 'day_off'">
                                            @if($isLead)
                                            <div style="display:flex; align-items:center; gap:6px;">
                                                <template x-if="schedule._type === 'schedule'">
                                                    <button type="button" @click="shareWhatsApp(schedule)" style="background:#22C55E; color:white; border:none; border-radius:6px; padding:3px 9px; font-size:11px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px; flex-shrink:0; box-shadow:0 2px 6px rgba(34,197,94,0.25);" title="Bagikan Undangan Meeting ke WhatsApp">
                                                        <svg style="width:12px; height:12px;" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                        Kirim ke WA
                                                    </button>
                                                </template>
                                                <button type="button" @click="openModal(schedule)" style="background:#F8F7F6; border:1px solid #E7E5E3; border-radius:6px; padding:3px 8px; font-size:11px; font-weight:600; color:#3D3A44; cursor:pointer; display:inline-flex; align-items:center; gap:4px; flex-shrink:0;" title="Edit Jadwal">
                                                    <svg style="width:11px; height:11px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                <button type="button" @click="deleteSchedule(schedule)" style="background:#FEF2F2; border:1px solid #FECACA; border-radius:6px; padding:3px 8px; font-size:11px; font-weight:600; color:#DC2626; cursor:pointer; display:inline-flex; align-items:center; gap:4px; flex-shrink:0;" title="Hapus Jadwal">
                                                    <svg style="width:11px; height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Hapus
                                                </button>
                                            </div>
                                            @endif
                                        </template>
                                    </div>
                                    <div class="jkw-day-meta">
                                        <template x-if="schedule.engineers && schedule.engineers.length > 0">
                                            <span style="display:inline-flex; align-items:center; gap:5px; flex-wrap:wrap;">
                                                <template x-for="eng in schedule.engineers" :key="eng.id">
                                                    <span style="display:inline-flex; align-items:center; gap:4px; background:#FFFFFF; border:1px solid #E7E5E3; padding:1px 6px; border-radius:12px; font-size:11px; font-weight:600; color:#17151C;">
                                                        <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(eng.name)" x-text="initials(eng.name)"></span>
                                                        <span x-text="eng.name"></span>
                                                    </span>
                                                </template>
                                            </span>
                                        </template>
                                        <template x-if="!schedule.engineers || schedule.engineers.length === 0">
                                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                                <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(schedule.engineer?.name || '-')" x-text="initials(schedule.engineer?.name || '-')"></span>
                                                <span x-text="schedule.engineer?.name || '-'"></span>
                                            </span>
                                        </template>
                                        <span class="jkw-sep">·</span>
                                        <span x-text="schedule.project?.name || '-'"></span>
                                        <template x-if="schedule.location">
                                            <span>
                                                <span class="jkw-sep">·</span>
                                                <span x-text="schedule.location"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="jkw-empty" x-show="daySchedules.length === 0" style="padding:48px 16px 56px; text-align:center;">
                            <div class="jkw-empty-icon" style="margin-bottom:12px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <p style="margin-bottom:20px; font-size:13.5px; color:#75727C;">Tidak ada jadwal atau deadline pada hari ini.</p>
                            <button type="button" @click="openModal()" style="margin-top:20px !important; background:#C81E2C; color:white; border:none; padding:10px 22px; border-radius:9px; font-weight:600; font-size:13px; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 14px rgba(200,30,44,0.22); transition:all 0.15s ease;">
                                <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Jadwal Hari Ini
                            </button>
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
                                            <div class="jkw-mini-card" 
                                                 :class="'jkw-mini-card--' + event._type"
                                                 :style="'border-left: 3px solid ' + event._color + ';' + (event._type === 'schedule' || event._type === 'day_off' ? ' cursor:pointer;' : ' cursor:default; pointer-events:none;')"
                                                 :title="event._tooltip"
                                                 @click="if (event._type === 'schedule' || event._type === 'day_off') { openModal(event); }">
                                                <div class="jkw-mini-time" x-text="event._timeLabel" style="font-size:10px; font-weight:700;" :style="{ color: event._color }"></div>
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
                                                <div class="jkw-month-event"
                                                     :style="'background:' + event._color + '; color:#fff; display:flex; align-items:center; gap:4px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; padding:2px 6px; border-radius:4px; font-size:10px; margin-bottom:2px;' + (event._type === 'schedule' || event._type === 'day_off' ? ' cursor:pointer; opacity:1;' : ' cursor:default; opacity:0.92; pointer-events:none;')"
                                                     :title="event._tooltip"
                                                     @click="if (event._type === 'schedule' || event._type === 'day_off') { openModal(event); }">
                                                    <span x-show="(event._type === 'schedule' || event._type === 'day_off') && event.start_time" style="font-family:'IBM Plex Mono',monospace; font-weight:700; font-size:9.5px; opacity:0.95; flex-shrink:0;" x-text="event.start_time"></span>
                                                    <span x-show="event._type === 'task' && event.deadline_time" style="font-family:'IBM Plex Mono',monospace; font-weight:700; font-size:9.5px; opacity:0.95; flex-shrink:0;" x-text="event.deadline_time"></span>
                                                    <span style="font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="event._displayTitle"></span>
                                                    <span x-show="event._subLabel" style="opacity:0.88; font-weight:400; font-size:9px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="'(' + event._subLabel + ')'"></span>
                                                </div>
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
                                    <th>Kategori</th>
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
                                        <td>
                                            <span style="font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:12px; display:inline-block;"
                                                  :style="schedule.category === 'Day Off' ? 'background:#F1F5F9; color:#475569; border:1px solid #CBD5E1;' : 'background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE;'"
                                                  x-text="schedule.category === 'Day Off' ? 'Day Off' : 'Meeting'"></span>
                                        </td>
                                        <td class="jkw-td-wrap" x-text="schedule.category === 'Day Off' ? '-' : (schedule.project?.name || '-')"></td>
                                        <td>
                                            <span class="jkw-eng-cell">
                                                <template x-if="schedule.engineers && schedule.engineers.length > 0">
                                                    <span style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                                                        <template x-for="eng in schedule.engineers" :key="eng.id">
                                                            <span style="display:inline-flex; align-items:center; gap:4px; background:#F8F7F6; border:1px solid #E7E5E3; padding:2px 7px; border-radius:12px; font-size:11px; font-weight:600; color:#17151C;">
                                                                <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(eng.name)" x-text="initials(eng.name)"></span>
                                                                <span x-text="eng.name"></span>
                                                            </span>
                                                        </template>
                                                    </span>
                                                </template>
                                                <template x-if="!schedule.engineers || schedule.engineers.length === 0">
                                                    <span style="display:inline-flex; align-items:center; gap:6px;">
                                                        <span class="jkw-avatar jkw-avatar--sm" :style="'background:' + colorFromName(schedule.engineer?.name || '-')" x-text="initials(schedule.engineer?.name || '-')"></span>
                                                        <span x-text="schedule.engineer?.name || '-'"></span>
                                                    </span>
                                                </template>
                                            </span>
                                        </td>
                                        <td class="jkw-mono" x-text="schedule.date.split('T')[0]"></td>
                                        <td class="jkw-mono" x-text="schedule.start_time ? (schedule.start_time.substring(0, 5) + ' WIB') : '-'"></td>
                                        <td class="jkw-td-wrap" x-text="schedule.location || '-'"></td>
                                        @if($isLead)
                                        <td>
                                            <div style="display:flex; justify-content:flex-end; gap:4px;">
                                                <template x-if="schedule.category !== 'Day Off'">
                                                    <button type="button"
                                                            @click="shareWhatsApp(schedule)"
                                                            style="background:none; border:none; cursor:pointer; color:#16A34A; padding:6px; border-radius:7px; transition:all 0.15s ease;"
                                                            title="Kirim Undangan ke WhatsApp"
                                                            onmouseover="this.style.background='#DCFCE7'; this.style.color='#15803D'"
                                                            onmouseout="this.style.background='transparent'; this.style.color='#16A34A'">
                                                        <svg style="width:16px; height:16px;" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                    </button>
                                                </template>
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
                                                        onmouseover="this.style.background='#FDF1F2'; this.style.color='#AF1424'"
                                                        onmouseout="this.style.background='transparent'; this.style.color='#C81E2C'">
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

            {{-- CONFIRM DELETE MODAL (STANDAR TIMESHEET) --}}
            <template x-teleport="body">
                <div x-show="confirmOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-sm"
                     @click.self="confirmOpen = false"
                     @keydown.escape.window="confirmOpen = false">

                    <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(14,13,18,0.2)] animate-fade-in-up">
                        <div class="w-14 h-14 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#C81E2C]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h3 class="text-center font-display text-[17px] font-bold text-[#17151C] mb-2">Yakin Hapus Jadwal?</h3>
                        <p class="text-center text-[13.5px] text-[#75727C] mb-6 break-words" x-text="'Jadwal &quot;' + (confirmData?.title || '') + '&quot; akan dihapus.'"></p>

                        <div class="flex gap-3">
                            <button type="button" @click="confirmDeleteAction()" class="flex-1 py-2.5 px-4 rounded-xl bg-[#C81E2C] text-white font-semibold text-[13.5px] hover:bg-[#A31622] transition cursor-pointer">
                                Hapus
                            </button>
                            <button type="button" @click="confirmOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[13.5px] hover:bg-[#F8F7F6] transition cursor-pointer">
                                Batal
                            </button>
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
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; position:sticky; top:0; background:#FFFFFF; border-bottom:1px solid #E2E8F0; z-index:10; border-radius:16px 16px 0 0; gap:12px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:36px; height:36px; border-radius:10px; background:#FDF1F2; display:flex; align-items:center; justify-content:center; color:#C81E2C; flex-shrink:0;">
                                    <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 style="margin:0; font-family:'Space Grotesk', sans-serif; font-size:17px; font-weight:700; color:#0F172A;" x-text="modalTitle"></h3>
                                    <p style="margin:2px 0 0; font-size:12px; color:#64748B;">Kelola jadwal kegiatan, meeting, atau cuti engineer</p>
                                </div>
                            </div>
                            <button type="button"
                                    @click="modalOpen = false"
                                    style="background:#F1F5F9; border:none; cursor:pointer; color:#64748B; width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; transition:all 0.15s ease; flex-shrink:0;"
                                    onmouseover="this.style.background='#E2E8F0'; this.style.color='#0F172A';"
                                    onmouseout="this.style.background='#F1F5F9'; this.style.color='#64748B';">
                                <svg style="width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div style="padding:22px;">
                            <form @submit.prevent="saveSchedule">
                                <div style="display:flex; flex-direction:column; gap:16px;">
                                    <!-- Pilihan Kategori Jadwal -->
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#64748B; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">
                                            Kategori Jadwal
                                        </label>
                                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                            <button type="button" 
                                                    class="jkw-cat-btn jkw-cat-btn--meeting"
                                                    :class="{ 'is-active': form.category === 'Meeting' }"
                                                    @click="setCategory('Meeting')">
                                                <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                <span>Jadwal Meeting</span>
                                            </button>

                                            <button type="button" 
                                                    class="jkw-cat-btn jkw-cat-btn--dayoff"
                                                    :class="{ 'is-active': form.category === 'Day Off' }"
                                                    @click="setCategory('Day Off')">
                                                <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <circle cx="12" cy="12" r="5"/>
                                                    <line x1="12" y1="1" x2="12" y2="3"/>
                                                    <line x1="12" y1="21" x2="12" y2="23"/>
                                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                                                    <line x1="1" y1="12" x2="3" y2="12"/>
                                                    <line x1="21" y1="12" x2="23" y2="12"/>
                                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                                                </svg>
                                                <span>Day Off / Libur</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#64748B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Judul Jadwal</label>
                                        <input type="text" x-model="form.title" :placeholder="form.category === 'Day Off' ? 'Contoh: Day Off / Cuti Tahunan' : 'Contoh: Meeting Koordinasi Proyek ABC'" style="width:100%; padding:10px 14px; border-radius:9px; border:1.5px solid #E2E8F0; font-size:13.5px; color:#0F172A; outline:none; background:#FFFFFF; box-sizing:border-box; transition:border-color 0.15s ease;" required>
                                    </div>

                                    <div x-show="form.category !== 'Day Off'">
                                        <label style="display:block; font-size:11px; font-weight:700; color:#64748B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Project Terkait</label>
                                        <select x-model="form.project_id" style="width:100%; padding:10px 14px; border-radius:9px; border:1.5px solid #E2E8F0; font-size:13.5px; color:#0F172A; outline:none; background:#FFFFFF; box-sizing:border-box; transition:border-color 0.15s ease;" :required="form.category !== 'Day Off'">
                                            <option value="">Pilih Project</option>
                                            <template x-for="project in projects" :key="project.id">
                                                <option :value="project.id" x-text="project.name"></option>
                                            </template>
                                            <option value="other" style="font-weight:700; color:#C81E2C;">+ Input Project Lainnya (Other)</option>
                                        </select>
                                        <!-- Input nama project jika memilih 'other' -->
                                        <div x-show="form.project_id === 'other'" style="margin-top:8px;">
                                            <label style="display:block; font-size:11px; font-weight:700; color:#C81E2C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">
                                                Nama Project / Meeting Baru
                                            </label>
                                            <input type="text" x-model="form.new_project_name"
                                                   placeholder="Masukkan nama project atau meeting..."
                                                   style="width:100%; padding:9px 12px; border-radius:8px; border:1.5px solid #C81E2C; font-size:13.5px; color:#0F172A; outline:none; background:#FFF5F5; box-sizing:border-box;"
                                                   :required="form.category !== 'Day Off' && form.project_id === 'other'">
                                        </div>
                                    </div>

                                    <div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                            <label style="font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:0.5px;">
                                                <span x-text="form.category === 'Day Off' ? 'Engineer yang Cuti / Libur' : 'Engineer / Peserta Meeting'"></span>
                                            </label>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <button type="button" @click="selectAllEngineers()" 
                                                        style="background:none; border:none; color:#C81E2C; font-size:11.5px; font-weight:700; cursor:pointer; padding:0; text-decoration:underline;">
                                                    + Pilih Semua
                                                </button>
                                                <span style="color:#CBD5E1; font-size:10px;">|</span>
                                                <button type="button" @click="clearEngineers()" 
                                                        style="background:none; border:none; color:#64748B; font-size:11.5px; font-weight:600; cursor:pointer; padding:0;">
                                                    Kosongkan
                                                </button>
                                                <span style="font-size:11px; color:#C81E2C; font-weight:700; background:#FDF1F2; padding:2px 8px; border-radius:12px;" 
                                                      x-text="(form.engineer_ids?.length || 0) + ' Dipilih'"></span>
                                            </div>
                                        </div>

                                        <!-- Chip tag engineer terpilih -->
                                        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px; min-height:38px; padding:6px 8px; background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:9px; align-items:center;">
                                            <template x-for="(engId, idx) in form.engineer_ids" :key="engId">
                                                <div style="display:inline-flex; align-items:center; gap:6px; background:#FFFFFF; border:1px solid #CBD5E1; padding:3px 8px 3px 6px; border-radius:20px; font-size:11.5px; box-shadow:0 1px 2px rgba(0,0,0,0.04);">
                                                    <span style="width:7px; height:7px; border-radius:50%;" :style="{ background: form.category === 'Day Off' ? '#64748B' : (idx === 0 ? '#C81E2C' : '#2563EB') }"></span>
                                                    <span style="font-weight:600; color:#0F172A;" x-text="getEngineerName(engId)"></span>
                                                    <template x-if="form.category === 'Meeting'">
                                                        <span style="font-size:9.5px; font-weight:700; padding:1px 6px; border-radius:10px;" :style="{ background: idx === 0 ? '#FDF1F2' : '#EFF6FF', color: idx === 0 ? '#C81E2C' : '#1D4ED8' }" x-text="idx === 0 ? 'PIC' : 'Peserta'"></span>
                                                    </template>
                                                    <button type="button" @click="toggleEngineer(engId)" style="background:none; border:none; color:#94A3B8; cursor:pointer; padding:0; display:flex; align-items:center; transition:color 0.15s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#94A3B8'" title="Hapus">
                                                        <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <span x-show="!form.engineer_ids || form.engineer_ids.length === 0" style="font-size:12px; color:#94A3B8; margin-left:4px;">Pilih engineer dari daftar checklist di bawah...</span>
                                        </div>

                                        <!-- Checklist engineer -->
                                        <div style="max-height:160px; overflow-y:auto; border:1.5px solid #E2E8F0; border-radius:9px; background:white; padding:4px;">
                                            <template x-for="engineer in engineers" :key="engineer.id">
                                                <div @click="toggleEngineer(engineer.id)" 
                                                     style="display:flex; align-items:center; justify-content:space-between; padding:7px 10px; border-radius:7px; cursor:pointer; font-size:12.5px; transition:all 0.12s ease; margin-bottom:2px;"
                                                     :style="{ 
                                                         background: form.engineer_ids && form.engineer_ids.includes(engineer.id) 
                                                             ? (form.category === 'Day Off' ? '#F1F5F9' : '#FDF1F2') 
                                                             : (isEngineerDayOff(engineer.id, form.date) && form.category !== 'Day Off' ? '#F8FAFC' : 'transparent')
                                                     }">
                                                    <div style="display:flex; align-items:center; gap:9px; min-width:0; flex:1;">
                                                        <input type="checkbox" 
                                                               :checked="form.engineer_ids && form.engineer_ids.includes(engineer.id)" 
                                                               :style="{ accentColor: form.category === 'Day Off' ? '#64748B' : '#C81E2C' }" 
                                                               style="cursor:pointer; width:15px; height:15px; flex-shrink:0;" 
                                                               @click.stop="toggleEngineer(engineer.id)">
                                                        <div style="display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
                                                            <span style="font-weight:600;" 
                                                                  :style="{ color: (isEngineerDayOff(engineer.id, form.date) && form.category !== 'Day Off') ? '#64748B' : '#0F172A' }" 
                                                                  x-text="engineer.name"></span>
                                                            <!-- Badge Sedang Day Off -->
                                                            <template x-if="form.category !== 'Day Off' && isEngineerDayOff(engineer.id, form.date)">
                                                                <span style="font-size:9.5px; font-weight:700; background:#E2E8F0; color:#334155; padding:1px 7px; border-radius:6px; border:1px solid #CBD5E1; display:inline-flex; align-items:center; gap:3px;">
                                                                    Sedang Cuti / Day Off
                                                                </span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <template x-if="form.category === 'Meeting' && form.engineer_ids && form.engineer_ids.includes(engineer.id)">
                                                        <span style="font-size:10px; font-weight:700; padding:1px 7px; border-radius:10px; flex-shrink:0;" 
                                                              :style="{ background: form.engineer_ids.indexOf(engineer.id) === 0 ? '#FDF1F2' : '#EFF6FF', color: form.engineer_ids.indexOf(engineer.id) === 0 ? '#C81E2C' : '#1D4ED8' }"
                                                              x-text="form.engineer_ids.indexOf(engineer.id) === 0 ? 'PIC' : 'Peserta'"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Sesi Tanggal & Waktu (Multi-Date / Multi-Session) -->
                                    <div style="margin-bottom:14px;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                                            <label style="font-size:11px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:0.5px;">
                                                Tanggal & Waktu Kegiatan
                                            </label>
                                            <template x-if="!editing && form.sessions && form.sessions.length > 1">
                                                <span style="font-size:11px; font-weight:700; color:#2563EB; background:#EFF6FF; border:1px solid #DBEAFE; padding:2px 8px; border-radius:12px;" 
                                                      x-text="form.sessions.length + ' Tanggal / Sesi'"></span>
                                            </template>
                                        </div>

                                        <!-- List Kartu Sesi -->
                                        <div style="display:flex; flex-direction:column; gap:10px;">
                                            <template x-for="(session, sIdx) in (form.sessions || [])" :key="sIdx">
                                                <div style="background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:10px; padding:12px 14px; position:relative; transition:all 0.15s ease;">
                                                    <!-- Header Sesi jika lebih dari 1 sesi -->
                                                    <div x-show="!editing && form.sessions && form.sessions.length > 1" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #E2E8F0;">
                                                        <div style="display:flex; align-items:center; gap:6px;">
                                                            <span style="display:inline-flex; align-items:center; justify-content:center; width:20px; height:20px; border-radius:50%; background:#2563EB; color:white; font-size:10.5px; font-weight:700;" x-text="sIdx + 1"></span>
                                                            <span style="font-size:12px; font-weight:700; color:#1E293B;" x-text="'Sesi Jadwal ke-' + (sIdx + 1)"></span>
                                                        </div>
                                                        <button type="button" @click="removeSession(sIdx)" style="background:none; border:none; color:#EF4444; font-size:11.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:4px; padding:2px 6px; border-radius:6px;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='none'">
                                                            <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>

                                                    <!-- Grid Tanggal & Jam -->
                                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                                        <div>
                                                            <label style="display:block; font-size:10.5px; font-weight:700; color:#64748B; margin-bottom:4px; text-transform:uppercase;">Tanggal</label>
                                                            <input type="date" x-model="session.date" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #CBD5E1; font-size:13px; color:#0F172A; outline:none; background:white; box-sizing:border-box;" required>
                                                        </div>
                                                        <div>
                                                            <label style="display:block; font-size:10.5px; font-weight:700; color:#64748B; margin-bottom:4px; text-transform:uppercase;">
                                                                <span x-text="form.category === 'Day Off' ? 'Jam (Opsional)' : 'Jam'"></span>
                                                            </label>
                                                            <input type="time" x-model="session.start_time" style="width:100%; padding:8px 10px; border-radius:8px; border:1.5px solid #CBD5E1; font-size:13px; color:#0F172A; outline:none; background:white; box-sizing:border-box;" :required="form.category === 'Meeting'">
                                                        </div>
                                                    </div>

                                                    <!-- Lokasi per Sesi jika Meeting -->
                                                    <div x-show="form.category !== 'Day Off'" style="margin-top:8px;">
                                                        <label style="display:block; font-size:10.5px; font-weight:700; color:#64748B; margin-bottom:4px; text-transform:uppercase;">Lokasi / Link Meeting (Sesi Ini)</label>
                                                        <input type="text" x-model="session.location" placeholder="Masukkan lokasi (misal: Ruang Rapat / Google Meet)..." style="width:100%; padding:8px 12px; border-radius:8px; border:1.5px solid #CBD5E1; font-size:12.5px; color:#0F172A; outline:none; background:white; box-sizing:border-box;">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Tombol Tambah Tanggal / Sesi -->
                                        <template x-if="!editing">
                                            <button type="button" 
                                                    @click="addSession()" 
                                                    style="width:100%; display:flex; align-items:center; justify-content:center; gap:6px; padding:9px 12px; background:#F8FAFC; border:1.5px dashed #94A3B8; border-radius:9px; font-size:12.5px; font-weight:700; color:#2563EB; cursor:pointer; transition:all 0.15s ease; margin-top:8px;"
                                                    onmouseover="this.style.background='#EFF6FF'; this.style.borderColor='#3B82F6'; this.style.color='#1D4ED8';"
                                                    onmouseout="this.style.background='#F8FAFC'; this.style.borderColor='#94A3B8'; this.style.color='#2563EB';">
                                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Tambah Tanggal / Sesi Jadwal</span>
                                            </button>
                                        </template>
                                    </div>

                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#64748B; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.5px;">Keterangan / Deskripsi</label>
                                        <textarea x-model="form.description" placeholder="Catatan tambahan, agenda pembahasan, atau alasan cuti..." style="width:100%; padding:10px 14px; border-radius:9px; border:1.5px solid #E2E8F0; font-size:13.5px; color:#0F172A; outline:none; background:white; min-height:75px; transition:border-color 0.15s ease; box-sizing:border-box;" rows="2"></textarea>
                                    </div>
                                </div>

                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:20px; padding-top:18px; border-top:1px solid #E2E8F0;">
                                    <button type="submit" 
                                            style="width:100%; justify-content:center; background:#C81E2C; color:white; box-shadow:0 4px 14px rgba(200,30,44,0.25); padding:11px 18px; border-radius:9px; border:none; font-weight:700; font-size:13.5px; cursor:pointer; display:flex; align-items:center; transition:all 0.15s ease; box-sizing:border-box;"
                                            onmouseover="this.style.filter='brightness(1.08)'; this.style.transform='translateY(-1px)';"
                                            onmouseout="this.style.filter='brightness(1)'; this.style.transform='translateY(0)';">
                                        Simpan Jadwal
                                    </button>
                                    <template x-if="editing">
                                        <button type="button"
                                                @click="deleteSchedule(form)"
                                                style="width:100%; justify-content:center; background:#FEF2F2; color:#DC2626; border:1.5px solid #FECACA; padding:11px 18px; border-radius:9px; font-weight:700; font-size:13.5px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.15s ease; box-sizing:border-box;"
                                                onmouseover="this.style.background='#FEE2E2'"
                                                onmouseout="this.style.background='#FEF2F2'">
                                            <svg style="width:14px; height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus Jadwal
                                        </button>
                                    </template>
                                    <template x-if="!editing">
                                        <button type="button"
                                                @click="modalOpen = false"
                                                style="width:100%; justify-content:center; background:#FFFFFF; color:#475569; border:1.5px solid #E2E8F0; padding:11px 18px; border-radius:9px; font-weight:600; font-size:13.5px; cursor:pointer; display:flex; align-items:center; transition:all 0.15s ease; box-sizing:border-box;"
                                                onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#CBD5E1';"
                                                onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#E2E8F0';">
                                            Batal
                                        </button>
                                    </template>
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
.jkw-eng-chip.is-dayoff { background:#F1F5F9 !important; border-color:#CBD5E1 !important; }
.jkw-eng-chip:hover { filter:brightness(0.97) !important; }
.jkw-eng-info { display:flex !important; flex-direction:column !important; line-height:1.25 !important; min-width:0 !important; }
.jkw-eng-name { font-size:12.5px !important; font-weight:700 !important; color:var(--jkw-ink) !important; overflow:hidden !important; text-overflow:ellipsis !important; white-space:nowrap !important; }
.jkw-eng-status { font-size:11px !important; color:var(--jkw-muted) !important; }
.jkw-dot { width:7px !important; height:7px !important; border-radius:50% !important; flex-shrink:0 !important; margin-left:auto !important; }
.jkw-dot.is-free { background:var(--jkw-success) !important; }
.jkw-dot.is-busy { background:var(--jkw-primary) !important; }
.jkw-dot.is-dayoff { background:#64748B !important; }

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
.jkw-day-item { display:flex !important; align-items:flex-start !important; gap:12px !important; padding:12px 16px !important; border-radius:8px !important; flex-wrap:wrap !important; transition:all 0.15s ease !important; }
.jkw-day-item--schedule { background:rgba(37,99,235,0.13) !important; border-left:4px solid #2563EB !important; }
.jkw-day-item--schedule .jkw-time { color:#1D4ED8 !important; }
.jkw-day-item--schedule .jkw-time svg { color:#2563EB !important; }
.jkw-day-item--day_off { background:rgba(100,116,139,0.20) !important; border-left:4px solid #475569 !important; }
.jkw-day-item--day_off .jkw-time { color:#334155 !important; font-weight:800 !important; }
.jkw-day-item--day_off .jkw-time svg { color:#475569 !important; }
.jkw-day-item--task { background:#FDF1F2 !important; border-left:4px solid #C81E2C !important; }
.jkw-day-item--task .jkw-time { color:#991B1B !important; }
.jkw-day-item--task .jkw-time svg { color:#C81E2C !important; }
.jkw-day-item--project { background:#FEF2F2 !important; border-left:4px solid #991B1B !important; }
.jkw-day-item--project .jkw-time { color:#7F1D1D !important; }
.jkw-day-item--project .jkw-time svg { color:#991B1B !important; }
.jkw-time { font-family:'IBM Plex Mono', monospace !important; font-size:12px !important; font-weight:700 !important; white-space:nowrap !important; }
.jkw-day-main { flex:1 1 160px !important; min-width:0 !important; }
.jkw-day-title { font-size:13.5px !important; font-weight:700 !important; color:var(--jkw-ink) !important; margin-bottom:3px !important; word-break:break-word !important; }
.jkw-day-meta { display:flex !important; align-items:center !important; gap:6px !important; font-size:11.5px !important; color:var(--jkw-muted) !important; flex-wrap:wrap !important; }
.jkw-sep { color:var(--jkw-line) !important; }

.jkw-empty { text-align:center !important; padding:48px 16px 56px !important; color:var(--jkw-muted) !important; }
.jkw-empty-icon { width:44px !important; height:44px !important; border-radius:10px !important; background:var(--jkw-bg-soft) !important; display:flex !important; align-items:center !important; justify-content:center !important; margin:0 auto 14px !important; }
.jkw-empty-icon svg { width:20px !important; height:20px !important; opacity:.6 !important; }
.jkw-empty p { font-size:13.5px !important; margin:0 0 20px 0 !important; color:#75727C !important; }
.jkw-empty button { margin-top:16px !important; }

/* ---------- week view ---------- */
.jkw-week-scroll { overflow-x:auto !important; -webkit-overflow-scrolling:touch !important; scroll-snap-type:x proximity !important; }
.jkw-week-grid { display:grid !important; grid-template-columns:repeat(7, minmax(120px,1fr)) !important; min-width:840px !important; }
.jkw-week-col { min-height:200px !important; padding:12px !important; border-right:1px solid var(--jkw-line-soft) !important; scroll-snap-align:start !important; }
.jkw-week-col:last-child { border-right:none !important; }
.jkw-week-col.is-today { background:var(--jkw-bg-soft) !important; }
.jkw-week-col-head { font-size:11px !important; color:var(--jkw-muted) !important; font-weight:700 !important; margin-bottom:10px !important; display:flex !important; align-items:center !important; gap:5px !important; }
.jkw-week-col-body { display:flex !important; flex-direction:column !important; gap:6px !important; }
.jkw-mini-card { border-radius:6px !important; padding:6px 9px !important; }
.jkw-mini-card--schedule { background:rgba(37,99,235,0.13) !important; }
.jkw-mini-card--day_off { background:rgba(100,116,139,0.20) !important; }
.jkw-mini-card--task { background:rgba(200,30,44,0.08) !important; }
.jkw-mini-card--project { background:rgba(153,27,27,0.08) !important; }
.jkw-mini-time { font-family:'IBM Plex Mono', monospace !important; font-size:10px !important; font-weight:700 !important; }
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
/* ---------- category selector buttons ---------- */
.jkw-cat-btn {
    all: unset !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    height: 42px !important;
    padding: 0 16px !important;
    border-radius: 9px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
    transition: all .15s ease !important;
    outline: none !important;
    user-select: none !important;
    text-align: center !important;
}
.jkw-cat-btn:focus, .jkw-cat-btn:active, .jkw-cat-btn:focus-visible {
    outline: none !important;
    box-shadow: none !important;
}
.jkw-cat-btn svg {
    width: 16px !important;
    height: 16px !important;
    flex-shrink: 0 !important;
    display: inline-block !important;
}
.jkw-cat-btn--meeting {
    background: #FFFFFF !important;
    color: #2563EB !important;
    border: 1.5px solid #BFDBFE !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
}
.jkw-cat-btn--meeting:hover {
    background: #EFF6FF !important;
    border-color: #93C5FD !important;
}
.jkw-cat-btn--meeting.is-active {
    background: #2563EB !important;
    color: #FFFFFF !important;
    border-color: #2563EB !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
}
.jkw-cat-btn--meeting.is-active svg {
    color: #FFFFFF !important;
}

.jkw-cat-btn--dayoff {
    background: #FFFFFF !important;
    color: #64748B !important;
    border: 1.5px solid #CBD5E1 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
}
.jkw-cat-btn--dayoff:hover {
    background: #F1F5F9 !important;
    border-color: #94A3B8 !important;
}
.jkw-cat-btn--dayoff.is-active {
    background: #64748B !important;
    color: #FFFFFF !important;
    border-color: #64748B !important;
    box-shadow: 0 4px 14px rgba(100, 116, 139, 0.35) !important;
}
.jkw-cat-btn--dayoff.is-active svg {
    color: #FFFFFF !important;
}

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
                    new_project_name: '',
                    engineer_id: null,
                    engineer_ids: [],
                    date: '',
                    start_time: '09:00',
                    end_time: '',
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
                        var engSchedules = this.schedules.filter(function(s) {
                            var engIds = s.engineer_ids || (s.engineers ? s.engineers.map(function(e){ return e.id; }) : [s.engineer_id]);
                            return engIds.some(function(id){ return String(id) === String(eng.id); }) && dates.indexOf(s.date.split('T')[0]) !== -1;
                        });
                        var dayOffCount = engSchedules.filter(function(s){ return s.category === 'Day Off'; }).length;
                        var meetingCount = engSchedules.length - dayOffCount;
                        var isDayOff = dayOffCount > 0;
                        var isAvailable = engSchedules.length === 0;
                        var statusLabel = isAvailable ? 'Tersedia' : (isDayOff && meetingCount === 0 ? 'Day Off' : engSchedules.length + ' jadwal');

                        return { 
                            id: eng.id, 
                            name: eng.name, 
                            scheduleCount: engSchedules.length, 
                            available: isAvailable,
                            isDayOff: isDayOff,
                            statusLabel: statusLabel
                        };
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
                            category: s.category || 'Meeting',
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

                    // --- Jadwal biasa & Day Off ---
                    var filtered = this.engineerFilter
                        ? this.schedules.filter(function(s) { 
                            var engIds = s.engineer_ids || (s.engineers ? s.engineers.map(function(e){ return e.id; }) : [s.engineer_id]);
                            return engIds.some(function(id) { return String(id) === String(self.engineerFilter); });
                        })
                        : this.schedules;
                    filtered.forEach(function(s) {
                        var d = (s.date || '').split('T')[0];
                        if (d === date) {
                            var sTime = s.start_time ? s.start_time.substring(0, 5) : '';
                            var eTime = s.end_time ? s.end_time.substring(0, 5) : '';
                            var isDayOff = s.category === 'Day Off';
                            var timeLabel = isDayOff ? 'Day Off' : (sTime ? (sTime + ' WIB') : 'Jadwal');
                            
                            var engLabel = '';
                            if (s.engineers && s.engineers.length > 0) {
                                engLabel = s.engineers.map(function(e) { return e.name; }).join(', ');
                            } else if (s.engineer && s.engineer.name) {
                                engLabel = s.engineer.name;
                            } else if (s.project && s.project.name) {
                                engLabel = s.project.name;
                            }

                            events.push({
                                _uid: 'sch-' + s.id,
                                _type: isDayOff ? 'day_off' : 'schedule',
                                _color: isDayOff ? '#64748B' : '#2563EB',
                                _displayTitle: s.title,
                                _timeLabel: timeLabel,
                                _tooltip: (isDayOff ? 'Day Off: ' : 'Jadwal: ') + s.title + (sTime && !isDayOff ? ' (' + timeLabel + ')' : '') + (engLabel ? '\nEngineer: ' + engLabel : '') + ' • Klik untuk edit',
                                _subLabel: engLabel,
                                category: s.category || 'Meeting',
                                id: s.id,
                                title: s.title,
                                project_id: s.project_id,
                                engineer_id: s.engineer_id,
                                engineer_ids: s.engineer_ids || [],
                                date: s.date,
                                start_time: sTime,
                                end_time: eTime,
                                location: s.location,
                                description: s.description,
                                project: s.project,
                                engineer: s.engineer,
                                engineers: s.engineers || []
                            });
                        }
                    });

                    // --- Deadline Task (MERAH) ---
                    var filteredTasks = this.engineerFilter
                        ? this.tasks.filter(function(t) { 
                            var engIds = t.engineer_ids || (t.engineers ? t.engineers.map(function(e){ return e.id; }) : [t.engineer_id]);
                            return engIds.some(function(id) { return String(id) === String(self.engineerFilter); });
                        })
                        : this.tasks;
                    filteredTasks.forEach(function(t) {
                        if (t.deadline === date) {
                            var dTime = t.deadline_time ? t.deadline_time.substring(0, 5) : '';
                            var taskTimeLabel = dTime ? (dTime + ' WIB') : 'Deadline';

                            var taskEngLabel = '';
                            if (t.engineers && t.engineers.length > 0) {
                                taskEngLabel = t.engineers.map(function(e) { return e.name; }).join(', ');
                            } else if (t.engineer && t.engineer.name) {
                                taskEngLabel = t.engineer.name;
                            } else if (t.project && t.project.name) {
                                taskEngLabel = t.project.name;
                            }

                            events.push({
                                _uid: 'task-' + t.id,
                                _type: 'task',
                                _color: '#C81E2C',
                                _displayTitle: t.title,
                                _timeLabel: taskTimeLabel,
                                _tooltip: 'Deadline Task: ' + t.title + (dTime ? ' (' + dTime + ' WIB)' : '') + (taskEngLabel ? '\nEngineer: ' + taskEngLabel : ''),
                                _subLabel: taskEngLabel,
                                id: t.id,
                                title: t.title,
                                project: t.project,
                                engineer: t.engineer,
                                engineers: t.engineers || [],
                                priority: t.priority,
                                status: t.status,
                                deadline: t.deadline,
                                deadline_time: dTime,
                                start_time: dTime,
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
                                _displayTitle: p.name,
                                _timeLabel: 'Project',
                                _tooltip: 'Deadline Project: ' + p.name + (p.client ? ' (' + p.client + ')' : ''),
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

                toggleEngineer: function(id) {
                    if (!this.form.engineer_ids) this.form.engineer_ids = [];
                    var idx = this.form.engineer_ids.indexOf(id);
                    if (idx === -1) {
                        this.form.engineer_ids.push(id);
                        if (this.form.category !== 'Day Off' && this.isEngineerDayOff(id, this.form.date)) {
                            var engName = this.getEngineerName(id);
                            this.showToast('Perhatian: ' + engName + ' sedang tercatat Cuti / Day Off pada tanggal ini.');
                        }
                    } else {
                        this.form.engineer_ids.splice(idx, 1);
                    }
                    this.form.engineer_id = this.form.engineer_ids[0] || null;
                },

                isEngineerDayOff: function(id, date) {
                    if (!date) return false;
                    var targetDate = date.split('T')[0];
                    return this.schedules.some(function(s) {
                        var sDate = (s.date || '').split('T')[0];
                        if (sDate !== targetDate) return false;
                        if (s.category !== 'Day Off') return false;
                        var engIds = s.engineer_ids || (s.engineers ? s.engineers.map(function(e) { return e.id; }) : [s.engineer_id]);
                        return engIds.some(function(engId) { return String(engId) === String(id); });
                    });
                },

                selectAllEngineers: function() {
                    this.form.engineer_ids = this.engineers.map(function(e) { return e.id; });
                    this.form.engineer_id = this.form.engineer_ids[0] || null;
                },

                clearEngineers: function() {
                    this.form.engineer_ids = [];
                    this.form.engineer_id = null;
                },

                getEngineerName: function(id) {
                    var eng = this.engineers.find(function(e) { return e.id === id; });
                    return eng ? eng.name : 'Engineer #' + id;
                },

                setCategory: function(cat) {
                    this.form.category = cat;
                    if (cat === 'Day Off') {
                        if (!this.form.title || this.form.title === '' || this.form.title.toLowerCase().includes('meeting')) {
                            this.form.title = 'Day Off / Cuti';
                        }
                        this.form.start_time = '';
                        this.form.end_time = '';
                        if (this.form.sessions) {
                            this.form.sessions.forEach(function(s) { s.start_time = ''; s.end_time = ''; });
                        }
                    } else if (cat === 'Meeting') {
                        if (this.form.title === 'Day Off / Cuti' || this.form.title === 'Day Off') {
                            this.form.title = '';
                        }
                        if (!this.form.start_time) {
                            this.form.start_time = '09:00';
                        }
                        if (this.form.sessions) {
                            this.form.sessions.forEach(function(s) { if (!s.start_time) s.start_time = '09:00'; });
                        }
                        if (!this.form.project_id && this.projects.length > 0) {
                            this.form.project_id = this.projects[0].id;
                        }
                    }
                },

                addSession: function() {
                    if (!this.form.sessions) this.form.sessions = [];
                    var last = this.form.sessions.length > 0 ? this.form.sessions[this.form.sessions.length - 1] : null;
                    var defaultTime = this.form.category === 'Day Off' ? '' : (last && last.start_time ? last.start_time : '09:00');
                    var defaultLoc = this.form.category === 'Day Off' ? '' : (last && last.location ? last.location : '');
                    this.form.sessions.push({
                        date: last && last.date ? last.date : this.formatDate(new Date()),
                        start_time: defaultTime,
                        end_time: '',
                        location: defaultLoc
                    });
                },

                removeSession: function(idx) {
                    if (this.form.sessions && this.form.sessions.length > 1) {
                        this.form.sessions.splice(idx, 1);
                    }
                },

                openModal: function(schedule) {
                    if (schedule) {
                        this.editing = true;
                        var engIds = [];
                        if (schedule.engineer_ids && schedule.engineer_ids.length > 0) {
                            engIds = schedule.engineer_ids.slice();
                        } else if (schedule.engineers && schedule.engineers.length > 0) {
                            engIds = schedule.engineers.map(function(e) { return e.id; });
                        } else if (schedule.engineer_id) {
                            engIds = [schedule.engineer_id];
                        }

                        var schDate = schedule.date ? schedule.date.split('T')[0] : '';
                        var schStart = schedule.start_time ? schedule.start_time.substring(0, 5) : (schedule.category === 'Day Off' ? '' : '09:00');
                        var schEnd = schedule.end_time ? schedule.end_time.substring(0, 5) : '';
                        var schLoc = schedule.location || '';

                        this.form = {
                            id: schedule.id,
                            title: schedule.title,
                            category: schedule.category || 'Meeting',
                            project_id: schedule.project_id,
                            new_project_name: '',
                            engineer_id: engIds[0] || null,
                            engineer_ids: engIds,
                            date: schDate,
                            start_time: schStart,
                            end_time: schEnd,
                            location: schLoc,
                            description: schedule.description || '',
                            send_wa: false,
                            sessions: [
                                {
                                    date: schDate,
                                    start_time: schStart,
                                    end_time: schEnd,
                                    location: schLoc
                                }
                            ]
                        };
                    } else {
                        this.editing = false;
                        var todayFormatted = this.formatDate(new Date());
                        var initialEngIds = this.engineers.length > 0 ? [this.engineers[0].id] : [];
                        var targetDate = this.viewMode === 'day' ? this.currentDateStr : todayFormatted;
                        this.form = {
                            id: null,
                            title: '',
                            category: 'Meeting',
                            project_id: this.projects[0] ? this.projects[0].id : null,
                            new_project_name: '',
                            engineer_id: initialEngIds[0] || null,
                            engineer_ids: initialEngIds,
                            date: targetDate,
                            start_time: '09:00',
                            end_time: '',
                            location: '',
                            description: '',
                            sessions: [
                                {
                                    date: targetDate,
                                    start_time: '09:00',
                                    end_time: '',
                                    location: ''
                                }
                            ]
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
                        if (this.form.category !== 'Day Off') {
                            if (this.form.project_id === 'other') {
                                if (!this.form.new_project_name || !this.form.new_project_name.trim()) {
                                    this.showToast('Silakan masukkan nama project / meeting!');
                                    return;
                                }
                            } else if (!this.form.project_id) {
                                this.showToast('Silakan pilih project!');
                                return;
                            }
                        }

                        if (!this.form.engineer_ids || this.form.engineer_ids.length === 0) {
                            this.showToast('Pilih minimal 1 orang engineer / peserta!');
                            return;
                        }
                        this.form.engineer_id = this.form.engineer_ids[0];

                        // Sinkronkan sesi pertama dengan form root
                        if (this.form.sessions && this.form.sessions.length > 0) {
                            this.form.date = this.form.sessions[0].date;
                            this.form.start_time = this.form.sessions[0].start_time;
                            this.form.end_time = this.form.sessions[0].end_time || this.form.sessions[0].start_time;
                            this.form.location = this.form.sessions[0].location || '';
                        }

                        // Jika end_time kosong dan ada start_time, samakan dengan start_time
                        if (this.form.start_time && !this.form.end_time) {
                            this.form.end_time = this.form.start_time;
                        }

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
                            if (data.schedules && Array.isArray(data.schedules)) {
                                data.schedules.forEach(function(sch) {
                                    if (sch.project && !this.projects.some(function(p) { return p.id === sch.project.id; })) {
                                        this.projects.push(sch.project);
                                    }
                                    this.schedules.push(sch);
                                }.bind(this));
                                this.modalOpen = false;
                                this.showToast(data.message || (data.schedules.length + ' Jadwal berhasil ditambahkan!'));
                            } else {
                                if (data.project && !this.projects.some(function(p) { return p.id === data.project.id; })) {
                                    this.projects.push(data.project);
                                }
                                if (this.editing) {
                                    var index = this.schedules.findIndex(function(s) { return s.id === this.form.id; }.bind(this));
                                    if (index !== -1) this.schedules[index] = data;
                                } else {
                                    this.schedules.push(data);
                                }
                                this.modalOpen = false;
                                this.showToast('Jadwal berhasil ' + (this.editing ? 'diperbarui' : 'ditambahkan') + '!');
                            }
                        } else {
                            var error = await response.json();
                            var errorMsg = error.message || 'Terjadi kesalahan';
                            if (error.errors) {
                                var firstKey = Object.keys(error.errors)[0];
                                if (firstKey && error.errors[firstKey][0]) {
                                    errorMsg = error.errors[firstKey][0];
                                }
                            }
                            this.showToast('Error: ' + errorMsg);
                        }
                    } catch (error) {
                        console.error('Error saving schedule:', error);
                        this.showToast('Terjadi kesalahan saat menyimpan jadwal.');
                    }
                },

                deleteSchedule: async function(schedule) {
                    if (!schedule || !schedule.id) return;
                    var title = schedule.title || schedule._displayTitle || 'jadwal ini';
                    if (!confirm('Apakah Anda yakin ingin menghapus ' + title + '?')) {
                        return;
                    }
                    try {
                        var response = await fetch('/schedules/' + schedule.id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            var targetId = schedule.id;
                            this.schedules = this.schedules.filter(function(s) {
                                return s.id !== targetId;
                            });
                            this.modalOpen = false;
                            this.showToast('Jadwal berhasil dihapus!');
                        } else {
                            this.showToast('Gagal menghapus jadwal.');
                        }
                    } catch (error) {
                        console.error('Error deleting schedule:', error);
                        this.showToast('Terjadi kesalahan saat menghapus jadwal.');
                    }
                },

                formatWhatsAppMessage: function(schedule) {
                    var project = (schedule.project && schedule.project.name) ? schedule.project.name : (schedule.new_project_name || '-');
                    
                    var dateFormatted = schedule.date || '';
                    if (schedule.date) {
                        var rawDate = schedule.date.split('T')[0];
                        var parts = rawDate.split('-');
                        if (parts.length === 3) {
                            var d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                            if (!isNaN(d.getTime())) {
                                var days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                                var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                                dateFormatted = days[d.getDay()] + ', ' + d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
                            }
                        }
                    }

                    var engNames = [];
                    if (schedule.engineers && schedule.engineers.length > 0) {
                        engNames = schedule.engineers.map(function(e) { return e.name; });
                    } else if (schedule.engineer_ids && schedule.engineer_ids.length > 0) {
                        var self = this;
                        engNames = schedule.engineer_ids.map(function(id) { return self.getEngineerName(id); });
                    } else if (schedule.engineer && schedule.engineer.name) {
                        engNames = [schedule.engineer.name];
                    } else if (schedule.engineer_id) {
                        engNames = [this.getEngineerName(schedule.engineer_id)];
                    }

                    var engText = engNames.length > 0 
                        ? engNames.map(function(name, i) { return (i === 0 ? '• ' + name + ' (PIC / Koordinator)' : '• ' + name); }).join('\n')
                        : '• Seluruh Rekan Tim Terkait';

                    var timeText = schedule.start_time ? schedule.start_time.substring(0, 5).replace(':', '.') + ' WIB' : 'Menyesuaikan';
                    var locText = (schedule.location && schedule.location.trim()) ? schedule.location.trim() : 'Menyesuaikan';
                    var noteText = (schedule.description && schedule.description.trim()) 
                        ? schedule.description.trim() 
                        : 'Mohon dapat hadir tepat waktu dan mempersiapkan informasi terkait progres, kebutuhan teknis, serta hal-hal yang perlu dikoordinasikan dalam pelaksanaan pekerjaan.';

                    var projectContext = (project && project !== '-' && project !== 'Other') ? 'pekerjaan ' + project : 'pekerjaan operasional lapangan';

                    var msg = "*UNDANGAN MEETING TIM ENGINEER KERJA*\n"
                            + "*PT IP-NET SOLUSINDO*\n\n"
                            + "Yth. Rekan-rekan Tim,\n\n"
                            + "Sehubungan dengan pelaksanaan koordinasi operasional terkait " + projectContext + ", bersama ini kami sampaikan jadwal pertemuan sebagai berikut:\n\n"
                            + "Agenda Pertemuan : " + (schedule.title || '-') + "\n"
                            + "Proyek / Layanan : " + project + "\n"
                            + "Hari, Tanggal : " + dateFormatted + "\n"
                            + "Waktu Pelaksanaan : " + timeText + "\n"
                            + "Lokasi / Platform : " + locText + "\n\n"
                            + "Peserta Terjadwal :\n" + engText + "\n\n"
                            + "Catatan / Instruksi Tambahan:\n" + noteText + "\n\n"
                            + "Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.";

                    return msg;
                },

                shareWhatsApp: function(schedule) {
                    try {
                        var text = this.formatWhatsAppMessage(schedule);
                        var url = "https://api.whatsapp.com/send?text=" + encodeURIComponent(text);
                        window.open(url, '_blank');
                    } catch (e) {
                        console.error('Error sharing to WhatsApp:', e);
                    }
                },

                shareWhatsAppFromForm: function() {
                    var formSchedule = {
                        title: this.form.title || 'Meeting Baru',
                        project_id: this.form.project_id,
                        new_project_name: this.form.new_project_name,
                        date: this.form.date,
                        start_time: this.form.start_time,
                        location: this.form.location,
                        description: this.form.description,
                        engineer_ids: this.form.engineer_ids
                    };
                    if (this.form.project_id && this.form.project_id !== 'other') {
                        var p = this.projects.find(function(pr) { return pr.id == formSchedule.project_id; });
                        if (p) formSchedule.project = p;
                    }
                    this.shareWhatsApp(formSchedule);
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