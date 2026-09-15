@extends('layouts.app')

@section('title', 'Project Sales - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Peluang Tender'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6" x-data="salesProjectManager()">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Filter & Action Bar --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('sales.projects.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative flex-1 sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                    </div>

                    <select name="division_id" onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <option value="">Select division</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>

                    <select name="approval_status" onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <option value="">Select status approval</option>
                        <option value="Approved" {{ request('approval_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Pending" {{ request('approval_status') == 'Pending' ? 'selected' : '' }}>Pending Review</option>
                        <option value="Rejected" {{ request('approval_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>

                <div class="flex items-center gap-2">
                    <button @click="openModal('Opportunity')" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-[13px] font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Proyek</span>
                    </button>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex items-center gap-6 border-b border-gray-200 px-2 text-sm font-bold">
                <a href="{{ route('sales.projects.index', ['tab' => 'Draft', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Draft' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Draft <span class="text-xs font-semibold text-gray-400">({{ $counts['draft'] }})</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'Opportunity', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Opportunity' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Opportunity <span class="text-xs font-semibold text-gray-400">({{ $counts['opportunity'] }})</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'In Progress', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'In Progress' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    In Progress <span class="text-xs font-semibold text-gray-400">({{ $counts['in_progress'] }})</span>
                </a>
                <a href="{{ route('sales.projects.index', ['tab' => 'Pending', 'search' => request('search')]) }}" 
                   class="pb-3 border-b-2 transition-all flex items-center gap-1.5 {{ $tab === 'Pending' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Pending <span class="text-xs font-semibold text-gray-400">({{ $counts['pending'] }})</span>
                </a>
            </div>

            {{-- Projects Content --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($filteredProjects->isEmpty())
                    <div class="py-20 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-sm font-bold text-gray-700">Tidak ada project pada tab {{ $tab }}</div>
                        <p class="text-xs text-gray-400 max-w-sm mx-auto">Gunakan tombol "+ Add new project" untuk menambahkan prospek atau opportunity bisnis baru.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                    <th class="py-4 px-6">PROJECT & KLIEN</th>
                                    <th class="py-4 px-6">DIVISI</th>
                                    <th class="py-4 px-6">NILAI KONTRAK</th>
                                    <th class="py-4 px-6">DEADLINE / TARGET</th>
                                    <th class="py-4 px-6 text-center">STATUS</th>
                                    <th class="py-4 px-6 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($filteredProjects as $proj)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900">{{ $proj->name }}</div>
                                            <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 mt-0.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                {{ $proj->client }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-gray-600 font-medium text-xs">
                                            {{ $proj->division ? $proj->division->name : ($proj->project_type ?: 'General') }}
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-gray-800 text-xs">
                                            {{ $proj->contract_value > 0 ? 'Rp ' . number_format($proj->contract_value, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="py-4 px-6 text-gray-500 text-xs whitespace-nowrap">
                                            {{ $proj->deadline ? \Carbon\Carbon::parse($proj->deadline)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="py-4 px-6 text-center whitespace-nowrap">
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
                                            <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg border {{ $stBadge }}">
                                                {{ $proj->status }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($proj->proposal_file)
                                                    <a href="{{ route('projects.proposal.download', $proj->id) }}" 
                                                       title="Unduh Proposal Teknis dari Presales (Akbar)"
                                                       class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-lg border border-emerald-200 transition-all inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Proposal
                                                    </a>
                                                @endif
                                                <a href="{{ route('projects.show', $proj->id) }}" 
                                                   class="px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200 shadow-sm transition-all">
                                                    Detail
                                                </a>
                                                <button @click="confirmDelete({{ $proj->id }}, '{{ addslashes($proj->name) }}')"
                                                        class="px-3 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-xs font-semibold rounded-lg border border-red-200 shadow-sm transition-all">
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
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isModalOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900" x-text="targetStatus === 'Completed' ? 'Add Complete Project' : 'Add New Project'"></h3>
                            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form action="{{ route('sales.projects.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="status" :value="targetStatus">

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Project *</label>
                                <input type="text" name="name" required placeholder="Contoh: Pengadaan & Setup Firewall FortiGate Data Center" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Klien / Perusahaan *</label>
                                    <input type="text" list="client-list" name="client" required placeholder="Pilih / ketik nama klien" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                    <datalist id="client-list">
                                        @foreach($clients as $c)
                                            <option value="{{ $c->name }}">{{ $c->department }}</option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Divisi Teknis</label>
                                    <select name="division_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nilai Kontrak / Est. (Rp)</label>
                                    <input type="number" name="contract_value" min="0" placeholder="Contoh: 150000000" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Deadline</label>
                                    <input type="date" name="deadline" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi & Ruang Lingkup</label>
                                <textarea name="description" rows="2" placeholder="Catatan spesifikasi awal atau kebutuhan klien..." 
                                          class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" 
                                        class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">
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
