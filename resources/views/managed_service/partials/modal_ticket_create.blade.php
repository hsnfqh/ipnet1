{{-- MODAL CREATE TICKET (STANDAR HELPDESK & SEVERITY IP-NET SESUAI GAMBAR 2) --}}
<template x-teleport="body">
    <div x-show="isCreateModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @keydown.escape.window="isCreateModalOpen = false">
        
        <div class="bg-white rounded-2xl w-[780px] max-w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up"
             @click.away="isCreateModalOpen = false"
             x-data="{
                 selectedCategory: 'Jaringan & Internet',
                 selectedPriority: 'Critical',
                 fileName: ''
             }">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] px-5 sm:px-6 py-4 shrink-0 bg-white">
                <div>
                    <div class="flex items-center gap-2 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-0.5">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        <span>FORMULIR TIKET ADUAN &amp; LAYANAN SLA</span>
                    </div>
                    <h3 class="text-[17px] font-extrabold text-[#1E293B] tracking-tight">Buat Tiket Gangguan Baru</h3>
                </div>
                <button type="button" @click="isCreateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form action="{{ route('ms.tickets.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf

                <div class="p-5 sm:p-6 overflow-y-auto space-y-6 text-[12.5px] flex-1">
                    
                    {{-- ======================================================== --}}
                    {{-- 01. IDENTITAS KLIEN & PELAPOR                            --}}
                    {{-- ======================================================== --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-2.5 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                01
                            </div>
                            <div>
                                <h4 class="text-[13px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Identitas Klien &amp; Pelapor</h4>
                                <p class="text-[11.5px] text-slate-500 leading-snug">Informasi instansi perusahaan dan kontak yang dapat dihubungi</p>
                            </div>
                        </div>

                        <div class="space-y-3.5">
                            {{-- Nama Perusahaan / Klien --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Perusahaan / Klien (PT / Instansi) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" 
                                       name="client_name" 
                                       list="client_suggestions"
                                       required
                                       placeholder="Masukkan nama perusahaan Anda"
                                       class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                <datalist id="client_suggestions">
                                    @if(isset($clients) && count($clients) > 0)
                                        @foreach($clients as $c)
                                            <option value="{{ is_object($c) ? ($c->client ?: $c->name) : $c }}">
                                        @endforeach
                                    @endif
                                </datalist>
                            </div>

                            {{-- Nama PIC & Email --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Nama PIC / Pelapor <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="reported_by" required
                                           placeholder="Masukkan nama lengkap Anda"
                                           value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                           class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Email Kontak Resmi <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="email" name="contact_email" required
                                           placeholder="Masukkan alamat email aktif"
                                           value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                           class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>

                            {{-- No Telepon/WA & Tanggal Waktu --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Nomor WhatsApp / Telepon <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="contact_phone" required
                                           placeholder="Masukkan nomor telepon / WhatsApp"
                                           class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Tanggal &amp; Waktu Gangguan <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="datetime-local" name="incident_date" required
                                           value="{{ now()->format('Y-m-d\TH:i') }}"
                                           class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- 02. KATEGORI & TINGKAT URGENSI (SEVERITY)                --}}
                    {{-- ======================================================== --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-2.5 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                02
                            </div>
                            <div>
                                <h4 class="text-[13px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Kategori &amp; Tingkat Urgensi (Severity)</h4>
                                <p class="text-[11.5px] text-slate-500 leading-snug">Pilih jenis layanan terdampak dan tingkat keparahan gangguan</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            {{-- Kategori Layanan --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Kategori Layanan Terdampak <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    @php
                                        $ticketCategories = [
                                            ['val' => 'Jaringan & Internet', 'label' => 'Jaringan & Internet', 'desc' => 'Koneksi lambat / putus'],
                                            ['val' => 'Firewall & Security', 'label' => 'Firewall & Security', 'desc' => 'Blokir / VPN / IPS'],
                                            ['val' => 'Server & Cloud', 'label' => 'Server & Cloud', 'desc' => 'Server down / Storage'],
                                            ['val' => 'Switch / Router', 'label' => 'Switch / Router', 'desc' => 'Perangkat error / mati'],
                                            ['val' => 'WiFi & Hotspot', 'label' => 'WiFi & Hotspot', 'desc' => 'SSID / Login portal'],
                                            ['val' => 'Layanan Lainnya', 'label' => 'Layanan Lainnya', 'desc' => 'Bantuan teknis umum'],
                                        ];
                                    @endphp

                                    @foreach($ticketCategories as $cat)
                                        <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all duration-150 select-none"
                                               :class="selectedCategory === '{{ $cat['val'] }}' ? 'border-[#8F0A0D] bg-[#FEF2F2] ring-2 ring-[#8F0A0D]/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                            <input type="radio" name="category" value="{{ $cat['val'] }}" x-model="selectedCategory" class="sr-only" required>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-slate-900 leading-tight">{{ $cat['label'] }}</span>
                                                <span class="w-2 h-2 rounded-full" :class="selectedCategory === '{{ $cat['val'] }}' ? 'bg-[#8F0A0D]' : 'bg-slate-200'"></span>
                                            </div>
                                            <span class="text-[11px] text-slate-500 leading-tight mt-0.5">{{ $cat['desc'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Tingkat Urgensi Severity (LOW, MEDIUM, HIGH, CRITICAL) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Tingkat Urgensi Kendala (Severity) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    
                                    {{-- LOW --}}
                                    <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                           :class="selectedPriority === 'Low' ? 'border-blue-500 bg-blue-50/70 ring-2 ring-blue-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                        <input type="radio" name="priority" value="Low" x-model="selectedPriority" class="sr-only" required>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-extrabold" :class="selectedPriority === 'Low' ? 'text-blue-700' : 'text-slate-700'">LOW</span>
                                            <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Low' ? 'bg-blue-500' : 'bg-slate-300'"></span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-600 leading-tight">Gangguan minor / tidak berdampak sistem.</p>
                                    </label>

                                    {{-- MEDIUM --}}
                                    <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                           :class="selectedPriority === 'Medium' ? 'border-amber-500 bg-amber-50/70 ring-2 ring-amber-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                        <input type="radio" name="priority" value="Medium" x-model="selectedPriority" class="sr-only" required>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-extrabold" :class="selectedPriority === 'Medium' ? 'text-amber-700' : 'text-slate-700'">MEDIUM</span>
                                            <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Medium' ? 'bg-amber-500' : 'bg-slate-300'"></span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-600 leading-tight">Fungsi sebagian lambat / terganggu parsial.</p>
                                    </label>

                                    {{-- HIGH --}}
                                    <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                           :class="selectedPriority === 'High' ? 'border-orange-500 bg-orange-50/70 ring-2 ring-orange-500/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                        <input type="radio" name="priority" value="High" x-model="selectedPriority" class="sr-only" required>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-extrabold" :class="selectedPriority === 'High' ? 'text-orange-700' : 'text-slate-700'">HIGH</span>
                                            <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'High' ? 'bg-orange-500' : 'bg-slate-300'"></span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-600 leading-tight">Layanan utama terhenti pada banyak user.</p>
                                    </label>

                                    {{-- CRITICAL --}}
                                    <label class="relative p-3 rounded-xl border cursor-pointer transition-all select-none text-left flex flex-col justify-between"
                                           :class="selectedPriority === 'Critical' ? 'border-[#8F0A0D] bg-[#FEF2F2] ring-2 ring-[#8F0A0D]/20 shadow-2xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300'">
                                        <input type="radio" name="priority" value="Critical" x-model="selectedPriority" class="sr-only" required>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-extrabold" :class="selectedPriority === 'Critical' ? 'text-[#8F0A0D]' : 'text-slate-700'">CRITICAL</span>
                                            <span class="w-2 h-2 rounded-full" :class="selectedPriority === 'Critical' ? 'bg-[#8F0A0D]' : 'bg-slate-300'"></span>
                                        </div>
                                        <p class="text-[10.5px] text-slate-600 leading-tight">Total Down! Operasional mati total.</p>
                                    </label>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ======================================================== --}}
                    {{-- 03. RINCIAN GANGGUAN & BUKTI LAMPIRAN                    --}}
                    {{-- ======================================================== --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4 pb-2.5 border-b border-slate-100">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#BA1B1D] text-white font-mono font-black text-xs flex items-center justify-center shrink-0 shadow-sm shadow-red-950/20">
                                03
                            </div>
                            <div>
                                <h4 class="text-[13px] font-extrabold uppercase tracking-wider text-slate-900 leading-snug">Rincian Gangguan &amp; Bukti Lampiran</h4>
                                <p class="text-[11.5px] text-slate-500 leading-snug">Deskripsikan permasalahan secara detail dan sertakan file bukti jika ada</p>
                            </div>
                        </div>

                        <div class="space-y-3.5">
                            {{-- Subjek / Judul Kendala Singkat --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Subjek / Judul Kendala Singkat <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" name="title" required
                                       placeholder="Masukkan judul atau ringkasan kendala yang dialami"
                                       class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-semibold text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>

                            {{-- Deskripsi Masalah --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Deskripsi Lengkap Masalah &amp; Kronologi <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <textarea name="description" rows="3.5" required
                                          placeholder="Jelaskan secara rinci kendala teknis yang dialami, perangkat atau lokasi yang terdampak, serta kronologi kejadian..."
                                          class="w-full px-4 py-2.5 text-xs sm:text-[13px] font-medium text-slate-800 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs"></textarea>
                            </div>

                            {{-- Tugaskan ke Teknisi PIC (Internal SLA) --}}
                            @php
                                $engList = $engineers ?? ($maintenanceEngineers ?? collect([]));
                            @endphp
                            @if($engList->count() > 0)
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Tugaskan ke Teknisi PIC (Opsional)
                                    </label>
                                    <select name="assigned_to"
                                            class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs sm:text-[12.5px] font-semibold text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                        <option value="">-- Tugaskan ke Tim Maintenance / Belum Ditugaskan --</option>
                                        @foreach($engList as $eng)
                                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Upload File Lampiran Box (Gambar 2 Dropzone) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Lampiran Screenshot / Foto / Dokumen <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-5 border-2 border-dashed border-slate-300 rounded-xl hover:border-[#8F0A0D] hover:bg-red-50/20 transition cursor-pointer group bg-[#F8FAFC]">
                                    <input type="file" 
                                           name="attachment" 
                                           accept=".jpg,.jpeg,.png,.pdf,.docx,.doc" 
                                           class="sr-only"
                                           @change="fileName = $event.target.files.length ? $event.target.files[0].name : ''">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 group-hover:bg-red-100 text-slate-500 group-hover:text-[#8F0A0D] flex items-center justify-center mb-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700 group-hover:text-[#8F0A0D] transition" x-text="fileName ? 'File Terpilih: ' + fileName : 'Klik untuk memilih file lampiran pendukung'">
                                        Klik untuk memilih file lampiran pendukung
                                    </span>
                                    <span class="text-[11px] text-slate-400 mt-0.5">Format yang didukung: JPG, PNG, PDF, DOCX (Maksimal 10MB)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 px-6 border-t border-[#E2E8F0] bg-[#FAF9F8] shrink-0">
                    <div class="flex items-center gap-2 text-[11px] text-slate-600">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Data terlindungi aman dan langsung masuk ke antrean tim NOC &amp; Engineer IP-NET.</span>
                    </div>

                    <div class="flex items-center gap-2.5 justify-end">
                        <button type="button" 
                                @click="isCreateModalOpen = false" 
                                class="px-4 py-2 text-[12.5px] font-bold text-[#475569] hover:bg-gray-100 border border-gray-300 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-gradient-to-r from-[#8F0A0D] to-[#BA1B1D] hover:from-[#7A080B] hover:to-[#9E0E1D] rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                            <span>Kirim Tiket Aduan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</template>
