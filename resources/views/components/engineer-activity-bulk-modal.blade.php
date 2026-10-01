{{-- MODAL INPUT AKTIVITAS SPREADSHEET (Untuk Lead Engineer & Engineer) --}}
@php
    $modalProjects = $projects ?? $myProjects ?? null;
    if ($modalProjects === null) {
        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        if ($isLead) {
            $modalProjects = \App\Models\Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])->orderBy('name')->get(['id', 'name', 'client']);
        } else {
            $modalProjects = \App\Models\Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->where(function($q) use ($authUser) {
                    $uid = $authUser->id;
                    $q->where('created_by', $uid)
                      ->orWhereHas('tasks', fn($tq) => $tq->where('engineer_id', $uid))
                      ->orWhereHas('schedules', fn($sq) => $sq->where('engineer_id', $uid));
                })
                ->orderBy('name')
                ->get(['id', 'name', 'client']);
        }
    }
@endphp

<div x-data="{
    isBulkModalOpen: false,
    bulkProjectId: '',
    activityTitle: '',
    bulkRows: [
        { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' },
        { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' },
        { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' },
        { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' },
        { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' }
    ],
    get filledRowsCount() {
        return this.bulkRows.filter(r => r.subject && r.subject.trim() !== '').length;
    },
    openModal() {
        this.isBulkModalOpen = true;
    },
    closeModal() {
        this.isBulkModalOpen = false;
    },
    addBulkRow() {
        const lastDate = this.bulkRows.length > 0 ? this.bulkRows[this.bulkRows.length - 1].activity_date : '{{ date('Y-m-d') }}';
        this.bulkRows.push({
            subject: '',
            activity_date: lastDate,
            client_pic: '',
            ipnet_pic: '',
            notes: ''
        });
    },
    addBulkMultiple(count) {
        for (let i = 0; i < count; i++) {
            this.addBulkRow();
        }
    },
    removeBulkRow(index) {
        if (this.bulkRows.length > 1) {
            this.bulkRows.splice(index, 1);
        } else {
            this.bulkRows[0] = { subject: '', activity_date: '{{ date('Y-m-d') }}', client_pic: '', ipnet_pic: '', notes: '' };
        }
    }
}"
@open-engineer-activity-modal.window="isBulkModalOpen = true"
@keydown.escape.window="isBulkModalOpen = false">

    {{-- Backdrop & Container Modal --}}
    <div x-show="isBulkModalOpen"
         x-cloak
         class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
         style="display: none;"
         @click.self="isBulkModalOpen = false">

        <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#E2E8F0] max-h-[92vh] flex flex-col overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">AKTIVITAS BARU</p>
                    <h3 class="text-[17px] font-bold text-[#1E293B]">Input Aktivitas</h3>
                </div>
                <button type="button" @click="isBulkModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Input dengan Scrollable Table & Sticky Footer --}}
            <form action="{{ route('engineer.activity_log.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf

                {{-- Bar Pilihan Proyek & Judul Topik --}}
                <div class="p-4 sm:p-5 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                PILIH PROSPEK / PROYEK TUJUAN <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <select name="project_id" x-model="bulkProjectId" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-xs">
                                <option value="">-- Pilih Proyek Terkait --</option>
                                @forelse($modalProjects as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->name }} {{ $p->client ? '('.$p->client.')' : '' }}
                                    </option>
                                @empty
                                    <option value="" disabled>-- Anda belum ditugaskan pada proyek manapun --</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                JUDUL / TOPIK AKTIVITAS (OPSIONAL)
                            </label>
                            <input type="text" name="activity_title" x-model="activityTitle"
                                   placeholder="Contoh: Troubleshooting Jaringan..."
                                   class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-xs">
                        </div>
                    </div>
                </div>

                {{-- Table Spreadsheet Body --}}
                <div class="flex-1 overflow-x-auto overflow-y-auto p-4 sm:p-5 bg-[#F8FAFC]/50">
                    <table class="w-full border-collapse bg-white rounded-xl shadow-xs border border-[#E2E8F0] text-left text-xs min-w-[1050px]">
                        <thead class="bg-[#F8FAFC] text-[#475569] font-bold uppercase text-[10.5px] tracking-wider border-b border-[#E2E8F0]">
                            <tr>
                                <th class="py-3 px-3 w-12 text-center">NO</th>
                                <th class="py-3 px-3 w-80">AKTIVITAS <span class="text-[#8F0A0D]">*</span></th>
                                <th class="py-3 px-3 w-36">TANGGAL</th>
                                <th class="py-3 px-3 w-36">PIC KLIEN</th>
                                <th class="py-3 px-3 w-36">PIC IPNET</th>
                                <th class="py-3 px-3 min-w-[200px]">NOTES</th>
                                <th class="py-3 px-2 w-12 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            <template x-for="(row, index) in bulkRows" :key="index">
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    {{-- No --}}
                                    <td class="py-2.5 px-3 text-center font-bold text-[#94A3B8] text-xs" x-text="index + 1"></td>

                                    {{-- Aktivitas / Subject --}}
                                    <td class="py-2 px-2.5">
                                        <textarea :name="'activities[' + index + '][subject]'"
                                                  x-model="row.subject"
                                                  rows="2"
                                                  placeholder="Rincian aktivitas teknis..."
                                                  class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition resize-none"></textarea>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="py-2 px-2.5">
                                        <input type="date"
                                               :name="'activities[' + index + '][activity_date]'"
                                               x-model="row.activity_date"
                                               class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition cursor-pointer">
                                    </td>

                                    {{-- PIC Klien --}}
                                    <td class="py-2 px-2.5">
                                        <input type="text"
                                               :name="'activities[' + index + '][client_pic]'"
                                               x-model="row.client_pic"
                                               placeholder="Nama PIC klien"
                                               class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                                    </td>

                                    {{-- PIC IPNET --}}
                                    <td class="py-2 px-2.5">
                                        <input type="text"
                                               :name="'activities[' + index + '][ipnet_pic]'"
                                               x-model="row.ipnet_pic"
                                               placeholder="Nama engineer / teknisi"
                                               class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                                    </td>

                                    {{-- Notes --}}
                                    <td class="py-2 px-2.5">
                                        <textarea :name="'activities[' + index + '][notes]'"
                                                  x-model="row.notes"
                                                  rows="2"
                                                  placeholder="Catatan / Notes kendala & durasi..."
                                                  class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition resize-none"></textarea>
                                    </td>

                                    {{-- Tombol Hapus Baris --}}
                                    <td class="py-2 px-2 text-center">
                                        <button type="button"
                                                @click="removeBulkRow(index)"
                                                title="Hapus baris ini"
                                                class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    {{-- Kontrol Tambah Baris --}}
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <button type="button"
                                    @click="addBulkRow()"
                                    class="px-4 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Tambah 1 Baris</span>
                            </button>
                            <button type="button"
                                    @click="addBulkMultiple(5)"
                                    class="px-3.5 py-2 bg-white hover:bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] font-semibold rounded-xl text-xs transition cursor-pointer">
                                <span>+ Tambah 5 Baris Sekaligus</span>
                            </button>
                        </div>
                        <div class="text-xs text-[#64748B] font-medium">
                            <span class="font-bold text-[#1E293B]" x-text="filledRowsCount"></span> baris aktivitas terisi
                        </div>
                    </div>
                </div>

                {{-- Sticky Modal Footer --}}
                <div class="border-t border-[#E2E8F0] p-4 sm:p-5 bg-white flex items-center justify-between shrink-0">
                    <button type="button"
                            @click="isBulkModalOpen = false"
                            class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-[#475569] hover:bg-[#F1F5F9] font-bold text-xs transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            style="background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);"
                            class="px-6 py-2.5 rounded-xl text-white font-bold text-xs shadow-md hover:shadow-lg transition cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Semua Aktivitas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
