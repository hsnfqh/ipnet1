@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        transition: all 0.15s ease;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="projectDetailPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 1. BREADCRUMBS (Matching Screenshot 5) --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-gray-800">Project</a>
                <span>&gt;</span>
                <span class="text-gray-900 font-bold">{{ $project->name }}</span>
            </div>

            {{-- 2. TOP STATUS BAR (Matching Screenshot 5) --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Status Badge (Draft ▶) --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-[#A2623D] text-white shadow-xs">
                        <span>{{ $project->status ?: 'Draft' }}</span>
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>

                    {{-- Team Badge + Button --}}
                    <div class="flex items-center gap-1.5">
                        <div class="text-[10.5px] font-bold text-gray-400">Team</div>
                        <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                            {{ $project->division ? $project->division->name : 'IPNET 01' }}
                        </span>
                        <button type="button" @click="isAssignModalOpen = true" 
                                class="w-6 h-6 rounded-full border border-red-300 text-red-600 flex items-center justify-center font-bold text-xs hover:bg-red-50 transition cursor-pointer" title="Assign Team / User">
                            +
                        </button>
                    </div>
                </div>

                <div class="text-xs text-gray-400 font-medium">
                    Created by <span class="font-bold text-gray-700">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Nabylla Berlianita') }}</span>, 
                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                </div>
            </div>

            {{-- 3. TITLE & ACTIONS ROW --}}
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h1>
                    <p class="text-xs text-gray-500 mt-1">{{ $project->description ?: $project->name }}</p>
                </div>

                {{-- Pending & Delete Action Buttons --}}
                <div class="flex items-center gap-3 shrink-0">
                    <form action="{{ route('sales.pipeline.update', $project->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="sales_stage" value="Qualification">
                        <input type="hidden" name="win_probability" value="25">
                        <button type="button" @click="toggleStatus('Pending')" class="btn-action-pill text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pending</span>
                        </button>
                    </form>

                    <button type="button" @click="confirmDeleteProject()" class="btn-action-pill text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete</span>
                    </button>
                </div>
            </div>

            {{-- 4. PROJECT ESTIMATION & META BANNER (Matching Screenshot 5) --}}
            <div class="bg-[#F6EBE4] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-[#E9D5C9]">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 flex-1">
                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT ESTIMATION</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">
                            Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT START</div>
                        <div class="text-sm font-bold text-gray-900 mt-1.5">
                            {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '24 Aug 2026' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT END</div>
                        <div class="text-sm font-bold text-gray-900 mt-1.5">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="shrink-0">
                    <button type="button" @click="isEditMetaModalOpen = true" class="p-2 rounded-lg bg-white/70 hover:bg-white text-gray-700 shadow-2xs transition">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                </div>
            </div>

            {{-- 5. 2-COLUMN MAIN CONTENT (Matching Screenshot 5) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT COLUMN (2 spans) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Client Info --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-900">Client Info</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CLIENT NAME</div>
                                <div class="font-bold text-gray-900 mt-1">{{ $project->client ?: 'Sarana Kreasi Teknoart' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CLIENT EMAIL</div>
                                <div class="font-bold text-gray-900 mt-1 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: 'sales@saranateknoart.com') }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CONTACT PIC</div>
                                <div class="font-bold text-gray-900 mt-1">{{ $project->customer_pic_business ?: '081299771333' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Milestone Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Milestone</h3>
                            <div class="text-xs text-gray-400 font-semibold">
                                Complete ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }})
                            </div>
                        </div>

                        <div class="space-y-2">
                            @forelse($project->tasks as $task)
                                <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <input type="checkbox" {{ $task->status === 'Completed' ? 'checked' : '' }} disabled class="rounded text-red-600">
                                        <span class="font-bold text-gray-800">{{ $task->name }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white border border-gray-200 text-gray-600">
                                        {{ $task->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-4 text-xs text-gray-400">Belum ada milestone yang terdaftar.</div>
                            @endforelse
                        </div>

                        <div>
                            <button type="button" @click="isAddMilestoneModalOpen = true" class="text-xs font-bold text-red-600 hover:text-red-800 flex items-center gap-1 cursor-pointer">
                                <span>+ ADD MILESTONE</span>
                            </button>
                        </div>
                    </div>

                    {{-- Attachments Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-900">Attachments</h3>
                        
                        <div class="space-y-2">
                            @if($project->relationLoaded('projectDocuments') && $project->projectDocuments->count() > 0)
                                @foreach($project->projectDocuments as $doc)
                                    <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span class="font-bold text-gray-800">{{ $doc->document_title ?? ($doc->document_name ?? 'Document') }}</span>
                                        </div>
                                        @if($doc->file_path)
                                            <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="text-red-600 font-bold hover:underline">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="py-2 text-xs text-gray-400">Belum ada berkas lampiran.</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 flex-wrap pt-2">
                            <button type="button" @click="isUploadDocModalOpen = true" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + ADD ATTACHMENT
                            </button>
                            <button type="button" @click="isUploadDocModalOpen = true" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + ADD COGS ATTACHMENT
                            </button>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN (1 span) --}}
                <div class="space-y-6">
                    
                    {{-- Project Status --}}
                    <div class="ipnet-card p-6 space-y-3">
                        <h3 class="text-sm font-bold text-gray-900">Project Status</h3>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2.5 py-1 rounded bg-[#F6EBE4] text-[#A2623D] font-bold text-[11px]">
                                {{ $project->status ?: 'Draft' }}
                            </span>
                            <span class="text-gray-400 font-medium">
                                Progress {{ $project->progress ?: 0 }}% (0/0 complete)
                            </span>
                        </div>
                    </div>

                    {{-- Project Activities --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-900">Project Activities</h3>
                        
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100 space-y-1">
                                <div class="font-bold text-gray-800">
                                    {{ $project->creator ? $project->creator->name : 'Nabylla Berlianita' }} created this project
                                </div>
                                <div class="text-[10.5px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ASSIGN MODAL (Matching Screenshot 5) --}}
    <div x-show="isAssignModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isAssignModalOpen = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">Assign</h3>
                <button type="button" @click="isAssignModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="name" value="Penugasan Proyek {{ $project->name }}">
                <input type="hidden" name="start_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" name="deadline" value="{{ date('Y-m-d', strtotime('+7 days')) }}">

                <div>
                    <label class="block text-gray-700 mb-1">USER</label>
                    <select name="engineer_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
                        <option value="">Select a user</option>
                        @php
                            $allUsers = \App\Models\User::orderBy('name')->get();
                        @endphp
                        @foreach($allUsers as $u)
                            @php
                                $prefix = $u->hasAnyRole(['Director', 'Direktur', 'Division Head', 'Group Leader']) ? '(Head)' : ($u->hasAnyRole(['Sales', 'Account Manager']) ? '(Sales)' : '(Engineer)');
                            @endphp
                            <option value="{{ $u->id }}">{{ $prefix }} {{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAssignModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-red-600 text-white shadow-md hover:bg-red-700">
                        Assign User
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE PROJECT FORM --}}
    <form id="deleteProjForm" action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>

<script>
    function projectDetailPage() {
        return {
            isAssignModalOpen: false,
            isEditMetaModalOpen: false,
            isAddMilestoneModalOpen: false,
            isUploadDocModalOpen: false,

            confirmDeleteProject() {
                if (confirm('Apakah Anda yakin ingin menghapus project ini?')) {
                    document.getElementById('deleteProjForm').submit();
                }
            }
        };
    }
</script>
@endsection
