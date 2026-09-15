@extends('layouts.app')

@section('title', 'Pengguna - PT IP Network Solusindo')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

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

    .cert-tab-pill {
        padding: 8px 14px;
        border-radius: 12px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        color: #334155;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
    }
    .cert-tab-pill:hover:not(.active) {
        background: #F8FAFC;
        border-color: #94A3B8;
        color: #0F172A;
        transform: translateY(-1px);
    }
    .cert-tab-pill.active {
        background: #1E293B;
        border-color: #1E293B;
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
    }
    .cert-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Pengguna'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto" x-data="usersManager()" x-init="init()">

            <!-- ========================================================== -->
            <!-- SECTION HEADER & FILTER CONTROLS (IPNET CARD STYLE)        -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PENGGUNA
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Data Pengguna & Tim Divisi</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola akun pengguna, wewenang hierarki, penempatan divisi teknis, dan verifikasi sertifikasi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Pengguna:</span>
                            <span class="text-[#8F0A0D] font-extrabold" x-text="users.length"></span>
                        </div>

                        @if(\App\Helpers\ScopeHelper::isManagerial(auth()->user()))
                        <button @click="openModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Pengguna</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Filter Controls matching Tasks Index -->
                <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative w-full sm:w-72">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama, email, nomor..."
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                    </div>

                    {{-- Role Filter --}}
                    <select x-model="roleFilter"
                            class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Role</option>
                        @foreach($filterableRoles as $r)
                        <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>

                    {{-- Division Filter --}}
                    @if($isGlobal || count($divisions) > 1)
                    <select x-model="divisionFilter"
                            class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Divisi</option>
                        @foreach($divisions as $division)
                        <option value="{{ $division->name }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    {{-- Status Filter --}}
                    <select x-model="statusFilter"
                            class="w-full sm:w-40 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>

                    {{-- Reset Button --}}
                    <button type="button"
                            x-show="search || roleFilter !== 'Semua' || divisionFilter !== 'Semua' || statusFilter !== 'Semua'"
                            @click="search = ''; roleFilter = 'Semua'; divisionFilter = 'Semua'; statusFilter = 'Semua'; currentPage = 1;"
                            class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 4 METRIC SUMMARY CARDS                                     -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 anim-fade-up anim-delay-1">
                {{-- Card 1: Total Pengguna --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Total Pengguna</p>
                        <h3 class="text-[22px] font-extrabold text-[#1E293B] mt-1" x-text="users.length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Terdaftar di sistem</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center text-[#1E293B] shadow-xs">
                        <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 2: Pengguna Aktif --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Pengguna Aktif</p>
                        <h3 class="text-[22px] font-extrabold text-[#16A34A] mt-1" x-text="users.filter(u => u.status === 'Active').length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Status akun aktif</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#F0FDF4] border border-[#BBF7D0] flex items-center justify-center text-[#16A34A] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 3: Tim Teknis & Lapangan --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Tim Teknis</p>
                        <h3 class="text-[22px] font-extrabold text-[#2563EB] mt-1" x-text="users.filter(u => ['Engineer', 'Maintenance', 'Team Leader', 'Lead Maintenance', 'Lead Engineer'].includes(u.role_name)).length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Engineer & Maintenance</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#EFF6FF] border border-[#BFDBFE] flex items-center justify-center text-[#2563EB] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 4: Menunggu Verifikasi Sertifikasi --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Perlu Verifikasi</p>
                        <h3 class="text-[22px] font-extrabold mt-1"
                            :class="users.filter(u => u.has_certification && u.certification_status === 'pending').length > 0 ? 'text-[#D97706]' : 'text-[#1E293B]'"
                            x-text="users.filter(u => u.has_certification && u.certification_status === 'pending').length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Sertifikat menunggu approval</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] flex items-center justify-center text-[#D97706] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- DATA TABLE PENGGUNA                                        -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-[13px]">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Nama Pengguna</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Kontak</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Role</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Divisi & Level</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Sertifikasi</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Status</th>
                                <th class="py-3.5 px-5 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            <template x-for="user in paginatedUsers" :key="user.id">
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors duration-100">
                                    {{-- Nama & Subtitle --}}
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-[11px] flex-shrink-0 text-white shadow-xs"
                                                 style="background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);">
                                                <span x-text="user.name ? user.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?'"></span>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-[#1E293B]" x-text="user.name"></span>
                                                    <template x-if="user.is_self">
                                                        <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded-md bg-[#EFF6FF] text-[#1D4ED8] border border-[#DBEAFE]">Anda</span>
                                                    </template>
                                                </div>
                                                <p class="text-[12px] text-[#64748B] mt-0.5" x-text="user.position || user.role_name"></p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kontak --}}
                                    <td class="py-3.5 px-5">
                                        <div class="text-[12px] space-y-0.5">
                                            <div class="text-[#334155] flex items-center gap-1.5 font-medium">
                                                <svg class="w-3.5 h-3.5 text-[#94A3B8] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                <span x-text="user.email"></span>
                                            </div>
                                            <div class="text-[#64748B] flex items-center gap-1.5 font-mono text-[11.5px]" x-show="user.phone">
                                                <svg class="w-3.5 h-3.5 text-[#94A3B8] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                <span x-text="user.phone"></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Role --}}
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <span x-html="getRoleBadge(user)"></span>
                                    </td>

                                    {{-- Divisi & Level --}}
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[13px] font-semibold text-[#1E293B]" x-text="user.division_name !== '-' ? user.division_name : '-'"></span>
                                            <template x-if="user.level">
                                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]" x-text="user.level"></span>
                                            </template>
                                        </div>
                                    </td>

                                    {{-- Sertifikasi Status --}}
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <template x-if="user.has_certification">
                                            <div class="flex items-center gap-2">
                                                <span x-html="getCertificationStatusBadge(user.certification_status)"></span>
                                                @if(\App\Helpers\ScopeHelper::isManagerial(auth()->user()))
                                                <button @click="viewCertification(user)"
                                                        class="p-1.5 text-[#8F0A0D] hover:bg-[#FEF2F2] rounded-lg transition cursor-pointer"
                                                        title="Lihat Dokumen Sertifikat">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </template>
                                        <template x-if="!user.has_certification">
                                            <span class="text-[12px] text-[#94A3B8]">Belum ada</span>
                                        </template>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-5 whitespace-nowrap" x-html="getStatusBadge(user.status)"></td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <template x-if="user.can_manage">
                                            <div class="flex items-center justify-end gap-1.5">
                                                {{-- Toggle Active Status --}}
                                                <button @click="toggleStatus(user)"
                                                        class="p-1.5 rounded-lg transition cursor-pointer"
                                                        :class="user.status === 'Active' ? 'text-[#16A34A] hover:bg-[#F0FDF4]' : 'text-[#94A3B8] hover:bg-[#F1F5F9]'"
                                                        :title="user.status === 'Active' ? 'Nonaktifkan Akun' : 'Aktifkan Akun'">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v4m0 4v4m0 4v4"/>
                                                    </svg>
                                                </button>

                                                {{-- Edit User --}}
                                                <button @click="editUser(user)"
                                                        class="p-1.5 rounded-lg text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] transition cursor-pointer"
                                                        title="Edit Data User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                {{-- Delete User --}}
                                                <button @click="deleteUser(user)"
                                                        class="p-1.5 rounded-lg text-[#8F0A0D] hover:bg-[#FEF2F2] transition cursor-pointer"
                                                        title="Hapus User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="!user.can_manage">
                                            <span class="text-[11.5px] text-[#94A3B8] italic" x-text="user.is_self ? 'Akun Anda' : 'Terproteksi'"></span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredUsers.length === 0" class="text-center py-12 px-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center mx-auto mb-3 text-[#94A3B8]">
                        <svg class="w-6 h-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-[14px] font-bold text-[#1E293B]">Tidak Ada Data Pengguna</h4>
                    <p class="text-[12.5px] text-[#64748B] mt-1">Tidak ada data pengguna yang sesuai dengan kriteria pencarian dan filter.</p>
                </div>

                {{-- Pagination Footer --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 py-3.5 border-t border-[#E2E8F0] bg-[#FFFFFF] gap-3">
                    <span class="text-[12px] text-[#64748B]">
                        Menampilkan
                        <strong class="text-[#1E293B]" x-text="filteredUsers.length ? (startIndex + 1) : 0"></strong> &ndash;
                        <strong class="text-[#1E293B]" x-text="Math.min(endIndex, filteredUsers.length)"></strong>
                        dari <strong class="text-[#1E293B]" x-text="filteredUsers.length"></strong> pengguna
                    </span>

                    <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                        {{-- Previous Button --}}
                        <button type="button"
                                @click="if(currentPage > 1) currentPage--"
                                :disabled="currentPage <= 1"
                                class="w-8 h-8 rounded-lg border border-[#CBD5E1] bg-white text-[#334155] flex items-center justify-center text-xs transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#F8FAFC]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        {{-- Page Number Buttons --}}
                        <template x-for="p in pageNumbers" :key="p">
                            <button type="button"
                                    @click="currentPage = p"
                                    x-text="p"
                                    class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition cursor-pointer"
                                    :class="currentPage === p ? 'btn-ipnet-gradient shadow-xs' : 'bg-white text-[#1E293B] border border-[#CBD5E1] hover:bg-[#F8FAFC]'"></button>
                        </template>

                        {{-- Next Button --}}
                        <button type="button"
                                @click="if(currentPage < totalPages) currentPage++"
                                :disabled="currentPage >= totalPages"
                                class="w-8 h-8 rounded-lg border border-[#CBD5E1] bg-white text-[#334155] flex items-center justify-center text-xs transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed hover:bg-[#F8FAFC]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- MODAL FORM (TAMBAH / EDIT PENGGUNA)                          -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="modalOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
                     @click.self="modalOpen = false">

                    <div class="bg-white rounded-2xl w-[560px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(15,23,42,0.25)] text-left animate-fade-in-up border border-[#E2E8F0]">

                        {{-- Modal Header --}}
                        <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] flex-shrink-0">
                            <div>
                                <h3 class="font-display text-[16px] font-bold text-[#1E293B] m-0" x-text="modalTitle"></h3>
                                <p class="text-[12px] text-[#64748B] mt-0.5">Isi informasi akun pengguna dan divisi teknis terkait.</p>
                            </div>
                            <button type="button" @click="modalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Form Body --}}
                        <form @submit.prevent="saveUser" class="flex flex-col flex-1 overflow-hidden m-0">
                            <div class="p-6 overflow-y-auto space-y-4 flex-1 text-[13px]">
                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                                    <input type="text" x-model="form.name" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="Contoh: Budi Santoso" required>
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Email</label>
                                    <input type="email" x-model="form.email" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="nama@ipnet.id" required>
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Nomor Handphone</label>
                                    <input type="text" x-model="form.phone" placeholder="0812xxxxxxxx" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Role / Wewenang</label>
                                        <select x-model="form.role" @change="autoFillPosition()" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" required>
                                            @foreach($creatableRoles as $cr)
                                            <option value="{{ $cr }}">{{ $cr === 'Team Leader' ? 'Team Leader (Leader Divisi)' : ($cr === 'Lead Maintenance' ? 'Lead Maintenance / Helpdesk' : ($cr === 'Maintenance' ? 'Maintenance / Helpdesk Staff' : $cr)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Status Akun</label>
                                        <select x-model="form.status" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" required>
                                            <option value="Active">Active (Dapat Login)</option>
                                            <option value="Inactive">Inactive (Dinonaktifkan)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Penempatan Divisi & Level --}}
                                <div class="p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl space-y-3">
                                    <p class="m-0 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Penempatan Divisi & Level Teknis</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div>
                                            <label class="block text-[11.5px] font-semibold text-[#64748B] mb-1">Divisi</label>
                                            <select x-model="form.division_id"
                                                    @change="filterTeams(); autoFillPosition();"
                                                    class="w-full py-2 px-3 text-[12.5px] bg-white border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#8F0A0D] transition text-[#1E293B]"
                                                    {{ !$isGlobal && count($divisions) === 1 ? 'disabled' : '' }}>
                                                @if($isGlobal)
                                                <option value="">-- Tanpa Divisi (Global) --</option>
                                                @endif
                                                @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11.5px] font-semibold text-[#64748B] mb-1">Level Teknis</label>
                                            <select x-model="form.level"
                                                    @change="autoFillPosition()"
                                                    class="w-full py-2 px-3 text-[12.5px] bg-white border border-[#CBD5E1] rounded-lg focus:outline-none focus:border-[#8F0A0D] transition text-[#1E293B]">
                                                <option value="">-- Tanpa Level --</option>
                                                <option value="L1">L1 (Junior / Implementor)</option>
                                                <option value="L2">L2 (Senior / Specialist)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="!editing || form.password">
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">
                                        Password <span x-show="editing" class="text-[#94A3B8] font-normal lowercase">(kosongkan jika tidak ingin diubah)</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                               x-model="form.password"
                                               placeholder="Minimal 6 karakter"
                                               class="w-full py-2.5 pl-3.5 pr-11 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]"
                                               :required="!editing">
                                        <button type="button"
                                                @click="showPassword = !showPassword"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[#94A3B8] hover:text-[#1E293B] transition p-1 cursor-pointer focus:outline-none"
                                                tabindex="-1"
                                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'">
                                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-end gap-3 flex-shrink-0">
                                <button type="button" @click="modalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[13px] hover:bg-[#F1F5F9] transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-[13px] transition shadow-md cursor-pointer">
                                    Simpan Pengguna
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <!-- ============================================================ -->
            <!-- MODAL LIHAT MULTI-SERTIFIKASI                                -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="viewCertModal"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
                     @click.self="viewCertModal = false">

                    <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[90vh] flex flex-col shadow-[0_20px_60px_rgba(15,23,42,0.25)] overflow-hidden animate-fade-in-up border border-[#E2E8F0]">

                        <!-- Modal Header -->
                        <div class="flex items-center justify-between p-5 bg-white border-b border-[#E2E8F0] flex-shrink-0">
                            <div>
                                <h3 class="m-0 font-display text-[16px] font-bold text-[#1E293B]">
                                    Dokumen Sertifikasi: <span x-text="viewingUser.name" class="text-[#8F0A0D]"></span>
                                </h3>
                                <p class="m-0 text-[12px] text-[#64748B] mt-0.5" x-text="(viewingUser.certifications ? viewingUser.certifications.length : 0) + ' dokumen sertifikat terdaftar'"></p>
                            </div>
                            <button @click="viewCertModal = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 overflow-y-auto flex-1 space-y-4">

                            <!-- TAB PILIHAN SERTIFIKAT -->
                            <div class="p-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl" x-show="viewingUser.certifications && viewingUser.certifications.length > 0">
                                <div class="flex items-center justify-between mb-2.5">
                                    <p class="m-0 text-[11.5px] font-bold text-[#475569] uppercase tracking-wider">
                                        Pilih Sertifikat untuk Ditinjau
                                    </p>
                                    <span class="text-[11px] font-medium text-[#94A3B8]">
                                        Klik untuk beralih dokumen
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="cert in (viewingUser.certifications || [])" :key="cert.id">
                                        <button type="button"
                                                @click="selectCert(cert)"
                                                class="cert-tab-pill"
                                                :class="selectedCert && selectedCert.id === cert.id ? 'active' : ''">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span x-text="cert.name"></span>
                                            <span class="cert-status-dot"
                                                  :style="'background:' + (cert.status === 'approved' ? '#16A34A' : (cert.status === 'rejected' ? '#8F0A0D' : '#D97706'))"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- DETAIL SERTIFIKAT YANG TERPILIH -->
                            <template x-if="selectedCert">
                                <div>
                                    <div class="grid grid-cols-2 gap-3 mb-4 p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl text-[13px]">
                                        <div>
                                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Nama Sertifikasi</p>
                                            <p class="font-bold text-[#1E293B]" x-text="selectedCert.name"></p>
                                        </div>
                                        <div>
                                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Status Verifikasi</p>
                                            <div x-html="getCertificationStatusBadge(selectedCert.status)"></div>
                                        </div>
                                        <div class="col-span-2 pt-2 border-t border-[#E2E8F0]">
                                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Waktu Upload</p>
                                            <p class="text-[12px] text-[#475569]" x-text="selectedCert.uploaded_at || '-'"></p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Pratinjau Dokumen</p>
                                        <div class="border border-[#CBD5E1] rounded-xl p-3 bg-[#F8FAFC] text-center min-h-[240px] flex items-center justify-center overflow-hidden">
                                            <template x-if="selectedCert.is_pdf">
                                                <iframe :src="'/certification-file/' + selectedCert.id"
                                                        class="w-full h-[450px] border-0 rounded-lg bg-white"></iframe>
                                            </template>
                                            <template x-if="!selectedCert.is_pdf">
                                                <div class="w-full">
                                                    <img :src="'/certification-file/' + selectedCert.id"
                                                         alt="Pratinjau Sertifikasi"
                                                         x-on:error="certImageError = true"
                                                         x-show="!certImageError"
                                                         class="w-full max-h-[480px] object-contain rounded-lg shadow-sm mx-auto">
                                                    <div x-show="certImageError" class="text-[#64748B] text-[13px] text-center py-6 px-4">
                                                        <svg class="w-8 h-8 text-[#8F0A0D] mx-auto mb-2 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                        </svg>
                                                        <strong class="block text-[14px] text-[#1E293B] mb-1">File Tidak Ditemukan</strong>
                                                        <span class="text-[12px]">File sertifikasi fisik belum diunggah atau tidak ditemukan di server.</span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Action Buttons per Selected Certificate -->
                                    <div class="pt-4 border-t border-[#E2E8F0]">
                                        {{-- JIKA STATUS MENUNGGU (PENDING) --}}
                                        <template x-if="selectedCert.status === 'pending'">
                                            <div>
                                                {{-- Team Leader: Memiliki Tombol Verifikasi --}}
                                                <template x-if="canVerify">
                                                    <div class="flex flex-wrap gap-2.5 w-full">
                                                        <button @click="approveCertification(selectedCert)"
                                                                class="flex-1 min-w-[160px] justify-center bg-[#16A34A] hover:bg-[#15803D] text-white py-2.5 px-4 rounded-xl font-bold text-[13px] cursor-pointer flex items-center gap-2 transition shadow-xs">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Setujui Sertifikat Ini
                                                        </button>
                                                        <button @click="rejectCertification(selectedCert)"
                                                                class="flex-1 min-w-[160px] justify-center btn-ipnet-gradient py-2.5 px-4 rounded-xl font-bold text-[13px] cursor-pointer flex items-center gap-2 transition shadow-xs">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                            Tolak & Hapus
                                                        </button>
                                                    </div>
                                                </template>

                                                {{-- Direktur / Group Leader: Mode Lihat / Monitoring Saja --}}
                                                <template x-if="!canVerify">
                                                    <div class="flex items-center justify-between p-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl flex-wrap gap-3">
                                                        <span class="text-[12px] text-[#64748B] flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-[#64748B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                            Verifikasi sertifikat ini dilakukan langsung oleh Team Leader divisi terkait.
                                                        </span>
                                                        <a :href="'/certification-file/' + selectedCert.id"
                                                           download
                                                           target="_blank"
                                                           class="inline-flex items-center gap-1.5 bg-[#1E293B] text-white py-2 px-3.5 rounded-xl font-bold text-[12px] text-decoration-none shadow-xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            Download Dokumen
                                                        </a>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- JIKA STATUS SUDAH DISETUJUI (APPROVED) --}}
                                        <template x-if="selectedCert.status === 'approved'">
                                            <div class="flex flex-wrap gap-2.5 w-full">
                                                <a :href="'/certification-file/' + selectedCert.id"
                                                   download
                                                   target="_blank"
                                                   class="flex-1 min-w-[160px] inline-flex items-center justify-center gap-2 bg-[#1E293B] hover:bg-[#334155] text-white py-2.5 px-4 rounded-xl font-bold text-[13px] text-decoration-none transition shadow-xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    Download Dokumen
                                                </a>
                                                <template x-if="canVerify">
                                                    <button @click="deleteCertification(selectedCert)"
                                                            class="flex-1 min-w-[160px] justify-center bg-[#FEF2F2] hover:bg-[#8F0A0D] text-[#8F0A0D] hover:text-white border border-[#FECACA] hover:border-[#8F0A0D] py-2.5 px-4 rounded-xl font-bold text-[13px] cursor-pointer flex items-center gap-2 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Hapus Sertifikat Ini
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedCert">
                                <div class="py-10 px-4 text-center text-[#64748B]">
                                    <p class="text-[13px] mb-3">Pengguna ini belum memiliki sertifikat yang diunggah.</p>
                                    <button type="button" @click="viewCertModal = false"
                                            class="py-2 px-4 rounded-xl border border-[#CBD5E1] bg-white text-[12px] font-bold text-[#1E293B] hover:bg-[#F8FAFC] cursor-pointer">
                                        Tutup
                                    </button>
                                </div>
                            </template>

                        </div>
                    </div>
                </div>
            </template>

            <!-- ========================================================== -->
            <!-- MODAL KONFIRMASI HAPUS                                     -->
            <!-- ========================================================== -->
            <template x-teleport="body">
                <div x-show="confirmModalOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
                     @click.self="confirmModalOpen = false"
                     @keydown.escape.window="confirmModalOpen = false">

                    <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] animate-fade-in-up border border-[#E2E8F0]">
                        <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5" x-text="confirmModalData.title || 'Yakin Hapus Pengguna?'"></h3>
                        <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words" x-text="confirmModalData.message"></p>

                        <div class="flex gap-2.5">
                            <button type="button"
                                    @click="confirmModalOpen = false"
                                    class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button"
                                    @click="if (confirmModalData.onConfirm) { confirmModalData.onConfirm(); } confirmModalOpen = false;"
                                    class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('usersManager', function() {
            return {
                users: @json($users),
                allTeams: @json($teams),
                canVerify: {{ auth()->user()->hasAnyRole(['Group Leader', 'Lead Divisi', 'Team Leader', 'Lead Maintenance', 'Lead Engineer']) ? 'true' : 'false' }},
                currentUserRole: '{{ auth()->user()->getRoleNames()->first() ?? '' }}',
                currentUserId: {{ auth()->id() }},
                search: '',
                roleFilter: 'Semua',
                divisionFilter: 'Semua',
                statusFilter: 'Semua',
                currentPage: 1,
                perPage: 10,
                modalOpen: false,
                viewCertModal: false,
                editing: false,
                showPassword: false,
                allDivisions: @json($divisions),
                confirmModalOpen: false,
                confirmModalData: {
                    title: '',
                    message: '',
                    onConfirm: null
                },
                certImageError: false,
                viewingUser: {},
                selectedCert: null,
                availableTeams: [],
                form: {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    role: 'Engineer',
                    status: 'Active',
                    position: '',
                    password: '',
                    division_id: '',
                    team_id: '',
                    level: '',
                    certification_file_name: '',
                    certification_file: null,
                },

                init() {
                    console.log('Users Manager initialized!');
                },

                get filteredUsers() {
                    return this.users.filter(u => {
                        const matchSearch = (u.name || '').toLowerCase().includes(this.search.toLowerCase()) ||
                                            (u.email || '').toLowerCase().includes(this.search.toLowerCase()) ||
                                            (u.phone || '').toLowerCase().includes(this.search.toLowerCase());

                        let matchRole = false;
                        if (this.roleFilter === 'Semua') {
                            matchRole = true;
                        } else if (this.roleFilter === 'Engineer L1') {
                            matchRole = (u.role_name === 'Engineer' && u.level === 'L1');
                        } else if (this.roleFilter === 'Engineer L2') {
                            matchRole = (u.role_name === 'Engineer' && u.level === 'L2');
                        } else if (this.roleFilter === 'Team Leader') {
                            matchRole = (u.role_name === 'Team Leader');
                        } else if (this.roleFilter === 'Group Leader') {
                            matchRole = (u.role_name === 'Group Leader');
                        } else if (this.roleFilter === 'Direktur') {
                            matchRole = (u.role_name === 'Direktur' || u.role_name === 'HD / Direktur');
                        } else {
                            matchRole = (u.role_name === this.roleFilter);
                        }

                        const matchDivision = this.divisionFilter === 'Semua' || u.division_name === this.divisionFilter;
                        const matchStatus = this.statusFilter === 'Semua' || u.status === this.statusFilter;
                        return matchSearch && matchRole && matchDivision && matchStatus;
                    });
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredUsers.length / this.perPage));
                },

                get startIndex() {
                    return (this.currentPage - 1) * this.perPage;
                },

                get endIndex() {
                    return Math.min(this.startIndex + this.perPage, this.filteredUsers.length);
                },

                get paginatedUsers() {
                    return this.filteredUsers.slice(this.startIndex, this.endIndex);
                },

                get pageNumbers() {
                    return Array.from({ length: this.totalPages }, (_, i) => i + 1);
                },

                get modalTitle() {
                    return this.editing ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru';
                },

                openModal(user = null) {
                    this.showPassword = false;
                    if (user) {
                        this.editing = true;
                        this.form = {
                            id: user.id,
                            name: user.name,
                            email: user.email,
                            phone: user.phone || '',
                            role: user.role_name,
                            status: user.status,
                            position: user.position || '',
                            password: '',
                            division_id: user.division_id || '',
                            team_id: user.team_id || '',
                            level: user.level || '',
                            certification_file_name: '',
                            certification_file: null,
                        };
                        this.filterTeams();
                    } else {
                        this.editing = false;
                        this.form = {
                            id: null,
                            name: '',
                            email: '',
                            phone: '',
                            role: '{{ $creatableRoles[0] ?? "Engineer" }}',
                            status: 'Active',
                            position: '',
                            password: '',
                            division_id: '{{ $userDivisionId ?? "" }}',
                            team_id: '',
                            level: 'L1',
                            certification_file_name: '',
                            certification_file: null,
                        };
                        this.filterTeams();
                        this.autoFillPosition();
                    }
                    this.modalOpen = true;
                },

                autoFillPosition() {
                    if (this.editing && this.form.position && this.form.position.trim()) return;
                    let divName = '';
                    const div = this.allDivisions.find(d => String(d.id) === String(this.form.division_id));
                    if (div) {
                        divName = div.name.replace('Divisi ', '').trim();
                    }
                    if (this.form.role === 'Direktur') {
                        this.form.position = 'Direktur Utama';
                    } else if (this.form.role === 'Group Leader') {
                        this.form.position = 'Group Leader';
                    } else if (this.form.role === 'Team Leader') {
                        this.form.position = (divName ? divName + ' Leader' : 'Team Leader');
                    } else if (this.form.role === 'Engineer') {
                        const lvl = this.form.level ? this.form.level + ' ' : '';
                        this.form.position = lvl + (divName ? divName + ' ' : '') + 'Engineer';
                    }
                },

                filterTeams() {
                    if (!this.form.division_id) {
                        this.availableTeams = [];
                        return;
                    }
                    this.availableTeams = this.allTeams.filter(
                        t => String(t.division_id) === String(this.form.division_id)
                    );
                },

                canManageUser(target) {
                    if (!target) return false;
                    if (this.currentUserRole === 'Direktur' || this.currentUserRole === 'HD / Direktur') {
                        return true;
                    }
                    if (this.currentUserRole === 'Group Leader' || this.currentUserRole === 'Lead Divisi') {
                        if (target.role_name === 'Direktur' || target.role_name === 'HD / Direktur') {
                            return false;
                        }
                        return true;
                    }
                    if (this.currentUserRole === 'Team Leader' || this.currentUserRole === 'Lead Maintenance' || this.currentUserRole === 'Lead Engineer') {
                        if (target.id === this.currentUserId) {
                            return true;
                        }
                        return ['Engineer', 'Maintenance', 'Engineer L1', 'Engineer L2'].includes(target.role_name);
                    }
                    return false;
                },

                editUser(user) {
                    if (!this.canManageUser(user)) return;
                    this.openModal(user);
                },

                async saveUser() {
                    try {
                        const url = this.editing ? `/users/${this.form.id}` : '/users';

                        const formData = new FormData();
                        formData.append('name', this.form.name || '');
                        formData.append('email', this.form.email || '');
                        formData.append('phone', this.form.phone || '');
                        formData.append('position', this.form.position || '');
                        formData.append('role', this.form.role || '');
                        formData.append('status', this.form.status || 'Active');
                        if (this.form.division_id) formData.append('division_id', this.form.division_id);
                        if (this.form.team_id) formData.append('team_id', this.form.team_id);
                        if (this.form.level) formData.append('level', this.form.level);

                        if (this.editing) {
                            formData.append('_method', 'PUT');
                        }

                        if (this.form.password) {
                            formData.append('password', this.form.password);
                        }

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: formData
                        });

                        if (response.ok) {
                            const data = await response.json();

                            if (this.editing) {
                                const index = this.users.findIndex(u => u.id === this.form.id);
                                this.users[index] = data;
                            } else {
                                this.users.push(data);
                            }
                            this.users = JSON.parse(JSON.stringify(this.users));
                            this.modalOpen = false;
                            this.showToast('Data pengguna berhasil ' + (this.editing ? 'diperbarui' : 'ditambahkan') + '!');
                        } else {
                            const error = await response.json();
                            this.showToast('Error: ' + (error.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error saving user:', error);
                        this.showToast('Terjadi kesalahan saat menyimpan data pengguna.');
                    }
                },

                deleteUser(user) {
                    if (user.id === {{ auth()->id() }}) {
                        this.showToast('Anda tidak dapat menghapus akun Anda sendiri!');
                        return;
                    }

                    this.confirmModalData = {
                        title: 'Yakin Hapus Pengguna?',
                        message: `Data pengguna "${user.name}" akan dihapus secara permanen dari sistem.`,
                        onConfirm: async () => {
                            try {
                                const response = await fetch(`/users/${user.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                });

                                if (response.ok) {
                                    this.users = this.users.filter(u => u.id !== user.id);
                                    this.showToast('Pengguna berhasil dihapus!');
                                } else {
                                    const err = await response.json();
                                    this.showToast('Gagal menghapus: ' + (err.message || 'Terjadi kesalahan'));
                                }
                            } catch (error) {
                                console.error('Error deleting user:', error);
                                this.showToast('Terjadi kesalahan saat menghapus data pengguna.');
                            }
                        }
                    };
                    this.confirmModalOpen = true;
                },

                async toggleStatus(user) {
                    try {
                        const response = await fetch(`/users/${user.id}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            const index = this.users.findIndex(u => u.id === user.id);
                            this.users[index] = data;
                            this.users = JSON.parse(JSON.stringify(this.users));
                            this.showToast(`Status user berhasil diubah menjadi ${data.status}!`);
                        }
                    } catch (error) {
                        console.error('Error toggling user status:', error);
                        this.showToast('Terjadi kesalahan saat mengubah status user.');
                    }
                },

                viewCertification(user) {
                    this.viewingUser = user;
                    this.selectedCert = (user.certifications && user.certifications.length > 0) ? user.certifications[0] : null;
                    this.certImageError = false;
                    this.viewCertModal = true;
                },

                selectCert(cert) {
                    this.selectedCert = cert;
                    this.certImageError = false;
                },

                async approveCertification(cert) {
                    if (!cert || !cert.id) {
                        this.showToast('Pilih sertifikat terlebih dahulu.');
                        return;
                    }
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/certifications/${cert.id}/approve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            cert.status = 'approved';
                            if (this.selectedCert && this.selectedCert.id === cert.id) {
                                this.selectedCert.status = 'approved';
                            }

                            if (data.user) {
                                const index = this.users.findIndex(u => u.id === data.user.id);
                                if (index !== -1) {
                                    this.users[index] = data.user;
                                    this.users = JSON.parse(JSON.stringify(this.users));
                                    this.viewingUser = data.user;
                                }
                            }

                            this.viewCertModal = false;
                            this.showToast(`Sertifikasi "${cert.name}" berhasil disetujui!`);
                        } else {
                            this.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error approving certification:', error);
                        this.showToast('Terjadi kesalahan saat menyetujui sertifikasi.');
                    }
                },

                async rejectCertification(cert) {
                    if (!cert || !cert.id) {
                        this.showToast('Pilih sertifikat terlebih dahulu.');
                        return;
                    }
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const response = await fetch(`/certifications/${cert.id}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            cert.status = 'rejected';
                            if (this.selectedCert && this.selectedCert.id === cert.id) {
                                this.selectedCert.status = 'rejected';
                            }

                            if (data.user) {
                                const index = this.users.findIndex(u => u.id === data.user.id);
                                if (index !== -1) {
                                    this.users[index] = data.user;
                                    this.users = JSON.parse(JSON.stringify(this.users));
                                    this.viewingUser = data.user;
                                }
                            }

                            this.viewCertModal = false;
                            this.showToast(`Sertifikasi "${cert.name}" berhasil ditolak dan dihapus!`);
                        } else {
                            this.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error rejecting certification:', error);
                        this.showToast('Terjadi kesalahan saat menolak sertifikasi.');
                    }
                },

                deleteCertification(cert) {
                    if (!cert || !cert.id) {
                        this.showToast('Pilih sertifikat terlebih dahulu.');
                        return;
                    }

                    this.confirmModalData = {
                        title: 'Yakin Hapus Sertifikat?',
                        message: `Dokumen sertifikat "${cert.name}" akan dihapus secara permanen.`,
                        onConfirm: async () => {
                            try {
                                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                                const response = await fetch(`/certifications/${cert.id}/reject`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token
                                    }
                                });

                                const data = await response.json();

                                if (response.ok) {
                                    if (data.user) {
                                        const index = this.users.findIndex(u => u.id === data.user.id);
                                        if (index !== -1) {
                                            this.users[index] = data.user;
                                            this.users = JSON.parse(JSON.stringify(this.users));
                                            this.viewingUser = data.user;
                                            this.selectedCert = null;
                                        }
                                    }
                                    this.showToast('Sertifikat berhasil dihapus.');
                                } else {
                                    this.showToast('Gagal menghapus sertifikat.');
                                }
                            } catch (error) {
                                console.error('Error deleting certification:', error);
                                this.showToast('Terjadi kesalahan saat menghapus sertifikat.');
                            }
                        }
                    };
                    this.confirmModalOpen = true;
                },

                getRoleBadge(user) {
                    const role = (typeof user === 'object' && user !== null) ? user.role_name : user;
                    const level = (typeof user === 'object' && user !== null) ? user.level : null;
                    const roleKey = (role === 'Engineer' && level) ? ('Engineer ' + level) : role;
                    const styles = {
                        'Direktur':         { bg: '#FEF2F2', fg: '#8F0A0D', border: '#FECACA', dot: '#8F0A0D' },
                        'HD / Direktur':    { bg: '#FEF2F2', fg: '#8F0A0D', border: '#FECACA', dot: '#8F0A0D' },
                        'Group Leader':     { bg: '#EEF2FF', fg: '#4338CA', border: '#C7D2FE', dot: '#4F46E5' },
                        'Team Leader':      { bg: '#FFFBEB', fg: '#B45309', border: '#FDE68A', dot: '#D97706' },
                        'Lead Engineer':    { bg: '#FFFBEB', fg: '#B45309', border: '#FDE68A', dot: '#D97706' },
                        'Lead Maintenance': { bg: '#F0FDFA', fg: '#0F766E', border: '#CCFBF1', dot: '#14B8A6' },
                        'Maintenance':      { bg: '#F0FDFA', fg: '#0F766E', border: '#CCFBF1', dot: '#14B8A6' },
                        'Engineer':         { bg: '#F0FDF4', fg: '#15803D', border: '#BBF7D0', dot: '#16A34A' },
                        'Engineer L1':      { bg: '#F0FDF4', fg: '#15803D', border: '#BBF7D0', dot: '#16A34A' },
                        'Engineer L2':      { bg: '#ECFDF5', fg: '#047857', border: '#A7F3D0', dot: '#059669' },
                    };
                    const s = styles[roleKey] || styles[role] || { bg: '#F1F5F9', fg: '#475569', border: '#E2E8F0', dot: '#64748B' };
                    const label = (role === 'Engineer' && level) ? ('Engineer ' + level) : role;
                    return `<span style="background: ${s.bg}; color: ${s.fg}; border: 1px solid ${s.border}; font-size: 11.5px; font-weight: 700; padding: 3px 9px 3px 7px; border-radius: 9999px; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                                ${label}
                            </span>`;
                },

                getStatusBadge(status) {
                    const styles = {
                        'Active': { bg: '#F0FDF4', fg: '#16A34A', border: '#BBF7D0', dot: '#16A34A' },
                        'Inactive': { bg: '#F1F5F9', fg: '#64748B', border: '#E2E8F0', dot: '#94A3B8' }
                    };
                    const s = styles[status] || styles['Inactive'];
                    return `<span style="background: ${s.bg}; color: ${s.fg}; border: 1px solid ${s.border}; font-size: 11.5px; font-weight: 700; padding: 3px 9px 3px 7px; border-radius: 9999px; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                                ${status}
                            </span>`;
                },

                getCertificationStatusBadge(status) {
                    const styles = {
                        'approved': { bg: '#F0FDF4', fg: '#16A34A', border: '#BBF7D0', dot: '#16A34A', text: 'Disetujui' },
                        'pending': { bg: '#FFFBEB', fg: '#D97706', border: '#FDE68A', dot: '#D97706', text: 'Menunggu' },
                        'rejected': { bg: '#FEF2F2', fg: '#8F0A0D', border: '#FECACA', dot: '#8F0A0D', text: 'Ditolak' }
                    };
                    const s = styles[status] || styles['pending'];
                    return `<span style="background: ${s.bg}; color: ${s.fg}; border: 1px solid ${s.border}; font-size: 11.5px; font-weight: 700; padding: 3px 9px 3px 7px; border-radius: 9999px; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                                ${s.text}
                            </span>`;
                },

                showToast(message) {
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#1E293B; color:white; padding:12px 20px; border-radius:12px; box-shadow:0 16px 40px rgba(15,23,42,0.25); font-size:13px; font-weight:600; animation:fadeUpStagger 0.25s ease; z-index:999999; display:flex; align-items:center; gap:8px; border:1px solid rgba(255,255,255,0.1);';
                    toast.innerHTML = `<svg style="width:16px;height:16px;color:#16A34A;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span>${message}</span>`;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }, 3200);
                }
            };
        });
    });
</script>
@endpush
@endsection