@extends('layouts.app')

@section('title', 'View Client - ' . $client->name)

@php
    $rawDepts = array_filter(array_map('trim', explode(',', (string) ($client->department ?? ''))));
    if (empty($rawDepts)) {
        $rawDepts = [$client->department ?: ''];
    }
@endphp

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.015);
    }
    .form-label-bold {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }
    .form-input-clean {
        width: 100%;
        padding: 9.5px 14px;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 500;
        color: #1E293B;
        background: #FFFFFF;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .form-input-clean:focus {
        border-color: #8F0A0D;
        box-shadow: 0 0 0 3px rgba(143, 10, 13, 0.08);
    }
    .project-item-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all .15s ease;
    }
    .project-item-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="clientShowPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Client'])

        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            {{-- 1. BREADCRUMB --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('clients.index') }}" class="hover:text-gray-800 transition">Client</a>
                <span>&gt;</span>
                <span class="text-gray-700">{{ $client->name }}</span>
                <span>&gt;</span>
                <span class="text-gray-900 font-bold">Edit</span>
            </div>

            {{-- 2. TITLE WITH ICON & DELETE ACTION --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-200 shadow-2xs">
                        <svg class="w-4.5 h-4.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-[#1E293B] tracking-tight">View Client</h1>
                </div>

                <button type="button" @click="showDeleteModal = true" 
                        class="px-3.5 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-[#8F0A0D] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Client
                </button>
            </div>

            {{-- 3. MAIN CONTENT: 2-COLUMN BALANCED GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- ══ LEFT COLUMN: CLIENT FORM CARD (col-span-8) ══ --}}
                <div class="lg:col-span-8">
                    <div class="ipnet-card p-6 sm:p-8">
                        <form action="{{ route('clients.update', $client->id) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            {{-- NAME --}}
                            <div>
                                <label class="form-label-bold">NAME</label>
                                <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                       class="form-input-clean font-semibold"
                                       placeholder="Nama Client...">
                            </div>

                            {{-- ADDRESS 1 --}}
                            <div>
                                <label class="form-label-bold">ADDRESS 1</label>
                                <textarea name="address" rows="4"
                                          class="form-input-clean leading-relaxed resize-y font-normal"
                                          placeholder="Masukkan alamat utama client...">{{ old('address', $client->address) }}</textarea>

                                {{-- Toggle Add Secondary Address --}}
                                <div class="mt-2.5" x-show="!showSecondaryAddress">
                                    <button type="button" @click="showSecondaryAddress = true"
                                            class="text-xs font-bold text-[#8F0A0D] hover:text-[#73080A] hover:underline cursor-pointer inline-flex items-center gap-1.5">
                                        <span>+ CREATE SECONDARY ADDRESS</span>
                                    </button>
                                </div>

                                {{-- Secondary Address Container --}}
                                <div x-show="showSecondaryAddress" x-cloak class="mt-3 p-4 rounded-xl bg-[#FAFBFD] border border-[#E2E8F0] space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="form-label-bold !mb-0">ADDRESS 2 (SECONDARY)</label>
                                        <button type="button" @click="showSecondaryAddress = false" 
                                                class="text-xs font-semibold text-gray-400 hover:text-red-600 transition flex items-center gap-1 cursor-pointer"
                                                title="Hapus alamat sekunder">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                    <textarea name="notes" rows="3"
                                              class="form-input-clean !bg-white leading-relaxed resize-y"
                                              placeholder="Alamat sekunder / cabang / catatan tambahan...">{{ old('notes', $client->notes) }}</textarea>
                                </div>
                            </div>

                            {{-- DEPARTMENTS --}}
                            <div class="space-y-4 pt-1">
                                <template x-for="(dept, idx) in departments" :key="idx">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <label class="form-label-bold !mb-0" x-text="'DEPARTMENT ' + (idx + 1)"></label>
                                            <template x-if="idx > 0">
                                                <button type="button" @click="removeDepartment(idx)" 
                                                        class="text-xs font-semibold text-gray-400 hover:text-red-600 transition flex items-center gap-1 cursor-pointer"
                                                        title="Hapus departemen ini">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </template>
                                        </div>

                                        <input type="text" name="department[]" x-model="departments[idx]"
                                               class="form-input-clean"
                                               :placeholder="'Nama Departemen ' + (idx + 1)">
                                    </div>
                                </template>

                                {{-- Single Clean Button: + CREATE DEPARTMENT --}}
                                <div class="pt-1">
                                    <button type="button" @click="addDepartment()" 
                                            class="text-xs font-bold text-[#8F0A0D] hover:text-[#73080A] hover:underline cursor-pointer inline-flex items-center gap-1.5">
                                        <span>+ CREATE DEPARTMENT</span>
                                    </button>
                                </div>
                            </div>

                            {{-- PIC & KONTAK UTAMA SECTION --}}
                            <div class="pt-5 border-t border-gray-100">
                                <label class="form-label-bold mb-3">PIC &amp; KONTAK UTAMA</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC NAME</label>
                                        <div class="relative">
                                            <input type="text" name="pic_name" value="{{ old('pic_name', $client->pic_name) }}"
                                                   placeholder="Nama kontak PIC..."
                                                   class="form-input-clean text-xs">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC PHONE</label>
                                        <div class="relative">
                                            <input type="tel" 
                                                   name="phone" 
                                                   value="{{ old('phone', $client->phone) }}"
                                                   inputmode="tel"
                                                   pattern="[0-9+\-\s()]+"
                                                   oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')"
                                                   placeholder="Contoh: 081234567890..."
                                                   class="form-input-clean text-xs">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC EMAIL</label>
                                        <div class="relative">
                                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                                   placeholder="Email resmi..."
                                                   class="form-input-clean text-xs">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SAVE & CANCEL BUTTONS --}}
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                                <a href="{{ route('clients.index') }}" 
                                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit" 
                                        class="px-7 py-2.5 rounded-xl text-white font-bold text-xs shadow-md cursor-pointer transition hover:opacity-95"
                                        style="background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);">
                                    Update Client
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ══ RIGHT COLUMN: PROJECTS & CLIENT SUMMARY (col-span-4) ══ --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Projects Card --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-bold text-gray-900">Projects</h3>
                            @if($client->projects && $client->projects->count() > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                    {{ $client->projects->count() }} Proyek
                                </span>
                            @endif
                        </div>

                        @if($client->projects && $client->projects->count() > 0)
                            <div class="space-y-3">
                                @foreach($client->projects as $p)
                                    <div class="project-item-card">
                                        <a href="{{ route('projects.show', $p->id) }}" class="text-xs font-bold text-gray-900 hover:text-[#8F0A0D] transition block mb-1">
                                            {{ $p->name }}
                                        </a>
                                        <div class="flex items-center justify-between text-[11px] pt-1">
                                            <span class="font-bold text-gray-600">
                                                Rp {{ number_format($p->contract_value ?? 0, 0, ',', '.') }}
                                            </span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                {{ $p->status ?: ($p->stage ?: 'Opportunity') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center rounded-xl bg-gray-50 border border-gray-200">
                                <p class="text-xs font-medium text-gray-500 leading-relaxed">
                                    Don't have any project, please add current or latest project here
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Client Meta Info Card --}}
                    @php
                        $creatorName = $client->creator ? $client->creator->name : ($client->created_by ? 'User #' . $client->created_by : null);
                        if (!$creatorName && $client->projects && $client->projects->isNotEmpty()) {
                            $firstProj = $client->projects->first();
                            $creatorName = $firstProj->sales_name ?: ($firstProj->creator ? $firstProj->creator->name : null);
                        }
                        if (!$creatorName) {
                            $creatorName = 'Raiza';
                        }
                    @endphp
                    <div class="ipnet-card p-5 space-y-3">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            Informasi Sistem
                        </div>
                        <div class="space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>ID Client</span>
                                <span class="font-bold text-gray-900 font-mono">#{{ $client->id }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Dibuat Oleh (PIC)</span>
                                <span class="font-bold text-[#8F0A0D] inline-flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-full bg-red-100 text-[#8F0A0D] flex items-center justify-center text-[9px] font-extrabold uppercase">
                                        {{ strtoupper(substr($creatorName, 0, 2)) }}
                                    </span>
                                    <span>{{ $creatorName }}</span>
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Tanggal Dibuat</span>
                                <span class="font-medium text-gray-800">{{ $client->created_at ? $client->created_at->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Terakhir Diperbarui</span>
                                <span class="font-medium text-gray-800">{{ $client->updated_at ? $client->updated_at->format('d M Y H:i') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- ═══ DELETE CONFIRMATION MODAL (PLEK KETIPLEK DENGAN LEAD ENGINEER) ═══ --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
             @click.self="showDeleteModal = false"
             @keydown.escape.window="showDeleteModal = false">
            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] animate-fade-in-up">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Client?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words">Client "{{ $client->name }}" akan dihapus secara permanen.</p>

                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="flex gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md text-white text-center">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
    function clientShowPage() {
        const initDepts = @json(array_values($rawDepts));
        return {
            showDeleteModal: false,
            showSecondaryAddress: {{ !empty($client->notes) ? 'true' : 'false' }},
            departments: JSON.parse(JSON.stringify(initDepts)),

            addDepartment() {
                this.departments.push('');
            },

            removeDepartment(idx) {
                if (this.departments.length > 1) {
                    this.departments.splice(idx, 1);
                }
            }
        };
    }
</script>
@endpush
@endsection
