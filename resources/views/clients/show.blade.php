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
        padding: 9px 14px;
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

                                <div class="mt-2 flex items-center gap-3">
                                    <button type="button" @click="showSecondaryAddress = true" x-show="!showSecondaryAddress"
                                            class="text-xs font-bold text-[#8F0A0D] hover:text-[#73080A] hover:underline cursor-pointer inline-flex items-center gap-1">
                                        + CREATE SECONDARY ADDRESS
                                    </button>
                                    <button type="button" @click="showSecondaryAddress = false" x-show="showSecondaryAddress" x-cloak
                                            class="text-xs font-bold text-gray-500 hover:text-red-600 hover:underline cursor-pointer inline-flex items-center gap-1">
                                        ✕ Batalkan Alamat Sekunder
                                    </button>
                                </div>

                                {{-- Secondary Address Drawer --}}
                                <div x-show="showSecondaryAddress" x-cloak class="mt-3 p-4 rounded-xl bg-[#FAFBFD] border border-[#E2E8F0] space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="form-label-bold !mb-0">ADDRESS 2 (SECONDARY)</label>
                                        <button type="button" @click="showSecondaryAddress = false" class="text-xs text-gray-400 hover:text-red-600 cursor-pointer font-semibold">✕ Tutup</button>
                                    </div>
                                    <textarea name="notes" rows="3"
                                              class="form-input-clean !bg-white leading-relaxed resize-y"
                                              placeholder="Alamat sekunder / cabang / catatan tambahan...">{{ old('notes', $client->notes) }}</textarea>
                                </div>
                            </div>

                            {{-- DEPARTMENTS (Dynamic Loop) --}}
                            <div class="space-y-4 pt-1">
                                <template x-for="(dept, idx) in departments" :key="idx">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="form-label-bold !mb-0" x-text="'DEPARTMENT ' + (idx + 1)"></label>
                                            <template x-if="departments.length > 1">
                                                <button type="button" @click="removeDepartment(idx)" 
                                                        class="px-2.5 py-0.5 rounded-md text-xs font-bold text-red-600 hover:bg-red-50 border border-transparent hover:border-red-200 transition cursor-pointer inline-flex items-center gap-1"
                                                        title="Hapus / batalkan departemen ini">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    <span>Hapus</span>
                                                </button>
                                            </template>
                                        </div>

                                        <input type="text" name="department[]" x-model="departments[idx]"
                                               class="form-input-clean"
                                               :placeholder="'Nama Departemen ' + (idx + 1)">
                                    </div>
                                </template>

                                {{-- Action Link: + CREATE DEPARTMENT & UNDO --}}
                                <div class="flex items-center gap-3 pt-1">
                                    <button type="button" @click="addDepartment()" 
                                            class="text-xs font-bold text-[#8F0A0D] hover:text-[#73080A] hover:underline cursor-pointer inline-flex items-center gap-1">
                                        + CREATE DEPARTMENT
                                    </button>

                                    {{-- Undo Button if new department added --}}
                                    <template x-if="departments.length > initialDeptCount">
                                        <button type="button" @click="undoAddDepartment()" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-bold text-gray-600 hover:text-red-700 bg-gray-100 hover:bg-red-50 border border-gray-200 transition cursor-pointer inline-flex items-center gap-1.5"
                                                title="Batalkan penambahan departemen terakhir">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m0 0l-4-4m4 4l4-4"/></svg>
                                            <span>Undo Tambah Dept</span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- PIC & KONTAK SECTION --}}
                            <div class="pt-5 border-t border-gray-100">
                                <label class="form-label-bold mb-3">PIC &amp; KONTAK UTAMA</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC NAME</label>
                                        <input type="text" name="pic_name" value="{{ old('pic_name', $client->pic_name) }}"
                                               placeholder="Nama kontak PIC..."
                                               class="form-input-clean text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC PHONE</label>
                                        <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                               placeholder="Nomor Telepon..."
                                               class="form-input-clean text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase">PIC EMAIL</label>
                                        <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                               placeholder="Email resmi..."
                                               class="form-input-clean text-xs">
                                    </div>
                                </div>
                            </div>

                            {{-- SAVE & CANCEL BUTTONS (Docked at Bottom Right) --}}
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                                <a href="{{ route('clients.index') }}" 
                                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit" 
                                        class="px-7 py-2.5 rounded-xl text-white font-bold text-xs shadow-md cursor-pointer transition"
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
                    <div class="ipnet-card p-5 space-y-3">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            Informasi Sistem
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>ID Client</span>
                                <span class="font-bold text-gray-900 font-mono">#{{ $client->id }}</span>
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

    {{-- ═══ DELETE CONFIRMATION MODAL ═══ --}}
    <div x-show="showDeleteModal" x-cloak
         style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(15,23,42,0.45); backdrop-filter: blur(3px);"
         @click.self="showDeleteModal = false">
        <div style="background: #FFFFFF; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.18); max-width: 420px; width: 100%; overflow: hidden;">
            {{-- Header --}}
            <div style="padding: 18px 22px 14px; border-bottom: 1px solid #FEE2E2; background: #FFF5F5; display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; background: #FEE2E2; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg style="width: 17px; height: 17px; color: #8F0A0D;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 14px; font-weight: 700; color: #1E293B; margin: 0 0 2px;">Hapus Client?</h3>
                    <p style="font-size: 11.5px; color: #64748B; margin: 0;">Tindakan ini tidak dapat dibatalkan</p>
                </div>
                <button type="button" @click="showDeleteModal = false" style="margin-left: auto; background: none; border: none; color: #94A3B8; cursor: pointer; font-size: 18px; line-height: 1; padding: 0;">✕</button>
            </div>
            {{-- Body --}}
            <div style="padding: 20px 22px;">
                <p style="font-size: 12.5px; color: #475569; line-height: 1.6; margin: 0 0 14px;">
                    Anda akan menghapus client <strong style="color: #1E293B;">{{ $client->name }}</strong> secara permanen.
                </p>
                <div style="background: #FFF5F5; border: 1px solid #FECACA; border-radius: 10px; padding: 11px 14px; display: flex; align-items: flex-start; gap: 8px;">
                    <svg style="width: 13px; height: 13px; color: #8F0A0D; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size: 11.5px; color: #8F0A0D; font-weight: 600; margin: 0;">Data yang dihapus tidak dapat dikembalikan.</p>
                </div>
            </div>
            {{-- Footer --}}
            <div style="padding: 12px 22px 18px; display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                <button type="button" @click="showDeleteModal = false"
                        style="padding: 8px 16px; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 12px; font-weight: 600; color: #475569; background: #FFFFFF; cursor: pointer; transition: all .15s;"
                        onmouseover="this.style.background='#F8FAFC';" onmouseout="this.style.background='#FFFFFF';">
                    Batal
                </button>
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="padding: 8px 18px; background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%); border: none; border-radius: 10px; font-size: 12px; font-weight: 700; color: #FFFFFF; cursor: pointer; box-shadow: 0 2px 8px rgba(143,10,13,0.22); transition: all .15s; display: inline-flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.background='linear-gradient(135deg, #73080A 0%, #9E0E1D 100%)';" onmouseout="this.style.background='linear-gradient(135deg, #8F0A0D 0%, #B81525 100%)';">
                        <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Ya, Hapus Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function clientShowPage() {
        const initDepts = @json(array_values($rawDepts));
        return {
            showDeleteModal: false,
            showSecondaryAddress: {{ !empty($client->notes) ? 'true' : 'false' }},
            departments: JSON.parse(JSON.stringify(initDepts)),
            initialDeptCount: initDepts.length,

            addDepartment() {
                this.departments.push('');
            },

            undoAddDepartment() {
                if (this.departments.length > this.initialDeptCount) {
                    this.departments.pop();
                } else if (this.departments.length > 1) {
                    this.departments.pop();
                }
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
