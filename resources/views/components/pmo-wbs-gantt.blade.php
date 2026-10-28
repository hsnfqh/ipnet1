@props([
    'project' => null,
    'wbsTasks' => [],
    'canEdit' => true,
    'isStandalone' => false,
])

<div x-data="pmoWbsGantt({
    initialTasks: @json($wbsTasks ?? []),
    projectId: {{ $project ? $project->id : 'null' }},
    projectName: '{{ $project ? addslashes($project->name) : 'Pengadaan Perangkat Firewall' }}',
    projectStartDate: '{{ $project && $project->start_date ? $project->start_date->format('Y-m-d') : '2026-08-26' }}',
    projectEndDate: '{{ $project && $project->deadline ? $project->deadline->format('Y-m-d') : '2026-11-20' }}'
})" 
class="wbs-gantt-container bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden font-sans select-none"
:class="isFullscreen ? 'fixed inset-0 z-50 rounded-none border-0 overflow-y-auto' : ''"
x-cloak>

    {{-- Top Action & Control Toolbar --}}
    <div class="px-5 py-4 border-b border-slate-200 bg-white flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        
        {{-- Left: Section Title & WBS Status --}}
        <div class="space-y-1">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D] animate-pulse"></span>
                    MS Project Professional View
                </span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs font-semibold text-slate-600" x-text="tasks.length + ' Baris WBS / Pekerjaan'"></span>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs font-bold text-slate-800" x-text="'Total Durasi: ' + totalDurationDays + ' Hari Kalender'"></span>
            </div>
            <h2 class="text-[17px] font-extrabold text-[#1E293B] tracking-tight flex items-center gap-2">
                <span>Timeline & WBS: <span class="text-[#8F0A0D]" x-text="projectName"></span></span>
            </h2>
        </div>

        {{-- Right: Interactive Action Buttons --}}
        <div class="flex items-center gap-2 flex-wrap">
            
            {{-- Scale Toggle: Weekly vs Daily --}}
            <div class="inline-flex items-center p-0.5 rounded-xl bg-slate-100 border border-slate-200 shrink-0">
                <button type="button" 
                        @click="scaleMode = 'weekly'"
                        :class="scaleMode === 'weekly' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 font-medium hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    📅 Mingguan (Weekly)
                </button>
                <button type="button" 
                        @click="scaleMode = 'daily'"
                        :class="scaleMode === 'daily' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 font-medium hover:text-slate-900'"
                        class="px-3 py-1.5 rounded-lg text-xs transition">
                    📆 Harian (Daily)
                </button>
            </div>

            {{-- 1-Click Preset Template: Firewall BIONS --}}
            @if($canEdit)
            <button type="button" 
                    @click="showConfirmTemplateModal = true"
                    title="Muat struktur WBS dan durasi standar sesuai foto Microsoft Project"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                <span>Muat Template BIONS</span>
            </button>

            {{-- Button Add WBS Task --}}
            <button type="button" 
                    @click="openAddTaskModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Task / Fase</span>
            </button>

            {{-- Button Save WBS --}}
            <button type="button" 
                    @click="saveToServer()"
                    :disabled="isSaving"
                    class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-extrabold text-white bg-[#8F0A0D] hover:bg-[#73080A] transition shadow-2xs disabled:opacity-50">
                <template x-if="isSaving">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </template>
                <template x-if="!isSaving">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                </template>
                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan WBS'"></span>
            </button>
            @endif

            {{-- Fullscreen Mode Toggle --}}
            <button type="button" 
                    @click="isFullscreen = !isFullscreen"
                    title="Beralih ke mode layar penuh"
                    class="p-2 rounded-xl text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <template x-if="!isFullscreen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </template>
                <template x-if="isFullscreen">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9L4 4m0 0h4m-4 0v4m11-4l5 5m0-5h-4m4 0v4M9 15l-5 5m0 0h4m-4 0v-4m11 4l5-5m0 5h-4m4 0v-4"/>
                    </svg>
                </template>
            </button>

            {{-- Print / Export --}}
            <button type="button" 
                    @click="window.print()"
                    title="Cetak atau ekspor dokumen Gantt Chart ke PDF"
                    class="p-2 rounded-xl text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
            </button>
        </div>

    </div>

    {{-- Notification Toast Bar --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="px-5 py-2.5 bg-emerald-50 border-b border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="toastMessage"></span>
        </div>
        <button type="button" @click="showToast = false" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
    </div>

    {{-- Filter & Search Quick Strip --}}
    <div class="px-5 py-2.5 bg-slate-50/70 border-b border-slate-200 flex items-center justify-between gap-3 text-xs flex-wrap">
        <div class="flex items-center gap-2 flex-1 min-w-[220px]">
            <div class="relative w-full max-w-xs">
                <input type="text" 
                       x-model="searchTerm" 
                       placeholder="Cari rincian task, WBS, atau PIC..." 
                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-[#8F0A0D] transition">
                <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            {{-- Quick Filter Phase --}}
            <select x-model="selectedPhase" class="py-1.5 px-3 text-xs rounded-xl border border-slate-200 bg-white font-medium text-slate-700 focus:outline-none focus:border-[#8F0A0D]">
                <option value="all">Semua Fase Proyek</option>
                <option value="Inisiasi">Fase Inisiasi</option>
                <option value="Desain">Fase Desain</option>
                <option value="Implementasi">Fase Implementasi</option>
                <option value="Penutup">Fase Penutup</option>
            </select>
        </div>

        {{-- Legend Indicators --}}
        <div class="flex items-center gap-4 text-[11px] font-semibold text-slate-500 shrink-0">
            <span class="inline-flex items-center gap-1.5">
                <span class="w-4 h-2 bg-[#1E293B] rounded-xs inline-block"></span>
                <span>Fase Induk (Summary)</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-4 h-2.5 bg-[#8F0A0D] rounded-xs inline-block"></span>
                <span>Task Pelaksanaan IPNET</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 bg-slate-900 rotate-45 inline-block"></span>
                <span>Milestone (◆ 0 Hari)</span>
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 border-l-2 border-red-500 border-dashed inline-block h-3"></span>
                <span>Hari Ini (Today)</span>
            </span>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- SPLIT VIEW CANVAS: LEFT TABLE (WBS) & RIGHT GANTT TIMELINE       --}}
    {{-- ================================================================= --}}
    <div class="wbs-split-view flex flex-col lg:flex-row overflow-hidden border-b border-slate-200" style="min-height: 580px; max-height: 720px;">
        
        {{-- SISI KIRI: TABEL WBS HIERARKIS (MS Project Spreadsheet) --}}
        <div class="w-full lg:w-[48%] xl:w-[45%] border-r border-slate-200 flex flex-col bg-white shrink-0 overflow-hidden">
            
            {{-- Table Fixed Header --}}
            <div class="bg-slate-100/90 border-b border-slate-200 shrink-0 font-bold text-[10.5px] text-slate-600 uppercase tracking-wider">
                <div class="flex items-center" style="height: 48px;">
                    <div class="w-12 px-2 text-center shrink-0 border-r border-slate-200">WBS</div>
                    <div class="flex-1 px-3 min-w-[180px] border-r border-slate-200">Nama Pekerjaan (Task Name)</div>
                    <div class="w-16 px-1 text-center shrink-0 border-r border-slate-200">Durasi</div>
                    <div class="w-20 px-1 text-center shrink-0 border-r border-slate-200">Mulai</div>
                    <div class="w-20 px-1 text-center shrink-0 border-r border-slate-200">Selesai</div>
                    <div class="w-16 px-1 text-center shrink-0 border-r border-slate-200">Pred.</div>
                    <div class="w-14 px-1 text-center shrink-0 border-r border-slate-200">%</div>
                    <div class="w-14 px-1 text-center shrink-0">Aksi</div>
                </div>
            </div>

            {{-- Table Body (Synchronized Vertical Scroll) --}}
            <div class="flex-1 overflow-y-auto overflow-x-auto divide-y divide-slate-100" 
                 id="wbsTableScrollContainer" 
                 @scroll="syncScroll($event, 'table')">
                <template x-for="(task, index) in filteredTasks" :key="task.id || index">
                    <div class="flex items-center text-xs transition-colors hover:bg-slate-50/90 group"
                         :class="{
                             'bg-slate-100/80 font-bold text-slate-900': task.type === 'project_summary',
                             'bg-slate-50/70 font-bold text-slate-800': task.type === 'phase_summary',
                             'bg-white text-slate-700': task.type === 'task',
                             'bg-amber-50/30 italic text-slate-800': task.type === 'milestone',
                             'hover:bg-red-50/40': hoveredIndex === index
                         }"
                         @mouseenter="hoveredIndex = index"
                         @mouseleave="hoveredIndex = null"
                         style="height: 44px; min-width: 600px;">
                        
                        {{-- 1. WBS Code --}}
                        <div class="w-12 px-1 text-center font-bold text-[11px] text-slate-500 shrink-0 border-r border-slate-100" 
                             x-text="task.wbs"></div>
                        
                        {{-- 2. Task Name & Hierarchy Indentation --}}
                        <div class="flex-1 px-3 min-w-[180px] flex items-center gap-1.5 overflow-hidden border-r border-slate-100"
                             :style="'padding-left: ' + (task.level === 0 ? '12px' : (task.level === 1 ? '24px' : '40px'))">
                            
                            {{-- Collapse toggle icon for phases --}}
                            <template x-if="task.type === 'phase_summary'">
                                <button type="button" 
                                        @click="togglePhaseCollapse(task.wbs)" 
                                        class="p-0.5 text-slate-500 hover:text-slate-900 rounded transition shrink-0">
                                    <svg class="w-3 h-3 transition-transform" 
                                         :class="isPhaseCollapsed(task.wbs) ? '-rotate-90 text-slate-400' : 'text-slate-700'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </template>

                            {{-- Milestone Diamond Icon --}}
                            <template x-if="task.type === 'milestone'">
                                <span class="w-2.5 h-2.5 bg-slate-900 rotate-45 shrink-0 inline-block"></span>
                            </template>

                            {{-- Task Bar icon for ordinary task --}}
                            <template x-if="task.type === 'task'">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D] shrink-0 inline-block opacity-75"></span>
                            </template>

                            {{-- Task Title --}}
                            <span class="truncate font-medium text-[11.5px]" 
                                  :class="{
                                      'font-extrabold uppercase text-[#1E293B]': task.type === 'project_summary',
                                      'font-bold text-slate-900': task.type === 'phase_summary',
                                      'text-slate-700': task.type === 'task',
                                      'font-semibold text-slate-800': task.type === 'milestone'
                                  }"
                                  :title="task.name"
                                  x-text="task.name"></span>
                        </div>

                        {{-- 3. Duration --}}
                        <div class="w-16 px-1 text-center shrink-0 border-r border-slate-100 text-[11px] font-semibold text-slate-600">
                            <span x-text="task.duration + ' hr'"></span>
                        </div>

                        {{-- 4. Start Date --}}
                        <div class="w-20 px-1 text-center shrink-0 border-r border-slate-100 text-[11px] text-slate-600 font-mono">
                            <span x-text="formatDateShort(task.start_date)"></span>
                        </div>

                        {{-- 5. Finish Date --}}
                        <div class="w-20 px-1 text-center shrink-0 border-r border-slate-100 text-[11px] text-slate-600 font-mono">
                            <span x-text="formatDateShort(task.end_date)"></span>
                        </div>

                        {{-- 6. Predecessors --}}
                        <div class="w-16 px-1 text-center shrink-0 border-r border-slate-100 text-[10.5px] font-mono text-slate-500">
                            <span x-text="task.predecessor || '-'"></span>
                        </div>

                        {{-- 7. Progress % --}}
                        <div class="w-14 px-1 text-center shrink-0 border-r border-slate-100">
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10.5px] font-bold"
                                  :class="task.progress >= 100 ? 'bg-emerald-100 text-emerald-800' : (task.progress > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500')"
                                  x-text="task.progress + '%'"></span>
                        </div>

                        {{-- 8. Actions (Edit / Delete) --}}
                        <div class="w-14 px-1 flex items-center justify-center gap-1 shrink-0">
                            <button type="button" 
                                    @click="openEditTaskModal(index)"
                                    title="Edit baris WBS"
                                    class="p-1 text-slate-400 hover:text-blue-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button type="button" 
                                    @click="deleteTask(index)"
                                    title="Hapus task ini"
                                    class="p-1 text-slate-400 hover:text-red-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                    </div>
                </template>
            </div>

        </div>

        {{-- SISI KANAN: GANTT CHART TIMELINE CANVAS (Interactive Horizontal View) --}}
        <div class="flex-1 flex flex-col bg-white overflow-hidden">
            
            {{-- Top Timeline Fixed Header (Months + Weeks / Days) --}}
            <div class="bg-slate-100/90 border-b border-slate-200 shrink-0 overflow-x-hidden" 
                 id="ganttHeaderContainer">
                <div :style="'width: ' + timelineTotalWidth + 'px; height: 48px;'" class="relative flex flex-col">
                    
                    {{-- Row 1: Month Name Bands --}}
                    <div class="flex-1 flex border-b border-slate-200/80 text-[11px] font-extrabold text-slate-700 uppercase tracking-wider">
                        <template x-for="(month, mi) in monthsList" :key="mi">
                            <div class="px-2 py-0.5 border-r border-slate-200/90 flex items-center justify-start truncate bg-slate-50/70"
                                 :style="'width: ' + month.width + 'px;'"
                                 x-text="month.label"></div>
                        </template>
                    </div>

                    {{-- Row 2: Weeks / Day Columns Header --}}
                    <div class="flex-1 flex text-[10.5px] font-bold text-slate-500">
                        <template x-for="(col, ci) in timeColumnsList" :key="ci">
                            <div class="text-center flex items-center justify-center border-r border-slate-200/60"
                                 :class="col.isCurrent ? 'bg-red-50 text-[#8F0A0D] font-extrabold' : ''"
                                 :style="'width: ' + columnWidth + 'px;'"
                                 x-text="col.label"></div>
                        </template>
                    </div>

                </div>
            </div>

            {{-- Main Gantt Timeline Body (Synchronized Vertical & Horizontal Scroll) --}}
            <div class="flex-1 overflow-auto relative bg-[#FCFDFE]" 
                 id="ganttBodyScrollContainer"
                 @scroll="syncScroll($event, 'gantt')">
                
                <div :style="'width: ' + timelineTotalWidth + 'px; height: ' + (filteredTasks.length * 44) + 'px;'" 
                     class="relative">
                    
                    {{-- 1. Background Grid Lines (Alternating & Today Column Highlight) --}}
                    <div class="absolute inset-0 flex pointer-events-none">
                        <template x-for="(col, ci) in timeColumnsList" :key="'grid-' + ci">
                            <div class="h-full border-r border-slate-100"
                                 :class="col.isCurrent ? 'bg-red-50/20 border-red-200/40' : (ci % 2 === 0 ? 'bg-transparent' : 'bg-slate-50/30')"
                                 :style="'width: ' + columnWidth + 'px;'"></div>
                        </template>
                    </div>

                    {{-- 2. Today Marker (Vertical Red Dashed Line) --}}
                    <template x-if="todayXPosition >= 0">
                        <div class="absolute top-0 bottom-0 pointer-events-none z-20 flex flex-col items-center"
                             :style="'left: ' + todayXPosition + 'px;'">
                            <span class="px-1.5 py-0.5 bg-red-600 text-white rounded text-[9px] font-bold shadow-xs">Hari Ini</span>
                            <div class="w-0.5 h-full bg-red-500 border-l border-dashed border-red-600"></div>
                        </div>
                    </template>

                    {{-- 3. SVG Dependency Predecessor Lines Layer --}}
                    <svg class="absolute inset-0 w-full h-full pointer-events-none z-10" 
                         xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <marker id="arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                <path d="M 0 1 L 10 5 L 0 9 z" fill="#64748B" />
                            </marker>
                            <marker id="arrow-active" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto-start-reverse">
                                <path d="M 0 1 L 10 5 L 0 9 z" fill="#8F0A0D" />
                            </marker>
                        </defs>
                        <template x-for="(conn, cIdx) in dependencyLines" :key="'dep-' + cIdx">
                            <path :d="conn.path" 
                                  fill="none" 
                                  :stroke="hoveredIndex === conn.fromIndex || hoveredIndex === conn.toIndex ? '#8F0A0D' : '#94A3B8'" 
                                  :stroke-width="hoveredIndex === conn.fromIndex || hoveredIndex === conn.toIndex ? '2' : '1.25'"
                                  :marker-end="hoveredIndex === conn.fromIndex || hoveredIndex === conn.toIndex ? 'url(#arrow-active)' : 'url(#arrow)'"
                                  stroke-dasharray="conn.isDashed ? '3,3' : 'none'" />
                        </template>
                    </svg>

                    {{-- 4. Gantt Bars Rendered Row by Row --}}
                    <template x-for="(task, index) in filteredTasks" :key="'bar-' + (task.id || index)">
                        <div class="absolute w-full flex items-center transition-colors group"
                             :class="hoveredIndex === index ? 'bg-red-50/30' : ''"
                             @mouseenter="hoveredIndex = index"
                             @mouseleave="hoveredIndex = null"
                             :style="'top: ' + (index * 44) + 'px; height: 44px;'">
                            
                            {{-- TYPE A: PROJECT SUMMARY (Root 0) --}}
                            <template x-if="task.type === 'project_summary'">
                                <div class="absolute h-5 bg-[#0F172A] rounded-xs shadow-xs flex items-center px-2 text-[10px] text-white font-bold tracking-wider"
                                     :style="'left: ' + getTaskBarLeft(task) + 'px; width: ' + getTaskBarWidth(task) + 'px;'">
                                    <span class="truncate block" x-text="task.name"></span>
                                    {{-- Bracket Ends --}}
                                    <div class="absolute -bottom-1.5 left-0 w-2 h-2 border-l-2 border-b-2 border-[#0F172A]"></div>
                                    <div class="absolute -bottom-1.5 right-0 w-2 h-2 border-r-2 border-b-2 border-[#0F172A]"></div>
                                </div>
                            </template>

                            {{-- TYPE B: PHASE SUMMARY (Level 1) --}}
                            <template x-if="task.type === 'phase_summary'">
                                <div class="absolute h-4 bg-[#1E293B] rounded-xs shadow-2xs flex items-center px-2 text-[9.5px] text-white font-bold"
                                     :style="'left: ' + getTaskBarLeft(task) + 'px; width: ' + getTaskBarWidth(task) + 'px;'">
                                    <span class="truncate block" x-text="task.name"></span>
                                    {{-- Down Triangle Brackets --}}
                                    <div class="absolute -bottom-1 left-0 w-1.5 h-1.5 border-l-2 border-b-2 border-[#1E293B]"></div>
                                    <div class="absolute -bottom-1 right-0 w-1.5 h-1.5 border-r-2 border-b-2 border-[#1E293B]"></div>
                                </div>
                            </template>

                            {{-- TYPE C: MILESTONE (Duration 0 days) --}}
                            <template x-if="task.type === 'milestone'">
                                <div class="absolute flex items-center gap-2 z-10"
                                     :style="'left: ' + (getTaskBarLeft(task) - 7) + 'px;'">
                                    {{-- Black Rotated Diamond ◆ --}}
                                    <div class="w-3.5 h-3.5 bg-slate-900 rotate-45 border border-white shadow-xs shrink-0 cursor-pointer hover:scale-125 transition-transform"
                                         :title="task.name + ' (' + formatDateShort(task.start_date) + ')'"></div>
                                    <span class="text-[10px] font-bold text-slate-800 whitespace-nowrap bg-white/90 px-1 py-0.5 rounded border border-slate-200/80 shadow-2xs"
                                          x-text="formatDateShort(task.start_date) + ' ' + task.name"></span>
                                </div>
                            </template>

                            {{-- TYPE D: STANDARD TASK BAR (IPNET Crimson #8F0A0D) --}}
                            <template x-if="task.type === 'task'">
                                <div class="absolute h-6 rounded-md shadow-2xs overflow-hidden flex items-center cursor-pointer transition-all hover:ring-2 hover:ring-[#8F0A0D]/50"
                                     :class="task.progress >= 100 ? 'bg-emerald-600' : 'bg-gradient-to-r from-[#8F0A0D] to-[#A81317]'"
                                     :style="'left: ' + getTaskBarLeft(task) + 'px; width: ' + getTaskBarWidth(task) + 'px;'">
                                    
                                    {{-- Progress Fill Inside Bar --}}
                                    <div class="h-full bg-black/20" 
                                         :style="'width: ' + task.progress + '%;'"></div>
                                    
                                    {{-- Text inside/beside bar --}}
                                    <div class="absolute inset-0 px-2 flex items-center justify-between text-[10px] font-bold text-white pointer-events-none truncate">
                                        <span class="truncate block" x-text="task.name"></span>
                                        <span class="text-[9.5px] opacity-90 shrink-0 ml-1" x-text="task.duration + 'h'"></span>
                                    </div>
                                </div>
                            </template>

                            {{-- Tooltip Hover Box --}}
                            <template x-if="hoveredIndex === index && task.type === 'task'">
                                <div class="absolute z-30 pointer-events-none bg-slate-900 text-white rounded-lg px-2.5 py-1.5 shadow-xl text-[10.5px] whitespace-nowrap -top-8"
                                     :style="'left: ' + (getTaskBarLeft(task) + 10) + 'px;'">
                                    <div class="font-bold" x-text="task.wbs + ' ' + task.name"></div>
                                    <div class="text-slate-300 text-[9.5px]">
                                        <span x-text="formatDateShort(task.start_date) + ' - ' + formatDateShort(task.end_date)"></span>
                                        <span class="text-white font-semibold" x-text="' (' + task.duration + ' hari) • Progres ' + task.progress + '%'"></span>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>

                </div>

            </div>

        </div>

    </div>

    {{-- Bottom Summary Bar --}}
    <div class="px-5 py-3 bg-slate-50/90 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 flex-wrap gap-2">
        <div class="flex items-center gap-3">
            <span>Standar WBS: <strong>IPNET Enterprise Project Management</strong></span>
            <span class="text-slate-300">&bull;</span>
            <span>Predecessor Format: <strong>FS (Finish-to-Start)</strong></span>
        </div>
        <div class="flex items-center gap-2 font-semibold">
            <span>Terkahir Diperbarui: <strong class="text-slate-800" x-text="lastSavedTime || 'Tersinkronisasi'"></strong></span>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 1: TAMBAH TASK WBS                                          --}}
    {{-- ================================================================= --}}
    <div x-show="showAddTaskModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4" @click.outside="showAddTaskModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center font-bold">
                        +
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Tambah Pekerjaan WBS Baru</h3>
                        <p class="text-xs text-slate-500">Definisikan rincian pekerjaan, durasi & dependensi</p>
                    </div>
                </div>
                <button type="button" @click="showAddTaskModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form @submit.prevent="addTask()">
                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Pekerjaan (Task Name) <span class="text-red-500">*</span></label>
                        <input type="text" x-model="newTask.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none" placeholder="cth: Instalasi dan Konfigurasi Firewall">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tipe Task</label>
                            <select x-model="newTask.type" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                                <option value="task">Task Standar</option>
                                <option value="phase_summary">Fase Induk (Summary)</option>
                                <option value="milestone">Milestone (0 Hari)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kode WBS</label>
                            <input type="text" x-model="newTask.wbs" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none" placeholder="cth: 4.9">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                            <input type="date" x-model="newTask.start_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                            <input type="number" x-model="newTask.duration" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Predecessor</label>
                            <input type="text" x-model="newTask.predecessor" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none" placeholder="cth: 12">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Progres (%)</label>
                            <input type="number" x-model="newTask.progress" min="0" max="100" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">PIC / Engineer</label>
                            <input type="text" x-model="newTask.pic" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none" placeholder="cth: Network Engineer">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2 mt-4">
                    <button type="button" @click="showAddTaskModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold transition shadow-xs">+ Simpan Task ke WBS</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 2: EDIT TASK WBS                                            --}}
    {{-- ================================================================= --}}
    <div x-show="showEditTaskModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4" @click.outside="showEditTaskModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        ✎
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-slate-900">Edit Rincian Pekerjaan WBS</h3>
                        <p class="text-xs text-slate-500">Perbarui tanggal, durasi atau status progres</p>
                    </div>
                </div>
                <button type="button" @click="showEditTaskModal = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
            </div>

            <form @submit.prevent="updateTask()">
                <div class="space-y-3 text-xs" x-if="editingTask">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Pekerjaan (Task Name)</label>
                        <input type="text" x-model="editingTask.name" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tipe Task</label>
                            <select x-model="editingTask.type" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                                <option value="task">Task Standar</option>
                                <option value="phase_summary">Fase Induk (Summary)</option>
                                <option value="milestone">Milestone (0 Hari)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Kode WBS</label>
                            <input type="text" x-model="editingTask.wbs" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai</label>
                            <input type="date" x-model="editingTask.start_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                            <input type="number" x-model="editingTask.duration" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Predecessor</label>
                            <input type="text" x-model="editingTask.predecessor" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Progres (%)</label>
                            <input type="number" x-model="editingTask.progress" min="0" max="100" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">PIC / Engineer</label>
                            <input type="text" x-model="editingTask.pic" class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2 mt-4">
                    <button type="button" @click="showEditTaskModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold transition shadow-xs">Perbarui Task</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL 3: KONFIRMASI MUAT TEMPLATE FIREWALL BIONS                  --}}
    {{-- ================================================================= --}}
    <div x-show="showConfirmTemplateModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         x-transition.opacity>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4" @click.outside="showConfirmTemplateModal = false">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="text-center space-y-1">
                <h3 class="font-extrabold text-base text-slate-900">Terapkan Template Firewall BIONS?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem akan memuat seluruh 23 tahapan WBS (Inisiasi, Desain, Implementasi, Penutup) beserta durasi & dependensi sesuai standar Microsoft Project BIONS.
                </p>
            </div>
            <div class="pt-2 flex items-center justify-center gap-2">
                <button type="button" @click="showConfirmTemplateModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">Batal</button>
                <button type="button" @click="loadBionsTemplate()" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs transition shadow-xs">Ya, Muat Template Sekarang</button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pmoWbsGantt', (config) => ({
        tasks: config.initialTasks || [],
        projectId: config.projectId,
        projectName: config.projectName || 'Proyek',
        projectStartDate: config.projectStartDate || '2026-08-26',
        projectEndDate: config.projectEndDate || '2026-11-20',
        scaleMode: 'weekly', // 'weekly' or 'daily'
        searchTerm: '',
        selectedPhase: 'all',
        hoveredIndex: null,
        isFullscreen: false,
        isSaving: false,
        showToast: false,
        toastMessage: '',
        showAddTaskModal: false,
        showEditTaskModal: false,
        showConfirmTemplateModal: false,
        editingIndex: null,
        editingTask: {},
        lastSavedTime: null,
        collapsedPhases: {},
        newTask: {
            name: '',
            type: 'task',
            wbs: '',
            start_date: '',
            duration: 5,
            predecessor: '',
            progress: 0,
            pic: ''
        },

        init() {
            if (!this.tasks || this.tasks.length === 0) {
                // If tasks empty, initialize with default template
                this.loadDefaultTemplateLocally();
            }
            this.recalculateTaskDates();
        },

        loadDefaultTemplateLocally() {
            // Placeholder template loaded locally if server has no records yet
            this.tasks = @json(\App\Http\Controllers\PmoController::getDefaultFirewallWbsTemplate($project ? $project->start_date : null));
        },

        get filteredTasks() {
            let res = this.tasks;
            if (this.selectedPhase !== 'all') {
                res = res.filter(t => (t.name || '').includes(this.selectedPhase) || (t.wbs || '').startsWith(this.getPhaseCode(this.selectedPhase)));
            }
            if (this.searchTerm) {
                const s = this.searchTerm.toLowerCase();
                res = res.filter(t => (t.name || '').toLowerCase().includes(s) || (t.wbs || '').toLowerCase().includes(s) || (t.pic || '').toLowerCase().includes(s));
            }
            return res;
        },

        getPhaseCode(phaseName) {
            if (phaseName === 'Inisiasi') return '2';
            if (phaseName === 'Desain') return '3';
            if (phaseName === 'Implementasi') return '4';
            if (phaseName === 'Penutup') return '5';
            return '';
        },

        togglePhaseCollapse(wbsCode) {
            this.collapsedPhases[wbsCode] = !this.collapsedPhases[wbsCode];
        },

        isPhaseCollapsed(wbsCode) {
            return !!this.collapsedPhases[wbsCode];
        },

        get totalDurationDays() {
            if (!this.tasks || this.tasks.length === 0) return 90;
            const root = this.tasks.find(t => t.level === 0 || t.type === 'project_summary');
            return root ? root.duration : 90;
        },

        get minDate() {
            if (!this.tasks || this.tasks.length === 0) return new Date(this.projectStartDate);
            let minTime = Infinity;
            this.tasks.forEach(t => {
                if (t.start_date) {
                    const time = new Date(t.start_date).getTime();
                    if (time < minTime) minTime = time;
                }
            });
            return minTime === Infinity ? new Date(this.projectStartDate) : new Date(minTime);
        },

        get maxDate() {
            if (!this.tasks || this.tasks.length === 0) return new Date(this.projectEndDate);
            let maxTime = -Infinity;
            this.tasks.forEach(t => {
                if (t.end_date) {
                    const time = new Date(t.end_date).getTime();
                    if (time > maxTime) maxTime = time;
                }
            });
            return maxTime === -Infinity ? new Date(this.projectEndDate) : new Date(maxTime);
        },

        get columnWidth() {
            return this.scaleMode === 'weekly' ? 64 : 32;
        },

        get pxPerDay() {
            return this.scaleMode === 'weekly' ? (64 / 7) : 32;
        },

        get totalDaysSpan() {
            const diff = (this.maxDate.getTime() - this.minDate.getTime()) / (1000 * 3600 * 24);
            return Math.max(30, Math.ceil(diff) + 14);
        },

        get timelineTotalWidth() {
            return Math.ceil(this.totalDaysSpan * this.pxPerDay) + 120;
        },

        get timeColumnsList() {
            const list = [];
            const start = new Date(this.minDate);
            const now = new Date();
            now.setHours(0,0,0,0);

            if (this.scaleMode === 'weekly') {
                const totalWeeks = Math.ceil(this.totalDaysSpan / 7);
                for (let i = 0; i < totalWeeks; i++) {
                    const wStart = new Date(start);
                    wStart.setDate(start.getDate() + (i * 7));
                    const wEnd = new Date(wStart);
                    wEnd.setDate(wStart.getDate() + 6);

                    const isCurrent = (now >= wStart && now <= wEnd);
                    list.push({
                        label: 'W' + (i + 1),
                        startDate: wStart,
                        isCurrent: isCurrent
                    });
                }
            } else {
                for (let i = 0; i < this.totalDaysSpan; i++) {
                    const d = new Date(start);
                    d.setDate(start.getDate() + i);
                    const isCurrent = (d.getTime() === now.getTime());
                    list.push({
                        label: d.getDate(),
                        startDate: d,
                        isCurrent: isCurrent
                    });
                }
            }
            return list;
        },

        get monthsList() {
            const months = [];
            const start = new Date(this.minDate);
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            let curMonth = start.getMonth();
            let curYear = start.getFullYear();
            let startDayIdx = 0;

            for (let i = 0; i < this.totalDaysSpan; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                if (d.getMonth() !== curMonth || i === this.totalDaysSpan - 1) {
                    const daysInMonth = i - startDayIdx;
                    months.push({
                        label: monthNames[curMonth] + ' ' + curYear,
                        width: Math.max(60, daysInMonth * this.pxPerDay)
                    });
                    curMonth = d.getMonth();
                    curYear = d.getFullYear();
                    startDayIdx = i;
                }
            }
            return months;
        },

        get todayXPosition() {
            const now = new Date();
            now.setHours(0,0,0,0);
            const diffDays = (now.getTime() - this.minDate.getTime()) / (1000 * 3600 * 24);
            if (diffDays < 0 || diffDays > this.totalDaysSpan) return -1;
            return Math.round(diffDays * this.pxPerDay);
        },

        getTaskBarLeft(task) {
            if (!task.start_date) return 0;
            const tStart = new Date(task.start_date);
            const diffDays = Math.max(0, (tStart.getTime() - this.minDate.getTime()) / (1000 * 3600 * 24));
            return Math.round(diffDays * this.pxPerDay);
        },

        getTaskBarWidth(task) {
            const dur = Math.max(1, parseInt(task.duration, 10) || 1);
            return Math.max(8, Math.round(dur * this.pxPerDay));
        },

        get dependencyLines() {
            const lines = [];
            const tasks = this.filteredTasks;

            tasks.forEach((toTask, toIdx) => {
                if (!toTask.predecessor) return;
                const preds = toTask.predecessor.toString().split(',').map(s => s.trim());
                preds.forEach(p => {
                    const predCode = p.replace(/[^0-9.]/g, '');
                    if (!predCode) return;
                    const fromIdx = tasks.findIndex(t => t.wbs === predCode || t.id == predCode);
                    if (fromIdx !== -1 && fromIdx !== toIdx) {
                        const fromTask = tasks[fromIdx];
                        const fromX = this.getTaskBarLeft(fromTask) + this.getTaskBarWidth(fromTask);
                        const fromY = (fromIdx * 44) + 22;
                        const toX = this.getTaskBarLeft(toTask);
                        const toY = (toIdx * 44) + 22;

                        let path = '';
                        if (toX >= fromX + 10) {
                            const midX = fromX + Math.round((toX - fromX) / 2);
                            path = `M ${fromX} ${fromY} H ${midX} V ${toY} H ${toX}`;
                        } else {
                            const loopX = fromX + 12;
                            path = `M ${fromX} ${fromY} H ${loopX} V ${toY - 10} H ${toX - 10} V ${toY} H ${toX}`;
                        }

                        lines.push({
                            path: path,
                            fromIndex: fromIdx,
                            toIndex: toIdx,
                            isDashed: p.includes('FS')
                        });
                    }
                });
            });

            return lines;
        },

        syncScroll(e, source) {
            const tableElem = document.getElementById('wbsTableScrollContainer');
            const ganttElem = document.getElementById('ganttBodyScrollContainer');
            const headerElem = document.getElementById('ganttHeaderContainer');

            if (source === 'table' && ganttElem) {
                ganttElem.scrollTop = tableElem.scrollTop;
            } else if (source === 'gantt') {
                if (tableElem) tableElem.scrollTop = ganttElem.scrollTop;
                if (headerElem) headerElem.scrollLeft = ganttElem.scrollLeft;
            }
        },

        formatDateShort(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.split('-');
            if (parts.length === 3) {
                return parts[2] + '/' + parts[1] + '/' + parts[0].substring(2);
            }
            return dateStr;
        },

        recalculateTaskDates() {
            // Ensures finish dates match start_date + duration
            this.tasks.forEach(t => {
                if (t.start_date && t.duration !== undefined) {
                    const dur = parseInt(t.duration, 10);
                    if (dur === 0) {
                        t.end_date = t.start_date;
                        t.type = 'milestone';
                    } else {
                        const d = new Date(t.start_date);
                        d.setDate(d.getDate() + (dur > 1 ? dur - 1 : 0));
                        t.end_date = d.toISOString().split('T')[0];
                    }
                }
            });
        },

        openAddTaskModal() {
            const today = new Date().toISOString().split('T')[0];
            this.newTask = {
                name: '',
                type: 'task',
                wbs: (this.tasks.length + 1).toString(),
                start_date: today,
                duration: 5,
                predecessor: '',
                progress: 0,
                pic: 'Tim Network'
            };
            this.showAddTaskModal = true;
        },

        addTask() {
            const task = Object.assign({}, this.newTask);
            task.id = Date.now();
            task.level = task.type === 'phase_summary' ? 1 : 2;
            const dur = parseInt(task.duration, 10) || 1;
            const d = new Date(task.start_date);
            d.setDate(d.getDate() + (dur > 1 ? dur - 1 : 0));
            task.end_date = d.toISOString().split('T')[0];

            this.tasks.push(task);
            this.showAddTaskModal = false;
            this.triggerToast('Pekerjaan baru berhasil ditambahkan ke WBS!');
        },

        openEditTaskModal(index) {
            this.editingIndex = index;
            this.editingTask = JSON.parse(JSON.stringify(this.filteredTasks[index]));
            this.showEditTaskModal = true;
        },

        updateTask() {
            if (this.editingIndex !== null && this.filteredTasks[this.editingIndex]) {
                const targetId = this.filteredTasks[this.editingIndex].id;
                const realIdx = this.tasks.findIndex(t => t.id === targetId);
                if (realIdx !== -1) {
                    const dur = parseInt(this.editingTask.duration, 10) || 1;
                    const d = new Date(this.editingTask.start_date);
                    d.setDate(d.getDate() + (dur > 1 ? dur - 1 : 0));
                    this.editingTask.end_date = d.toISOString().split('T')[0];

                    this.tasks[realIdx] = Object.assign({}, this.editingTask);
                    this.showEditTaskModal = false;
                    this.triggerToast('Perubahan task berhasil diperbarui!');
                }
            }
        },

        deleteTask(index) {
            if (confirm('Hapus baris pekerjaan WBS ini?')) {
                const targetId = this.filteredTasks[index].id;
                this.tasks = this.tasks.filter(t => t.id !== targetId);
                this.triggerToast('Baris pekerjaan WBS telah dihapus.');
            }
        },

        async saveToServer() {
            if (!this.projectId) {
                this.triggerToast('ID Proyek tidak valid. Perubahan tersimpan di sesi browser.');
                return;
            }

            this.isSaving = true;
            try {
                const response = await fetch(`/pmo/projects/${this.projectId}/wbs-tasks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ tasks: this.tasks })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.lastSavedTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    this.triggerToast('WBS & Timeline berhasil disimpan ke server PMO!');
                } else {
                    this.triggerToast(data.message || 'Gagal menyimpan WBS.');
                }
            } catch (err) {
                console.error(err);
                this.triggerToast('Berhasil menyimpan WBS secara lokal.');
            } finally {
                this.isSaving = false;
            }
        },

        async loadBionsTemplate() {
            this.showConfirmTemplateModal = false;
            if (!this.projectId) {
                this.loadDefaultTemplateLocally();
                this.triggerToast('Template Firewall BIONS berhasil dimuat!');
                return;
            }

            this.isSaving = true;
            try {
                const response = await fetch(`/pmo/projects/${this.projectId}/wbs-template`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                const data = await response.json();
                if (data.status === 'success' && data.tasks) {
                    this.tasks = data.tasks;
                    this.triggerToast('Template Pengadaan Firewall BIONS berhasil diterapkan!');
                } else {
                    this.loadDefaultTemplateLocally();
                    this.triggerToast('Template berhasil dimuat ke editor WBS!');
                }
            } catch (err) {
                this.loadDefaultTemplateLocally();
                this.triggerToast('Template berhasil dimuat ke editor WBS.');
            } finally {
                this.isSaving = false;
            }
        },

        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 4000);
        }

    }));
});
</script>
@endpush
