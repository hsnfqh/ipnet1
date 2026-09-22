@extends('layouts.app')

@section('title', 'Project Management - PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .btn-ipnet-primary {
        background: #DC2626;
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .btn-ipnet-primary:hover {
        background: #B91C1C;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        transform: translateY(-1px);
    }

    .btn-ipnet-green {
        background: #10B981;
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
    }

    .btn-ipnet-green:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transform: translateY(-1px);
    }

    /* Custom Kanban Scrollbar */
    .kanban-scroll::-webkit-scrollbar {
        height: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="projectKanbanPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-full mx-auto">
            
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

            {{-- HEADER: Title with Icon matching reference --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 border border-red-200 text-[#8F0A0D] flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-[#1E293B] tracking-tight">Project</h1>
            </div>

            {{-- CONTROLS & FILTER BAR (Matching Reference Image) --}}
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                {{-- Left Filter Controls --}}
                <form method="GET" action="{{ route('sales.pipeline.index') }}" class="flex flex-wrap items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-64">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Search" 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all shadow-xs">
                    </div>

                    {{-- Select Division Dropdown --}}
                    <select name="division_id" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all cursor-pointer shadow-xs">
                        <option value="">Select division</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $filterDivision == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>

                    {{-- Select Status Approval Dropdown --}}
                    <select name="approval_status" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all cursor-pointer shadow-xs">
                        <option value="">Select status approval</option>
                        <option value="Draft" {{ $filterApproval == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Submitted" {{ $filterApproval == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="Approved" {{ $filterApproval == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Pending" {{ $filterApproval == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ $filterApproval == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    @if($search || $filterDivision || $filterApproval)
                        <a href="{{ route('sales.pipeline.index') }}" class="px-2 py-1 text-xs font-bold text-gray-400 hover:text-red-600 transition">
                            ✕ Reset
                        </a>
                    @endif
                </form>

                {{-- Right Action Buttons --}}
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" 
                            @click="openAddProjectModal('Opportunity')"
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold shadow-md cursor-pointer whitespace-nowrap">
                        <span class="text-base leading-none font-bold">+</span>
                        <span>Add new project</span>
                    </button>

                    <button type="button" 
                            @click="openAddProjectModal('Completed')"
                            class="btn-ipnet-green inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold shadow-md cursor-pointer whitespace-nowrap">
                        <span class="text-base leading-none font-bold">+</span>
                        <span>Add complete project</span>
                    </button>
                </div>
            </div>

            {{-- KANBAN BOARD CONTAINER (Fixed Horizontal Scroll, Never Cut Off) --}}
            <div class="kanban-scroll overflow-x-auto pb-6 pt-2">
                <div class="flex items-start gap-5 min-w-[1350px] w-full">
                    
                    {{-- 1. DRAFT COLUMN --}}
                    <div class="flex-1 min-w-[250px] max-w-[320px] bg-[#F1F5F9]/60 rounded-2xl p-3.5 border border-slate-200/80 flex flex-col min-h-[550px]">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <h3 class="text-sm font-bold text-slate-800">Draft</h3>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                {{ $kanban['draft']->count() }}
                            </span>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            @forelse($kanban['draft'] as $p)
                                @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Draft'])
                            @empty
                                <div class="py-12 text-center text-slate-400 text-xs font-medium border-2 border-dashed border-slate-200 rounded-xl">
                                    Belum ada project draft
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 2. OPPORTUNITY COLUMN --}}
                    <div class="flex-1 min-w-[250px] max-w-[320px] bg-blue-50/40 rounded-2xl p-3.5 border border-blue-200/80 flex flex-col min-h-[550px]">
                        <div class="flex items-center justify-between pb-3 border-b border-blue-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                <h3 class="text-sm font-bold text-blue-900">Opportunity</h3>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                {{ $kanban['opportunity']->count() }}
                            </span>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            @forelse($kanban['opportunity'] as $p)
                                @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Opportunity'])
                            @empty
                                <div class="py-12 text-center text-blue-300 text-xs font-medium border-2 border-dashed border-blue-200 rounded-xl">
                                    Belum ada opportunity
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. IN PROGRESS COLUMN --}}
                    <div class="flex-1 min-w-[250px] max-w-[320px] bg-amber-50/40 rounded-2xl p-3.5 border border-amber-200/80 flex flex-col min-h-[550px]">
                        <div class="flex items-center justify-between pb-3 border-b border-amber-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <h3 class="text-sm font-bold text-amber-900">In Progress</h3>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                {{ $kanban['in_progress']->count() }}
                            </span>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            @forelse($kanban['in_progress'] as $p)
                                @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'In Progress'])
                            @empty
                                <div class="py-12 text-center text-amber-300 text-xs font-medium border-2 border-dashed border-amber-200 rounded-xl">
                                    Belum ada project in progress
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 4. PENDING COLUMN --}}
                    <div class="flex-1 min-w-[250px] max-w-[320px] bg-purple-50/40 rounded-2xl p-3.5 border border-purple-200/80 flex flex-col min-h-[550px]">
                        <div class="flex items-center justify-between pb-3 border-b border-purple-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                <h3 class="text-sm font-bold text-purple-900">Pending</h3>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-purple-100 text-purple-800">
                                {{ $kanban['pending']->count() }}
                            </span>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            @forelse($kanban['pending'] as $p)
                                @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Pending'])
                            @empty
                                <div class="py-12 text-center text-purple-300 text-xs font-medium border-2 border-dashed border-purple-200 rounded-xl">
                                    Belum ada project pending
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 5. COMPLETED COLUMN (Far right, fully visible and nicely framed) --}}
                    <div class="flex-1 min-w-[250px] max-w-[320px] bg-emerald-50/40 rounded-2xl p-3.5 border border-emerald-200/80 flex flex-col min-h-[550px]">
                        <div class="flex items-center justify-between pb-3 border-b border-emerald-200 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <h3 class="text-sm font-bold text-emerald-900">Completed</h3>
                            </div>
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                {{ $kanban['completed']->count() }}
                            </span>
                        </div>

                        <div class="space-y-3 flex-1 overflow-y-auto pr-1">
                            @forelse($kanban['completed'] as $p)
                                @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Completed'])
                            @empty
                                <div class="py-12 text-center text-emerald-300 text-xs font-medium border-2 border-dashed border-emerald-200 rounded-xl">
                                    Belum ada project completed
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- MODAL: + ADD NEW PROJECT / + ADD COMPLETE PROJECT --}}
    <div x-show="isAddModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isAddModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900" x-text="modalTitle"></h3>
                <button type="button" @click="isAddModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('sales.pipeline.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="status" :value="formStatus">
                <input type="hidden" name="is_completed" :value="formStatus === 'Completed' ? '1' : '0'">

                <div>
                    <label class="block text-gray-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Pengadaan Firewall & Switch Datacenter"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Nama Client <span class="text-red-500">*</span></label>
                    <input type="text" name="client" required placeholder="Contoh: PT Telkom Indonesia / Bank BRI"
                           list="clientListOptions"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    <datalist id="clientListOptions">
                        @foreach($clients as $cl)
                            <option value="{{ $cl->name }}">{{ $cl->department ? "({$cl->department})" : '' }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Divisi Terkait</label>
                        <select name="division_id" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
                            <option value="">Pilih Divisi...</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Nilai Kontrak (Rp)</label>
                        <input type="number" name="contract_value" min="0" placeholder="0"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" x-show="formStatus !== 'Completed'">
                    <div>
                        <label class="block text-gray-700 mb-1">Tahapan Sales</label>
                        <select name="sales_stage" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
                            @foreach($stages as $k => $stg)
                                <option value="{{ $k }}">{{ $stg['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Estimasi Closing</label>
                        <input type="date" name="expected_closing_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="sales_notes" rows="2.5" placeholder="Detail kebutuhan klien atau spesifikasi..."
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            :class="formStatus === 'Completed' ? 'btn-ipnet-green' : 'btn-ipnet-primary'"
                            class="px-5 py-2 rounded-xl font-bold text-white shadow-md">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function projectKanbanPage() {
        return {
            isAddModalOpen: false,
            modalTitle: 'Tambah Project Baru',
            formStatus: 'Opportunity',

            openAddProjectModal(statusType) {
                this.formStatus = statusType;
                if (statusType === 'Completed') {
                    this.modalTitle = 'Tambah Project Selesai (Completed)';
                } else {
                    this.modalTitle = 'Tambah Project Baru';
                }
                this.isAddModalOpen = true;
            }
        };
    }
</script>
@endsection
