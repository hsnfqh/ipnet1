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

                {{-- KARTU 2: SERTIFIKASI --}}
                <div style="background:white; border:1px solid #E7E5E3; border-radius:16px; padding:24px; box-shadow:0 1px 3px rgba(14,13,18,0.04);">

                    {{-- Header Kartu --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; padding-bottom:16px; margin-bottom:20px; border-bottom:1px solid #EFEDEB;">
                        <div>
                            <h3 style="margin:0 0 3px; font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:700; color:#17151C;">Sertifikasi Keahlian</h3>
                            <p style="margin:0; font-size:12.5px; color:#75727C;">Upload dokumen sertifikat untuk diverifikasi oleh Lead Engineer</p>
                        </div>

                        {{-- Status Badge --}}
                        @php
                            $certStatus = $user->certification_status ?? 'pending';
                            if (!$user->certification_file) $certStatus = 'none';
                        @endphp

                        @if($certStatus === 'approved')
                        <span style="background:#E4F3EA; color:#1B7A46; font-size:12px; font-weight:700; padding:5px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; flex-shrink:0; white-space:nowrap;">
                            <span style="width:7px; height:7px; border-radius:50%; background:#1B7A46;"></span>
                            Disetujui
                        </span>
                        @elseif($certStatus === 'pending')
                        <span style="background:#FFF3E0; color:#E67E22; font-size:12px; font-weight:700; padding:5px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; flex-shrink:0; white-space:nowrap;">
                            <span style="width:7px; height:7px; border-radius:50%; background:#E67E22;"></span>
                            Menunggu
                        </span>
                        @elseif($certStatus === 'rejected')
                        <span style="background:#FDF1F2; color:#C81E2C; font-size:12px; font-weight:700; padding:5px 12px; border-radius:20px; display:inline-flex; align-items:center; gap:6px; flex-shrink:0; white-space:nowrap;">
                            <span style="width:7px; height:7px; border-radius:50%; background:#C81E2C;"></span>
                            Ditolak
                        </span>
                        @else
                        <span style="background:#F1F0EE; color:#75727C; font-size:12px; font-weight:600; padding:5px 12px; border-radius:20px; flex-shrink:0; white-space:nowrap;">
                            Belum Ada File
                        </span>
                        @endif
                    </div>

                    {{-- Banner Status --}}
                    @if($certStatus === 'approved')
                    <div style="margin-bottom:20px; padding:13px 16px; background:#E4F3EA; border:1px solid #B8E3CA; border-radius:12px; color:#1B7A46; font-size:13px; display:flex; align-items:flex-start; gap:12px;">
                        <svg style="width:20px; height:20px; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong style="display:block; font-size:13.5px; margin-bottom:2px;">Sertifikasi Disetujui!</strong>
                            <span>Dokumen Anda telah ditinjau dan diverifikasi resmi oleh Lead Engineer.</span>
                        </div>
                    </div>
                    @elseif($certStatus === 'pending')
                    <div style="margin-bottom:20px; padding:13px 16px; background:#FFF8EC; border:1px solid #FCE4C6; border-radius:12px; color:#B76E00; font-size:13px; display:flex; align-items:flex-start; gap:12px;">
                        <svg style="width:20px; height:20px; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong style="display:block; font-size:13.5px; margin-bottom:2px;">Menunggu Peninjauan</strong>
                            <span>Dokumen Anda sedang menunggu proses verifikasi oleh Lead Engineer.</span>
                        </div>
                    </div>
                    @elseif($certStatus === 'rejected')
                    <div style="margin-bottom:20px; padding:13px 16px; background:#FDF1F2; border:1px solid #F8C8CC; border-radius:12px; color:#C81E2C; font-size:13px; display:flex; align-items:flex-start; gap:12px;">
                        <svg style="width:20px; height:20px; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong style="display:block; font-size:13.5px; margin-bottom:2px;">Sertifikasi Ditolak</strong>
                            <span>Dokumen sebelumnya belum memenuhi syarat. Silakan unggah kembali sertifikat yang sesuai.</span>
                        </div>
                    </div>
                    @endif

                    {{-- File Terpasang Saat Ini --}}
                    @if($user->certification_file)
                    <div style="margin-bottom:20px;">
                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.4px;">File Sertifikasi Saat Ini</label>

                        {{-- Preview gambar --}}
                        @if(preg_match('/\.(jpg|jpeg|png|webp)$/i', $user->certification_file))
                        <div style="margin-bottom:12px; border-radius:12px; overflow:hidden; border:1px solid #E7E5E3; background:#F8F7F6; padding:12px; text-align:center;">
                            <img src="{{ route('users.certification-file', $user->id) }}" style="width:100%; max-height:500px; object-fit:contain; border-radius:8px; box-shadow:0 4px 12px rgba(14,13,18,0.08); display:block; margin:0 auto;" alt="Pratinjau Sertifikat">
                        </div>
                        @endif

                        <div style="display:flex; align-items:center; gap:10px; padding:11px 14px; background:#F8F7F6; border:1px solid #E7E5E3; border-radius:10px;">
                            <div style="width:34px; height:34px; border-radius:8px; background:#E4F3EA; color:#1B7A46; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div style="flex:1; overflow:hidden;">
                                <div style="font-size:13px; font-weight:600; color:#17151C; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ basename($user->certification_file) }}</div>
                                <div style="font-size:11px; color:#75727C; margin-top:1px;">
                                    Upload: {{ $user->certification_uploaded_at ? $user->certification_uploaded_at->format('d M Y, H:i') : '-' }}
                                </div>
                            </div>
                            <a href="{{ route('users.certification-file', $user->id) }}" target="_blank" download
                               style="display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:600; color:#C81E2C; background:white; padding:6px 12px; border-radius:8px; border:1px solid #E7E5E3; text-decoration:none; flex-shrink:0; transition:all 0.15s ease;"
                               onmouseover="this.style.background='#FDF1F2'; this.style.borderColor='#F8C8CC';"
                               onmouseout="this.style.background='white'; this.style.borderColor='#E7E5E3';">
                                <svg style="width:13px; height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Unduh
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Form Upload Sertifikasi --}}
                    <form action="{{ route('profile.certification') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <label style="display:block; font-size:11px; font-weight:700; color:#75727C; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.4px;">
                            {{ $user->certification_file ? 'Ganti / Upload Sertifikasi Baru' : 'Upload Dokumen Sertifikasi' }}
                        </label>

                        {{-- Drop Zone --}}
                        <div style="border:2px dashed #E7E5E3; border-radius:10px; padding:22px 16px; text-align:center; background:#F9F9F8; cursor:pointer; transition:border-color 0.18s ease, background 0.18s ease;"
                             onmouseover="this.style.borderColor='#C81E2C'; this.style.background='#FEF5F5';"
                             onmouseout="this.style.borderColor='#E7E5E3'; this.style.background='#F9F9F8';"
                             @click="document.getElementById('profileCertFile').click()">
                            <input type="file" id="profileCertFile" name="certification_file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" @change="handleFileSelect($event)">
                            <div style="width:42px; height:42px; border-radius:50%; background:#FDF1F2; color:#C81E2C; display:flex; align-items:center; justify-content:center; margin:0 auto 10px;">
                                <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <p style="margin:0 0 3px; font-size:13.5px; font-weight:600; color:#17151C;">Klik untuk memilih file sertifikasi</p>
                            <p style="margin:0; font-size:11.5px; color:#75727C;">Format: PDF, JPG, PNG · Maksimal 5 MB</p>
                        </div>

                        {{-- Indikator file terpilih --}}
                        <div x-show="selectedFileName" x-cloak
                             style="margin-top:10px; padding:10px 14px; background:#E4F3EA; border:1px solid #B8E3CA; border-radius:8px; display:flex; align-items:center; gap:8px;">
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

                        {{-- Tombol AKTIF: muncul saat file sudah dipilih --}}
                        <button type="submit"
                                x-show="selectedFileName" x-cloak
                                style="width:100%; margin-top:14px; padding:12px 20px; border-radius:10px; border:none; background:linear-gradient(135deg, #C81E2C, #AF1424); color:white; font-weight:600; font-size:14px; box-shadow:0 6px 18px rgba(200,30,44,0.28); cursor:pointer; transition:all 0.15s ease; box-sizing:border-box;"
                                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 8px 22px rgba(200,30,44,0.35)';"
                                onmouseout="this.style.transform='none'; this.style.boxShadow='0 6px 18px rgba(200,30,44,0.28)';">
                            <div style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%;">
                                <svg style="width:17px; height:17px; flex-shrink:0; display:inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span style="line-height:1.2;">Upload &amp; Ajukan Verifikasi</span>
                            </div>
                        </button>

                    </form>


                </div>

            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('profileManager', function() {
            return {
                selectedFileName: '',
                handleFileSelect: function(event) {
                    var file = event.target.files[0];
                    if (file) {
                        this.selectedFileName = file.name;
                    }
                },
                clearSelectedFile: function() {
                    this.selectedFileName = '';
                    var inp = document.getElementById('profileCertFile');
                    if (inp) inp.value = '';
                }
            };
        });
    });
</script>
@endpush
@endsection

