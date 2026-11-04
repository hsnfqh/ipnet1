{{-- MODAL CATAT GANGGUAN PERANGKAT & DOWNTIME SLA --}}
<div x-show="isDowntimeModalOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
     x-data="downtimeModalForm()"
     style="display: none;">

    <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden anim-hero-reveal"
         @click.outside="isDowntimeModalOpen = false">
        
        {{-- Header Modal --}}
        <div class="px-6 py-4.5 bg-gradient-to-r from-slate-900 to-[#1E293B] text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#8F0A0D] flex items-center justify-center text-white shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-[15px] font-bold text-white tracking-tight">Catat Gangguan &amp; Downtime Perangkat</h3>
                    <p class="text-[11.5px] text-slate-300">Data insiden akan otomatis masuk ke tabel perhitungan SLA Availability bulanan.</p>
                </div>
            </div>
            <button type="button" 
                    @click="isDowntimeModalOpen = false" 
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('ms.downtime.store') }}" method="POST" class="p-6 space-y-4.5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- Klien / Proyek --}}
                <div class="sm:col-span-2">
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Klien / Nama Proyek <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <select name="client_name" required class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                        <option value="">-- Pilih Klien / Proyek Kontrak SLA --</option>
                        @foreach($operateProjects as $proj)
                            <option value="{{ $proj->client ?: $proj->name }}" data-project-id="{{ $proj->id }}">
                                {{ $proj->client ?: $proj->name }} &bull; Kontrak {{ $proj->sla_tier ?: 'Gold' }}
                            </option>
                        @endforeach
                        <option value="Klien Korporat">Klien Korporat (Umum)</option>
                    </select>
                </div>

                {{-- Kategori Perangkat --}}
                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Perangkat <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <select name="device_category" required class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                        <option value="Router">Router (Core / Edge)</option>
                        <option value="Switch">Switch (Distribution / Access)</option>
                        <option value="Firewall">Firewall / Security Appliance</option>
                        <option value="Server">Server / Gateway</option>
                        <option value="Access Point">Access Point / Wireless AP</option>
                        <option value="Radio Wireless">Radio Wireless / PTP</option>
                        <option value="Perangkat Jaringan">Perangkat Jaringan Lainnya</option>
                    </select>
                </div>

                {{-- Nama Perangkat --}}
                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama / Tipe Perangkat <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <input type="text" name="device_name" required placeholder="Contoh: Router Core Cisco ISR 4331" class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                </div>

                {{-- Mulai Down --}}
                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Mulai Gangguan (Down) <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <input type="datetime-local" 
                           name="start_down" 
                           x-model="startDown" 
                           @input="recalcDuration()" 
                           required 
                           class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                </div>

                {{-- Up Kembali --}}
                <div>
                    <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu Up Kembali (Normal)
                    </label>
                    <input type="datetime-local" 
                           name="end_down" 
                           x-model="endDown" 
                           @input="recalcDuration()" 
                           class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                    <p class="text-[10.5px] text-slate-400 mt-1">Kosongkan jika saat ini perangkat masih mengalami gangguan.</p>
                </div>

            </div>

            {{-- Realtime Auto-Duration Preview Box --}}
            <div x-show="calculatedHours > 0" 
                 x-transition 
                 class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-md bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center font-bold text-[11px]">
                        &Delta;t
                    </div>
                    <div>
                        <span class="text-slate-500 font-medium">Durasi Downtime Terhitung:</span>
                        <strong class="text-slate-900 font-bold ml-1" x-text="calculatedHoursText"></strong>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 font-medium">Realisasi SLA Perangkat:</span>
                    <strong class="text-[#8F0A0D] font-bold ml-1" x-text="projectedSla + '%'"></strong>
                </div>
            </div>

            {{-- Penyebab Gangguan (Root Cause) --}}
            <div>
                <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Penyebab Gangguan (Root Cause) <span class="text-[#8F0A0D]">*</span>
                </label>
                <input type="text" name="root_cause" required placeholder="Contoh: Power failure, Port flap, FO cut pihak ketiga, SFP hardware fault" class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
            </div>

            {{-- Catatan Penanganan --}}
            <div>
                <label class="block text-[11.5px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tindakan &amp; Catatan Penanganan
                </label>
                <textarea name="resolution_notes" rows="2" placeholder="Jelaskan langkah penanganan yang telah dilakukan tim NOC / Engineer lapangan..." class="w-full text-xs font-medium rounded-xl border border-slate-300 px-3.5 py-2.5 bg-slate-50/50 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition"></textarea>
            </div>

            {{-- Priority --}}
            <input type="hidden" name="priority" value="P1 - Critical">

            {{-- Actions --}}
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" 
                        @click="isDowntimeModalOpen = false" 
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan &amp; Perbarui SLA</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function downtimeModalForm() {
        return {
            startDown: '',
            endDown: '',
            calculatedHours: 0,
            calculatedHoursText: '0 Jam 0 Menit',
            projectedSla: '100.00',
            recalcDuration() {
                if (!this.startDown) {
                    this.calculatedHours = 0;
                    return;
                }
                const start = new Date(this.startDown);
                const end = this.endDown ? new Date(this.endDown) : new Date();
                const diffMs = end - start;
                if (diffMs > 0) {
                    const diffMins = Math.floor(diffMs / 60000);
                    const hours = diffMins / 60;
                    this.calculatedHours = parseFloat(hours.toFixed(2));
                    const h = Math.floor(hours);
                    const m = diffMins % 60;
                    this.calculatedHoursText = `${h} Jam ${m} Menit (${this.calculatedHours} Jam)`;
                    
                    const totalPeriod = 720; // 30 hari x 24 jam
                    const sla = Math.max(0, Math.min(100, ((totalPeriod - this.calculatedHours) / totalPeriod) * 100));
                    this.projectedSla = sla.toFixed(2);
                } else {
                    this.calculatedHours = 0;
                    this.calculatedHoursText = '0 Jam 0 Menit';
                    this.projectedSla = '100.00';
                }
            }
        };
    }
</script>

