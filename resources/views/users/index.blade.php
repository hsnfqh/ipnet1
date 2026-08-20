@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Manajemen Pengguna'])
        
        <div class="p-[26px] animate-fade-in" x-data="usersManager()" x-init="init()">
            <!-- Filter & Actions -->
            <div class="flex flex-wrap justify-between items-center gap-2.5 mb-4">
                <div class="flex flex-wrap gap-2.5">
                    <div class="relative">
                        <svg style="width:14px; height:14px; position:absolute; left:10px; top:11px; color:#948F99;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="search" 
                               placeholder="Cari nama atau email..." 
                               style="width:240px; padding:9px 11px 9px 32px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;">
                    </div>
                    <select x-model="roleFilter" style="width:170px; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;">
                        <option value="Semua">Semua Role</option>
                        <option value="Lead Engineer">Lead Engineer</option>
                        <option value="Engineer L1">Engineer L1</option>
                        <option value="Engineer L2">Engineer L2</option>
                    </select>
                    <select x-model="statusFilter" style="width:150px; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;">
                        <option value="Semua">Semua Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                @if(auth()->user()->hasRole('Lead Engineer'))
                <button @click="openModal()" style="background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:10px 17px; border-radius:8px; border:none; font-weight:600; font-size:14px; display:flex; align-items:center; gap:6px; cursor:pointer;">
                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah User
                </button>
                @endif
            </div>

            <!-- Users Table -->
            <div style="background:white; border:1px solid #E7E5E3; border-radius:12px; box-shadow:0 1px 2px rgba(14,13,18,0.05); overflow:hidden;">
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
                        <thead>
                            <tr style="background:#F1F0EE;">
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Nama</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Email</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">No. HP</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Role</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Jabatan</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Sertifikasi</th>
                                <th style="text-align:left; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Status</th>
                                <th style="text-align:right; padding:12px 16px; font-size:11.5px; font-weight:600; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="user in paginatedUsers" :key="user.id">
                                <tr style="border-top:1px solid #EFEDEB; transition:background 0.12s ease;" @mouseenter="this.style.background='#F1F0EE'" @mouseleave="this.style.background='transparent'">
                                    <td style="padding:10px 16px;">
                                        <div style="display:flex; align-items:center; gap:9px;">
                                            <div style="width:28px; height:28px; border-radius:50%; background:#C81E2C; color:white; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:600; flex-shrink:0;">
                                                <span x-text="user.name ? user.name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase() : '?'"></span>
                                            </div>
                                            <span style="font-weight:500; color:#17151C;" x-text="user.name"></span>
                                        </div>
                                    </td>
                                    <td style="padding:10px 16px; color:#3D3A44;" x-text="user.email"></td>
                                    <td style="padding:10px 16px; color:#3D3A44; font-family:'IBM Plex Mono',monospace; font-size:12.5px;" x-text="user.phone"></td>
                                    <td style="padding:10px 16px; color:#3D3A44;" x-text="user.role_name"></td>
                                    <td style="padding:10px 16px; color:#3D3A44;" x-text="user.position"></td>
                                    <!-- Sertifikasi Status -->
                                    <td style="padding:10px 16px;">
                                        <template x-if="user.has_certification">
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <span x-html="getCertificationStatusBadge(user.certification_status)"></span>
                                                @if(auth()->user()->hasRole('Lead Engineer'))
                                                <button @click="viewCertification(user)" 
                                                        style="background:none; border:none; cursor:pointer; color:#C81E2C; padding:4px;">
                                                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </template>
                                        <template x-if="!user.has_certification">
                                            <span style="color:#948F99; font-size:12px;">Belum upload</span>
                                        </template>
                                    </td>
                                    <td style="padding:10px 16px;" x-html="getStatusBadge(user.status)"></td>
                                    <td style="padding:10px 16px;">
                                        <div style="display:flex; justify-content:flex-end; gap:10px;">
                                            @if(auth()->user()->hasRole('Lead Engineer'))
                                            <button @click="toggleStatus(user)" 
                                                    style="background:none; border:none; cursor:pointer; padding:6px; border-radius:8px;"
                                                    :style="user.status === 'Active' ? 'color:#1B7A46;' : 'color:#948F99;'">
                                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 4v4m0 4v4"/>
                                                </svg>
                                            </button>
                                            <button @click="editUser(user)" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px;">
                                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button @click="deleteUser(user)" 
                                                    style="background:none; border:none; cursor:pointer; color:#C81E2C; padding:6px; border-radius:8px;"
                                                    :disabled="user.id === {{ auth()->id() }}">
                                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                <div x-show="filteredUsers.length === 0" style="text-align:center; padding:48px 20px; color:#75727C;">
                    <div style="width:44px; height:44px; border-radius:10px; background:#F1F0EE; display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                        <svg style="width:20px; height:20px; opacity:0.6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p style="font-size:13.5px; margin:0;">Tidak ada user yang cocok dengan filter</p>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-top:1px solid #EFEDEB;">
                    <span style="font-size:12px; color:#75727C;">
                        Menampilkan <span x-text="startIndex + 1"></span> - <span x-text="Math.min(endIndex, filteredUsers.length)"></span> dari <span x-text="filteredUsers.length"></span> user
                    </span>
                    <div style="display:flex; gap:6px;">
                        <button @click="currentPage--" 
                                :disabled="currentPage === 1"
                                style="padding:6px 10px; border-radius:8px; border:1px solid #E7E5E3; background:white; cursor:pointer;"
                                :style="currentPage === 1 ? 'opacity:0.4; cursor:default;' : ''">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span style="padding:6px 12px; border-radius:8px; border:1px solid #E7E5E3; background:white; font-size:12px; font-weight:600; color:#17151C; text-align:center; min-width:40px;" x-text="currentPage"></span>
                        <button @click="currentPage++" 
                                :disabled="currentPage === totalPages"
                                style="padding:6px 10px; border-radius:8px; border:1px solid #E7E5E3; background:white; cursor:pointer;"
                                :style="currentPage === totalPages ? 'opacity:0.4; cursor:default;' : ''">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- MODAL - USER FORM (TAMBAH/EDIT) -->
            <!-- ============================================================ -->
            <div x-show="modalOpen" 
                 x-cloak
                 style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(2px);"
                 @click.self="modalOpen = false">
                
                <div style="background:white; border-radius:16px; width:640px; max-width:100%; max-height:88vh; overflow-y:auto; animation:fadeInUp 0.18s ease; box-shadow:0 16px 40px rgba(14,13,18,0.12); margin:auto; position:relative;">
                    
                    <!-- Modal Header -->
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; position:sticky; top:0; background:white; border-bottom:1px solid #E7E5E3; z-index:1; border-radius:16px 16px 0 0;">
                        <h3 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:600; color:#17151C;" x-text="modalTitle"></h3>
                        <button @click="modalOpen = false" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px;">
                            <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div style="padding:22px;">
                        <form @submit.prevent="saveUser">
                            <div style="display:flex; flex-direction:column; gap:14px;">
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Nama Lengkap</label>
                                    <input type="text" x-model="form.name" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" required>
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Email</label>
                                    <input type="email" x-model="form.email" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" required>
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Nomor HP</label>
                                    <input type="text" x-model="form.phone" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;">
                                </div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Role</label>
                                        <select x-model="form.role" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" required>
                                            <option value="Lead Engineer">Lead Engineer</option>
                                            <option value="Engineer L1">Engineer L1</option>
                                            <option value="Engineer L2">Engineer L2</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Status</label>
                                        <select x-model="form.status" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" required>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">Jabatan</label>
                                    <input type="text" x-model="form.position" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" required>
                                </div>


                                <div x-show="!editing || form.password">
                                    <label style="display:block; font-size:12px; font-weight:700; color:#75727C; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.3px;">
                                        Password <span x-show="editing" style="color:#948F99; font-weight:400; text-transform:none;">(kosongkan jika tidak diubah)</span>
                                    </label>
                                    <input type="password" x-model="form.password" style="width:100%; padding:9px 11px; border-radius:8px; border:1px solid #E7E5E3; font-size:14px; color:#17151C; outline:none; background:white;" :required="!editing">
                                </div>
                            </div>
                            
                            <!-- BUTTONS -->
                            <div style="display:flex; gap:10px; margin-top:20px; padding-top:16px; border-top:1px solid #EFEDEB;">
                                <button type="submit" style="flex:1; justify-content:center; background:#C81E2C; color:white; box-shadow:0 8px 20px rgba(200,30,44,0.24); padding:10px 17px; border-radius:8px; border:none; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px;">
                                    Simpan
                                </button>
                                <button type="button" @click="modalOpen = false" style="flex:1; justify-content:center; background:white; color:#3D3A44; border:1px solid #E7E5E3; padding:10px 17px; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; gap:7px;">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- MODAL LIHAT MULTI-SERTIFIKASI                                -->
            <!-- ============================================================ -->
            <div x-show="viewCertModal" 
                 x-cloak
                 style="position:fixed; inset:0; background:rgba(14,13,18,0.6); z-index:99999; display:flex; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(2px);"
                 @click.self="viewCertModal = false">
                
                <div style="background:white; border-radius:16px; width:720px; max-width:100%; max-height:90vh; display:flex; flex-direction:column; animation:fadeInUp 0.18s ease; box-shadow:0 16px 40px rgba(14,13,18,0.12); margin:auto; position:relative; overflow:hidden;">
                    
                    <!-- Modal Header -->
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:white; border-bottom:1px solid #E7E5E3; flex-shrink:0;">
                        <div>
                            <h3 style="margin:0; font-family:'Space Grotesk',sans-serif; font-size:17px; font-weight:600; color:#17151C;">
                                Sertifikasi <span x-text="viewingUser.name" style="color:#C81E2C;"></span>
                            </h3>
                            <p style="margin:2px 0 0; font-size:12px; color:#75727C;" x-text="(viewingUser.certifications ? viewingUser.certifications.length : 0) + ' dokumen sertifikat terdaftar'"></p>
                        </div>
                        <button @click="viewCertModal = false" style="background:none; border:none; cursor:pointer; color:#75727C; padding:6px; border-radius:8px;">
                            <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Modal Body -->
                    <div style="padding:22px; overflow-y:auto; flex:1;">
                        
                        <!-- TAB PILIHAN SERTIFIKAT -->
                        <div style="margin-bottom:20px; padding:12px 14px; background:#F8F7F6; border:1px solid #E7E5E3; border-radius:12px;" x-show="viewingUser.certifications && viewingUser.certifications.length > 0">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                <p style="margin:0; font-size:11.5px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.4px;">
                                    Pilih Sertifikat untuk Dilihat
                                </p>
                                <span style="font-size:11px; font-weight:600; color:#948F99;">
                                    Klik tombol untuk berpindah dokumen
                                </span>
                            </div>
                            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                <template x-for="cert in (viewingUser.certifications || [])" :key="cert.id">
                                    <button type="button"
                                            @click="selectCert(cert)"
                                            class="cert-tab-pill"
                                            :class="selectedCert && selectedCert.id === cert.id ? 'active' : ''">
                                        <svg style="width:14px; height:14px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span x-text="cert.name"></span>
                                        <span class="cert-status-dot"
                                              :style="'background:' + (cert.status === 'approved' ? '#1B7A46' : (cert.status === 'rejected' ? '#C81E2C' : '#E67E22'))"></span>
                                    </button>
                                </template>
                            </div>
                        </div>


                        <!-- DETAIL SERTIFIKAT YANG TERPILIH -->
                        <template x-if="selectedCert">
                            <div>
                                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px; padding:14px; background:#F8F7F6; border:1px solid #E7E5E3; border-radius:10px;">
                                    <div>
                                        <p style="margin:0 0 3px; font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Nama Sertifikasi</p>
                                        <p style="margin:0; color:#17151C; font-size:14px; font-weight:700;" x-text="selectedCert.name"></p>
                                    </div>
                                    <div>
                                        <p style="margin:0 0 3px; font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Status Verifikasi</p>
                                        <div x-html="getCertificationStatusBadge(selectedCert.status)"></div>
                                    </div>
                                    <div style="grid-column: span 2;">
                                        <p style="margin:0 0 2px; font-size:11px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Waktu Upload</p>
                                        <p style="margin:0; color:#3D3A44; font-size:12.5px;" x-text="selectedCert.uploaded_at || '-'"></p>
                                    </div>
                                </div>

                                <div style="margin-bottom:16px;">
                                    <p style="margin:0 0 8px; font-size:11.5px; font-weight:700; color:#75727C; text-transform:uppercase; letter-spacing:0.3px;">Preview Dokumen</p>
                                    <div style="border:1px solid #E7E5E3; border-radius:12px; padding:14px; background:#F8F7F6; text-align:center; min-height:220px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                        <template x-if="selectedCert.is_pdf">
                                            <iframe :src="'/certification-file/' + selectedCert.id"
                                                    style="width:100%; height:450px; border:none; border-radius:8px; background:white;"></iframe>
                                        </template>
                                        <template x-if="!selectedCert.is_pdf">
                                            <div style="width:100%;">
                                                <img :src="'/certification-file/' + selectedCert.id"
                                                     alt="Pratinjau Sertifikasi"
                                                     x-on:error="certImageError = true"
                                                     x-show="!certImageError"
                                                     style="width:100%; max-height:480px; object-fit:contain; border-radius:8px; box-shadow:0 4px 16px rgba(14,13,18,0.08); display:block; margin:0 auto;">
                                                <div x-show="certImageError" style="color:#75727C; font-size:13px; text-align:center; padding:24px 16px;">
                                                    <svg style="width:36px; height:36px; color:#C81E2C; margin:0 auto 10px; opacity:0.7;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    <strong style="display:block; font-size:14px; color:#17151C; margin-bottom:4px;">File Tidak Ditemukan</strong>
                                                    <span>File sertifikasi fisik belum di-upload atau tidak ditemukan di server.</span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Action Buttons per Selected Certificate -->
                                <div style="margin-top:20px; padding-top:16px; border-top:1px solid #EFEDEB;">
                                    
                                    {{-- JIKA STATUS MENUNGGU (PENDING) --}}
                                    <template x-if="selectedCert.status === 'pending'">
                                        <div style="display:flex; flex-wrap:wrap; gap:10px; width:100%;">
                                            <button @click="approveCertification(selectedCert)" 
                                                    style="flex:1; min-width:180px; justify-content:center; background:#1B7A46; color:white; padding:11px 16px; border-radius:8px; border:none; font-weight:600; font-size:13.5px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.15s ease;"
                                                    onmouseover="this.style.background='#145E36'"
                                                    onmouseout="this.style.background='#1B7A46'">
                                                <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Setujui Sertifikat Ini
                                            </button>
                                            <button @click="rejectCertification(selectedCert)" 
                                                    style="flex:1; min-width:180px; justify-content:center; background:#C81E2C; color:white; padding:11px 16px; border-radius:8px; border:none; font-weight:600; font-size:13.5px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:background 0.15s ease;"
                                                    onmouseover="this.style.background='#A31622'"
                                                    onmouseout="this.style.background='#C81E2C'">
                                                <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Tolak & Hapus
                                            </button>
                                        </div>
                                    </template>


                                    {{-- JIKA STATUS SUDAH DISETUJUI (APPROVED) --}}
                                    <template x-if="selectedCert.status === 'approved'">
                                        <div style="display:flex; flex-wrap:wrap; gap:10px; width:100%;">
                                            <a :href="'/certification-file/' + selectedCert.id"
                                               download
                                               target="_blank"
                                               style="flex:1; min-width:180px; display:inline-flex; align-items:center; justify-content:center; gap:8px; background:#17151C; color:white; padding:11px 18px; border-radius:8px; font-weight:600; font-size:13.5px; text-decoration:none; transition:all 0.15s ease;"
                                               onmouseover="this.style.background='#2C2933';"
                                               onmouseout="this.style.background='#17151C';">
                                                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Download Dokumen
                                            </a>
                                            <button @click="deleteCertification(selectedCert)" 
                                                    style="flex:1; min-width:180px; justify-content:center; background:#FFF0F0; color:#C81E2C; border:1px solid #F8C8CC; padding:11px 18px; border-radius:8px; font-weight:600; font-size:13.5px; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all 0.15s ease;"
                                                    onmouseover="this.style.background='#C81E2C'; this.style.color='white'; this.style.borderColor='#C81E2C';"
                                                    onmouseout="this.style.background='#FFF0F0'; this.style.color='#C81E2C'; this.style.borderColor='#F8C8CC';">
                                                <svg style="width:15px; height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus Sertifikat Ini
                                            </button>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        </template>

                        <template x-if="!selectedCert">
                            <div style="padding:40px 16px; text-align:center; color:#75727C;">
                                <p style="margin:0 0 16px; font-size:13px;">Pengguna ini belum memiliki sertifikat yang diunggah.</p>
                                <button type="button" @click="viewCertModal = false"
                                        style="padding:8px 16px; border-radius:8px; border:1px solid #E7E5E3; background:white; font-size:13px; font-weight:600; cursor:pointer;">
                                    Tutup
                                </button>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .cert-tab-pill {
        padding: 9px 14px;
        border-radius: 10px;
        border: 1.5px solid #E7E5E3;
        background: #ffffff;
        color: #3D3A44;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.15s ease;
        box-shadow: 0 1px 3px rgba(14, 13, 18, 0.04);
    }

    .cert-tab-pill:hover:not(.active) {
        background: #F1F0EE;
        border-color: #D3D0CB;
        color: #17151C;
        transform: translateY(-1px);
    }

    .cert-tab-pill.active {
        background: #17151C;
        border-color: #17151C;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(23, 21, 28, 0.2);
    }

    .cert-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
