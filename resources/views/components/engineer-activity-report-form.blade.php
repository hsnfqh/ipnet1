{{-- FORM LAPORAN AKTIVITAS ENGINEER (INTERAKTIF SESUAI TEMPLATE RESMI: PROJECT / MANAGED SERVICE / HELP DESK) --}}
<div class="space-y-6">

    {{-- Alert Sukses Simpan --}}
    <div x-show="reportFormSavedSuccess" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Formulir Laporan Aktivitas berhasil disimpan! Data tersinkronisasi dan otomatis tercetak pada PDF.</span>
        </div>
        <button type="button" @click="reportFormSavedSuccess = false" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">✕</button>
    </div>

    {{-- Category Switcher Tabs di Detail Modal --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-xl border border-[#CBD5E1] shadow-xs">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">Kategori Laporan:</span>
            <div class="inline-flex rounded-lg p-1 bg-[#F1F5F9] border border-[#CBD5E1]">
                <button type="button" @click="setDetailDocCategory('project')"
                        :class="(reportFormData?.category || 'project') === 'project' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                    💼 Project
                </button>
                <button type="button" @click="setDetailDocCategory('managed_service')"
                        :class="(reportFormData?.category || 'project') === 'managed_service' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                    ⚙️ Managed Service
                </button>
                <button type="button" @click="setDetailDocCategory('help_desk')"
                        :class="(reportFormData?.category || 'project') === 'help_desk' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3 py-1.5 rounded-md text-xs font-bold transition cursor-pointer">
                    🎧 Help Desk
                </button>
            </div>
        </div>

        {{-- Quick Actions: Simpan & Download PDF --}}
        <div class="flex items-center gap-2">
            <button type="button" @click="saveReportForm()" :disabled="reportFormSaving"
                    class="px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                <svg x-show="reportFormSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg x-show="!reportFormSaving" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="reportFormSaving ? 'Menyimpan...' : 'Simpan Form Laporan'"></span>
            </button>
            <div class="inline-flex rounded-lg shadow-xs">
                <button type="button" @click="downloadReportPdf('download')"
                   class="px-3 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 font-bold text-xs rounded-l-lg transition inline-flex items-center gap-1.5 cursor-pointer"
                   title="Download file PDF Resmi">
                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Download PDF</span>
                </button>
                <button type="button" @click="downloadReportPdf('stream')"
                   class="px-2.5 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border-t border-b border-r border-red-200 hover:border-red-300 font-bold text-xs rounded-r-lg transition inline-flex items-center cursor-pointer"
                   title="Buka / Preview PDF di Tab Baru">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Container Format Kertas Resmi (Paper-Like) --}}
    <template x-if="reportFormData">
        <div>

            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- ══════════════ 1. DETAIL TEMPLATE: PROJECT ════════════════════════ --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="(reportFormData.category || 'project') === 'project'"
                 class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-md space-y-5 text-[#0F172A] font-sans">
                
                {{-- Header Form Dokumen --}}
                <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                        <div>
                            <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                FORM LAPORAN AKTIVITAS ENGINEER – PROJECT
                            </h2>
                            <p class="text-[11px] text-[#475569] mt-0.5">
                                Dokumen ini digunakan sebagai laporan aktivitas engineer di lokasi pekerjaan dan sebagai bukti pelaksanaan pekerjaan lapangan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- A. IDENTITAS PEKERJAAN --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS PEKERJAAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Hari/Tanggal</td>
                                    <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                        <input type="date" x-model="reportFormData.identitas.hari_tanggal" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-medium text-xs cursor-pointer">
                                    </td>
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Laporan</td>
                                    <td class="w-3/10 py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.no_laporan" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#8F0A0D]">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Project</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.identitas.nama_project" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. SO / SPK / Contract</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.no_so_spk" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Lokasi / Site</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.identitas.lokasi_site" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Work Order / WO</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.work_order" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Engineer</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.identitas.nama_engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.customer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jenis Pekerjaan</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.identitas.jenis_pekerjaan" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC Customer</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.pic_customer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kategori Pekerjaan</td>
                                    <td class="py-1 px-2.5 border-r border-[#1E293B]">
                                        <div class="flex items-center gap-4 text-xs font-semibold">
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.identitas.kategori_implementasi" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                <span>Implementasi</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.identitas.kategori_managed_service" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                <span>Managed Service</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jabatan</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.identitas.jabatan" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Mulai</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="time" x-model="reportFormData.identitas.jam_mulai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs cursor-pointer">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Selesai</td>
                                    <td class="py-1 px-2">
                                        <input type="time" x-model="reportFormData.identitas.jam_selesai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs cursor-pointer">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- B. KOMPOSISI TENAGA KERJA --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. KOMPOSISI TENAGA KERJA</h3>
                        <button type="button" @click="addManpowerRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Personel</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Nama Personel</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Unit Kerja / Divisi</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Posisi / Jabatan</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                    <th class="py-1.5 px-2 w-10 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(mp, idx) in reportFormData.manpower" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.nama" placeholder="Nama..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.unit_kerja" placeholder="Divisi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.jabatan" placeholder="Jabatan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center">
                                            <button type="button" @click="removeManpowerRow(idx)" class="text-red-500 hover:text-red-700 font-bold">✕</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- C. RUANG LINGKUP & TARGET PEKERJAAN --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. RUANG LINGKUP &amp; TARGET PEKERJAAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Target Hari Ini</td>
                                    <td class="w-3/4 p-1"><input type="text" x-model="reportFormData.ruang_lingkup.target_hari_ini" placeholder="Target hari ini..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Durasi Project</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.durasi_project" placeholder="Contoh: 1 Hari / Sesuai WO" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Scope Pekerjaan</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.scope_pekerjaan" placeholder="Ruang lingkup..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Perangkat / Sistem</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.perangkat_sistem" placeholder="Perangkat..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kriteria Selesai</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.kriteria_selesai" placeholder="Kriteria penyelesaian..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- D. RINCIAN AKTIVITAS PEKERJAAN --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN AKTIVITAS PEKERJAAN</h3>
                        <button type="button" @click="addActRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Aktivitas</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-1.5 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-1.5 w-16 border-r border-[#1E293B] text-center">Waktu</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Aktivitas / Tindakan</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Perangkat / Area</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Hasil</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kendala</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindak Lanjut</th>
                                    <th class="py-1.5 px-1.5 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(act, idx) in reportFormData.rincian_aktivitas" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="act.waktu" class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.aktivitas" placeholder="Aktivitas..." class="w-full p-0.5 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.perangkat" placeholder="Area..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.hasil" placeholder="Hasil..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]">
                                            <select x-model="act.status" class="w-full p-0.5 text-xs border-0 bg-transparent font-semibold">
                                                <option value="Selesai">Selesai</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                        </td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.kendala" placeholder="Kendala..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.tindak_lanjut" placeholder="Tindak lanjut..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeActRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- E. MATERIAL & SPARE PART --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. MATERIAL, PERALATAN &amp; SPARE PART</h3>
                        <button type="button" @click="addMaterialRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Material</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Nama Item</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Spesifikasi/Type</th>
                                    <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Qty</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Satuan</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Kondisi</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                    <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(mat, idx) in reportFormData.materials" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.nama" placeholder="Item..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.spesifikasi" placeholder="Spesifikasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="number" x-model="mat.qty" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.satuan" placeholder="Pcs" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.kondisi" placeholder="Baik" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeMaterialRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- H. HASIL AKHIR PEKERJAAN & REKOMENDASI --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. HASIL AKHIR PEKERJAAN &amp; REKOMENDASI</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/3 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Status Pekerjaan</td>
                                    <td class="w-2/3 p-2">
                                        <div class="flex items-center gap-5 text-xs font-semibold">
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="selesai" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D]">
                                                <span>Selesai</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="selesai sebagian" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D]">
                                                <span>Selesai Sebagian</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="belum selesai" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D]">
                                                <span>Belum Selesai</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Persentase Progress</td>
                                    <td class="p-1"><input type="number" min="0" max="100" x-model="reportFormData.hasil_akhir.progress_percent" class="w-24 p-1 text-xs border font-bold text-center"> %</td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kondisi Sistem</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.kondisi_sistem" placeholder="Normal..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Rekomendasi Engineer</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.rekomendasi" placeholder="Rekomendasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- I. DOKUMENTASI FOTO PEKERJAAN --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">I. DOKUMENTASI FOTO PEKERJAAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-center">Foto Sebelum Pekerjaan (Before)</th>
                                    <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-center">Foto Progress Pekerjaan (In-Progress)</th>
                                    <th class="py-1.5 px-2 w-1/3 text-center">Foto Setelah Pekerjaan (After)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="p-2.5 border-r border-[#1E293B] text-center align-top">
                                        <template x-if="reportFormData.foto_dokumentasi?.before?.url">
                                            <div class="space-y-1.5">
                                                <img :src="reportFormData.foto_dokumentasi.before.url" class="max-h-28 mx-auto rounded border object-contain">
                                                <button type="button" @click="removePhoto('before')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                            </div>
                                        </template>
                                        <template x-if="!reportFormData.foto_dokumentasi?.before?.url">
                                            <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                <span>Pilih Foto</span>
                                                <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'before')">
                                            </label>
                                        </template>
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.before.area" placeholder="Area..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                    <td class="p-2.5 border-r border-[#1E293B] text-center align-top">
                                        <template x-if="reportFormData.foto_dokumentasi?.progress?.url">
                                            <div class="space-y-1.5">
                                                <img :src="reportFormData.foto_dokumentasi.progress.url" class="max-h-28 mx-auto rounded border object-contain">
                                                <button type="button" @click="removePhoto('progress')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                            </div>
                                        </template>
                                        <template x-if="!reportFormData.foto_dokumentasi?.progress?.url">
                                            <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                <span>Pilih Foto</span>
                                                <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'progress')">
                                            </label>
                                        </template>
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.progress.area" placeholder="Area..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                    <td class="p-2.5 text-center align-top">
                                        <template x-if="reportFormData.foto_dokumentasi?.after?.url">
                                            <div class="space-y-1.5">
                                                <img :src="reportFormData.foto_dokumentasi.after.url" class="max-h-28 mx-auto rounded border object-contain">
                                                <button type="button" @click="removePhoto('after')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                            </div>
                                        </template>
                                        <template x-if="!reportFormData.foto_dokumentasi?.after?.url">
                                            <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                <span>Pilih Foto</span>
                                                <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'after')">
                                            </label>
                                        </template>
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.after.area" placeholder="Area..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- ════════════ 2. DETAIL TEMPLATE: MANAGED SERVICE ══════════════════ --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="(reportFormData.category || 'project') === 'managed_service'"
                 class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-md space-y-5 text-[#0F172A] font-sans">
                
                {{-- Kop Surat Dokumen Managed Service --}}
                <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                        <div>
                            <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                FORM LAPORAN AKTIVITAS ENGINEER – MANAGED SERVICE
                            </h2>
                            <p class="text-[11px] text-[#475569] mt-0.5">
                                Laporan aktivitas operasi managed service: monitoring, preventive maintenance, corrective maintenance, incident, request, dan onsite support.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- A. IDENTITAS SERVICE --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. . IDENTITAS SERVICE</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal</td>
                                    <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                        <input type="date" x-model="reportFormData.ms_identitas.tanggal" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-medium text-xs cursor-pointer">
                                    </td>
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Report</td>
                                    <td class="w-3/10 py-1 px-2">
                                        <input type="text" x-model="reportFormData.ms_identitas.no_report" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#8F0A0D]">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.ms_identitas.customer" placeholder="Nama Customer..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Contract</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.ms_identitas.no_contract" placeholder="No. Contract..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Site / Lokasi</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.ms_identitas.site_lokasi" placeholder="Lokasi site..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Ticket / Incident No.</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.ms_identitas.ticket_incident_no" placeholder="INC/TICK..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.ms_identitas.engineer_pic" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Shift</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.ms_identitas.shift" placeholder="Pagi / Siang / Malam" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jenis Aktivitas</td>
                                    <td class="py-1 px-2.5 border-r border-[#1E293B]">
                                        <div class="grid grid-cols-3 gap-2 text-xs font-semibold">
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.monitoring" class="rounded text-[#8F0A0D]">
                                                <span>Monitoring</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.pm" class="rounded text-[#8F0A0D]">
                                                <span>PM</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.cm" class="rounded text-[#8F0A0D]">
                                                <span>CM</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.incident" class="rounded text-[#8F0A0D]">
                                                <span>Incident</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.request" class="rounded text-[#8F0A0D]">
                                                <span>Request</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.visit" class="rounded text-[#8F0A0D]">
                                                <span>Visit</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service / Device</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.ms_identitas.service_device" placeholder="Core Router / Switch..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Mulai</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="time" x-model="reportFormData.ms_identitas.jam_mulai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs cursor-pointer">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Selesai</td>
                                    <td class="py-1 px-2">
                                        <input type="time" x-model="reportFormData.ms_identitas.jam_selesai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs cursor-pointer">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- B. SLA TRACKING --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. SLA TRACKING</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Parameter</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Waktu</th>
                                    <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Target SLA</th>
                                    <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Aktual</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(sla, idx) in reportFormData.ms_sla" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]" x-text="sla.parameter"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="sla.waktu" class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="sla.target_sla" class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="sla.aktual" class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]">
                                            <select x-model="sla.status" class="w-full p-0.5 text-xs border-0 bg-transparent font-bold text-center">
                                                <option value="Met">Met</option>
                                                <option value="Breach">Breach</option>
                                            </select>
                                        </td>
                                        <td class="p-1"><input type="text" x-model="sla.keterangan" placeholder="Keterangan..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- C. KONDISI SERVICE / PERANGKAT --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. KONDISI SERVICE / PERANGKAT</h3>
                        <button type="button" @click="addMsKondisiRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Baris</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Service / Device</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Parameter</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Before</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">After</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                    <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(k, idx) in reportFormData.ms_kondisi_perangkat" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.service_device" placeholder="Core Switch..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.parameter" placeholder="CPU/Latency..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.before" placeholder="30%" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.after" placeholder="25%" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.status" placeholder="Good" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.keterangan" placeholder="Normal" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeMsKondisiRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- D. RINCIAN AKTIVITAS ENGINEER (MS) --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN AKTIVITAS ENGINEER</h3>
                        <button type="button" @click="addMsActRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Baris Aktivitas</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-1.5 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-1.5 w-16 border-r border-[#1E293B] text-center">Waktu</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Aktivitas</th>
                                    <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Ticket / Alarm</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Hasil</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kendala</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Follow-up</th>
                                    <th class="py-1.5 px-1.5 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(act, idx) in reportFormData.ms_aktivitas" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="act.waktu" class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.aktivitas" placeholder="Aktivitas engineer..." class="w-full p-0.5 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.ticket_alarm" placeholder="TICK-01" class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.hasil" placeholder="Hasil..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]">
                                            <select x-model="act.status" class="w-full p-0.5 text-xs border-0 bg-transparent font-semibold">
                                                <option value="Done">Done</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                        </td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.kendala" placeholder="Nihil" class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.follow_up" placeholder="Follow-up..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeMsActRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- H. DOKUMENTASI & EVIDENCE (MS) --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. DOKUMENTASI &amp; EVIDENCE</h3>
                        <button type="button" @click="addMsEvidenceRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Evidence</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-left">Evidence</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Waktu</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-center">Foto / Screenshot / Log</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                    <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(evi, idx) in reportFormData.ms_evidence" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="evi.evidence" placeholder="Screenshot monitoring..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="evi.waktu" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1.5 border-r border-[#1E293B] text-center">
                                            <template x-if="evi.foto_url">
                                                <div class="flex items-center justify-center gap-2">
                                                    <img :src="evi.foto_url" class="h-8 w-auto rounded border">
                                                    <button type="button" @click="evi.foto_url = ''" class="text-red-500 font-bold text-[10px]">✕</button>
                                                </div>
                                            </template>
                                            <template x-if="!evi.foto_url">
                                                <label class="cursor-pointer inline-flex items-center gap-1 px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold">
                                                    <span>Upload File</span>
                                                    <input type="file" accept="image/*" class="hidden" @change="handleMsEvidenceUpload($event, idx)">
                                                </label>
                                            </template>
                                        </td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="evi.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeMsEvidenceRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- I. SERVICE CLOSURE --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">I. SERVICE CLOSURE</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service Status</td>
                                    <td class="w-3/10 py-1.5 px-2.5 border-r border-[#1E293B]">
                                        <div class="grid grid-cols-2 gap-1.5 text-xs font-semibold">
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.service_status.resolved" class="rounded text-[#8F0A0D]">
                                                <span>Resolved</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.service_status.monitoring" class="rounded text-[#8F0A0D]">
                                                <span>Monitoring</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.service_status.escalated" class="rounded text-[#8F0A0D]">
                                                <span>Escalated</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.service_status.closed" class="rounded text-[#8F0A0D]">
                                                <span>Closed</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="w-1/6 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">SLA</td>
                                    <td class="w-1/3 py-1.5 px-2.5">
                                        <div class="flex items-center gap-4 text-xs font-semibold">
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.sla.met" class="rounded text-[#8F0A0D]">
                                                <span>Met</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.ms_closure.sla.breach" class="rounded text-[#8F0A0D]">
                                                <span>Breach</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer Confirmation</td>
                                    <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.ms_closure.customer_confirmation" placeholder="Konfirmasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ms_closure.outstanding" placeholder="Outstanding issue..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>


            {{-- ══════════════════════════════════════════════════════════════════ --}}
            {{-- ════════════ 3. DETAIL TEMPLATE: HELP DESK ════════════════════════ --}}
            {{-- ══════════════════════════════════════════════════════════════════ --}}
            <div x-show="(reportFormData.category || 'project') === 'help_desk'"
                 class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-md space-y-5 text-[#0F172A] font-sans">
                
                {{-- Kop Surat Dokumen Help Desk --}}
                <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                        <div>
                            <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                FORM LAPORAN AKTIVITAS ENGINEER – HELP DESK
                            </h2>
                            <p class="text-[11px] text-[#475569] mt-0.5">
                                Laporan operasional harian/shift (Help Desk) untuk merekap kondisi layanan, aktivitas, ticket, pekerjaan, dan handover engineer.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- A. IDENTITAS SHIFT --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS SHIFT</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal</td>
                                    <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                        <input type="date" x-model="reportFormData.hd_identitas.tanggal" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-medium text-xs cursor-pointer">
                                    </td>
                                    <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Shift</td>
                                    <td class="w-3/10 py-1 px-2.5">
                                        <div class="flex items-center gap-4 text-xs font-semibold">
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.pagi" class="rounded text-[#8F0A0D]">
                                                <span>Pagi</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.siang" class="rounded text-[#8F0A0D]">
                                                <span>Siang</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.malam" class="rounded text-[#8F0A0D]">
                                                <span>Malam</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Engineer</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.hd_identitas.nama_engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.hd_identitas.team_leader" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Area / Site</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.hd_identitas.area_site" placeholder="Area..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Service</td>
                                    <td class="py-1 px-2">
                                        <input type="text" x-model="reportFormData.hd_identitas.customer_service" placeholder="Customer..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Shift</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.hd_identitas.jam_shift" placeholder="08:00 - 16:00 WIB" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jumlah Engineer</td>
                                    <td class="py-1 px-2">
                                        <input type="number" min="1" x-model="reportFormData.hd_identitas.jumlah_engineer" class="w-20 p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-bold"> Orang
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- B. KONDISI AWAL SHIFT --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. KONDISI AWAL SHIFT</h3>
                        <button type="button" @click="addHdKondisiRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Item</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Item / Service</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kondisi Awal</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Alarm / Issue</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                    <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(k, idx) in reportFormData.hd_kondisi_awal" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.item_service" placeholder="Item..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.kondisi_awal" placeholder="Normal" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.alarm_issue" placeholder="Clear" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.status" placeholder="OK" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><button type="button" @click="removeHdKondisiRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- C. REKAP AKTIVITAS SHIFT --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. REKAP AKTIVITAS SHIFT</h3>
                        <button type="button" @click="addHdActRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Aktivitas</button>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                    <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Waktu</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Aktivitas</th>
                                    <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Ticket / WO</th>
                                    <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Lokasi / Device</th>
                                    <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Hasil</th>
                                    <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                    <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(act, idx) in reportFormData.hd_aktivitas" :key="idx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="act.waktu" class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.aktivitas" placeholder="Aktivitas..." class="w-full p-0.5 text-xs border-0 bg-transparent font-medium"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.ticket_wo" placeholder="TICK/WO..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.lokasi_device" placeholder="Lokasi..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.hasil" placeholder="Hasil..." class="w-full p-0.5 text-xs border-0 bg-transparent text-center"></td>
                                        <td class="p-1 border-r border-[#1E293B]">
                                            <select x-model="act.status" class="w-full p-0.5 text-xs border-0 bg-transparent font-semibold">
                                                <option value="Done">Done</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                        </td>
                                        <td class="p-1 text-center"><button type="button" @click="removeHdActRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- I. REKAP SHIFT --}}
                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">I. REKAP SHIFT</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total ticket diterima</td>
                                    <td class="w-1/4 p-1 border-r border-[#1E293B]"><input type="number" min="0" x-model="reportFormData.hd_rekap_shift.total_ticket_diterima" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                    <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total ticket closed</td>
                                    <td class="w-1/4 p-1"><input type="number" min="0" x-model="reportFormData.hd_rekap_shift.total_ticket_closed" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total incident</td>
                                    <td class="p-1 border-r border-[#1E293B]"><input type="number" min="0" x-model="reportFormData.hd_rekap_shift.total_incident" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                    <td class="p-1"><input type="number" min="0" x-model="reportFormData.hd_rekap_shift.outstanding" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service kritis / alert</td>
                                    <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_rekap_shift.service_kritis" placeholder="0 / Nihil" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Handover diperlukan</td>
                                    <td class="p-2">
                                        <div class="flex items-center gap-4 text-xs font-semibold">
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="radio" :value="true" x-model="reportFormData.hd_rekap_shift.handover_diperlukan" class="text-[#8F0A0D]">
                                                <span>Ya</span>
                                            </label>
                                            <label class="flex items-center gap-1 cursor-pointer">
                                                <input type="radio" :value="false" x-model="reportFormData.hd_rekap_shift.handover_diperlukan" class="text-[#8F0A0D]">
                                                <span>Tidak</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </template>
</div>
