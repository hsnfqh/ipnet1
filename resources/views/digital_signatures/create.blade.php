@extends('layouts.app')

@section('title', 'Buat Dokumen Digital Signature - PT IP Network Solusindo')

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
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.22) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3) !important;
    }
    .btn-ipnet-soft {
        background-color: #FEF2F2 !important;
        border: 1px solid #FECACA !important;
        color: #8F0A0D !important;
        font-weight: 700 !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-ipnet-soft:hover {
        background-color: #8F0A0D !important;
        border-color: #8F0A0D !important;
        color: #FFFFFF !important;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="documentCreator()" x-cloak>
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Buat Dokumen Digital Signature'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto anim-fade-up">

            {{-- HEADER CARD SESUAI STYLE IPNET PROJECT --}}
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-[12px] text-[#64748B] mb-1">
                            <a href="{{ route('digital_signatures.index') }}" class="hover:text-[#8F0A0D] font-semibold transition">Digital Signature</a>
                            <span>/</span>
                            <span class="text-[#1E293B] font-bold">Buat Dokumen Baru</span>
                        </div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> FORMULIR DIGITAL SIGNATURE
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Upload & Konfigurasi Dokumen</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Unggah berkas PDF, tentukan pihak penandatangan internal, dan lengkapi target PIC klien.</p>
                    </div>
                    <div class="shrink-0 self-start sm:self-auto">
                        <a href="{{ route('digital_signatures.index') }}" class="px-4 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#475569] hover:text-[#8F0A0D] hover:bg-[#FEF2F2] hover:border-[#FECACA] transition-all flex items-center gap-1.5 cursor-pointer shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Kembali ke Daftar</span>
                        </a>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1 shadow-xs">
                    <div class="font-bold flex items-center gap-2 text-[13px]">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Terdapat kesalahan pada input formulir:</span>
                    </div>
                    <ul class="list-disc pl-6 text-[12px] space-y-0.5 mt-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('digital_signatures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="category" value="BAST">

                {{-- ========================================================== --}}
                {{-- BAGIAN 1: BERKAS DOKUMEN (PDF) --}}
                {{-- Urutan: 1. Judul Dokumen, 2. Upload Dokumen PDF --}}
                {{-- Sisanya kolom input dihapus sesuai arahan user --}}
                {{-- ========================================================== --}}
                <div class="ipnet-card p-6 sm:p-7 space-y-5">
                    <div class="flex items-center gap-3 border-b border-[#F1F5F9] pb-4">
                        <div class="w-8 h-8 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#8F0A0D] flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">1</div>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Berkas Dokumen (PDF)</h3>
                            <p class="text-[12.5px] text-[#64748B]">Tuliskan judul dokumen dan unggah berkas PDF resmi yang akan ditandatangani.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- 1. JUDUL DOKUMEN (PERTAMA) --}}
                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-2">
                                Judul Dokumen <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   placeholder="Contoh: Berita Acara Serah Terima (BAST) - Pengadaan & Instalasi Switch Hub IPNET"
                                   class="w-full px-4 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        {{-- 2. UPLOAD BERKAS DOKUMEN PDF (KEDUA) --}}
                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-2">
                                Upload Berkas Dokumen PDF <span class="text-[#8F0A0D]">*</span>
                            </label>
                            
                            <div class="border-2 border-dashed border-[#CBD5E1] hover:border-[#8F0A0D] rounded-2xl p-6 sm:p-8 text-center transition-all bg-[#F8FAFC]/70 hover:bg-[#FEF2F2]/20 cursor-pointer group"
                                 @dragover.prevent=""
                                 @drop.prevent="handleFileDrop($event)">
                                <input type="file" 
                                       name="document_file" 
                                       id="document_file" 
                                       accept="application/pdf" 
                                       required 
                                       class="hidden" 
                                       @change="handleFileSelect($event)">
                                <label for="document_file" class="cursor-pointer flex flex-col items-center justify-center gap-2.5">
                                    <div class="w-14 h-14 rounded-2xl bg-[#FEF2F2] text-[#8F0A0D] flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <div class="text-[13.5px] font-bold text-[#1E293B]" x-text="selectedFileName || 'Klik untuk memilih file PDF atau drag & drop di sini'"></div>
                                    <div class="text-[12px] text-[#64748B]">Mendukung format berkas <span class="font-semibold text-[#8F0A0D]">.PDF</span> (Ukuran maksimal 25 MB)</div>
                                    
                                    <template x-if="selectedFileName">
                                        <div class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[12px] font-semibold">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Berkas siap diunggah</span>
                                        </div>
                                    </template>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================================== --}}
                {{-- BAGIAN 2: PENANDATANGAN INTERNAL IPNET --}}
                {{-- ========================================================== --}}
                <div class="ipnet-card p-6 sm:p-7 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#F1F5F9] pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#8F0A0D] flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">2</div>
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Pihak Penandatangan Internal (IPNET)</h3>
                                <p class="text-[12.5px] text-[#64748B]">Daftar tim internal IPNET yang wajib menandatangani berkas sebelum diteruskan ke PIC klien.</p>
                            </div>
                        </div>
                        <button type="button" 
                                @click="addInternalSigner()" 
                                class="btn-ipnet-soft px-3.5 py-2 rounded-xl text-[12px] font-bold flex items-center gap-1.5 cursor-pointer shadow-xs self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Penandatangan</span>
                        </button>
                    </div>

                    {{-- Mode Alur Penandatanganan --}}
                    <div class="bg-[#F8FAFC] p-4 rounded-xl border border-[#E2E8F0] flex flex-col md:flex-row md:items-center justify-between gap-3 text-[12.5px]">
                        <div>
                            <div class="font-bold text-[#1E293B]">Alur Urutan Penandatanganan Internal:</div>
                            <div class="text-[12px] text-[#64748B] mt-0.5">Tentukan apakah penandatanganan harus berurutan satu per satu atau bebas (paralel).</div>
                        </div>
                        <div class="flex items-center gap-5">
                            <label class="flex items-center gap-2 cursor-pointer font-semibold text-[#334155] select-none">
                                <input type="radio" name="workflow_type" value="sequential" checked class="accent-[#8F0A0D] w-4 h-4">
                                <span>Berurutan (Satu per satu)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-semibold text-[#334155] select-none">
                                <input type="radio" name="workflow_type" value="parallel" class="accent-[#8F0A0D] w-4 h-4">
                                <span>Bebas (Paralel)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Daftar Penandatangan Internal (Repeater) --}}
                    <div class="space-y-3">
                        <template x-for="(s, idx) in internalSigners" :key="idx">
                            <div class="flex items-center gap-3 p-3.5 rounded-xl border border-[#E2E8F0] bg-white hover:border-[#CBD5E1] transition-all shadow-xs">
                                <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#475569] flex items-center justify-center font-bold text-[12px] shrink-0" x-text="idx + 1"></div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-wider mb-1">Pilih Anggota Tim</label>
                                        <select :name="`internal_signers[${idx}][user_id]`" 
                                                x-model="s.user_id" 
                                                @change="onUserSelect(s)" 
                                                required 
                                                class="w-full px-3 py-2 text-[12.5px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all cursor-pointer">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" data-pos="{{ $u->position ?: 'Engineer Pelaksana' }}">
                                                    {{ $u->name }} ({{ $u->position ?: 'Engineer' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-[#64748B] uppercase tracking-wider mb-1">Jabatan pada Lembar Dokumen</label>
                                        <input type="text" 
                                               :name="`internal_signers[${idx}][role_title]`" 
                                               x-model="s.role_title" 
                                               placeholder="Contoh: Field Engineer / Project Manager" 
                                               required 
                                               class="w-full px-3 py-2 text-[12.5px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all placeholder-[#94A3B8]">
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="removeInternalSigner(idx)" 
                                        x-show="internalSigners.length > 1" 
                                        class="p-2 rounded-lg text-[#94A3B8] hover:text-rose-600 hover:bg-rose-50 transition cursor-pointer shrink-0" 
                                        title="Hapus Penandatangan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ========================================================== --}}
                {{-- BAGIAN 3: TARGET PENANDATANGAN PIC KLIEN --}}
                {{-- ========================================================== --}}
                <div class="ipnet-card p-6 sm:p-7 space-y-5">
                    <div class="flex items-center gap-3 border-b border-[#F1F5F9] pb-4">
                        <div class="w-8 h-8 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#8F0A0D] flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">3</div>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Target Penandatangan PIC Klien</h3>
                            <p class="text-[12.5px] text-[#64748B]">Data perwakilan klien yang akan menerima tautan resmi untuk menandatangani dokumen ini.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                Nama Lengkap PIC Klien <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="text" 
                                   name="client_name" 
                                   value="{{ old('client_name') }}" 
                                   required 
                                   placeholder="Contoh: Bpk. Bambang Supriyanto"
                                   class="w-full px-3.5 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                Jabatan PIC Klien
                            </label>
                            <input type="text" 
                                   name="client_position" 
                                   value="{{ old('client_position') }}" 
                                   placeholder="Contoh: IT Manager / Operation Lead"
                                   class="w-full px-3.5 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                Nama Perusahaan / Instansi Klien
                            </label>
                            <input type="text" 
                                   name="client_company" 
                                   value="{{ old('client_company') }}" 
                                   placeholder="Contoh: PT Angkasa Pura Support"
                                   class="w-full px-3.5 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        <div>
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                No. WhatsApp / HP Klien (Untuk share link)
                            </label>
                            <input type="text" 
                                   name="client_phone" 
                                   value="{{ old('client_phone') }}" 
                                   placeholder="Contoh: 081234567890"
                                   class="w-full px-3.5 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[12px] font-bold text-[#334155] uppercase tracking-wider mb-1.5">
                                Email Klien (Opsional)
                            </label>
                            <input type="email" 
                                   name="client_email" 
                                   value="{{ old('client_email') }}" 
                                   placeholder="Contoh: pic.klien@perusahaan.co.id"
                                   class="w-full px-3.5 py-2.5 text-[13px] font-medium text-[#1E293B] rounded-xl border border-[#CBD5E1] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>
                    </div>
                </div>

                {{-- ========================================================== --}}
                {{-- BUTTON ACTIONS --}}
                {{-- ========================================================== --}}
                <div class="ipnet-card p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-[12.5px] text-[#64748B]">
                        Setelah dokumen dibuat, sistem akan membuka halaman tanda tangan internal tim IPNET terlebih dahulu.
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <a href="{{ route('digital_signatures.index') }}" 
                           class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-[13px] font-bold text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </a>
                        <button type="submit" 
                                class="btn-ipnet-gradient px-6 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <span>Simpan & Lanjut ke TTD</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function documentCreator() {
    return {
        selectedFileName: '',
        internalSigners: [
            {
                user_id: '{{ auth()->id() }}',
                role_title: '{{ auth()->user()->position ?: "Engineer Pelaksana" }}'
            }
        ],
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) {
                this.selectedFileName = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            }
        },
        handleFileDrop(e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                const file = dt.files[0];
                if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                    const input = document.getElementById('document_file');
                    input.files = dt.files;
                    this.selectedFileName = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                } else {
                    alert('Mohon pilih berkas dengan format .PDF saja.');
                }
            }
        },
        addInternalSigner() {
            this.internalSigners.push({
                user_id: '',
                role_title: 'Field Engineer'
            });
        },
        removeInternalSigner(idx) {
            if (this.internalSigners.length > 1) {
                this.internalSigners.splice(idx, 1);
            }
        },
        onUserSelect(s) {
            const selectEl = event.target;
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.pos) {
                s.role_title = selectedOpt.dataset.pos;
            }
        }
    };
}
</script>
@endpush
@endsection
