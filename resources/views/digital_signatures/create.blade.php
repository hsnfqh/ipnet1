@extends('layouts.app')

@section('title', 'Buat Dokumen Digital Signature - PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.02);
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.22) !important;
        transition: all 0.2s ease !important;
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="documentCreator()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Buat Dokumen Digital Signature'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-5xl mx-auto animate-fade-in">

            {{-- BREADCRUMB & HEADER --}}
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('digital_signatures.index') }}" class="hover:text-[#8F0A0D] font-medium">Digital Signature</a>
                        <span>/</span>
                        <span class="text-slate-800 font-bold">Buat Dokumen Baru</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Upload & Konfigurasi Dokumen</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Unggah berkas PDF, tentukan pihak penandatangan internal, dan isi data PIC klien.</p>
                </div>
                <a href="{{ route('digital_signatures.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                    &larr; Kembali
                </a>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    <div class="font-bold">Terdapat beberapa kesalahan:</div>
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('digital_signatures.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- BAGIAN 1: UPLOAD BERKAS DOKUMEN --}}
                <div class="ipnet-card p-6 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center font-bold text-xs">1</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Berkas Dokumen (PDF)</h3>
                            <p class="text-[11px] text-slate-500">Upload dokumen yang akan ditandatangani oleh internal dan PIC klien.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">File Dokumen PDF <span class="text-rose-500">*</span></label>
                            <div class="border-2 border-dashed border-slate-200 hover:border-[#8F0A0D] rounded-2xl p-6 text-center transition-all bg-slate-50/50">
                                <input type="file" name="document_file" id="document_file" accept="application/pdf" required class="hidden" @change="handleFileSelect($event)">
                                <label for="document_file" class="cursor-pointer flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-red-50 text-[#8F0A0D] flex items-center justify-center shadow-xs">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    </div>
                                    <div class="text-xs font-bold text-slate-800" x-text="selectedFileName || 'Klik untuk memilih file PDF atau drag & drop'"></div>
                                    <div class="text-[11px] text-slate-400">Format: .PDF (Maksimal 25MB)</div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Dokumen <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: BAST - Pengadaan Switch Hub Angkasa Pura"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Dokumen <span class="text-rose-500">*</span></label>
                            <select name="category" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                                <option value="BAST" {{ old('category') === 'BAST' ? 'selected' : '' }}>BAST (Berita Acara Serah Terima)</option>
                                <option value="Work Order" {{ old('category') === 'Work Order' ? 'selected' : '' }}>Work Order / SPK</option>
                                <option value="Laporan Pekerjaan" {{ old('category') === 'Laporan Pekerjaan' ? 'selected' : '' }}>Laporan Pekerjaan / Activity Report</option>
                                <option value="Berita Acara Uji Terima (BAUT)" {{ old('category') === 'Berita Acara Uji Terima (BAUT)' ? 'selected' : '' }}>Berita Acara Uji Terima (BAUT)</option>
                                <option value="Formulir Peminjaman" {{ old('category') === 'Formulir Peminjaman' ? 'selected' : '' }}>Formulir Peminjaman / Handover</option>
                                <option value="Lainnya" {{ old('category') === 'Lainnya' ? 'selected' : '' }}>Dokumen Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Proyek Terkait (Opsional)</label>
                            <select name="project_id" @change="onProjectSelect($event)" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                                <option value="">-- Pilih Proyek Terkait (Bisa Dikosongkan) --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" data-client="{{ $p->client }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} {{ $p->client ? "({$p->client})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / Catatan Singkat</label>
                            <input type="text" name="description" value="{{ old('description') }}" placeholder="Catatan tambahan untuk dokumen ini..."
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: PENANDATANGAN INTERNAL IPNET --}}
                <div class="ipnet-card p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center font-bold text-xs">2</div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Pihak Penandatangan Internal (IPNET)</h3>
                                <p class="text-[11px] text-slate-500">Daftar tim IPNET yang wajib menandatangani berkas ini sebelum diteruskan ke klien.</p>
                            </div>
                        </div>
                        <button type="button" @click="addInternalSigner()" class="px-2.5 py-1.5 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] hover:bg-[#8F0A0D] hover:text-white text-xs font-bold transition-all">
                            + Tambah Penandatangan
                        </button>
                    </div>

                    {{-- Mode Urutan --}}
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="font-bold text-slate-800">Alur Urutan Penandatanganan Internal:</div>
                            <div class="text-[11px] text-slate-500">Pilih apakah penandatanganan harus berurutan atau bisa bebas secara bersamaan.</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                <input type="radio" name="workflow_type" value="sequential" checked class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                <span>Berurutan (Satu per satu)</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                <input type="radio" name="workflow_type" value="parallel" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                <span>Bebas (Paralel)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Table / List of Signers --}}
                    <div class="space-y-2.5">
                        <template x-for="(s, idx) in internalSigners" :key="idx">
                            <div class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 bg-white">
                                <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs shrink-0" x-text="idx + 1"></div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Pilih User Tim</label>
                                        <select :name="`internal_signers[${idx}][user_id]`" x-model="s.user_id" @change="onUserSelect(s)" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#8F0A0D]">
                                            <option value="">-- Pilih User --</option>
                                            @foreach($users as $u)
                                                <option value="{{ $u->id }}" data-pos="{{ $u->position ?: 'Engineer Pelaksana' }}">
                                                    {{ $u->name }} ({{ $u->position ?: 'Engineer' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Jabatan pada Dokumen</label>
                                        <input type="text" :name="`internal_signers[${idx}][role_title]`" x-model="s.role_title" placeholder="Contoh: Engineer Pelaksana / Project Manager" required class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#8F0A0D]">
                                    </div>
                                </div>
                                <button type="button" @click="removeInternalSigner(idx)" x-show="internalSigners.length > 1" class="text-slate-400 hover:text-rose-600 p-1.5 shrink-0" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- BAGIAN 3: TARGET PENANDATANGAN PIC KLIEN --}}
                <div class="ipnet-card p-6 space-y-4">
                    <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                        <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center font-bold text-xs">3</div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Target Penandatangan PIC Klien</h3>
                            <p class="text-[11px] text-slate-500">Data PIC klien yang akan menerima tautan untuk menandatangani berkas ini.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap PIC Klien <span class="text-rose-500">*</span></label>
                            <input type="text" name="client_name" x-model="clientName" value="{{ old('client_name') }}" required placeholder="Contoh: Bpk. Bambang Supriyanto"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan PIC Klien</label>
                            <input type="text" name="client_position" value="{{ old('client_position') }}" placeholder="Contoh: IT Manager / Operation Lead"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Instansi Klien</label>
                            <input type="text" name="client_company" x-model="clientCompany" value="{{ old('client_company') }}" placeholder="Contoh: PT Angkasa Pura Support"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP Klien (Untuk share link)</label>
                            <input type="text" name="client_phone" value="{{ old('client_phone') }}" placeholder="Contoh: 081234567890"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Klien (Opsional)</label>
                            <input type="email" name="client_email" value="{{ old('client_email') }}" placeholder="Contoh: pic.klien@perusahaan.co.id"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>
                    </div>
                </div>

                {{-- BUTTON ACTIONS --}}
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="text-xs text-slate-500">
                        Setelah dokumen dibuat, sistem akan membuka halaman untuk melakukan tanda tangan internal terlebih dahulu.
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('digital_signatures.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white">
                            Batal
                        </a>
                        <button type="submit" class="btn-ipnet-primary px-6 py-2.5 rounded-xl font-bold text-xs tracking-wide">
                            Simpan & Lanjut ke TTD &rarr;
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
        clientName: '',
        clientCompany: '',
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
        },
        onProjectSelect(e) {
            const selectEl = e.target;
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.client) {
                this.clientCompany = selectedOpt.dataset.client;
            }
        }
    };
}
</script>
@endpush
@endsection
