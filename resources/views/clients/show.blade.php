@extends('layouts.app')

@section('title', 'View Client - ' . $client->name)

@php
    // Parse department values if comma-separated
    $rawDepts = array_filter(array_map('trim', explode(',', (string) ($client->department ?? ''))));
    if (empty($rawDepts)) {
        $rawDepts = [$client->department ?: ''];
    }
@endphp

@section('content')
<div class="flex h-screen overflow-hidden font-sans" style="background:#FFFFFF;"
     x-data="clientShowPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto" style="background:#FFFFFF;">
        @include('components.topbar', ['title' => 'Client'])

        <div style="padding: 24px 32px 48px; max-width: 1300px; margin: 0 auto;">

            {{-- Flash Message --}}
            @if(session('success'))
                <div style="margin-bottom: 20px; padding: 12px 18px; background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; color: #166534; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 16px; height: 16px; color: #16A34A; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" style="color: #16A34A; font-weight: 700; background: none; border: none; cursor: pointer; padding: 0 4px; font-size: 14px;">✕</button>
                </div>
            @endif

            {{-- Breadcrumb --}}
            <nav style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 500; color: #64748B; margin-bottom: 12px;">
                <a href="{{ route('clients.index') }}" style="color: #1E293B; text-decoration: none; font-weight: 600;" onmouseover="this.style.color='#8F0A0D'" onmouseout="this.style.color='#1E293B'">Client</a>
                <svg style="width: 12px; height: 12px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span style="color: #1E293B; font-weight: 700; text-transform: uppercase;">{{ $client->name }}</span>
                <svg style="width: 12px; height: 12px; color: #94A3B8;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span style="color: #94A3B8;">Edit</span>
            </nav>

            {{-- Page Title Header --}}
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 24px;">
                <div style="width: 32px; height: 32px; border: 1.5px solid #8F0A0D; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #8F0A0D; flex-shrink: 0; background: #FFFFFF;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h1 style="font-size: 22px; font-weight: 700; color: #111827; letter-spacing: -0.02em; margin: 0;">View Client</h1>
            </div>

            {{-- Two Column Content --}}
            <div style="display: flex; gap: 32px; align-items: flex-start;">

                {{-- LEFT COLUMN: Form --}}
                <div style="flex: 1; min-width: 0;">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- NAME --}}
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">NAME</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                   style="width: 100%; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 500; color: #1F2937; background: #FFFFFF; outline: none; transition: border-color .15s, box-shadow .15s; box-sizing: border-box;"
                                   onfocus="this.style.borderColor='#8F0A0D'; this.style.boxShadow='0 0 0 3px rgba(143,10,13,0.08)';"
                                   onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';">
                        </div>

                        {{-- ADDRESS 1 --}}
                        <div style="margin-bottom: 12px;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">ADDRESS 1</label>
                            <textarea name="address" rows="5"
                                      style="width: 100%; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 400; color: #1F2937; background: #FFFFFF; outline: none; line-height: 1.6; resize: vertical; transition: border-color .15s, box-shadow .15s; box-sizing: border-box;"
                                      onfocus="this.style.borderColor='#8F0A0D'; this.style.boxShadow='0 0 0 3px rgba(143,10,13,0.08)';"
                                      onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
                                      placeholder="Masukkan alamat utama client...">{{ old('address', $client->address) }}</textarea>

                            <div style="margin-top: 6px;">
                                <button type="button" @click="showSecondaryAddress = !showSecondaryAddress"
                                        style="background: none; border: none; padding: 0; font-size: 11.5px; font-weight: 700; color: #8F0A0D; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                        onmouseover="this.style.textDecoration='underline'"
                                        onmouseout="this.style.textDecoration='none'">
                                    + CREATE SECONDARY ADDRESS
                                </button>
                            </div>

                            {{-- Secondary Address drawer --}}
                            <div x-show="showSecondaryAddress" x-cloak style="margin-top: 10px;">
                                <label style="display: block; font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">ADDRESS 2 (SECONDARY)</label>
                                <textarea name="notes" rows="3"
                                          style="width: 100%; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 400; color: #1F2937; background: #FFFFFF; outline: none; line-height: 1.6; resize: vertical; box-sizing: border-box;"
                                          placeholder="Alamat sekunder / cabang / catatan tambahan...">{{ old('notes', $client->notes) }}</textarea>
                            </div>
                        </div>

                        {{-- DEPARTMENTS (Dynamic list) --}}
                        <template x-for="(dept, idx) in departments" :key="idx">
                            <div style="margin-top: 20px;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                                    <label style="font-size: 11px; font-weight: 700; color: #4B5563; text-transform: uppercase; letter-spacing: 0.05em;" x-text="'DEPARTMENT ' + (idx + 1)"></label>
                                    <template x-if="departments.length > 1">
                                        <button type="button" @click="removeDepartment(idx)" style="background: none; border: none; color: #9CA3AF; font-size: 11px; font-weight: 600; cursor: pointer;" onmouseover="this.style.color='#8F0A0D'" onmouseout="this.style.color='#9CA3AF'">
                                            ✕ Hapus Dept
                                        </button>
                                    </template>
                                </div>
                                <input type="text" name="department[]" x-model="departments[idx]"
                                       style="width: 100%; padding: 10px 14px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 13px; font-weight: 500; color: #1F2937; background: #FFFFFF; outline: none; transition: border-color .15s, box-shadow .15s; box-sizing: border-box;"
                                       onfocus="this.style.borderColor='#8F0A0D'; this.style.boxShadow='0 0 0 3px rgba(143,10,13,0.08)';"
                                       onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
                                       :placeholder="'Nama Departemen ' + (idx + 1)">

                                {{-- Gray Box: + CREATE PIC [DEPT] --}}
                                <div style="margin-top: 8px;">
                                    <button type="button" @click="togglePic(idx)"
                                            style="width: 100%; padding: 12px 16px; background: #F8FAFC; border: 1px solid #F1F5F9; border-radius: 8px; text-align: left; cursor: pointer; transition: all .15s; box-sizing: border-box;"
                                            onmouseover="this.style.background='#F1F5F9';"
                                            onmouseout="this.style.background='#F8FAFC';">
                                        <span style="font-size: 11.5px; font-weight: 700; color: #8F0A0D; text-transform: uppercase; letter-spacing: 0.04em;">
                                            + CREATE PIC <span x-text="(departments[idx] || ('DEPARTMENT ' + (idx + 1))).toUpperCase()"></span>
                                        </span>
                                    </button>

                                    {{-- Expandable PIC Form for this department --}}
                                    <div x-show="activePicDeptIndex === idx" x-cloak
                                         style="margin-top: 8px; padding: 16px; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 10px; display: flex; flex-direction: column; gap: 12px;">
                                        <p style="font-size: 11px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Kontak PIC Departemen</p>
                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                            <div>
                                                <label style="display: block; font-size: 10.5px; font-weight: 600; color: #64748B; margin-bottom: 4px;">Nama PIC</label>
                                                <input type="text" name="pic_name" value="{{ old('pic_name', $client->pic_name) }}" placeholder="Nama PIC..."
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 12.5px; color: #1E293B; background: #FFFFFF; outline: none; box-sizing: border-box;">
                                            </div>
                                            <div>
                                                <label style="display: block; font-size: 10.5px; font-weight: 600; color: #64748B; margin-bottom: 4px;">Telepon / WA</label>
                                                <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="Nomor Telepon..."
                                                       style="width: 100%; padding: 8px 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 12.5px; color: #1E293B; background: #FFFFFF; outline: none; box-sizing: border-box;">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 10.5px; font-weight: 600; color: #64748B; margin-bottom: 4px;">Email</label>
                                            <input type="email" name="email" value="{{ old('email', $client->email) }}" placeholder="Email PIC..."
                                                   style="width: 100%; padding: 8px 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 12.5px; color: #1E293B; background: #FFFFFF; outline: none; box-sizing: border-box;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- + CREATE DEPARTMENT Link --}}
                        <div style="margin-top: 16px;">
                            <button type="button" @click="addDepartment()"
                                    style="background: none; border: none; padding: 0; font-size: 11.5px; font-weight: 700; color: #8F0A0D; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;"
                                    onmouseover="this.style.textDecoration='underline'"
                                    onmouseout="this.style.textDecoration='none'">
                                + CREATE DEPARTMENT
                            </button>
                        </div>

                        {{-- Action Buttons --}}
                        <div style="display: flex; align-items: center; justify-content: center; gap: 24px; margin-top: 48px; margin-bottom: 24px;">
                            <a href="{{ route('clients.index') }}"
                               style="font-size: 13px; font-weight: 600; color: #4B5563; text-decoration: none; padding: 10px 18px; transition: color .15s;"
                               onmouseover="this.style.color='#111827'"
                               onmouseout="this.style.color='#4B5563'">
                                Cancel
                            </a>

                            <button type="submit"
                                    style="padding: 10px 36px; background: #8F0A0D; color: #FFFFFF; font-size: 13px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 8px rgba(143,10,13,0.25); transition: all .18s;"
                                    onmouseover="this.style.background='#73080A'; this.style.transform='translateY(-1px)';"
                                    onmouseout="this.style.background='#8F0A0D'; this.style.transform='none';">
                                Update Client
                            </button>
                        </div>

                        {{-- Subtle Delete Client Link at the bottom --}}
                        <div style="border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 24px; display: flex; align-items: center; justify-content: flex-start;">
                            <button type="button" @click="showDeleteModal = true"
                                    style="background: none; border: none; padding: 0; font-size: 11.5px; font-weight: 600; color: #DC2626; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: color .15s;"
                                    onmouseover="this.style.color='#991B1B'; this.style.textDecoration='underline';"
                                    onmouseout="this.style.color='#DC2626'; this.style.textDecoration='none';">
                                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus data client ini
                            </button>
                        </div>
                    </form>
                </div>

                {{-- RIGHT COLUMN: Projects Panel --}}
                <div style="width: 340px; flex-shrink: 0;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 14px;">Projects</h3>

                    @if($client->projects && $client->projects->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            @foreach($client->projects as $p)
                                <div style="background: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px; transition: all .15s;"
                                     onmouseover="this.style.borderColor='#CBD5E1'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.04)';"
                                     onmouseout="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';">
                                    <a href="{{ route('projects.show', $p->id) }}" style="font-size: 13px; font-weight: 700; color: #111827; text-decoration: none; display: block; margin-bottom: 6px;"
                                       onmouseover="this.style.color='#8F0A0D'" onmouseout="this.style.color='#111827'">
                                        {{ $p->name }}
                                    </a>
                                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11.5px;">
                                        <span style="color: #6B7280; font-weight: 600;">
                                            Rp {{ number_format($p->contract_value ?? 0, 0, ',', '.') }}
                                        </span>
                                        <span style="padding: 2px 8px; border-radius: 999px; font-size: 10.5px; font-weight: 700; background: #F3F4F6; color: #374151;">
                                            {{ $p->status ?? 'Active' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="background: #F8FAFC; border: 1px solid #F1F5F9; border-radius: 10px; padding: 24px 18px; text-align: center;">
                            <p style="font-size: 12px; font-weight: 600; color: #374151; line-height: 1.5; margin: 0;">
                                Don't have any project, please add current or latest project here
                            </p>
                        </div>
                    @endif
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
