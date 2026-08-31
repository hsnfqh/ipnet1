@extends('layouts.app')

@section('title', 'Task Management')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Task Management'])

        <div class="p-4 sm:p-[26px] animate-fade-in"
             x-data="tasksManager()"
             x-init="init()"
             data-current-user-id="{{ $currentUserId }}"
             data-is-lead="{{ $isLead ? 'true' : 'false' }}">

            <!-- ========================================================== -->
            <!-- HEADER - FILTER (WRAP DI LAYAR SEMPIT) -->
            <!-- ========================================================== -->
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <p style="font-size:13px; font-weight:600; color:#3D3A44; margin:0; white-space:nowrap;">
                        Total: <span x-text="tasks.length" style="color:#C81E2C;"></span> task
                    </p>

                    <div class="hidden sm:block" style="width:1px; height:24px; background:#E7E5E3;"></div>

                    <select x-model="filterProject" class="filter-select"
                            onfocus="this.style.borderColor='#C81E2C'" onblur="this.style.borderColor='#E7E5E3'">
                        <option value="">Semua Project</option>
                        <template x-for="project in projects" :key="project.id">
                            <option :value="project.id" x-text="project.name"></option>
                        </template>
                    </select>

                    <select x-model="filterPriority" class="filter-select"
                            onfocus="this.style.borderColor='#C81E2C'" onblur="this.style.borderColor='#E7E5E3'">
                        <option value="">Semua Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>

                    @if($isLead)
                    <select x-model="filterEngineer" class="filter-select"
                            onfocus="this.style.borderColor='#C81E2C'" onblur="this.style.borderColor='#E7E5E3'">
                        <option value="">Semua Engineer</option>
                        <template x-for="engineer in engineers" :key="engineer.id">
                            <option :value="engineer.id" x-text="engineer.name"></option>
                        </template>
                    </select>
                    @endif
                </div>

                @if($canManage)
                <button @click="openModal()"
                        class="w-full sm:w-auto"
                        style="background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:9px 18px; border-radius:8px; border:none; font-weight:600; font-size:13px; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; transition:all 0.15s ease;"
                        onmouseover="this.style.filter='brightness(1.05)'"
                        onmouseout="this.style.filter='brightness(1)'">
                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat & Assign Task
                </button>
                @endif
            </div>

            <!-- ========================================================== -->
            <!-- KANBAN BOARD - RESPONSIVE (4 kolom desktop, 2 kolom tablet, scroll-snap di HP) -->
            <!-- ========================================================== -->
            <div class="kanban-board">
                <template x-for="column in ['Assigned', 'In Progress', 'Waiting Review', 'Completed']" :key="column">
                    <div class="kanban-col">
                        <div class="kanban-col-head">
                            <span style="font-size:12px; font-weight:700; color:#3D3A44; text-transform:uppercase; letter-spacing:0.5px;" x-text="column"></span>
                            <span style="font-size:11px; background:#F1F0EE; color:#75727C; padding:1px 8px; border-radius:12px; font-weight:600;"
                                  x-text="getFilteredTasksByStatus(column).length"></span>
                        </div>
                        <div class="kanban-col-body">
                            <template x-for="task in getFilteredTasksByStatus(column)" :key="task.id">
                                <div style="background:white; border:1px solid #E7E5E3; border-radius:12px; padding:14px; box-shadow:0 1px 2px rgba(14,13,18,0.05); transition:all 0.15s ease;"
                                     onmouseenter="this.style.boxShadow='0 4px 16px rgba(14,13,18,0.06)'"
                                     onmouseleave="this.style.boxShadow='0 1px 2px rgba(14,13,18,0.05)'">

                                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; gap:8px;">
                                        <span x-html="getPriorityFlag(task.priority)"></span>
                                    </div>

                                    <div style="font-size:13px; font-weight:600; color:#17151C; margin-bottom:4px; word-break:break-word;" x-text="task.title"></div>
                                    <div style="font-size:11.5px; color:#75727C; margin-bottom:10px; word-break:break-word;" x-text="task.project?.name"></div>

                                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; gap:8px; flex-wrap:wrap;">
                                        <div style="display:flex; align-items:center; gap:6px; min-width:0;">
                                            <!-- Stacked avatars jika ditugaskan ke tim (multi-assignee) -->
                                            <div style="display:flex; align-items:center; margin-right:2px;">
                                                <template x-for="(eng, idx) in (task.engineers && task.engineers.length > 0 ? task.engineers.slice(0, 2) : (task.engineer ? [task.engineer] : []))" :key="eng.id">
                                                    <div style="width:22px; height:22px; border-radius:50%; color:white; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; flex-shrink:0; border:1.5px solid white;"
                                                         :style="{ background: idx === 0 ? '#C81E2C' : '#2563EB', marginLeft: idx > 0 ? '-6px' : '0' }"
                                                         :title="eng.name + (idx === 0 ? ' (PIC Utama)' : ' (Pendamping Lapangan)')">
                                                        <span x-text="eng.name ? eng.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?'"></span>
                                                    </div>
                                                </template>
                                                <template x-if="task.engineers && task.engineers.length > 2">
                                                    <div style="width:20px; height:20px; border-radius:50%; background:#75727C; color:white; display:flex; align-items:center; justify-content:center; font-size:8.5px; font-weight:700; flex-shrink:0; border:1.5px solid white; margin-left:-6px;"
                                                         :title="task.engineers.map(e => e.name).join(', ')">
                                                        <span x-text="'+' + (task.engineers.length - 2)"></span>
                                                    </div>
                                                </template>
                                            </div>

                                            <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:11px; color:#3D3A44; font-weight:500;">
                                                <span x-text="task.engineer?.name || 'Belum diassign'"></span>
                                                <template x-if="task.engineers && task.engineers.length > 1">
                                                    <span style="font-size:9.5px; font-weight:700; color:#1D4ED8; background:#EFF6FF; padding:1px 5px; border-radius:8px; margin-left:3px;"
                                                          x-text="'+' + (task.engineers.length - 1) + ' tim'"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <span style="font-size:10px; font-family:'IBM Plex Mono',monospace; color:#75727C; white-space:nowrap;" x-text="formatDeadline(task.deadline)"></span>
                                    </div>

                                    <div style="width:100%; background:#EFEDEB; border-radius:20px; height:5px; overflow:hidden;">
                                        <div style="height:100%; border-radius:20px; background:linear-gradient(90deg, #AF1424, #D62E3C); transition:width .3s ease;" :style="{ width: (task.progress || 0) + '%' }"></div>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px; font-size:10.5px; color:#75727C;">
                                        <span>Progress: <strong style="color:#C81E2C;" x-text="(task.progress || 0) + '%'"></strong></span>
                                        <template x-if="task.doc_file || task.attachments > 0">
                                            <span style="display:inline-flex; align-items:center; gap:3px; color:#1B7A46; font-weight:600; cursor:pointer;" @click="openDetailModal(task)">
                                                <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                Foto/Dokumen
                                            </span>
                                        </template>
                                    </div>

                                    @if($canManage)
                                    <div style="display:flex; gap:6px; margin-top:10px;">
                                        <button @click="openDetailModal(task)"
                                                style="flex:1; padding:6px 0; border-radius:6px; border:1px solid #E7E5E3; background:#F8F7F6; display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer; transition:all 0.15s ease; font-size:11px; font-weight:600; color:#3D3A44;"
                                                onmouseover="this.style.background='#EFEDEB'" onmouseout="this.style.background='#F8F7F6'">
                                            <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </button>
                                        <button @click="editTask(task)"
                                                style="flex:1; padding:6px 0; border-radius:6px; border:1px solid #E7E5E3; background:white; display:flex; align-items:center; justify-content:center; gap:4px; cursor:pointer; transition:all 0.15s ease; font-size:11px; font-weight:600; color:#3D3A44;"
                                                onmouseover="this.style.background='#F1F0EE'" onmouseout="this.style.background='white'">
                                            <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <button @click="confirmDelete(task)"
                                                style="width:32px; flex-shrink:0; padding:6px 0; border-radius:6px; border:1px solid #E7E5E3; background:white; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all 0.15s ease; color:#C81E2C;"
                                                onmouseover="this.style.background='#FDF1F2'" onmouseout="this.style.background='white'">
                                            <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @else
                                    <div style="margin-top:10px;">
                                        <button @click="openDetailModal(task)"
                                                style="width:100%; padding:7px 0; border-radius:6px; border:1px solid #E7E5E3; background:#F8F7F6; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; transition:all 0.15s ease; font-size:12px; font-weight:600; color:#3D3A44;"
                                                onmouseover="this.style.background='#EFEDEB'" onmouseout="this.style.background='#F8F7F6'">
                                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Lihat Detail Task
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </template>

                            <div x-show="getFilteredTasksByStatus(column).length === 0"
                                 style="font-size:12px; color:#B7B3BB; padding:20px 4px; text-align:center;">
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
                            <h3 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:600; color:#17151C;" x-text="editing ? 'Edit Task' : 'Buat & Assign Task'"></h3>
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
                                            <template x-for="project in projects" :key="project.id">
                                                <option :value="project.id" x-text="project.name"></option>
                                            </template>
                                        </select>
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
                                                     :style="{ background: form.engineer_ids && form.engineer_ids.includes(engineer.id) ? '#FDF1F2' : 'transparent' }">
                                                    <div style="display:flex; align-items:center; gap:8px;">
                                                        <input type="checkbox" :checked="form.engineer_ids && form.engineer_ids.includes(engineer.id)" style="accent-color:#C81E2C; cursor:pointer;" @click.stop="toggleEngineer(engineer.id)">
                                                        <div>
                                                            <div style="font-weight:600; color:#17151C;" x-text="engineer.name"></div>
                                                            <div style="font-size:10.5px; color:#75727C;" x-text="engineer.position || engineer.role"></div>
                                                        </div>
                                                    </div>
                                                    <template x-if="form.engineer_ids && form.engineer_ids.includes(engineer.id)">
                                                        <span style="font-size:9.5px; font-weight:700; padding:1px 6px; border-radius:10px;" 
                                                              :style="{ background: form.engineer_ids.indexOf(engineer.id) === 0 ? '#FDF1F2' : '#EFF6FF', color: form.engineer_ids.indexOf(engineer.id) === 0 ? '#C81E2C' : '#1D4ED8' }"
                                                              x-text="form.engineer_ids.indexOf(engineer.id) === 0 ? 'PIC Utama' : 'Pendamping'"></span>
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
                                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Deadline</label>
                                            <input type="date" x-model="form.deadline"
                                                   style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; transition:border 0.15s ease; box-sizing:border-box;"
                                                   required>
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
                                            style="flex:1 1 140px; justify-content:center; background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:10px 17px; border-radius:8px; border:none; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
                                            onmouseover="this.style.filter='brightness(1.05)'"
                                            onmouseout="this.style.filter='brightness(1)'">
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
                            <h3 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:600; color:#17151C;">Update Progress</h3>
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
                                <p x-show="progressForm.fileName" style="font-size:10.5px; color:#1B7A46; margin-top:4px; margin-bottom:0;">✓ File siap diupload</p>
                            </div>
                            <div style="margin-bottom:16px;">
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.3px;">Keterangan / Hasil Pekerjaan</label>
                                <textarea x-model="progressForm.description"
                                          style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white; min-height:70px; transition:border 0.15s ease; box-sizing:border-box;"
                                          placeholder="Tuliskan keterangan update progress..."></textarea>
                            </div>
                            
                            <div style="display:flex; gap:10px; margin-top:16px; flex-wrap:wrap;">
                                <button @click="saveProgress()"
                                        style="flex:1 1 140px; justify-content:center; background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:10px 17px; border-radius:8px; border:none; font-weight:600; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:7px; transition:all 0.15s ease;"
                                        onmouseover="this.style.filter='brightness(1.05)'"
                                        onmouseout="this.style.filter='brightness(1)'">
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
                     style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:999999; display:flex; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px);"
                     @click.self="confirmOpen = false">

                    <div style="background:white; border-radius:16px; width:420px; max-width:100%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(14,13,18,0.2); margin:auto; animation:fadeInUp 0.2s ease;">
                        <div style="padding:24px;">
                            <div style="display:flex; justify-content:center; margin-bottom:16px;">
                                <div style="width:56px; height:56px; border-radius:50%; background:#FEF2F2; display:flex; align-items:center; justify-content:center;">
                                    <svg style="width:28px; height:28px; color:#C81E2C;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 style="margin:0 0 8px; text-align:center; font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; color:#17151C;">Yakin hapus task?</h3>
                            <p style="text-align:center; font-size:14px; color:#75727C; margin:0 0 24px; word-break:break-word;">
                                Task "<span x-text="confirmData?.title" style="font-weight:600; color:#17151C;"></span>" akan dihapus permanen.
                            </p>
                            <div style="display:flex; flex-direction:column; gap:10px;">
                                <button @click="deleteTask()" style="padding:10px 16px; border-radius:8px; background:#C81E2C; color:white; font-weight:600; font-size:14px; border:none; cursor:pointer;">
                                    Yakin, Hapus
                                </button>
                                <button @click="confirmOpen = false" style="padding:10px 16px; border-radius:8px; background:white; color:#3D3A44; border:1px solid #E7E5E3; font-weight:600; font-size:14px; cursor:pointer;">
                                    Batal
                                </button>
                            </div>
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
                            <h3 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:16px; font-weight:600; color:#17151C;">Detail & Dokumentasi Task</h3>
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
                                    <span style="color:#75727C; font-weight:500;">Deadline:</span>
                                    <strong style="color:#17151C; display:block;" x-text="formatDeadline(selectedTask?.deadline)"></strong>
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
        background: #F8F7F6;
        border-radius: 12px;
        border: 1px solid #EFEDEB;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 200px);
        min-height: 200px;
        overflow: hidden;
    }

    .kanban-col-head {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 12px 10px;
        flex-shrink: 0;
        background: #F8F7F6;
        border-bottom: 1px solid #EFEDEB;
        border-radius: 12px 12px 0 0;
    }

    .kanban-col-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px 10px 12px;
        overflow-y: auto;
        flex: 1;
        scrollbar-width: thin;
        scrollbar-color: #E7E5E3 transparent;
    }

    .kanban-col-body::-webkit-scrollbar {
        width: 4px;
    }
    .kanban-col-body::-webkit-scrollbar-track {
        background: transparent;
    }
    .kanban-col-body::-webkit-scrollbar-thumb {
        background: #E7E5E3;
        border-radius: 99px;
    }
    .kanban-col-body::-webkit-scrollbar-thumb:hover {
        background: #C7C4CD;
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
                engineers: @json($engineers),
                currentUserId: parseInt(document.querySelector('[data-current-user-id]')?.dataset?.currentUserId || '0'),
                isLead: document.querySelector('[data-is-lead]')?.dataset?.isLead === 'true',
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
                    engineer_id: null,
                    engineer_ids: [],
                    priority: 'Medium',
                    deadline: '',
                    status: 'Assigned',
                    progress: 0,
                    description: ''
                },
                progressForm: {
                    taskId: null,
                    progress: 0,
                    fileName: '',
                    docFile: null
                },

                init: function() {
                    console.log('Tasks Manager initialized!');
                },

                getEngineerName: function(id) {
                    var eng = this.engineers.find(function(e) { return e.id == id; });
                    return eng ? eng.name : 'Teknisi';
                },

                toggleEngineer: function(id) {
                    if (!this.form.engineer_ids) this.form.engineer_ids = [];
                    var idx = this.form.engineer_ids.indexOf(id);
                    if (idx === -1) {
                        this.form.engineer_ids.push(id);
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
                        engineer_id: initialEngId,
                        engineer_ids: initialEngId ? [initialEngId] : [],
                        priority: 'Medium',
                        deadline: '',
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
                    var validIds = this.engineers.map(function(e) { return e.id; });
                    var ids = (task.engineers && task.engineers.length > 0)
                        ? task.engineers.map(function(e) { return e.id; }).filter(function(id) { return validIds.includes(id); })
                        : (validIds.includes(task.engineer_id) ? [task.engineer_id] : (validIds.length > 0 ? [validIds[0]] : []));
                    this.form = {
                        id: task.id,
                        title: task.title,
                        project_id: task.project_id,
                        engineer_id: ids[0] || (validIds.length > 0 ? validIds[0] : null),
                        engineer_ids: ids,
                        priority: task.priority,
                        deadline: task.deadline ? task.deadline.split('T')[0] : '',
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
                            this.showToast('Error: ' + (error.message || 'Terjadi kesalahan'));
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
                        'High': '#C81E2C',
                        'Medium': '#9A6206',
                        'Low': '#75727C'
                    };
                    var color = colors[level] || '#75727C';
                    var rail = level === 'High' ? '<span style="display:inline-block; width:14px; height:6px; border-radius:2px; background-image:linear-gradient(135deg, #E14B54 0%, #AF1424 55%, #5C0A13 100%); margin-left:2px;"></span>' : '';
                    return '<span style="display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:700; color:' + color + '; text-transform:uppercase; letter-spacing:0.3px;">' +
                                '<svg style="width:12px; height:12px;" viewBox="0 0 24 24" fill="' + color + '" stroke="' + color + '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                                    '<path d="M4 21V3M4 7l16-3v12l-16 3Z"/>' +
                                '</svg>' +
                                level +
                                rail +
                            '</span>';
                },

                formatDeadline: function(deadline) {
                    if (!deadline) return '';
                    var d = new Date(deadline);
                    if (isNaN(d.getTime())) return deadline;
                    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                    return String(d.getUTCDate()).padStart(2,'0') + ' ' + months[d.getUTCMonth()] + ' ' + d.getUTCFullYear();
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