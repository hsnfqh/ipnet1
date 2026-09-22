@extends('layouts.app')

@section('title', 'View Vendor - ' . $vendor->name)

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
    .vendor-stat-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all .15s ease;
    }
    .vendor-stat-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="vendorShowPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Vendor'])

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
                <a href="{{ route('vendors.index') }}" class="hover:text-gray-800 transition">Vendor</a>
                <span>&gt;</span>
                <span class="text-gray-700">{{ $vendor->name }}</span>
                <span>&gt;</span>
                <span class="text-gray-900 font-bold">Edit</span>
            </div>

            {{-- 2. TITLE WITH ICON & DELETE ACTION (Identik Client) --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-200 shadow-2xs">
                        <svg class="w-4.5 h-4.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-[#1E293B] tracking-tight">View Vendor</h1>
                </div>

                <button type="button" @click="showDeleteModal = true" 
                        class="px-3.5 py-1.5 rounded-lg border border-red-200 bg-red-50 hover:bg-red-100 text-[#8F0A0D] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Vendor</span>
                </button>
            </div>

            {{-- 3. MAIN CONTENT: 2-COLUMN BALANCED GRID (Identik Client) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- ══ LEFT COLUMN: VENDOR FORM CARD (col-span-8) ══ --}}
                <div class="lg:col-span-8">
                    <div class="ipnet-card p-6 sm:p-8">
                        <form action="{{ route('vendors.update', $vendor->id) }}" method="POST" class="space-y-6" @submit="prepareSubmit()">
                            @csrf
                            @method('PUT')

                            {{-- Hidden fields synced from dynamic Alpine departments/PICs --}}
                            <input type="hidden" name="department" :value="primaryDepartment">
                            <input type="hidden" name="channel_manager" :value="primaryChannelManager">
                            <input type="hidden" name="email" :value="primaryEmail">
                            <input type="hidden" name="phone" :value="primaryPhone">
                            <input type="hidden" name="product_category" :value="primaryPosition">

                            {{-- NAME --}}
                            <div>
                                <label class="form-label-bold">NAME</label>
                                <input type="text" name="name" value="{{ old('name', $vendor->name) }}" required
                                       class="form-input-clean font-semibold"
                                       placeholder="Nama Perusahaan Vendor / Distributor...">
                            </div>

                            {{-- ADDRESS 1 --}}
                            <div>
                                <label class="form-label-bold">ADDRESS 1</label>
                                <textarea name="address" rows="3"
                                          class="form-input-clean leading-relaxed resize-y font-normal"
                                          placeholder="Masukkan alamat utama vendor...">{{ old('address', $vendor->address ?: 'Centennial Tower 12/I - Jl. Gatot Subroto Kav 24-25 Jakarta 12930') }}</textarea>

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
                                              placeholder="Alamat sekunder / cabang / catatan tambahan...">{{ old('notes', $vendor->notes) }}</textarea>
                                </div>
                            </div>

                            {{-- DEPARTMENTS & PIC CONTAINER (Matching User Request & Screenshot) --}}
                            <div class="space-y-5 pt-2">
                                <template x-for="(dept, deptIdx) in departments" :key="deptIdx">
                                    <div class="p-4 sm:p-5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4">
                                        
                                        {{-- Department Header --}}
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex-1">
                                                <label class="form-label-bold !mb-1.5" x-text="'DEPARTMENT ' + (deptIdx + 1)"></label>
                                                <input type="text" x-model="dept.name"
                                                       class="form-input-clean font-semibold !bg-white text-xs"
                                                       placeholder="Nama Departemen (cth: DEPT 01, Extreme, Cisco...)">
                                            </div>
                                            <template x-if="departments.length > 1">
                                                <button type="button" @click="removeDepartment(deptIdx)" 
                                                        class="mt-5 p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer"
                                                        title="Hapus departemen ini">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        {{-- PIC List for this Department --}}
                                        <div class="space-y-3">
                                            <template x-for="(pic, picIdx) in dept.pics" :key="picIdx">
                                                <div class="p-3.5 rounded-xl bg-white border border-[#E2E8F0] shadow-2xs space-y-2">
                                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                                        <div class="sm:col-span-3">
                                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" x-text="'PIC NAME ' + (picIdx + 1)"></label>
                                                            <input type="text" x-model="pic.name"
                                                                   class="form-input-clean text-xs font-semibold"
                                                                   placeholder="Nama PIC...">
                                                        </div>
                                                        <div class="sm:col-span-3">
                                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" x-text="'PIC EMAIL ' + (picIdx + 1)"></label>
                                                            <input type="email" x-model="pic.email"
                                                                   class="form-input-clean text-xs font-normal"
                                                                   placeholder="Email PIC...">
                                                        </div>
                                                        <div class="sm:col-span-3">
                                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" x-text="'PIC PHONE ' + (picIdx + 1)"></label>
                                                            <input type="text" x-model="pic.phone"
                                                                   class="form-input-clean text-xs font-normal"
                                                                   placeholder="No. Telepon / HP...">
                                                        </div>
                                                        <div class="sm:col-span-2">
                                                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1" x-text="'PIC POSITION ' + (picIdx + 1)"></label>
                                                            <input type="text" x-model="pic.position"
                                                                   class="form-input-clean text-xs font-normal"
                                                                   placeholder="Jabatan...">
                                                        </div>
                                                        <div class="sm:col-span-1 flex justify-center pb-1">
                                                            <template x-if="dept.pics.length > 1">
                                                                <button type="button" @click="removePic(deptIdx, picIdx)"
                                                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition cursor-pointer"
                                                                        title="Hapus PIC ini">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- + CREATE PIC BUTTON --}}
                                        <div>
                                            <button type="button" @click="addPic(deptIdx)"
                                                    class="w-full py-2.5 rounded-xl border border-dashed border-[#CBD5E1] hover:border-[#8F0A0D] bg-white hover:bg-red-50/40 text-[#8F0A0D] text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ CREATE PIC</span>
                                                <span x-text="dept.name ? dept.name.toUpperCase() : ('DEPT ' + (deptIdx + 1))"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                {{-- + CREATE DEPARTMENT BUTTON --}}
                                <div class="pt-1">
                                    <button type="button" @click="addDepartment()"
                                            class="text-xs font-bold text-[#8F0A0D] hover:text-[#73080A] hover:underline cursor-pointer inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ CREATE DEPARTMENT</span>
                                    </button>
                                </div>
                            </div>

                            {{-- SAVE & CANCEL BUTTONS (Identik Client) --}}
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                                <a href="{{ route('vendors.index') }}" 
                                   class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                    Cancel
                                </a>

                                <button type="submit" 
                                        class="btn-ipnet-gradient px-7 py-2.5 rounded-xl font-bold text-xs shadow-md hover:shadow-lg transition cursor-pointer">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- ══ RIGHT COLUMN: VENDOR SUMMARY & SYSTEM INFO (col-span-4) ══ --}}
                <div class="lg:col-span-4 space-y-6">

                    {{-- Vendor Summary Card --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-bold text-gray-900">Vendor Overview</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                Official Partner
                            </span>
                        </div>

                        <div class="space-y-3">
                            <div class="vendor-stat-card">
                                <div class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Perusahaan</div>
                                <div class="text-sm font-bold text-[#1E293B]">{{ $vendor->name }}</div>
                            </div>

                            <div class="vendor-stat-card">
                                <div class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Kontak PIC Utama</div>
                                <div class="text-xs font-bold text-[#1E293B]">{{ $vendor->channel_manager ?: '-' }}</div>
                                @if($vendor->email)
                                    <a href="mailto:{{ $vendor->email }}" class="text-[11px] text-[#8F0A0D] hover:underline block mt-0.5">
                                        {{ $vendor->email }}
                                    </a>
                                @endif
                                @if($vendor->phone)
                                    <div class="text-[11px] text-[#64748B] mt-0.5 font-mono">
                                        {{ $vendor->phone }}
                                    </div>
                                @endif
                            </div>

                            @if($vendor->department)
                                <div class="vendor-stat-card">
                                    <div class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Departemen / Divisi</div>
                                    <div class="text-xs font-semibold text-[#1E293B]">{{ $vendor->department }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Informasi Sistem Card --}}
                    <div class="ipnet-card p-5 space-y-3">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 pb-2">
                            Informasi Sistem
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between text-gray-600">
                                <span>ID Vendor</span>
                                <span class="font-bold text-gray-900 font-mono">#{{ $vendor->id }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Dibuat Oleh</span>
                                <span class="font-medium text-gray-800">{{ $vendor->creator->name ?? 'System' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Tanggal Dibuat</span>
                                <span class="font-medium text-gray-800">{{ $vendor->created_at ? $vendor->created_at->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Terakhir Diperbarui</span>
                                <span class="font-medium text-gray-800">{{ $vendor->updated_at ? $vendor->updated_at->format('d M Y H:i') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS (PLEK KETIPLEK LEAD ENGINEER) --}}
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
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Vendor?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words">Vendor "{{ $vendor->name }}" akan dihapus secara permanen.</p>

                <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="flex gap-2.5">
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

<script>
    function vendorShowPage() {
        return {
            showDeleteModal: false,
            showSecondaryAddress: {{ $vendor->notes ? 'true' : 'false' }},
            
            primaryDepartment: '{{ addslashes($vendor->department ?: "DEPT 01") }}',
            primaryChannelManager: '{{ addslashes($vendor->channel_manager ?: "") }}',
            primaryEmail: '{{ addslashes($vendor->email ?: "") }}',
            primaryPhone: '{{ addslashes($vendor->phone ?: "") }}',
            primaryPosition: '{{ addslashes($vendor->product_category ?: "Channel Manager") }}',

            departments: [
                {
                    name: '{{ addslashes($vendor->department ?: "DEPT 01") }}',
                    pics: [
                        {
                            name: '{{ addslashes($vendor->channel_manager ?: "Ferilda") }}',
                            email: '{{ addslashes($vendor->email ?: "ferilda@bluepowertechnology.com") }}',
                            phone: '{{ addslashes($vendor->phone ?: "081280399808") }}',
                            position: '{{ addslashes($vendor->product_category ?: "Channel Manager") }}'
                        }
                    ]
                }
            ],

            addDepartment() {
                const nextNum = this.departments.length + 1;
                this.departments.push({
                    name: 'DEPARTMENT ' + nextNum,
                    pics: [
                        {
                            name: '',
                            email: '',
                            phone: '',
                            position: ''
                        }
                    ]
                });
            },

            removeDepartment(index) {
                if (this.departments.length > 1) {
                    this.departments.splice(index, 1);
                }
            },

            addPic(deptIndex) {
                this.departments[deptIndex].pics.push({
                    name: '',
                    email: '',
                    phone: '',
                    position: ''
                });
            },

            removePic(deptIndex, picIndex) {
                if (this.departments[deptIndex].pics.length > 1) {
                    this.departments[deptIndex].pics.splice(picIndex, 1);
                }
            },

            prepareSubmit() {
                if (this.departments.length > 0) {
                    const deptNames = this.departments.map(d => d.name).filter(Boolean);
                    this.primaryDepartment = deptNames.join(', ');

                    const firstDept = this.departments[0];
                    if (firstDept && firstDept.pics && firstDept.pics.length > 0) {
                        const firstPic = firstDept.pics[0];
                        this.primaryChannelManager = firstPic.name || '';
                        this.primaryEmail = firstPic.email || '';
                        this.primaryPhone = firstPic.phone || '';
                        this.primaryPosition = firstPic.position || '';
                    }
                }
            }
        };
    }
</script>
@endsection
