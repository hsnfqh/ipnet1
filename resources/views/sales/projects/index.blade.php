@extends('layouts.app')

@section('title', 'Project Sales & Peluang Tender - Sales Portal')

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

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .anim-fade-up {
        animation: fadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Project Sales & Peluang Tender'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto anim-fade-up" x-data="salesProjectManager()">
            
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

            {{-- Filter & Action Bar --}}
            <div class="ipnet-card p-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('sales.projects.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative flex-1 sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari project, klien..." 
                               class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="division_id" onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] shadow-xs cursor-pointer">
                        <option value="">Semua Divisi Teknis</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>

                    <select name="approval_status" onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] shadow-xs cursor-pointer">
                        <option value="">Status Approval</option>
                        <option value="Approved" {{ request('approval_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Pending" {{ request('approval_status') == 'Pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="Rejected" {{ request('approval_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>

                    @if(request('search') || request('division_id') || request('approval_status'))
                        <a href="{{ route('sales.projects.index', ['tab' => $tab]) }}" 
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button @click="openModal('Opportunity')" 
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold rounded-xl shadow-md transition-all whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Proyek</span>
                    </button>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex items-center gap-6 border-b border-gray-200 px-2 text-xs font-bold">
                <a href="{{ route('sales.projects.index', ['tab' => 'Draft', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Draft' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Draft <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ $tab === 'Draft' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-gray-500' }}">{{ $counts['draft'] }}</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'Opportunity', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Opportunity' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Opportunity <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ $tab === 'Opportunity' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-gray-500' }}">{{ $counts['opportunity'] }}</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'In Progress', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'In Progress' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    In Progress <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ $tab === 'In Progress' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-gray-500' }}">{{ $counts['in_progress'] }}</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'Pending', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Pending' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Pending <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ $tab === 'Pending' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-gray-500' }}">{{ $counts['pending'] }}</span>
                </a>
            </div>

            {{-- Projects Content Table --}}
            <div class="ipnet-card overflow-hidden">
                @if($filteredProjects->isEmpty())
                    <div class="py-16 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-xs font-bold text-gray-700">Tidak ada project pada tab {{ $tab }}</div>
                        <p class="text-[11.5px] text-gray-400 max-w-sm mx-auto">Gunakan tombol "+ Tambah Proyek" untuk menambahkan prospek atau opportunity tender baru.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-[10.5px] font-bold tracking-wider text-gray-500 uppercase">
                                    <th class="py-3 px-4 rounded-l-lg">Project &amp; Klien</th>
                                    <th class="py-3 px-4">Divisi Teknis</th>
                                    <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                    <th class="py-3 px-4">Deadline / Target</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @foreach($filteredProjects as $proj)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-gray-900">{{ $proj->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1.5 mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>{{ $proj->client }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 text-xs whitespace-nowrap">
                                            <span class="px-2.5 py-0.5 rounded-md bg-gray-100 font-semibold text-gray-700 border border-gray-200 text-[11px]">
                                                {{ $proj->division ? $proj->division->name : ($proj->project_type ?: 'General') }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-black text-gray-900 text-right whitespace-nowrap">
                                            {{ $proj->contract_value > 0 ? 'Rp ' . number_format($proj->contract_value, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-gray-600 text-xs whitespace-nowrap">
                                            {{ $proj->deadline ? \Carbon\Carbon::parse($proj->deadline)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @php
                                                $stBadge = match($proj->status) {
                                                    'Draft'       => 'bg-gray-100 text-gray-700 border-gray-200',
                                                    'Opportunity' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'Planning'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'On Progress' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    default       => 'bg-gray-50 text-gray-700 border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2.5 py-0.5 text-[11px] font-bold rounded-lg border {{ $stBadge }}">
                                                {{ $proj->status }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($proj->proposal_file)
                                                    <a href="{{ route('projects.proposal.download', $proj->id) }}" 
                                                       title="Unduh Proposal Teknis Presales"
                                                       class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-lg border border-emerald-200 transition-all inline-flex items-center gap-1 shadow-xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Proposal</span>
                                                    </a>
                                                @endif
                                                <a href="{{ route('projects.show', $proj->id) }}" 
                                                   class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition-all">
                                                    Detail
                                                </a>
                                                <button @click="confirmDelete({{ $proj->id }}, '{{ addslashes($proj->name) }}')"
                                                        class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-semibold rounded-lg border border-red-200 shadow-xs transition-all">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Modal Add Project --}}
            <template x-teleport="body">
                <div x-show="isModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @click.self="isModalOpen = false">
                    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden anim-fade-up">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                            <div>
                                <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Proyek Komersial</p>
                                <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="targetStatus === 'Completed' ? 'Tambah Proyek Closed Won' : 'Tambah Proyek Komersial Baru'"></h3>
                            </div>
                            <button type="button" @click="isModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Form Body --}}
                        <form action="{{ route('sales.projects.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                            @csrf
                            <input type="hidden" name="status" :value="targetStatus">

                            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-3.5 text-[12.5px]">
                                <div>
                                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                        Nama Project <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="name" required placeholder="Contoh: Pengadaan & Setup Firewall FortiGate Data Center" 
                                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                            Klien / Perusahaan <span class="text-[#8F0A0D]">*</span>
                                        </label>
                                        <input type="text" list="client-list" name="client" required placeholder="Pilih / ketik nama klien" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                        <datalist id="client-list">
                                            @foreach($clients as $c)
                                                <option value="{{ $c->name }}">{{ $c->department }}</option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                            Divisi Teknis
                                        </label>
                                        <select name="division_id" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                            <option value="">-- Pilih Divisi --</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                            Nilai Kontrak / Est. (Rp)
                                        </label>
                                        <input type="number" name="contract_value" min="0" placeholder="Contoh: 150000000" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                            Target Deadline
                                        </label>
                                        <input type="date" name="deadline" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                        Deskripsi &amp; Ruang Lingkup
                                    </label>
                                    <textarea name="description" rows="2.5" placeholder="Catatan spesifikasi awal atau kebutuhan klien..." 
                                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                                </div>
                            </div>

                            {{-- Fixed Footer --}}
                            <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                                <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer">
                                    Simpan Project
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Hidden Delete Form --}}
            <form id="delete-sales-project-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>
</div>

<script>
    function salesProjectManager() {
        return {
            isModalOpen: false,
            targetStatus: 'Opportunity',

            openModal(status) {
                this.targetStatus = status;
                this.isModalOpen = true;
            },

            confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus project "${name}"?`)) {
                    const form = document.getElementById('delete-sales-project-form');
                    form.action = `/sales-projects/${id}`;
                    form.submit();
                }
            }
        }
    }
</script>
@endsection
