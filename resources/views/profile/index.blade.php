@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Profil Saya'])

        <div class="p-4 sm:p-[26px] animate-fade-in" x-data="profileManager()">

            {{-- FLASH MESSAGES --}}
            @if(session('success'))
            <div style="margin-bottom:20px; padding:13px 16px; background:#E4F3EA; border:1px solid #B8E3CA; border-radius:10px; color:#1B7A46; font-size:13.5px; font-weight:600; display:flex; align-items:center; gap:10px;">
                <svg style="width:18px; height:18px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div style="margin-bottom:20px; padding:13px 16px; background:#FDF1F2; border:1px solid #F8C8CC; border-radius:10px; color:#C81E2C; font-size:13.5px; font-weight:600; display:flex; align-items:center; gap:10px;">
                <svg style="width:18px; height:18px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(340px, 1fr)); gap:24px; align-items:start;">

                {{-- KARTU 1: INFORMASI AKUN --}}
                <div style="background:white; border:1px solid #E7E5E3; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(14,13,18,0.04);">

                    <div style="display:flex; align-items:center; gap:16px; padding-bottom:20px; margin-bottom:20px; border-bottom:1px solid #EFEDEB;">
                        <div style="width:60px; height:60px; border-radius:50%; background:linear-gradient(135deg, #AF1424, #D62E3C); color:white; display:flex; align-items:center; justify-content:center; font-size:20px; font-weight:700; flex-shrink:0; box-shadow:0 6px 16px rgba(200,30,44,0.25);">
                            {{ $user->initials }}
                        </div>
                        <div style="min-width:0;">
                            <h2 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:18px; font-weight:700; color:#17151C; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $user->name }}</h2>
                            <p style="margin:2px 0 6px; font-size:13px; color:#75727C;">{{ $user->position ?? 'Staff / Engineer' }}</p>
                            <span style="font-size:11px; font-weight:700; background:#F1F0EE; color:#3D3A44; padding:3px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.4px; display:inline-block;">
                                {{ $user->role_label }}
                            </span>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:14px;">
                        <div>
                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Nama Lengkap</label>
                            <input type="text" value="{{ $user->name }}" readonly style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#17151C; cursor:not-allowed; outline:none; box-sizing:border-box;">
                        </div>

                        <div>
                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Alamat Email</label>
                            <input type="email" value="{{ $user->email }}" readonly style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#17151C; cursor:not-allowed; outline:none; box-sizing:border-box;">
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Nomor Telepon</label>
                                <input type="text" value="{{ $user->phone ?? '-' }}" readonly style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#17151C; cursor:not-allowed; outline:none; box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Status Akun</label>
                                <div style="padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#1B7A46; font-weight:600; display:flex; align-items:center; gap:6px;">
                                    <span style="width:8px; height:8px; border-radius:50%; background:#1B7A46; flex-shrink:0;"></span>
                                    {{ $user->status }}
                                </div>
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Jabatan / Posisi</label>
                                <input type="text" value="{{ $user->position ?? '-' }}" readonly style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#17151C; cursor:not-allowed; outline:none; box-sizing:border-box;">
                            </div>
                            <div>
                                <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">Role Hak Akses</label>
                                <input type="text" value="{{ $user->role_label }}" readonly style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:#F8F7F6; font-size:13.5px; color:#17151C; cursor:not-allowed; outline:none; box-sizing:border-box;">
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding:12px 14px; background:#F8F7F6; border-radius:10px; border:1px solid #EFEDEB; display:flex; align-items:flex-start; gap:10px;">
                        <svg style="width:16px; height:16px; color:#75727C; flex-shrink:0; margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p style="margin:0; font-size:12px; color:#75727C; line-height:1.5;">
                            Informasi akun (Nama, Email, Jabatan, Role) dikelola langsung oleh Lead Engineer.
                        </p>
                    </div>

                </div>

                {{-- KARTU 2: SERTIFIKASI MULTI-UPLOAD --}}
                <div style="background:white; border:1px solid #E7E5E3; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(14,13,18,0.04);">

                    {{-- Header Kartu --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding-bottom:16px; margin-bottom:20px; border-bottom:1px solid #EFEDEB;">
                        <div>
                            <h3 style="margin:0 0 3px; font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:700; color:#17151C;">Sertifikasi Keahlian</h3>
                            <p style="margin:0; font-size:12.5px; color:#75727C;">Upload dokumen sertifikat keahlian Anda untuk diverifikasi oleh Lead Engineer</p>
                        </div>
                        <span style="background:#FDF1F2; color:#C81E2C; font-size:12px; font-weight:700; padding:4px 10px; border-radius:20px; flex-shrink:0;">
                            {{ $user->certifications->count() }} Sertifikat
                        </span>
                    </div>

                    {{-- DAFTAR SERTIFIKAT YANG SUDAH DIUPLOAD --}}
                    <div style="margin-bottom:24px;">
                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.4px;">
                            Daftar Sertifikat Terdaftar
                        </label>

                        @if($user->certifications->isEmpty())
                        <div style="padding:24px 16px; border:1px dashed #E7E5E3; border-radius:12px; text-align:center; background:#FAFAF9;">
                            <div style="width:38px; height:38px; border-radius:50%; background:#F1F0EE; color:#75727C; display:flex; align-items:center; justify-content:center; margin:0 auto 8px;">
                                <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p style="margin:0; font-size:13px; font-weight:600; color:#17151C;">Belum ada sertifikasi</p>
                            <p style="margin:2px 0 0; font-size:11.5px; color:#75727C;">Gunakan form di bawah untuk mengunggah sertifikat pertama Anda.</p>
                        </div>
                        @else
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @foreach($user->certifications as $cert)
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 14px; background:#F8F7F6; border:1px solid #E7E5E3; border-radius:12px; transition:border-color 0.15s ease;">
                                <div style="display:flex; align-items:center; gap:12px; min-width:0; flex:1;">
                                    <div style="width:36px; height:36px; border-radius:8px; background:{{ $cert->status === 'approved' ? '#E4F3EA' : ($cert->status === 'rejected' ? '#FDF1F2' : '#FFF3E0') }}; color:{{ $cert->status === 'approved' ? '#1B7A46' : ($cert->status === 'rejected' ? '#C81E2C' : '#E67E22') }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div style="min-width:0; flex:1;">
                                        <div style="font-size:13.5px; font-weight:700; color:#17151C; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            {{ $cert->name }}
                                        </div>
                                        <div style="display:flex; align-items:center; gap:8px; margin-top:2px; font-size:11px; color:#75727C; flex-wrap:wrap;">
                                            <span>Upload: {{ $cert->uploaded_at ? $cert->uploaded_at->format('d M Y, H:i') : $cert->created_at->format('d M Y') }}</span>
                                            <span>•</span>
                                            @if($cert->status === 'approved')
                                            <span style="color:#1B7A46; font-weight:700;">✓ Disetujui</span>
                                            @elseif($cert->status === 'rejected')
                                            <span style="color:#C81E2C; font-weight:700;">✕ Ditolak</span>
                                            @else
                                            <span style="color:#E67E22; font-weight:700;">⏳ Menunggu Verifikasi</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                                    {{-- Tombol Lihat/Unduh --}}
                                    <a href="{{ route('certifications.file', $cert->id) }}" target="_blank"
                                       style="display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600; color:#17151C; background:white; padding:6px 10px; border-radius:8px; border:1px solid #E7E5E3; text-decoration:none; transition:all 0.15s ease;"
                                       onmouseover="this.style.background='#F1F0EE';"
                                       onmouseout="this.style.background='white';">
                                        <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Lihat
                                    </a>

                                    {{-- Tombol Hapus (jika belum disetujui / jika ditolak) --}}
                                    <button type="button"
                                            @click="confirmDeleteCert({{ $cert->id }}, '{{ addslashes($cert->name) }}')"
                                            style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:8px; border:1px solid #E7E5E3; background:white; color:#75727C; cursor:pointer; transition:all 0.15s ease;"
                                            title="Hapus Sertifikat Ini"
                                            onmouseover="this.style.background='#FDF1F2'; this.style.color='#C81E2C'; this.style.borderColor='#F8C8CC';"
                                            onmouseout="this.style.background='white'; this.style.color='#75727C'; this.style.borderColor='#E7E5E3';">
                                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    <div style="width:100%; height:1px; background:#EFEDEB; margin-bottom:20px;"></div>

                    {{-- FORM TAMBAH SERTIFIKASI BARU --}}
                    <form action="{{ route('profile.certification') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label style="display:block; font-size:12px; font-weight:700; color:#17151C; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.4px;">
                            + Tambah Sertifikasi Baru
                        </label>

                        {{-- Input Nama Sertifikasi --}}
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                                Nama Sertifikasi <span style="color:#C81E2C;">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   x-model="certName"
                                   required
                                   placeholder="Contoh: MikroTik MTCNA / Cisco CCNA / AWS Cloud"
                                   style="width:100%; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; font-size:13.5px; color:#17151C; outline:none; box-sizing:border-box; transition:border-color 0.15s;"
                                   onfocus="this.style.borderColor='#C81E2C';"
                                   onblur="this.style.borderColor='#E7E5E3';">
                        </div>

                        {{-- Drop Zone File --}}
                        <div style="margin-bottom:12px;">
                            <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.4px;">
                                File Dokumen Sertifikat <span style="color:#C81E2C;">*</span>
                            </label>
                            <div style="border:2px dashed #E7E5E3; border-radius:10px; padding:18px 14px; text-align:center; background:#F9F9F8; cursor:pointer; transition:border-color 0.18s ease, background 0.18s ease;"
                                 onmouseover="this.style.borderColor='#C81E2C'; this.style.background='#FEF5F5';"
                                 onmouseout="this.style.borderColor='#E7E5E3'; this.style.background='#F9F9F8';"
                                 @click="document.getElementById('profileCertFile').click()">
                                <input type="file" id="profileCertFile" name="certification_file" accept=".pdf,.jpg,.jpeg,.png,.webp" style="display:none;" @change="handleFileSelect($event)">
                                <div style="width:36px; height:36px; border-radius:50%; background:#FDF1F2; color:#C81E2C; display:flex; align-items:center; justify-content:center; margin:0 auto 8px;">
                                    <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                </div>
                                <p style="margin:0 0 2px; font-size:13px; font-weight:600; color:#17151C;">Pilih file sertifikat</p>
                                <p style="margin:0; font-size:11px; color:#75727C;">Format: PDF, JPG, PNG, WEBP · Maksimal 5 MB</p>
                            </div>
                        </div>

                        {{-- Indikator file terpilih --}}
                        <div x-show="selectedFileName" x-cloak
                             style="margin-bottom:12px; padding:9px 12px; background:#E4F3EA; border:1px solid #B8E3CA; border-radius:8px; display:flex; align-items:center; gap:8px;">
                            <svg style="width:15px; height:15px; color:#1B7A46; flex-shrink:0;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            <span style="font-size:12.5px; font-weight:600; color:#1B7A46; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" x-text="selectedFileName"></span>
                            <button type="button" @click="clearSelectedFile()" style="background:none; border:none; cursor:pointer; color:#1B7A46; padding:2px 4px; flex-shrink:0; line-height:0;" title="Hapus Pilihan">
                                <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit"
                                class="profile-upload-submit-btn"
                                :disabled="!selectedFileName || !certName.trim()">
                            <svg style="width:16px; height:16px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span>Upload Sertifikasi</span>
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS SERTIFIKAT --}}
    <div x-show="deleteModalOpen" x-cloak
         style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(2px);"
         @click.self="deleteModalOpen = false">
        <div style="background:white; border-radius:16px; width:400px; max-width:100%; padding:24px; text-align:center; box-shadow:0 20px 60px rgba(14,13,18,0.2); animation:fadeInUp 0.18s ease;">
            <div style="width:50px; height:50px; border-radius:50%; background:#FDF1F2; color:#C81E2C; display:flex; align-items:center; justify-content:center; margin:0 auto 14px;">
                <svg style="width:24px; height:24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 style="margin:0 0 6px; font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:700; color:#17151C;">Hapus Sertifikasi?</h3>
            <p style="margin:0 0 20px; font-size:13px; color:#75727C; line-height:1.5;">
                Anda yakin ingin menghapus sertifikat <strong x-text="deletingCertName" style="color:#17151C;"></strong>?
            </p>
            <div style="display:flex; gap:10px;">
                <button type="button" @click="deleteModalOpen = false" style="flex:1; padding:10px 14px; border-radius:8px; border:1px solid #E7E5E3; background:white; font-weight:600; font-size:13px; color:#3D3A44; cursor:pointer;">
                    Batal
                </button>
                <form :action="'/profile/certification/' + deletingCertId" method="POST" style="flex:1; margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width:100%; padding:10px 14px; border-radius:8px; border:none; background:#C81E2C; font-weight:600; font-size:13px; color:white; cursor:pointer;">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .profile-upload-submit-btn {
        width: 100%;
        padding: 12px 18px;
        border-radius: 10px;
        border: none;
        background: #C81E2C;
        color: #ffffff;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 14px rgba(200, 30, 44, 0.28);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.15s ease;
        box-sizing: border-box;
    }

    .profile-upload-submit-btn:hover:not(:disabled) {
        background: #A31622;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(200, 30, 44, 0.35);
    }

    .profile-upload-submit-btn:active:not(:disabled) {
        transform: translateY(0);
    }

    .profile-upload-submit-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #C81E2C;
        box-shadow: none;
    }
</style>
@endpush


@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('profileManager', function() {
            return {
                certName: '',
                selectedFileName: '',
                deleteModalOpen: false,
                deletingCertId: null,
                deletingCertName: '',

                handleFileSelect: function(event) {
                    var file = event.target.files[0];
                    if (file) {
                        this.selectedFileName = file.name;
                        // Auto-fill nama sertifikat jika belum diisi
                        if (!this.certName.trim()) {
                            var baseName = file.name.replace(/\.[^/.]+$/, "").replace(/[_-]/g, " ");
                            this.certName = baseName;
                        }
                    }
                },

                clearSelectedFile: function() {
                    this.selectedFileName = '';
                    var inp = document.getElementById('profileCertFile');
                    if (inp) inp.value = '';
                },

                confirmDeleteCert: function(id, name) {
                    this.deletingCertId = id;
                    this.deletingCertName = name;
                    this.deleteModalOpen = true;
                }
            };
        });
    });
</script>
@endpush
@endsection


