@extends('layouts.app')

@section('title', 'View Client - ' . $client->name)

@php
    // Parse department values if comma-separated
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
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 20px rgba(0, 0, 0, 0.02);
    }
    .ipnet-card-header {
        border-bottom: 1px solid #F1F5F9;
        padding-bottom: 18px;
        margin-bottom: 26px;
    }
    .form-label-bold {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 7px;
    }
    .form-input-clean {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #E2E8F0;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 500;
        color: #1E293B;
        background: #FAFBFD;
        outline: none;
        transition: all .15s ease;
        box-sizing: border-box;
    }
    .form-input-clean:focus {
        border-color: #8F0A0D;
        background: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(143,10,13,0.08);
    }
    .project-card-clean {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 14px 16px;
        transition: all .18s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .project-card-clean:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden font-sans bg-[#F8FAFC]"
     x-data="clientShowPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Client'])

        <div class="p-4 sm:p-6 lg:p-8 max-w-[1440px] mx-auto">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="mb-5 p-3.5 px-4.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            {{-- THE MAIN IPNET CARD --}}
            <div class="ipnet-card p-6 sm:p-8 lg:p-9">

                {{-- Card Header: Breadcrumb & Title --}}
                <div class="ipnet-card-header">
                    <nav class="flex items-center gap-2 text-xs font-semibold text-[#64748B] mb-2.5">
                        <a href="{{ route('clients.index') }}" class="text-[#1E293B] hover:text-[#8F0A0D] transition">Client</a>
                        <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-[#1E293B] font-bold uppercase">{{ $client->name }}</span>
                        <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-[#94A3B8]">Edit</span>
                    </nav>

                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg border border-[#8F0A0D] flex items-center justify-center text-[#8F0A0D] bg-white shrink-0 shadow-2xs">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-[#1E293B] tracking-tight">View Client</h1>
                    </div>
                </div>

                {{-- 2-Column Grid inside the Card --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

                    {{-- Left Column: Form (col-span-8) --}}
                    <div class="lg:col-span-8">
                        <form action="{{ route('clients.update', $client->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- NAME --}}
                            <div class="mb-5">
                                <label class="form-label-bold">NAME</label>
                                <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                       class="form-input-clean"
                                       placeholder="Nama Client...">
                            </div>

                            {{-- ADDRESS 1 --}}
                            <div class="mb-4">
                                <label class="form-label-bold">ADDRESS 1</label>
                                <textarea name="address" rows="5"
                                          class="form-input-clean resize-y"
                                          placeholder="Masukkan alamat utama client...">{{ old('address', $client->address) }}</textarea>

                                <div class="mt-2">
                                    <button type="button" @click="showSecondaryAddress = !showSecondaryAddress"
                                            class="text-[11.5px] font-bold text-[#8F0A0D] hover:underline uppercase tracking-wide cursor-pointer inline-flex items-center gap-1">
                                        + CREATE SECONDARY ADDRESS
                                    </button>
                                </div>

                                {{-- Secondary Address drawer --}}
                                <div x-show="showSecondaryAddress" x-cloak class="mt-3.5 p-4 rounded-xl bg-[#FAFBFD] border border-[#E2E8F0]">
                                    <label class="form-label-bold">ADDRESS 2 (SECONDARY)</label>
                                    <textarea name="notes" rows="3"
                                              class="form-input-clean bg-white resize-y"
                                              placeholder="Alamat sekunder / cabang / catatan tambahan...">{{ old('notes', $client->notes) }}</textarea>
                                </div>
                            </div>

                            {{-- DEPARTMENTS (Dynamic list) --}}
                            <template x-for="(dept, idx) in departments" :key="idx">
                                <div class="mt-5">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="form-label-bold !mb-0" x-text="'DEPARTMENT ' + (idx + 1)"></label>
                                        <template x-if="departments.length > 1">
                                            <button type="button" @click="removeDepartment(idx)" class="text-[11px] font-semibold text-[#94A3B8] hover:text-[#8F0A0D] cursor-pointer">
                                                ✕ Hapus Dept
                                            </button>
                                        </template>
                                    </div>
                                    <input type="text" name="department[]" x-model="departments[idx]"
                                           class="form-input-clean"
                                           :placeholder="'Nama Departemen ' + (idx + 1)">

                                    {{-- Gray Box: + CREATE PIC [DEPT] --}}
                                    <div class="mt-2.5">
                                        <button type="button" @click="togglePic(idx)"
                                                class="w-full py-3 px-4 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-left cursor-pointer transition flex items-center justify-between">
                                            <span class="text-[11.5px] font-bold text-[#8F0A0D] uppercase tracking-wide">
                                                + CREATE PIC <span x-text="(departments[idx] || ('DEPARTMENT ' + (idx + 1))).toUpperCase()"></span>
                                            </span>
                                            <svg class="w-3.5 h-3.5 text-[#94A3B8] transition-transform duration-200" :class="activePicDeptIndex === idx ? 'rotate-180 text-[#8F0A0D]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>

                                        {{-- Expandable PIC Form for this department --}}
                                        <div x-show="activePicDeptIndex === idx" x-cloak
                                             class="mt-2.5 p-4 rounded-xl bg-[#FAFBFD] border border-[#E2E8F0] space-y-3">
                                            <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Kontak PIC Departemen</p>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[10.5px] font-semibold text-[#64748B] mb-1">Nama PIC</label>
                                                    <input type="text" name="pic_name" value="{{ old('pic_name', $client->pic_name) }}" placeholder="Nama PIC..."
                                                           class="form-input-clean !bg-white">
                                                </div>
                                                <div>
                                                    <label class="block text-[10.5px] font-semibold text-[#64748B] mb-1">Telepon / WhatsApp</label>
                                                    <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="Nomor Telepon..."
                                                           class="form-input-clean !bg-white">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10.5px] font-semibold text-[#64748B] mb-1">Email Resmi</label>
                                                <input type="email" name="email" value="{{ old('email', $client->email) }}" placeholder="Email PIC..."
                                                       class="form-input-clean !bg-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- + CREATE DEPARTMENT Link --}}
                            <div class="mt-4">
                                <button type="button" @click="addDepartment()"
                                        class="text-[11.5px] font-bold text-[#8F0A0D] hover:underline uppercase tracking-wide cursor-pointer inline-flex items-center gap-1">
                                    + CREATE DEPARTMENT
                                </button>
                            </div>

                            {{-- Center-aligned Action Buttons --}}
                            <div class="flex items-center justify-center gap-5 mt-10 pt-4">
                                <a href="{{ route('clients.index') }}"
                                   class="px-5 py-2.5 text-xs font-bold text-[#475569] hover:text-[#1E293B] transition rounded-xl">
                                    Cancel
                                </a>

                                <button type="submit"
                                        class="px-8 py-2.5 rounded-xl font-bold text-xs text-white uppercase tracking-wider transition cursor-pointer shadow-sm hover:shadow-md"
                                        style="background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);">
                                    Update Client
                                </button>
                            </div>

                            {{-- Subtle Delete Client Link --}}
                            <div class="border-t border-[#F1F5F9] pt-4 mt-8 flex items-center justify-start">
                                <button type="button" @click="showDeleteModal = true"
                                        class="text-[11.5px] font-semibold text-red-600 hover:text-red-800 hover:underline cursor-pointer inline-flex items-center gap-1.5 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus data client ini
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Right Column: Projects Panel (col-span-4) with clear border separator --}}
                    <div class="lg:col-span-4 lg:border-l lg:border-[#E2E8F0] lg:pl-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Projects</h3>
                            @if($client->projects && $client->projects->count() > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FEF2F2] text-[#8F0A0D] border border-[#FECACA]">
                                    {{ $client->projects->count() }} Proyek
                                </span>
                            @endif
                        </div>

                        @if($client->projects && $client->projects->count() > 0)
                            <div class="space-y-3">
                                @foreach($client->projects as $p)
                                    <div class="project-card-clean">
                                        <a href="{{ route('projects.show', $p->id) }}" class="text-[13px] font-bold text-[#1E293B] hover:text-[#8F0A0D] transition block mb-1.5">
                                            {{ $p->name }}
                                        </a>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="font-bold text-[#64748B]">
                                                Rp {{ number_format($p->contract_value ?? 0, 0, ',', '.') }}
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569]">
                                                {{ $p->status ?: ($p->stage ?: 'Opportunity') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <p class="text-xs font-semibold text-[#64748B] leading-relaxed">
                                    Don't have any project, please add current or latest project here
                                </p>
                            </div>
                        @endif
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
        return {
            showDeleteModal: false,
            showSecondaryAddress: {{ !empty($client->notes) ? 'true' : 'false' }},
            departments: @json(array_values($rawDepts)),
            activePicDeptIndex: null,

            addDepartment() {
                this.departments.push('');
            },

            removeDepartment(idx) {
                if (this.departments.length > 1) {
                    this.departments.splice(idx, 1);
                }
            },

            togglePic(idx) {
                this.activePicDeptIndex = (this.activePicDeptIndex === idx) ? null : idx;
            }
        };
    }
</script>
@endpush
@endsection