</style>


@push('scripts')
<script>
    document.addEventListener('alpine:init', function() {
        Alpine.data('usersManager', function() {
            return {
                users: @json($users),
                search: '',
                roleFilter: 'Semua',
                statusFilter: 'Semua',
                currentPage: 1,
                perPage: 5,
                modalOpen: false,
                viewCertModal: false,
                editing: false,
                certImageError: false,
                viewingUser: {},
                selectedCert: null,
                form: {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    role: 'Engineer L1',
                    status: 'Active',
                    position: '',
                    password: '',
                    certification_file_name: '',
                    certification_file: null,
                },

                init() {
                    console.log(' Users Manager initialized!');
                },

                get filteredUsers() {
                    return this.users.filter(u => {
                        const matchSearch = u.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                           u.email.toLowerCase().includes(this.search.toLowerCase());
                        const matchRole = this.roleFilter === 'Semua' || u.role_name === this.roleFilter;
                        const matchStatus = this.statusFilter === 'Semua' || u.status === this.statusFilter;
                        return matchSearch && matchRole && matchStatus;
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

                get modalTitle() {
                    return this.editing ? ' Edit User' : ' Tambah User';
                },

                openModal(user = null) {
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
                            certification_file_name: '',
                            certification_file: null,
                        };
                    } else {
                        this.editing = false;
                        this.form = {
                            id: null,
                            name: '',
                            email: '',
                            phone: '',
                            role: 'Engineer L1',
                            status: 'Active',
                            position: '',
                            password: '',
                            certification_file_name: '',
                            certification_file: null,
                        };
                    }
                    this.modalOpen = true;
                },

                editUser(user) {
                    this.openModal(user);
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.form.certification_file = file;
                        this.form.certification_file_name = file.name;
                    }
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
                            this.modalOpen = false;
                            this.showToast(' User berhasil ' + (this.editing ? 'diperbarui' : 'ditambahkan') + '!');
                        } else {
                            const error = await response.json();
                            this.showToast(' Error: ' + (error.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error(' Error saving user:', error);
                        this.showToast(' Terjadi kesalahan saat menyimpan user.');
                    }
                },

                async deleteUser(user) {
                    if (user.id === {{ auth()->id() }}) {
                        this.showToast(' Anda tidak dapat menghapus akun sendiri!');
                        return;
                    }
                    if (!confirm(`Apakah Anda yakin ingin menghapus user "${user.name}"?`)) return;
                    
                    try {
                        const response = await fetch(`/users/${user.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.users = this.users.filter(u => u.id !== user.id);
                            this.showToast(' User berhasil dihapus!');
                        }
                    } catch (error) {
                        console.error(' Error deleting user:', error);
                        this.showToast(' Terjadi kesalahan saat menghapus user.');
                    }
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
                            this.showToast(` Status user berhasil diubah menjadi ${data.status}!`);
                        }
                    } catch (error) {
                        console.error(' Error toggling user status:', error);
                        this.showToast(' Terjadi kesalahan saat mengubah status user.');
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
                            
                            // Update data user di list dengan trigger reactivity
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
                            
                            // Update data user di list dengan trigger reactivity
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

                async deleteCertification(cert) {
                    if (!cert || !cert.id) {
                        this.showToast('Pilih sertifikat terlebih dahulu.');
                        return;
                    }
                    if (!confirm(`Apakah Anda yakin ingin menghapus sertifikat "${cert.name}"? Dokumen ini juga akan otomatis terhapus dari akun engineer.`)) {
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
                            if (data.user) {
                                const index = this.users.findIndex(u => u.id === data.user.id);
                                if (index !== -1) {
                                    this.users[index] = data.user;
                                    this.users = JSON.parse(JSON.stringify(this.users));
                                    this.viewingUser = data.user;
                                    this.selectedCert = (data.user.certifications && data.user.certifications.length > 0) ? data.user.certifications[0] : null;
                                }
                            }
                            
                            this.viewCertModal = false;
                            this.showToast(`Sertifikasi "${cert.name}" berhasil dihapus!`);
                        } else {
                            this.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    } catch (error) {
                        console.error('Error deleting certification:', error);
                        this.showToast('Terjadi kesalahan saat menghapus sertifikasi.');
                    }
                },





                getStatusBadge(status) {
                    const styles = {
                        'Active': { bg: '#E4F3EA', fg: '#1B7A46', dot: '#1B7A46' },
                        'Inactive': { bg: '#EFEDEC', fg: '#75727C', dot: '#948F99' }
                    };
                    const s = styles[status] || styles['Inactive'];
                    return `<span style="background: ${s.bg}; color: ${s.fg}; font-size: 11.5px; font-weight: 700; padding: 4px 10px 4px 8px; border-radius: 20px; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.1px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                                ${status}
                            </span>`;
                },

                getCertificationStatusBadge(status) {
                    const styles = {
                        'approved': { bg: '#E4F3EA', fg: '#1B7A46', dot: '#1B7A46', text: 'Disetujui' },
                        'pending': { bg: '#FFF3E0', fg: '#E67E22', dot: '#E67E22', text: 'Menunggu' },
                        'rejected': { bg: '#FFEBEE', fg: '#C81E2C', dot: '#C81E2C', text: 'Ditolak' }
                    };
                    const s = styles[status] || styles['pending'];
                    return `<span style="background: ${s.bg}; color: ${s.fg}; font-size: 11.5px; font-weight: 700; padding: 4px 10px 4px 8px; border-radius: 20px; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.1px;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                                ${s.text}
                            </span>`;
                },

                showToast(message) {
                    const toast = document.createElement('div');
                    toast.style.cssText = 'position:fixed; bottom:16px; right:16px; background:#17151C; color:white; padding:12px 24px; border-radius:8px; box-shadow:0 16px 40px rgba(14,13,18,0.12); font-size:14px; animation:fadeInUp 0.18s ease; z-index:999999;';
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                }
            };
        });
    });
</script>
@endpush
@endsection