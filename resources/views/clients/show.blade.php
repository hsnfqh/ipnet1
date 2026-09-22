@extends('layouts.app')

@section('title', 'Detail Client - ' . $client->name)

@push('styles')
<style>
    .client-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.02);
    }

    .form-label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 9px 14px;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 500;
        color: #1E293B;
        background: #FAFBFD;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
        box-sizing: border-box;
    }
    .form-input:focus {
        border-color: #8F0A0D;
        box-shadow: 0 0 0 3px rgba(143,10,13,0.08);
        background: #FFFFFF;
    }

    textarea.form-input {
        resize: vertical;
        line-height: 1.6;
    }

    /* Buttons */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-size: 12px;
        font-weight: 700;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all .18s ease;
        box-shadow: 0 2px 8px rgba(143,10,13,0.20);
        text-decoration: none;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #73080A 0%, #9E0E1D 100%);
        box-shadow: 0 4px 14px rgba(143,10,13,0.30);
        transform: translateY(-1px);
    }

    .btn-danger {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 20px;
        background: #FEF2F2;
        color: #8F0A0D;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #FECACA;
        border-radius: 10px;
        cursor: pointer;
        transition: all .18s ease;
        text-decoration: none;
    }
    .btn-danger:hover {
        background: #8F0A0D;
        color: #FFFFFF;
        border-color: #8F0A0D;
        box-shadow: 0 3px 10px rgba(143,10,13,0.22);
    }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: transparent;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        cursor: pointer;
        transition: all .15s ease;
        text-decoration: none;
    }
    .btn-ghost:hover {
        background: #F8FAFC;
        color: #1E293B;
        border-color: #CBD5E1;
    }

    .link-red {
        font-size: 11.5px;
        font-weight: 700;
        color: #8F0A0D;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: color .15s;
    }
    .link-red:hover { color: #73080A; text-decoration: underline; }

    /* Project cards */
    .project-item {
        background: #FAFBFD;
        border: 1px solid #E8EDF3;
        border-radius: 12px;
        padding: 12px 14px;
        transition: border-color .15s, box-shadow .15s;
    }
    .project-item:hover {
        border-color: #CBD5E1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .project-item a { text-decoration: none; color: #1E293B; }
    .project-item a:hover { color: #8F0A0D; }

    /* Delete Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.45);
        backdrop-filter: blur(3px);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        max-width: 420px;
        width: 100%;
        overflow: hidden;
        animation: modalIn .22s cubic-bezier(0.16,1,0.3,1);
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.95) translateY(10px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden font-sans" style="background:#F5F7FA;"
     x-data="clientShowPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Client'])

        <div style="padding: 24px 28px 40px; max-width: 1200px; margin: 0 auto;">

            {{-- Flash Message --}}
            @if(session('success'))
                <div style="margin-bottom:16px; padding:12px 16px; background:#F0FDF4; border:1px solid #BBF7D0; border-radius:10px; color:#166534; font-size:12px; font-weight:600; display:flex; align-items:center; justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <svg style="width:16px; height:16px; color:#16A34A; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ session('success') }}
                    </div>
                    <button onclick="this.parentElement.remove()" style="color:#16A34A; font-weight:700; background:none; border:none; cursor:pointer; padding:0 4px;">✕</button>
                </div>
            @endif

            {{-- Breadcrumb --}}
            <div style="display:flex; align-items:center; gap:6px; font-size:11.5px; font-weight:600; color:#94A3B8; margin-bottom:16px;">
                <a href="{{ route('clients.index') }}" style="color:#64748B; text-decoration:none; hover:color:#1E293B;">Client</a>
                <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span style="color:#1E293B; font-weight:700;">{{ $client->name }}</span>
            </div>

            {{-- Page Header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:40px; height:40px; background:#FEF2F2; border:1px solid #FECACA; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg style="width:18px; height:18px; color:#8F0A0D;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:11px; font-weight:700; color:#8F0A0D; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 2px;">Detail & Edit Client</p>
                        <h1 style="font-size:20px; font-weight:700; color:#1E293B; letter-spacing:-0.3px; margin:0;">{{ $client->name }}</h1>
                    </div>
                </div>

                {{-- Delete Button --}}
                <button type="button" @click="showDeleteModal = true" class="btn-danger">
                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Client
                </button>
            </div>

            {{-- Main 2-Column Grid --}}
            <div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

                {{-- ══ LEFT: Form Card ══ --}}
                <div class="client-card" style="padding: 28px;">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="display:flex; flex-direction:column; gap:20px;">

                            {{-- Name --}}
                            <div>
                                <label class="form-label">Nama Client</label>
                                <input type="text" name="name" value="{{ $client->name }}" required class="form-input" placeholder="Masukkan nama client...">
                            </div>

                            {{-- Address --}}
                            <div>
                                <label class="form-label">Alamat Utama</label>
                                <textarea name="address" rows="4" class="form-input" placeholder="Masukkan alamat lengkap...">{{ $client->address }}</textarea>
                                <div style="margin-top:8px;">
                                    <a href="#" class="link-red">
                                        <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Tambah Alamat Sekunder
                                    </a>
                                </div>
                            </div>

                            {{-- Department Section --}}
                            <div>
                                <label class="form-label">Departemen</label>
                                <input type="text" name="department" value="{{ $client->department }}" class="form-input" placeholder="Nama departemen...">

                                <div style="margin-top:10px; padding:12px 16px; background:#F8FAFC; border:1px solid #E8EDF3; border-radius:10px; display:flex; align-items:center; justify-content:space-between;">
                                    <span style="font-size:11.5px; color:#64748B; font-weight:500;">PIC Departemen</span>
                                    <a href="#" class="link-red">
                                        <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Tambah PIC
                                    </a>
                                </div>
                            </div>

                            {{-- Add Department --}}
                            <div>
                                <a href="#" class="link-red">
                                    <svg style="width:12px; height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Tambah Departemen Baru
                                </a>
                            </div>

                            {{-- Action Buttons --}}
                            <div style="display:flex; align-items:center; justify-content:space-between; padding-top:16px; border-top:1px solid #F1F5F9; flex-wrap:wrap; gap:10px;">
                                <a href="{{ route('clients.index') }}" class="btn-ghost">
                                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    Kembali
                                </a>
                                <button type="submit" class="btn-primary">
                                    <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ══ RIGHT: Projects Panel ══ --}}
                <div style="display:flex; flex-direction:column; gap:16px;">

                    {{-- Projects Card --}}
                    <div class="client-card" style="padding:20px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #F1F5F9;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:#8F0A0D; display:inline-block;"></span>
                                <h3 style="font-size:13px; font-weight:700; color:#1E293B; margin:0;">Projects</h3>
                            </div>
                            @if($client->projects && $client->projects->count() > 0)
                                <span style="font-size:11px; font-weight:700; color:#8F0A0D; background:#FEF2F2; border:1px solid #FECACA; padding:2px 8px; border-radius:999px;">
                                    {{ $client->projects->count() }}
                                </span>
                            @endif
                        </div>

                        @if($client->projects && $client->projects->count() > 0)
                            <div style="display:flex; flex-direction:column; gap:8px;">
                                @foreach($client->projects as $p)
                                    <div class="project-item">
                                        <a href="{{ route('projects.show', $p->id) }}" style="font-size:12.5px; font-weight:700; display:block; margin-bottom:6px;">
                                            {{ $p->name }}
                                        </a>
                                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:4px;">
                                            <span style="font-size:11px; color:#64748B; font-weight:500;">
                                                Rp {{ number_format($p->contract_value, 0, ',', '.') }}
                                            </span>
                                            @php
                                                $sLow = strtolower($p->status ?? '');
                                                $sBadge = match(true) {
                                                    str_contains($sLow,'complete')||str_contains($sLow,'done')       => 'background:#F0FDF4;color:#166534;border:1px solid #BBF7D0;',
                                                    str_contains($sLow,'progress')||str_contains($sLow,'active')    => 'background:#FFFBEB;color:#92400E;border:1px solid #FDE68A;',
                                                    str_contains($sLow,'opportunity')                               => 'background:#EFF6FF;color:#1D4ED8;border:1px solid #BFDBFE;',
                                                    str_contains($sLow,'pending')||str_contains($sLow,'hold')       => 'background:#FFF7ED;color:#9A3412;border:1px solid #FED7AA;',
                                                    default                                                         => 'background:#F8FAFC;color:#475569;border:1px solid #E2E8F0;',
                                                };
                                            @endphp
                                            <span style="font-size:10px; font-weight:700; padding:2px 8px; border-radius:999px; {{ $sBadge }}">
                                                {{ $p->status }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="padding:24px 16px; text-align:center; background:#F8FAFC; border:1px dashed #CBD5E1; border-radius:10px;">
                                <svg style="width:32px; height:32px; color:#CBD5E1; margin:0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                </svg>
                                <p style="font-size:12px; font-weight:600; color:#94A3B8; margin:0 0 2px;">Belum ada project</p>
                                <p style="font-size:11px; color:#CBD5E1; margin:0;">Tambahkan project terkait client ini</p>
                            </div>
                        @endif
                    </div>

                    {{-- Client Info Card --}}
                    <div class="client-card" style="padding:16px;">
                        <p style="font-size:10.5px; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:0.06em; margin:0 0 10px;">Info Client</p>
                        <div style="display:flex; flex-direction:column; gap:8px; font-size:11.5px;">
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:#64748B; font-weight:500;">ID</span>
                                <span style="color:#1E293B; font-weight:700; font-family:monospace;">#{{ $client->id }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:#64748B; font-weight:500;">Dibuat</span>
                                <span style="color:#1E293B; font-weight:600;">{{ $client->created_at ? $client->created_at->format('d M Y') : '—' }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between;">
                                <span style="color:#64748B; font-weight:500;">Diperbarui</span>
                                <span style="color:#1E293B; font-weight:600;">{{ $client->updated_at ? $client->updated_at->format('d M Y') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- ═══ DELETE CONFIRMATION MODAL ═══ --}}
    <div class="modal-overlay" x-show="showDeleteModal" x-cloak @click.self="showDeleteModal = false"
         style="display:none;" :style="showDeleteModal ? 'display:flex;' : 'display:none;'">
        <div class="modal-box">
            {{-- Modal Header --}}
            <div style="padding:20px 24px 16px; border-bottom:1px solid #FEE2E2; background:#FFF5F5; display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; background:#FEE2E2; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg style="width:18px; height:18px; color:#8F0A0D;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:14px; font-weight:700; color:#1E293B; margin:0 0 2px;">Hapus Client?</h3>
                    <p style="font-size:11.5px; color:#64748B; margin:0;">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>

            {{-- Modal Body --}}
            <div style="padding:20px 24px;">
                <p style="font-size:12.5px; color:#475569; line-height:1.6; margin:0 0 14px;">
                    Anda akan menghapus client <strong style="color:#1E293B;">{{ $client->name }}</strong> secara permanen.
                    Semua data yang terkait dengan client ini mungkin terpengaruh.
                </p>
                <div style="background:#FFF5F5; border:1px solid #FECACA; border-radius:10px; padding:12px 14px; display:flex; align-items:flex-start; gap:8px;">
                    <svg style="width:14px; height:14px; color:#8F0A0D; flex-shrink:0; margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p style="font-size:11.5px; color:#8F0A0D; font-weight:600; margin:0;">
                        Pastikan Anda sudah yakin sebelum melanjutkan. Data yang dihapus tidak dapat dikembalikan.
                    </p>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div style="padding:14px 24px 20px; display:flex; align-items:center; justify-content:flex-end; gap:10px;">
                <button type="button" @click="showDeleteModal = false" class="btn-ghost">
                    Batal
                </button>
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#8F0A0D 0%,#C01020 100%);">
                        <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
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
        };
    }
</script>
@endpush
@endsection
