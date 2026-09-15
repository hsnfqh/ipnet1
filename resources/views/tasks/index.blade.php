@extends('layouts.app')

@section('title', 'Penugasan Tim & Daftar Tugas - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Penugasan Tim'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto" x-data="tasksManager()" x-init="init()">

            <!-- ========================================================== -->
            <!-- SECTION HEADER & FILTER CONTROLS                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PENUGASAN
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Penugasan & Aktivitas Lapangan</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Pantau status pengerjaan tugas, delegasi teknisi, dan verifikasi penyelesaian kegiatan</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Penugasan:</span>
                            <span class="text-[#8F0A0D] font-extrabold" x-text="tasks.length"></span>
                        </div>

                        @if($canManage)
                        <button @click="openModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat & Delegasikan Tugas</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    <select x-model="filterProject" 
                            class="w-full sm:w-52 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="">Semua Project</option>
                        <template x-for="project in projects" :key="project.id">
                            <option :value="project.id" x-text="project.name"></option>
                        </template>
                    </select>

                    <select x-model="filterPriority" 
                            class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="">Semua Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>

                    @if($isLead)
                    <select x-model="filterEngineer" 
                            class="w-full sm:w-52 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="">Semua Engineer</option>
                        <template x-for="engineer in engineers" :key="engineer.id">
                            <option :value="engineer.id" x-text="engineer.name"></option>
                        </template>
                    </select>
                    @endif
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- KANBAN BOARD - RESPONSIVE (4 kolom desktop)                 -->
            <!-- ========================================================== -->
            <div class="kanban-board anim-fade-up anim-delay-2">
                <template x-for="column in ['Assigned', 'In Progress', 'Waiting Review', 'Completed']" :key="column">
                    <div class="kanban-col">
                        <div class="kanban-col-head">
                            <span class="text-[12px] font-bold text-[#334155] uppercase tracking-wider" x-text="column"></span>
                            <span class="text-[11px] font-bold bg-[#E2E8F0] text-[#475569] px-2 py-0.5 rounded-full"
                                  x-text="getFilteredTasksByStatus(column).length"></span>
                        </div>
                        <div class="kanban-col-body">
                            <template x-for="task in getFilteredTasksByStatus(column)" :key="task.id">
                                <div class="bg-white border border-[#E2E8F0] rounded-xl p-3.5 shadow-xs hover:shadow-md transition-all">

                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <span x-html="getPriorityFlag(task.priority)"></span>
                                    </div>

                                    <div class="text-[13.5px] font-bold text-[#1E293B] mb-1 break-words line-clamp-2" x-text="task.title"></div>
                                    <div class="text-[12px] font-medium text-[#64748B] mb-2.5 break-words line-clamp-1" x-text="task.project?.name"></div>

                                    <div class="flex items-center justify-between mb-2.5 gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <!-- Stacked avatars jika ditugaskan ke tim (multi-assignee) -->
                                            <div class="flex items-center mr-0.5">
                                                <template x-for="(eng, idx) in (task.engineers && task.engineers.length > 0 ? task.engineers.slice(0, 2) : (task.engineer ? [task.engineer] : []))" :key="eng.id">
                                                    <div class="w-5.5 h-5.5 rounded-full text-white flex items-center justify-center text-[9px] font-bold shrink-0 border-2 border-white shadow-xs"
                                                         :style="{ background: idx === 0 ? '#8F0A0D' : '#2563EB', marginLeft: idx > 0 ? '-6px' : '0' }"
                                                         :title="eng.name + (idx === 0 ? ' (PIC Utama)' : ' (Pendamping Lapangan)')">
                                                        <span x-text="eng.name ? eng.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?'"></span>
                                                    </div>
                                                </template>
                                                <template x-if="task.engineers && task.engineers.length > 2">
                                                    <div class="w-5 h-5 rounded-full bg-[#64748B] text-white flex items-center justify-center text-[8.5px] font-bold shrink-0 border-2 border-white -ml-1.5"
                                                         :title="task.engineers.map(e => e.name).join(', ')">
                                                        <span x-text="'+' + (task.engineers.length - 2)"></span>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="truncate text-[11.5px] text-[#334155] font-medium">
                                                <span x-text="task.engineer?.name || 'Belum diassign'"></span>
                                                <template x-if="task.engineers && task.engineers.length > 1">
                                                    <span class="text-[9.5px] font-bold text-[#1D4ED8] bg-[#EFF6FF] border border-[#DBEAFE] px-1.5 py-0.5 rounded-md ml-1"
                                                          x-text="'+' + (task.engineers.length - 1) + ' tim'"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <span class="text-[10.5px] font-mono text-[#64748B] whitespace-nowrap" x-text="formatDeadline(task.deadline, task.deadline_time)"></span>
                                    </div>

                                    <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full bg-linear-to-r from-[#8F0A0D] to-[#DC2626] transition-all duration-300" :style="{ width: (task.progress || 0) + '%' }"></div>
                                    </div>

                                    <div class="flex justify-between items-center mt-1.5 text-[11px] text-[#64748B]">
                                        <span>Progress: <strong class="text-[#8F0A0D] font-bold" x-text="(task.progress || 0) + '%'"></strong></span>
                                        <template x-if="task.doc_file || task.attachments > 0">
                                            <span class="inline-flex items-center gap-1 text-[#15803D] font-semibold cursor-pointer hover:underline" @click="openDetailModal(task)">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                Foto/Dokumen
                                            </span>
                                        </template>
                                    </div>

                                    @if($canManage)
                                    <div class="flex items-center gap-1.5 mt-3">
                                        <button @click="openDetailModal(task)"
                                                class="flex-1 py-1.5 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#334155] text-[11px] font-semibold flex items-center justify-center gap-1 transition-all cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </button>
                                        <button @click="editTask(task)"
                                                class="flex-1 py-1.5 rounded-lg border border-[#CBD5E1] bg-white hover:bg-[#F8FAFC] text-[#334155] text-[11px] font-semibold flex items-center justify-center gap-1 transition-all cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <template x-if="task.status !== 'Completed' || task.progress < 100">
                                            <button type="button" 
                                                    @click="quickCompleteTask(task)"
                                                    class="w-8 shrink-0 py-1.5 rounded-lg border border-[#86EFAC] bg-[#F0FDF4] hover:bg-[#DCFCE7] text-[#16A34A] flex items-center justify-center transition-all cursor-pointer"
                                                    title="Tandai Selesai Langsung (100% Completed)">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <button @click="confirmDelete(task)"
                                                class="w-8 shrink-0 py-1.5 rounded-lg border border-[#FECACA] bg-white hover:bg-[#FEF2F2] text-[#DC2626] flex items-center justify-center transition-all cursor-pointer"
                                                title="Hapus Task">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @elseif($isEngineer)
                                    <div class="flex items-center gap-1.5 mt-3">
                                        <button @click="openDetailModal(task)"
                                                class="flex-1 py-1.5 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#334155] text-[11px] font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail Task
                                        </button>
                                        <template x-if="(task.status !== 'Completed' || task.progress < 100) && (task.engineer_id == {{ $currentUserId }} || (task.engineers && task.engineers.some(e => e.id == {{ $currentUserId }})))">
                                            <button type="button" 
                                                    @click="openProgressModal(task)"
                                                    class="py-1.5 px-3 rounded-lg border border-[#86EFAC] bg-[#F0FDF4] hover:bg-[#DCFCE7] text-[#16A34A] text-[11px] font-bold flex items-center justify-center gap-1 transition-all cursor-pointer"
                                                    title="Update Progress & Dokumentasi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Update
                                            </button>
                                        </template>
                                    </div>
                                    @else
                                    {{-- READ-ONLY / PMO / VIEWERS: HANYA LIHAT DETAIL TASK --}}
                                    <div class="flex items-center gap-1.5 mt-3">
                                        <button @click="openDetailModal(task)"
                                                class="w-full py-1.5 rounded-lg border border-[#CBD5E1] bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#334155] text-[11px] font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail Task
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </template>

                            <div x-show="getFilteredTasksByStatus(column).length === 0"
                                 class="text-[12.5px] font-medium text-[#94A3B8] py-8 px-2 text-center">
                                Tidak ada task.
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- ========================================================== -->
            <!-- TASK MODAL (dipakai untuk Buat & Edit) -->
            <!-- ========================================================== -->
            <template x-teleport="body">
                <div x-show="modalOpen"
                     x-cloak
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="modalOpen = false">

                    <div style="background:white; border-radius:16px; width:640px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(14,13,18,0.15); margin:auto; position:relative; animation:fadeInUp 0.2s ease;">

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 18px; position:sticky; top:0; background:white; border-bottom:1px solid #E7E5E3; border-radius:16px 16px 0 0;">
                            <h3 style="margin:0; font-family:'Inter',sans-serif; font-size:16px; font-weight:600; color:#17151C;" x-text="editing ? 'Edit Task' : 'Buat & Assign Task'"></h3>
                            <button @click="modalOpen = false" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px; transition:all 0.15s ease; flex-shrink:0;" onmouseover="this.style.background='#F1F0EE'" onmouseout="this.style.background='transparent'">
                                <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div style="padding:18px;">
                            <form @submit.prevent="saveTask">
                                <div style="display:flex; flex-direction:column; gap:14px;">
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Judul Task</label>
                                        <input type="text" x-model="form.title"
                                               style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;"
                                               required>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Project</label>
                                        <select x-model="form.project_id"
                                                style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;"
                                                required>
                                            <option value="">Pilih Project</option>
                                            <template x-for="project in modalProjects" :key="project.id">
                                                <option :value="project.id" x-text="project.name"></option>
                                            </template>
                                            <option value="other" style="font-weight:600; color:#C81E2C;">Other</option>
                                        </select>
                                        <!-- Input nama project jika memilih 'other' -->
                                        <div x-show="form.project_id === 'other'" style="margin-top:8px; animation:fadeInUp 0.2s ease;">
                                            <label style="display:block; font-size:11px; font-weight:700; color:#C81E2C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">
                                                Nama Project
                                            </label>
                                            <input type="text" x-model="form.new_project_name"
                                                   placeholder="Masukkan nama project..."
                                                   style="width:100%; padding:9px 12px; border-radius:8px; border:1.5px solid #C81E2C; font-size:14px; color:#17151C; outline:none; background:#FFF5F5; box-sizing:border-box;"
                                                   :required="form.project_id === 'other'">
                                        </div>
                                    </div>
                                    <div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                            <label style="font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">
                                                Tim Pelaksana & Pendamping Lapangan
                                            </label>
                                            <span style="font-size:11px; color:#C81E2C; font-weight:600;" x-text="(form.engineer_ids?.length || 0) + ' Teknisi Dipilih'"></span>
                                        </div>

                                        <!-- Chip tag teknisi terpilih -->
                                        <div style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px; min-height:36px; padding:6px; background:#F8F7F6; border:1px solid #E7E5E3; border-radius:8px; align-items:center;">
                                            <template x-for="(engId, idx) in form.engineer_ids" :key="engId">
                                                <div style="display:inline-flex; align-items:center; gap:5px; background:white; border:1px solid #E7E5E3; padding:3px 8px; border-radius:20px; font-size:11.5px; box-shadow:0 1px 2px rgba(0,0,0,0.03);">
                                                    <span style="width:6px; height:6px; border-radius:50%;" :style="{ background: idx === 0 ? '#C81E2C' : '#2563EB' }"></span>
                                                    <span style="font-weight:600; color:#17151C;" x-text="getEngineerName(engId)"></span>
                                                    <span style="font-size:9.5px; font-weight:700; padding:1px 5px; border-radius:10px;" :style="{ background: idx === 0 ? '#FDF1F2' : '#EFF6FF', color: idx === 0 ? '#C81E2C' : '#1D4ED8' }" x-text="idx === 0 ? 'PIC' : 'Pendamping'"></span>
                                                    <button type="button" @click="toggleEngineer(engId)" style="background:none; border:none; color:#75727C; cursor:pointer; padding:0; display:flex; align-items:center;" title="Hapus">
                                                        <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            </template>
                                            <span x-show="!form.engineer_ids || form.engineer_ids.length === 0" style="font-size:12px; color:#948F99; margin-left:4px;">Pilih minimal 1 teknisi dari daftar di bawah...</span>
                                        </div>

                                        <!-- Checklist teknisi (dengan tinggi lega 210px agar nyaman dan tidak terpotong) -->
                                        <div style="max-height:210px; overflow-y:auto; border:1px solid #E7E5E3; border-radius:8px; background:white; padding:4px;">
                                            <template x-for="engineer in engineers" :key="engineer.id">
                                                <div @click="toggleEngineer(engineer.id)" 
                                                     style="display:flex; align-items:center; justify-content:space-between; padding:6px 10px; border-radius:6px; cursor:pointer; font-size:12px; transition:all 0.1s ease;"
                                                     :style="{ background: isEngineerSelected(engineer.id) ? '#FDF1F2' : 'transparent' }">
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <input type="checkbox" :checked="isEngineerSelected(engineer.id)" style="accent-color:#C81E2C; cursor:pointer; pointer-events:none;">
                                                        <div>
                                                            <div style="font-weight:600; color:#17151C;" x-text="engineer.name"></div>
                                                            <div style="font-size:10.5px; color:#75727C;" x-text="engineer.position || engineer.role"></div>
                                                        </div>
                                                    </div>
                                                    <template x-if="isEngineerSelected(engineer.id)">
                                                        <span style="font-size:9.5px; font-weight:700; padding:1px 6px; border-radius:10px;" 
                                                              :style="{ background: (form.engineer_ids && parseInt(form.engineer_ids[0], 10) === parseInt(engineer.id, 10)) ? '#FDF1F2' : '#EFF6FF', color: (form.engineer_ids && parseInt(form.engineer_ids[0], 10) === parseInt(engineer.id, 10)) ? '#C81E2C' : '#1D4ED8' }"
                                                              x-text="(form.engineer_ids && parseInt(form.engineer_ids[0], 10) === parseInt(engineer.id, 10)) ? 'PIC Utama' : 'Pendamping'"></span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="modal-grid-2">
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Priority</label>
                                            <select x-model="form.priority" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;">
                                                <option value="High">High</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Tanggal Kegiatan</label>
                                            <input type="date" x-model="form.deadline"
                                                   style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;"
                                                   required>
                                        </div>
                                    </div>
                                    <div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                            <label style="font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">
                                                Jam Kegiatan
                                            </label>
                                            <span style="font-size:10.5px; color:#948F99; font-weight:500;">(Opsional)</span>
                                        </div>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <input type="time" x-model="form.deadline_time"
                                                   style="flex:1; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;">
                                            <button type="button" x-show="form.deadline_time" @click="form.deadline_time = ''" 
                                                    style="background:#F1F0EE; border:none; padding:9px 12px; border-radius:8px; font-size:11.5px; color:#75727C; cursor:pointer;"
                                                    title="Hapus jam">Reset Jam</button>
                                        </div>
                                    </div>
                                    <div x-show="editing" class="modal-grid-2">
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Status</label>
                                            <select x-model="form.status" style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;">
                                                <option value="Assigned">Assigned</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Waiting Review">Waiting Review</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Progress (<span x-text="form.progress"></span>%)</label>
                                            <input type="range" min="0" max="100" x-model="form.progress" style="width:100%; margin-top:8px; accent-color:#C81E2C;">
                                        </div>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Deskripsi</label>
                                        <textarea x-model="form.description"
                                                  style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; min-height:70px; transition:border 0.15s ease; box-sizing:border-box;"
                                                  rows="3"></textarea>
                                    </div>
                                </div>
                                <div style="display:flex; gap:10px; margin-top:16px; padding-top:16px; border-top:1px solid #EFEDEB; flex-wrap:wrap;">
                                    <button type="submit"
                                            class="btn-ipnet-gradient"
                                            style="flex:1 1 140px; justify-content:center; padding:10px 17px; border-radius:10px; font-weight:700; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px;">
                                        <span x-text="editing ? 'Simpan Perubahan' : 'Simpan Task'"></span>
                                    </button>
                                    <button type="button" @click="modalOpen = false"
                                            style="flex:1 1 140px; justify-content:center; background:white; color:#3D3A44; border:1px solid #E7E5E3; padding:10px 17px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
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

            <!-- ========================================================== -->
            <!-- PROGRESS MODAL -->
            <!-- ========================================================== -->
            <template x-teleport="body">
                <div x-show="progressModalOpen"
                     x-cloak
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="progressModalOpen = false">

                    <div style="background:white; border-radius:16px; width:460px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(14,13,18,0.15); margin:auto; position:relative; animation:fadeInUp 0.2s ease;">

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 18px; position:sticky; top:0; background:white; border-bottom:1px solid #E7E5E3; border-radius:16px 16px 0 0;">
                            <h3 style="margin:0; font-family:'Inter',sans-serif; font-size:16px; font-weight:600; color:#17151C;">Update Progress</h3>
                            <button @click="progressModalOpen = false" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px; transition:all 0.15s ease; flex-shrink:0;" onmouseover="this.style.background='#F1F0EE'" onmouseout="this.style.background='transparent'">
                                <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div style="padding:18px;">
                            <div style="margin-bottom:16px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">
                                    Progress (<span x-text="progressForm.progress"></span>%)
                                </label>
                                <input type="range" min="0" max="100" x-model="progressForm.progress" style="width:100%; accent-color:#C81E2C;">
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Upload Dokumentasi</label>
                                <label style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:13px; color:#75727C; background:white; display:flex; align-items:center; gap:8px; cursor:pointer; transition:border 0.15s ease; box-sizing:border-box;" onmouseover="this.style.borderColor='#C81E2C'" onmouseout="this.style.borderColor='#E7E5E3'">
                                    <svg style="width:14px; height:14px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="progressForm.fileName || 'Pilih file foto/dokumen pekerjaan...'"></span>
                                    <input type="file" id="doc_file_input" accept="image/*,.pdf,.doc,.docx" style="display:none;" @change="handleFileChange($event)">
                                </label>
                                <p x-show="progressForm.fileName" style="font-size:10.5px; color:#1B7A46; margin-top:4px; margin-bottom:0;">File siap diunggah</p>
                            </div>
                            <div style="margin-bottom:16px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Keterangan / Hasil Pekerjaan</label>
                                <textarea x-model="progressForm.description"
                                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; min-height:70px; transition:border 0.15s ease; box-sizing:border-box;"
                                          placeholder="Tuliskan keterangan update progress..."></textarea>
                            </div>
                            
                            <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                                <button @click="saveProgress()"
                                        class="btn-ipnet-gradient"
                                        style="flex:1 1 140px; justify-content:center; padding:10px 17px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:7px;">
                                    Simpan Progress
                                </button>
                                <button type="button" @click="progressModalOpen = false"
                                        style="flex:1 1 140px; justify-content:center; background:white; color:#3D3A44; border:1px solid #E7E5E3; padding:10px 17px; border-radius:8px; font-weight:600; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
                                        onmouseover="this.style.background='#F8F7F6'"
                                        onmouseout="this.style.background='white'">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ========================================================== -->
            <!-- CONFIRM DELETE MODAL -->
            <!-- ========================================================== -->
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
                        
                        <h3 class="text-center font-display text-[17px] font-bold text-[#17151C] mb-2">Yakin Hapus Task?</h3>
                        <p class="text-center text-[13.5px] text-[#75727C] mb-6 break-words" x-text="'Task &quot;' + (confirmData?.title || '') + '&quot; akan dihapus.'"></p>

                        <div class="flex gap-3">
                            <button type="button" @click="deleteTask()" class="flex-1 py-2.5 px-4 rounded-xl bg-[#C81E2C] text-white font-semibold text-[13.5px] hover:bg-[#A31622] transition cursor-pointer">
                                Hapus
                            </button>
                            <button type="button" @click="confirmOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[13.5px] hover:bg-[#F8F7F6] transition cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ========================================================== -->
            <!-- TASK DETAIL & DOCUMENTATION MODAL -->
            <!-- ========================================================== -->
            <template x-teleport="body">
                <div x-show="detailModalOpen"
                     x-cloak
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="detailModalOpen = false">

                    <div style="background:white; border-radius:16px; width:520px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(14,13,18,0.15); margin:auto; position:relative; animation:fadeInUp 0.2s ease;">

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 18px; position:sticky; top:0; background:white; border-bottom:1px solid #E7E5E3; border-radius:16px 16px 0 0;">
                            <h3 style="margin:0; font-family:'Inter',sans-serif; font-size:16px; font-weight:600; color:#17151C;">Detail & Dokumentasi Task</h3>
                            <button @click="detailModalOpen = false" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px; transition:all 0.15s ease;" onmouseover="this.style.background='#F1F0EE'" onmouseout="this.style.background='transparent'">
                                <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div style="padding:18px;" x-show="selectedTask">
                            <h4 style="margin:0 0 4px; font-size:15px; font-weight:600; color:#17151C;" x-text="selectedTask?.title"></h4>
                            <p style="margin:0 0 14px; font-size:12px; color:#75727C;" x-text="selectedTask?.project?.name"></p>

                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px; background:#F8F7F6; padding:12px; border-radius:8px; font-size:12px;">
                                <div style="grid-column: 1 / -1; background:white; border:1px solid #E7E5E3; border-radius:8px; padding:10px 12px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                        <span style="font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Tim Pelaksana & Pendamping Lapangan</span>
                                        <span style="font-size:11px; font-weight:600; color:#C81E2C;" x-text="(selectedTask?.engineers?.length || (selectedTask?.engineer ? 1 : 0)) + ' Personil'"></span>
                                    </div>
                                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                        <template x-for="(eng, idx) in (selectedTask?.engineers && selectedTask.engineers.length > 0 ? selectedTask.engineers : (selectedTask?.engineer ? [selectedTask.engineer] : []))" :key="eng.id">
                                            <div style="display:inline-flex; align-items:center; gap:6px; background:#F8F7F6; border:1px solid #E7E5E3; padding:4px 10px 4px 6px; border-radius:20px; font-size:11.5px;">
                                                <div style="width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:white;" :style="{ background: idx === 0 ? '#C81E2C' : '#2563EB' }">
                                                    <span x-text="eng.name ? eng.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?'"></span>
                                                </div>
                                                <div>
                                                    <span style="font-weight:600; color:#17151C;" x-text="eng.name"></span>
                                                    <span x-show="eng.position" style="font-size:10px; color:#75727C; margin-left:2px;" x-text="'(' + eng.position + ')'"></span>
                                                </div>
                                                <span style="font-size:9.5px; font-weight:700; padding:1px 6px; border-radius:10px;" :style="{ background: idx === 0 ? '#FDF1F2' : '#EFF6FF', color: idx === 0 ? '#C81E2C' : '#1D4ED8' }" x-text="idx === 0 ? 'PIC Utama' : 'Pendamping'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div>
                                    <span style="color:#75727C; font-weight:500;">Priority:</span>
                                    <span style="display:block;" x-html="getPriorityFlag(selectedTask?.priority)"></span>
                                </div>
                                <div>
                                    <span style="color:#75727C; font-weight:500;">Status:</span>
                                    <strong style="color:#C81E2C; display:block;" x-text="selectedTask?.status"></strong>
                                </div>
                                <div>
                                    <span style="color:#75727C; font-weight:500;">Tanggal Kegiatan:</span>
                                    <strong style="color:#17151C; display:block;" x-text="formatDeadline(selectedTask?.deadline, selectedTask?.deadline_time)"></strong>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div style="margin-bottom:16px;">
                                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                                    <span style="font-weight:600; color:#3D3A44;">Progress Pengerjaan</span>
                                    <span style="font-weight:700; color:#C81E2C;" x-text="(selectedTask?.progress || 0) + '%'"></span>
                                </div>
                                <div style="width:100%; background:#EFEDEB; border-radius:20px; height:8px; overflow:hidden;">
                                    <div style="height:100%; border-radius:20px; background:linear-gradient(90deg, #AF1424, #D62E3C); transition:width .3s ease;" :style="{ width: (selectedTask?.progress || 0) + '%' }"></div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div style="margin-bottom:16px;" x-show="selectedTask?.description">
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase;">Deskripsi</label>
                                <p style="margin:0; font-size:13px; color:#3D3A44; background:#F9F9F8; padding:10px; border-radius:8px; white-space:pre-line;" x-text="selectedTask?.description"></p>
                            </div>

                            <!-- Uploaded Proof / Photo Documentation -->
                            <div style="margin-bottom:14px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase;">Dokumentasi Foto / File Upload</label>

                                <template x-if="selectedTask?.doc_file">
                                    <div>
                                        <!-- Image Preview if image -->
                                        <div x-show="selectedTask.doc_file.match(/\.(jpg|jpeg|png|gif|webp)$/i)" style="margin-bottom:10px; border-radius:8px; overflow:hidden; border:1px solid #E7E5E3; background:#17151C; text-align:center;">
                                            <img :src="'/storage/' + selectedTask.doc_file" style="max-width:100%; max-height:300px; object-fit:contain; margin:auto; display:block;" alt="Dokumentasi Pekerjaan">
                                        </div>

                                        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:#E4F3EA; border-radius:8px; gap:8px;">
                                            <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                                                <svg style="width:18px; height:18px; color:#1B7A46; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span style="font-size:12px; font-weight:600; color:#1B7A46; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="selectedTask.doc_file.split('/').pop()"></span>
                                            </div>
                                            <a :href="'/storage/' + selectedTask.doc_file" target="_blank" download style="display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600; color:#1B7A46; background:white; padding:5px 12px; border-radius:6px; border:1px solid #1B7A46; text-decoration:none; flex-shrink:0;">
                                                <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Buka / Unduh
                                            </a>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!selectedTask?.doc_file">
                                    <div style="padding:16px; border:1px dashed #E7E5E3; border-radius:8px; text-align:center; color:#948F99; font-size:12px;">
                                        Belum ada dokumentasi foto/file yang diupload untuk task ini.
                                    </div>
                                </template>
                            </div>

                            <!-- Detail Modal Footer Actions -->
                            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #E7E5E3; display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                                @if($canManage)
                                <template x-if="selectedTask?.status !== 'Completed' || selectedTask?.progress < 100">
                                    <button type="button" 
                                            @click="quickCompleteTask(selectedTask); detailModalOpen = false;"
                                            style="padding:9px 18px; border-radius:8px; border:none; background:#16A34A; color:white; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(22,163,74,0.25); transition:all 0.15s ease;"
                                            onmouseover="this.style.background='#15803D'"
                                            onmouseout="this.style.background='#16A34A'">
                                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Tandai Selesai (100% Completed)
                                    </button>
                                </template>
                                @endif
                                <button type="button" @click="detailModalOpen = false" style="padding:9px 18px; border-radius:8px; border:1px solid #E7E5E3; background:white; font-size:13px; font-weight:600; color:#3D3A44; cursor:pointer; margin-left:auto;">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ================= FILTER SELECT ================= */
    .filter-select {
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid #E7E5E3;
        font-size: 12px;
        background: white;
        cursor: pointer;
        min-width: 140px;
        flex: 1 1 140px;
        max-width: 220px;
        color: #3D3A44;
        outline: none;
        transition: border 0.15s ease;
        box-sizing: border-box;
    }

    /* ================= KANBAN BOARD ================= */
    .kanban-board {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        align-items: start;
    }

    .kanban-col {
        background: #F8FAFC;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 200px);
        min-height: 200px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .kanban-col-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 13px 14px 11px;
        flex-shrink: 0;
        background: #F1F5F9;
        border-bottom: 1px solid #E2E8F0;
        border-radius: 16px 16px 0 0;
    }

    .kanban-col-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 12px;
        overflow-y: auto;
        flex: 1;
        scrollbar-width: thin;
        scrollbar-color: #CBD5E1 transparent;
    }

    .kanban-col-body::-webkit-scrollbar {
        width: 5px;
    }
    .kanban-col-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .kanban-col-body::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 99px;
    }
    .kanban-col-body::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Tablet: 2 kolom */
    @media (max-width: 1024px) {
        .kanban-board {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    /* Mobile: kolom di-scroll horizontal (gaya Trello/Jira) */
    @media (max-width: 640px) {
        .kanban-board {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding-bottom: 10px;
            -webkit-overflow-scrolling: touch;
        }
        .kanban-col {
            flex: 0 0 88%;
            scroll-snap-align: start;
            max-height: calc(100vh - 180px);
        }
    }

    /* ================= MODAL FORM GRID ================= */
    .modal-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 420px) {
        .modal-grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('tasksManager', function() {
            return {
                tasks: @json($tasks),
                projects: @json($projects),
                formProjects: @json($formProjects ?? $projects),
                engineers: @json($engineers),
                currentUserId: {{ (int) $currentUserId }},
                isLead: {{ $isLead ? 'true' : 'false' }},
                modalOpen: false,
                progressModalOpen: false,
                detailModalOpen: false,
                confirmOpen: false,
                confirmData: null,
                selectedTask: null,
                editing: false,
                filterProject: '',
                filterPriority: '',
                filterEngineer: '',
                engineerDropdownOpen: false,
                engineerSearch: '',
                columnPagination: {
                    'Assigned': 10,
                    'In Progress': 10,
                    'Waiting Review': 10,
                    'Completed': 10
                },
                form: {
                    id: null,
                    title: '',
                    project_id: null,
                    new_project_name: '',
                    engineer_id: null,
                    engineer_ids: [],
                    priority: 'Medium',
                    deadline: '',
                    deadline_time: '',
                    status: 'Assigned',
                    progress: 0,
                    description: ''
                },
                progressForm: {
                    taskId: null,
                    progress: 0,
                    notes: '',
                    fileName: '',
                    docFile: null
                },

                get modalProjects() {
                    var list = (this.formProjects && this.formProjects.length > 0) ? this.formProjects.slice() : this.projects.slice();
                    if (this.editing && this.form.project_id && !list.some(function(p){ return p.id === this.form.project_id; }.bind(this))) {
                        var currentP = this.projects.find(function(p){ return p.id === this.form.project_id; }.bind(this));
                        if (currentP) list.unshift(currentP);
                    }
                    return list;
                },

                init: function() {
                    console.log('Tasks Manager initialized!');
                },

                getEngineerName: function(id) {
                    var eng = this.engineers.find(function(e) { return e.id == id; });
                    return eng ? eng.name : 'Teknisi';
                },

                isEngineerSelected: function(id) {
                    if (!this.form.engineer_ids || !Array.isArray(this.form.engineer_ids)) return false;
                    var target = parseInt(id, 10);
                    return this.form.engineer_ids.some(function(i) { return parseInt(i, 10) === target; });
                },

                toggleEngineer: function(id) {
                    if (!this.form.engineer_ids || !Array.isArray(this.form.engineer_ids)) this.form.engineer_ids = [];
                    var target = parseInt(id, 10);
                    var idx = this.form.engineer_ids.findIndex(function(i) { return parseInt(i, 10) === target; });
                    if (idx === -1) {
                        this.form.engineer_ids.push(target);
                    } else {
                        this.form.engineer_ids.splice(idx, 1);
                    }
                    this.form.engineer_id = this.form.engineer_ids.length > 0 ? this.form.engineer_ids[0] : null;
                },

                openDetailModal: function(task) {
                    this.selectedTask = task;
                    this.detailModalOpen = true;
                },

                getFilteredTasksByStatus: function(status) {
                    var self = this;
                    return this.tasks.filter(function(t) {
                        var matchStatus = t.status === status;
                        var matchProject = self.filterProject === '' || t.project_id == self.filterProject;
                        var matchPriority = self.filterPriority === '' || t.priority === self.filterPriority;
                        var matchEngineer = self.filterEngineer === '' || 
                            t.engineer_id == self.filterEngineer || 
                            (t.engineers && t.engineers.some(function(e) { return e.id == self.filterEngineer; }));
                        return matchStatus && matchProject && matchPriority && matchEngineer;
                    });
                },

                getPaginatedTasks: function(status) {
                    var filtered = this.getFilteredTasksByStatus(status);
                    var limit = this.columnPagination[status] || 10;
                    return filtered.slice(0, limit);
                },

                loadMoreTasks: function(status) {
                    this.columnPagination[status] = (this.columnPagination[status] || 10) + 10;
                },

                openModal: function() {
                    this.editing = false;
                    this.engineerDropdownOpen = false;
                    this.engineerSearch = '';
                    var initialEngId = this.engineers[0]?.id || null;
                    this.form = {
                        id: null,
                        title: '',
                        project_id: this.projects[0]?.id || null,
                        new_project_name: '',
                        engineer_id: initialEngId,
                        engineer_ids: initialEngId ? [initialEngId] : [],
                        priority: 'Medium',
                        deadline: '',
                        deadline_time: '',
                        status: 'Assigned',
                        progress: 0,
                        description: ''
                    };
                    this.modalOpen = true;
                },

                editTask: function(task) {
                    this.editing = true;
                    this.engineerDropdownOpen = false;
                    this.engineerSearch = '';
                    var validIds = this.engineers.map(function(e) { return parseInt(e.id, 10); });
                    var ids = (task.engineers && task.engineers.length > 0)
                        ? task.engineers.map(function(e) { return parseInt(e.id, 10); }).filter(function(id) { return validIds.includes(id); })
                        : (task.engineer_id && validIds.includes(parseInt(task.engineer_id, 10)) ? [parseInt(task.engineer_id, 10)] : (validIds.length > 0 ? [validIds[0]] : []));

                    var dateStr = '';
                    var timeStr = task.deadline_time ? task.deadline_time.substring(0, 5) : '';
                    if (task.deadline) {
                        if (task.deadline.includes('T')) {
                            var parts = task.deadline.split('T');
                            dateStr = parts[0];
                            if (!timeStr && parts[1] && parts[1].substring(0, 5) !== '00:00') {
                                timeStr = parts[1].substring(0, 5);
                            }
                        } else if (task.deadline.includes(' ')) {
                            var parts = task.deadline.split(' ');
                            dateStr = parts[0];
                            if (!timeStr && parts[1] && parts[1].substring(0, 5) !== '00:00') {
                                timeStr = parts[1].substring(0, 5);
                            }
                        } else {
                            dateStr = task.deadline;
                        }
                    }

                    this.form = {
                        id: task.id,
                        title: task.title,
                        project_id: task.project_id,
                        new_project_name: '',
                        engineer_id: ids[0] || (validIds.length > 0 ? validIds[0] : null),
                        engineer_ids: ids,
                        priority: task.priority,
                        deadline: dateStr,
                        deadline_time: timeStr,
                        status: task.status,
                        original_status: task.status,
                        progress: task.progress || 0,
                        description: task.description || ''
                    };
                    this.modalOpen = true;
                },

                confirmDelete: function(task) {
                    this.confirmData = task;
                    this.confirmOpen = true;
                },

                openProgressModal: function(task) {
                    this.progressForm.taskId = task.id;
                    this.progressForm.progress = task.progress || 0;
                    this.progressForm.description = task.description || '';
                    this.progressForm.fileName = '';
                    this.progressForm.docFile = null;
                    // Reset file input
                    var inp = document.getElementById('doc_file_input');
                    if (inp) inp.value = '';
                    this.progressModalOpen = true;
                },

                handleFileChange: function(event) {
                    var file = event.target.files[0];
                    if (file) {
                        this.progressForm.docFile = file;
                        this.progressForm.fileName = file.name;
                    }
                },

                saveTask: async function() {
                    try {
                        if (this.form.project_id === 'other') {
                            if (!this.form.new_project_name || !this.form.new_project_name.trim()) {
                                this.showToast('Silakan masukkan nama project!');
                                return;
                            }
                        } else if (!this.form.project_id) {
                            this.showToast('Silakan pilih project!');
                            return;
                        }

                        if (!this.form.engineer_ids || this.form.engineer_ids.length === 0) {
                            this.showToast('Pilih minimal 1 orang teknisi / pendamping lapangan!');
                            return;
                        }
                        this.form.engineer_id = this.form.engineer_ids[0];

                        var url = this.editing ? '/tasks/' + this.form.id : '/tasks';
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

                            // Jika ada project baru dan belum ada di list projects lokal, tambahkan ke dropdown
                            if (data.project && !this.projects.some(function(p) { return p.id === data.project.id; })) {
                                this.projects.push(data.project);
                            }

                            if (this.editing) {
                                var index = this.tasks.findIndex(function(t) { return t.id === data.id; });
                                if (index !== -1) this.tasks[index] = data;
                            } else {
                                this.tasks.push(data);
                            }
                            this.columnPagination = {
                                'Assigned': 10,
                                'In Progress': 10,
                                'Waiting Review': 10,
                                'Completed': 10
                            };
                            this.modalOpen = false;
                            this.showToast(this.editing ? 'Task berhasil diperbarui!' : 'Task berhasil dibuat dan ditugaskan ke tim!');
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
                        console.error('Error saving task:', error);
                        this.showToast('Terjadi kesalahan saat menyimpan task.');
                    }
                },

                updateTask: async function(task) {
                    try {
                        var response = await fetch('/tasks/' + task.id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: task.status })
                        });

                        if (response.ok) {
                            var data = await response.json();
                            if (data) {
                                task.progress = data.progress;
                                task.status = data.status;
                            }
                            this.showToast('Task berhasil diperbarui!');
                        }
                    } catch (error) {
                        console.error('Error updating task:', error);
                        this.showToast('Terjadi kesalahan saat update task.');
                    }
                },

                startTask: async function(task) {
                    try {
                        var response = await fetch('/tasks/' + task.id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status: 'In Progress' })
                        });

                        if (response.ok) {
                            var data = await response.json();
                            var idx = this.tasks.findIndex(function(t) { return t.id === task.id; });
                            if (idx !== -1 && data) {
                                this.tasks[idx] = data;
                            }
                            this.showToast('Task dimulai! Status diubah ke In Progress.');
                        } else {
                            var err = await response.json();
                            this.showToast('Error: ' + (err.message || 'Gagal memulai task.'));
                        }
                    } catch (error) {
                        console.error('Error starting task:', error);
                        this.showToast('Terjadi kesalahan saat memulai task.');
                    }
                },

                quickCompleteTask: async function(task) {
                    if (!task || !task.id) return;
                    try {
                        var response = await fetch('/tasks/' + task.id, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ 
                                status: 'Completed', 
                                progress: 100 
                            })
                        });

                        if (response.ok) {
                            var data = await response.json();
                            var idx = this.tasks.findIndex(function(t) { return t.id === task.id; });
                            if (idx !== -1 && data) {
                                this.tasks[idx] = data;
                            } else {
                                task.status = 'Completed';
                                task.progress = 100;
                            }
                            if (this.selectedTask && this.selectedTask.id === task.id) {
                                this.selectedTask.status = 'Completed';
                                this.selectedTask.progress = 100;
                            }
                            this.showToast('Task "' + task.title + '" berhasil diselesaikan (100% Completed)!');
                        } else {
                            var err = await response.json();
                            this.showToast('Error: ' + (err.message || 'Gagal menyelesaikan task.'));
                        }
                    } catch (error) {
                        console.error('Error completing task:', error);
                        this.showToast('Terjadi kesalahan saat menyelesaikan task.');
                    }
                },

                deleteTask: async function() {
                    if (!this.confirmData) return;
                    try {
                        var response = await fetch('/tasks/' + this.confirmData.id, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.tasks = this.tasks.filter(function(t) { return t.id !== this.confirmData.id; }.bind(this));
                            this.showToast('Task berhasil dihapus!');
                        } else {
                            this.showToast('Gagal menghapus task.');
                        }
                    } catch (error) {
                        console.error('Error deleting task:', error);
                        this.showToast('Terjadi kesalahan saat menghapus task.');
                    } finally {
                        this.confirmOpen = false;
                        this.confirmData = null;
                    }
                },

                saveProgress: async function() {
                    try {
                        var formData = new FormData();
                        formData.append('progress', this.progressForm.progress);
                        if (this.progressForm.description) {
                            formData.append('description', this.progressForm.description);
                        }
                        formData.append('_method', 'PUT'); // method spoofing
                        if (this.progressForm.docFile) {
                            formData.append('doc_file', this.progressForm.docFile);
                        }

                        var response = await fetch('/tasks/' + this.progressForm.taskId, {
                            method: 'POST', // POST + _method=PUT untuk multipart
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        if (response.ok) {
                            var data = await response.json();
                            var task = this.tasks.find(function(t) {
                                return t.id === this.progressForm.taskId;
                            }.bind(this));
                            if (task && data) {
                                task.progress = data.progress !== undefined ? data.progress : parseInt(this.progressForm.progress);
                                task.doc_file = data.doc_file !== undefined ? data.doc_file : task.doc_file;
                                task.description = data.description !== undefined ? data.description : task.description;
                                task.status = data.status !== undefined ? data.status : task.status;
                                task.attachments = data.attachments !== undefined ? data.attachments : task.attachments;
                            }
                            this.progressModalOpen = false;
                            var msg = this.progressForm.docFile
                                ? 'Progress & dokumentasi berhasil disimpan!'
                                : 'Progress berhasil diperbarui!';
                            this.showToast(msg);
                        } else {
                            var err = await response.json();
                            this.showToast('Error: ' + (err.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error saving progress:', error);
                        this.showToast('Terjadi kesalahan saat update progress.');
                    }
                },

                getPriorityFlag: function(level) {
                    var colors = {
                        'High': '#DC2626',
                        'Medium': '#D97706',
                        'Low': '#64748B'
                    };
                    var color = colors[level] || '#64748B';
                    var rail = level === 'High' ? '<span style="display:inline-block; width:12px; height:5px; border-radius:2px; background:linear-gradient(135deg, #EF4444, #8F0A0D); margin-left:2px;"></span>' : '';
                    return '<span style="display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:800; color:' + color + '; text-transform:uppercase; letter-spacing:0.4px;">' +
                                '<svg style="width:12px; height:12px;" viewBox="0 0 24 24" fill="' + color + '" stroke="' + color + '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                                    '<path d="M4 21V3M4 7l16-3v12l-16 3Z"/>' +
                                '</svg>' +
                                level +
                                rail +
                            '</span>';
                },

                formatDeadline: function(deadline, deadlineTime) {
                    if (!deadline) return '';
                    var datePart = '';
                    var timePart = '';

                    if (deadline.includes('T')) {
                        var parts = deadline.split('T');
                        datePart = parts[0];
                        if (!deadlineTime && parts[1] && parts[1].substring(0, 5) !== '00:00') {
                            timePart = parts[1].substring(0, 5);
                        }
                    } else if (deadline.includes(' ')) {
                        var parts = deadline.split(' ');
                        datePart = parts[0];
                        if (!deadlineTime && parts[1] && parts[1].substring(0, 5) !== '00:00') {
                            timePart = parts[1].substring(0, 5);
                        }
                    } else {
                        datePart = deadline;
                    }

                    if (deadlineTime) {
                        timePart = deadlineTime.substring(0, 5);
                    }

                    var d = new Date(datePart + 'T00:00:00');
                    if (isNaN(d.getTime())) return deadline;
                    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    var formattedDate = String(d.getDate()).padStart(2,'0') + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();

                    if (timePart && timePart !== '00:00') {
                        return formattedDate + ' • ' + timePart;
                    }
                    return formattedDate;
                },

                showToast: function(message) {
                    var toast = document.createElement('div');
                    toast.style.cssText = 'position:fixed; bottom:16px; right:16px; background:#17151C; color:white; padding:12px 24px; border-radius:8px; box-shadow:0 16px 40px rgba(14,13,18,0.12); font-size:14px; animation:fadeInUp 0.18s ease; z-index:999999;';
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