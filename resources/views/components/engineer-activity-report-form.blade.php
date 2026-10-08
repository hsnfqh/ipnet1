<div class="space-y-6">

    <div x-show="reportFormSavedSuccess" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Formulir Laporan Aktivitas berhasil disimpan! Data akan otomatis tercetak pada PDF.</span>
        </div>
        <button type="button" @click="reportFormSavedSuccess = false" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">&times;</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#CBD5E1] shadow-xs">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">Kategori Laporan:</span>
            <div class="inline-flex rounded-xl p-1 bg-[#F1F5F9] border border-[#CBD5E1]">
                <button type="button" @click="setDetailDocCategory('project')"
                        :class="(reportFormData?.category || 'project') === 'project' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                    Project
                </button>
                <button type="button" @click="setDetailDocCategory('managed_service')"
                        :class="(reportFormData?.category || 'project') === 'managed_service' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                    Managed Service
                </button>
                <button type="button" @click="setDetailDocCategory('help_desk')"
                        :class="(reportFormData?.category || 'project') === 'help_desk' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer">
                    Help Desk
                </button>
            </div>
        </div>

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
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="(reportFormData?.category || 'project') === 'project'"
         class="!mt-3.5 bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">

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

            <div class="text-right shrink-0">
                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                    Dokumen Resmi
                </span>
            </div>
        </div>

        <template x-if="reportFormData">
            <div class="space-y-6">

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
                                                <input type="checkbox" x-model="reportFormData.identitas.kategori_implementasi" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                <span>Implementasi</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="checkbox" x-model="reportFormData.identitas.kategori_managed_service" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
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
                                        <input type="time" x-model="reportFormData.identitas.jam_mulai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                    </td>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Selesai</td>
                                    <td class="py-1 px-2">
                                        <input type="time" x-model="reportFormData.identitas.jam_selesai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. KOMPOSISI TENAGA KERJA</h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="addManpowerRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Personel</span>
                            </button>
                            <button type="button" @click="removeManpowerRow()" x-show="reportFormData.manpower.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>- Hapus Personel</span>
                            </button>
                        </div>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-2 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-left">Nama Personel</th>
                                    <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Unit Kerja</th>
                                    <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Jabatan</th>
                                    <th class="py-1.5 px-2 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(mp, mpIdx) in reportFormData.manpower" :key="mpIdx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="mpIdx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.nama" placeholder="Nama Personel" class="w-full p-1 text-xs border-0 bg-transparent font-semibold"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.unit_kerja" placeholder="Unit Kerja" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mp.jabatan" placeholder="Jabatan" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="mp.keterangan" placeholder="Keterangan" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-[11.5px] font-semibold text-[#334155] pt-0.5">
                        Total tenaga kerja: <u>&nbsp;&nbsp;<strong x-text="reportFormData.manpower.length"></strong>&nbsp;&nbsp;</u> orang
                    </div>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. RUANG LINGKUP / TARGET PEKERJAAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-2/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">1. Target pekerjaan hari ini</td>
                                    <td class="w-3/5 p-1"><textarea rows="1" x-model="reportFormData.ruang_lingkup.target_hari_ini" class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">2. Durasi waktu Project yang ditugaskan</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.durasi_project" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">3. Scope / pekerjaan yang ditugaskan</td>
                                    <td class="p-1"><textarea rows="1" x-model="reportFormData.ruang_lingkup.scope_pekerjaan" class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">4. Perangkat / sistem yang ditangani</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.perangkat_sistem" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">5. Kriteria pekerjaan dinyatakan selesai</td>
                                    <td class="p-1"><textarea rows="1" x-model="reportFormData.ruang_lingkup.kriteria_selesai" class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN AKTIVITAS ENGINEER</h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="addActivityRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Baris</span>
                            </button>
                            <button type="button" @click="removeActivityRow()" x-show="reportFormData.rincian_aktivitas.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>- Hapus Baris</span>
                            </button>
                        </div>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                        <table class="w-full border-collapse border border-[#1E293B] min-w-[750px]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1.5 px-1.5 w-24 text-center border-r border-[#1E293B]">Waktu</th>
                                    <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Aktivitas / Tindakan</th>
                                    <th class="py-1.5 px-2 w-1/6 border-r border-[#1E293B] text-left">Perangkat / Area</th>
                                    <th class="py-1.5 px-2 w-1/6 border-r border-[#1E293B] text-left">Hasil / Kondisi</th>
                                    <th class="py-1.5 px-1.5 w-28 text-center border-r border-[#1E293B]">Status</th>
                                    <th class="py-1.5 px-2 w-1/8 border-r border-[#1E293B] text-left">Kendala</th>
                                    <th class="py-1.5 px-2 text-left">Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(act, actIdx) in reportFormData.rincian_aktivitas" :key="actIdx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="act.no || (actIdx + 1)"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="act.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="act.aktivitas" class="w-full p-1 text-xs border-0 bg-transparent font-semibold resize-none"></textarea></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.perangkat" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.hasil" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center">
                                            <select x-model="act.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer focus:ring-0">
                                                <option value="">- Status -</option>
                                                <option value="Selesai">Selesai</option>
                                                <option value="Dalam Proses">Dalam Proses</option>
                                                <option value="Tertunda">Tertunda</option>
                                                <option value="Kendala">Kendala</option>
                                            </select>
                                        </td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.kendala" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="act.tindak_lanjut" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. MATERIAL, PERALATAN &amp; SPARE PART</h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="addMaterialRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Baris</span>
                            </button>
                            <button type="button" @click="removeMaterialRow()" x-show="reportFormData.materials.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>- Hapus Baris</span>
                            </button>
                        </div>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1 px-2 w-1/4 border-r border-[#1E293B] text-left">Nama Item</th>
                                    <th class="py-1 px-2 w-1/4 border-r border-[#1E293B] text-left">Spesifikasi/Type</th>
                                    <th class="py-1 px-1 w-14 text-center border-r border-[#1E293B]">Qty</th>
                                    <th class="py-1 px-1.5 w-16 text-center border-r border-[#1E293B]">Satuan</th>
                                    <th class="py-1 px-2 w-20 text-center border-r border-[#1E293B]">Kondisi</th>
                                    <th class="py-1 px-2 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(mat, matIdx) in reportFormData.materials" :key="matIdx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="matIdx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.nama" placeholder="Item" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.spesifikasi" placeholder="Spesifikasi" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="number" min="1" x-model.number="mat.qty" placeholder="1" class="w-full p-1 text-center text-xs border-0 bg-transparent font-semibold"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.satuan" placeholder="Pcs" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.kondisi" placeholder="" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="mat.keterangan" placeholder="Keterangan" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. HASIL PENGUJIAN / PENGUKURAN</h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="addTestResultRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Baris</span>
                            </button>
                            <button type="button" @click="removeTestResultRow()" x-show="reportFormData.test_results.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>- Hapus Baris</span>
                            </button>
                        </div>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1 px-2 w-1/4 border-r border-[#1E293B] text-left">Parameter</th>
                                    <th class="py-1 px-2 w-1/6 text-center border-r border-[#1E293B]">Sebelum</th>
                                    <th class="py-1 px-2 w-1/6 text-center border-r border-[#1E293B]">Sesudah</th>
                                    <th class="py-1 px-1.5 w-16 text-center border-r border-[#1E293B]">Satuan</th>
                                    <th class="py-1 px-2 w-1/6 border-r border-[#1E293B] text-left">Metode/Alat</th>
                                    <th class="py-1 px-2 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(test, testIdx) in reportFormData.test_results" :key="testIdx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="testIdx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="test.parameter" placeholder="Parameter" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="number" step="any" x-model.number="test.sebelum" placeholder="0" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="number" step="any" x-model.number="test.sesudah" placeholder="0" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="test.satuan" placeholder="ms / Mbps" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="test.metode" placeholder="Ping / Speedtest" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="test.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. KENDALA / INCIDENT / DEVIASI</h3>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="addIncidentRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Baris</span>
                            </button>
                            <button type="button" @click="removeIncidentRow()" x-show="reportFormData.incidents.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                <span>- Hapus Baris</span>
                            </button>
                        </div>
                    </div>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1 px-2 w-24 text-center border-r border-[#1E293B]">Waktu</th>
                                    <th class="py-1 px-2 w-1/4 border-r border-[#1E293B] text-left">Kendala / Incident</th>
                                    <th class="py-1 px-2 w-1/4 border-r border-[#1E293B] text-left">Dampak</th>
                                    <th class="py-1 px-2 border-r border-[#1E293B] text-left">Tindakan Penanganan</th>
                                    <th class="py-1 px-2 w-20 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(inc, incIdx) in reportFormData.incidents" :key="incIdx">
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="incIdx + 1"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="inc.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent cursor-pointer"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.kendala" placeholder="Kendala" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.dampak" placeholder="Dampak" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.tindakan" placeholder="Tindakan" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 text-center"><input type="text" x-model="inc.status" placeholder="" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. HASIL AKHIR PEKERJAAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-2/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Status pekerjaan</td>
                                    <td class="w-3/5 p-2">
                                        <div class="flex items-center gap-6 font-semibold">
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="selesai" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                <span>Selesai</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="selesai sebagian" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                <span>Selesai Sebagian</span>
                                            </label>
                                            <label class="flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" value="belum selesai" x-model="reportFormData.hasil_akhir.status_pekerjaan" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                <span>Belum Selesai</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Persentase progress</td>
                                    <td class="p-1 flex items-center gap-2">
                                        <input type="number" min="0" max="100" x-model="reportFormData.hasil_akhir.progress_percent" class="w-24 p-1 text-xs border border-gray-300 rounded font-bold text-center">
                                        <span class="font-bold">%</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kondisi sistem/perangkat setelah pekerjaan</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.kondisi_sistem" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding / pekerjaan tersisa</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.outstanding" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Rekomendasi / kebutuhan tindak lanjut</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.rekomendasi" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Dilakukan Eskalasi pekerjaan (PIC)</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.eskalasi_pic" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">I. REKAP DOKUMENTASI FOTO</h3>
                    <p class="text-[11px] text-[#475569]">
                        Lampirkan foto yang menunjukkan kondisi aktual. Minimal: Before, Progress (bila ada), dan After. Cantumkan waktu/lokasi singkat pada caption.
                    </p>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                    <th class="py-1.5 px-2 w-24 text-center border-r border-[#1E293B]">Tahap</th>
                                    <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Area / Objek</th>
                                    <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-center">Foto / Tempat Menempel Foto</th>
                                    <th class="py-1.5 px-2 text-left">Keterangan / Caption</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B]">1</td>
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B] bg-slate-50">BEFORE</td>
                                    <td class="p-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.before.area" placeholder="Area / Objek" class="w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                    <td class="p-2 border-r border-[#1E293B] text-center">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <template x-if="reportFormData.foto_dokumentasi.before.url">
                                                <div class="relative group">
                                                    <img :src="reportFormData.foto_dokumentasi.before.url" class="h-20 w-auto max-w-full rounded object-contain border">
                                                    <button type="button" @click="removePhoto('before')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">&times;</button>
                                                </div>
                                            </template>
                                            <template x-if="!reportFormData.foto_dokumentasi.before.url">
                                                <div class="w-full py-3 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                    <span class="text-[11px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                    <label class="px-2 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'before')">
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <textarea rows="2" x-model="reportFormData.foto_dokumentasi.before.caption" placeholder="Keterangan / Caption" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
                                    </td>
                                </tr>

                                <tr class="border-b border-[#1E293B]">
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B]">2</td>
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B] bg-slate-50">PROGRESS</td>
                                    <td class="p-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.progress.area" placeholder="Area / Objek" class="w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                    <td class="p-2 border-r border-[#1E293B] text-center">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <template x-if="reportFormData.foto_dokumentasi.progress.url">
                                                <div class="relative group">
                                                    <img :src="reportFormData.foto_dokumentasi.progress.url" class="h-20 w-auto max-w-full rounded object-contain border">
                                                    <button type="button" @click="removePhoto('progress')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">&times;</button>
                                                </div>
                                            </template>
                                            <template x-if="!reportFormData.foto_dokumentasi.progress.url">
                                                <div class="w-full py-3 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                    <span class="text-[11px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                    <label class="px-2 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'progress')">
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <textarea rows="2" x-model="reportFormData.foto_dokumentasi.progress.caption" placeholder="Keterangan / Caption" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B]">3</td>
                                    <td class="text-center font-bold py-2 border-r border-[#1E293B] bg-slate-50">AFTER</td>
                                    <td class="p-2 border-r border-[#1E293B]">
                                        <input type="text" x-model="reportFormData.foto_dokumentasi.after.area" placeholder="Area / Objek" class="w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                    </td>
                                    <td class="p-2 border-r border-[#1E293B] text-center">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <template x-if="reportFormData.foto_dokumentasi.after.url">
                                                <div class="relative group">
                                                    <img :src="reportFormData.foto_dokumentasi.after.url" class="h-20 w-auto max-w-full rounded object-contain border">
                                                    <button type="button" @click="removePhoto('after')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">&times;</button>
                                                </div>
                                            </template>
                                            <template x-if="!reportFormData.foto_dokumentasi.after.url">
                                                <div class="w-full py-3 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                    <span class="text-[11px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                    <label class="px-2 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'after')">
                                                    </label>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <textarea rows="2" x-model="reportFormData.foto_dokumentasi.after.caption" placeholder="Keterangan / Caption" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI &amp; PENGESAHAN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                <tr>
                                    <th class="py-1.5 px-3 w-1/5 border-r border-[#1E293B] text-left">Pihak</th>
                                    <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Nama</th>
                                    <th class="py-1.5 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal</th>
                                    <th class="py-1.5 px-3 w-2/5 text-center">Tanda Tangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Engineer</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B] align-middle">
                                        <input type="text" x-model="reportFormData.identitas.nama_engineer" placeholder="Nama Engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-2.5 px-3 text-center border-r border-[#1E293B] align-middle" x-text="sigInfo?.pic?.signed ? sigInfo?.pic?.signed_at : '{{ date('d/m/Y') }}'"></td>
                                    <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Project Manager / Team Leader</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B] align-middle">
                                        <input type="text" x-model="reportFormData.identitas.nama_leader" placeholder="Nama PM / Team Leader" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-2.5 px-3 text-center border-r border-[#1E293B] align-middle" x-text="sigInfo?.lead?.signed ? sigInfo?.lead?.signed_at : '{{ date('d/m/Y') }}'"></td>
                                    <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Customer / Site Representative</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B] align-middle">
                                        <input type="text" x-model="reportFormData.identitas.pic_customer" placeholder="Ketik nama PIC Klien / Customer..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-2.5 px-3 text-center border-r border-[#1E293B] align-middle">{{ date('d/m/Y') }}</td>
                                    <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">K. CATATAN ADMINISTRASI DOKUMEN</h3>
                    <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                        <table class="w-full border-collapse border border-[#1E293B]">
                            <tbody>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="w-2/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nomor WO / Ticket</td>
                                    <td class="w-3/5 p-1"><input type="text" x-model="reportFormData.administrasi.nomor_wo" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nomor BA / BAST / Checklist terkait</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.administrasi.nomor_ba" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Lampiran tambahan</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.administrasi.lampiran" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                                <tr>
                                    <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama file / folder dokumentasi</td>
                                    <td class="p-1"><input type="text" x-model="reportFormData.administrasi.folder" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-[11px] text-[#475569] leading-relaxed pt-1">
                        <strong>Catatan penggunaan:</strong> Form diisi oleh engineer/PIC berdasarkan aktivitas aktual. Setiap pekerjaan utama harus memiliki bukti aktivitas dan dokumentasi foto yang dapat ditelusuri ke project/WO. Jika pekerjaan berlangsung lebih dari satu hari, gunakan satu laporan per hari.
                    </div>
                </div>

            </div>
        </template>

    </div>

                    <div x-show="(reportFormData?.category || 'project') === 'managed_service'"
                         class="!mt-3.5 bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
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
                            <div class="text-right shrink-0">
                                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Dokumen Resmi
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS SERVICE</h3>
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
                                                <input type="text" x-model="reportFormData.ms_identitas.customer" placeholder="Nama Customer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Contract</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.no_contract" placeholder="No. Contract" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Site / Lokasi</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.site_lokasi" placeholder="Lokasi site / gedung" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Ticket / Incident No.</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.ticket_incident_no" placeholder="INC/TICK-xxx" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.engineer_pic" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Shift</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.shift" placeholder="Pagi / Regular" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jenis Aktivitas</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B]">
                                                <div class="grid grid-cols-3 gap-2 text-xs font-semibold">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.monitoring" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Monitoring</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.pm" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>PM</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.cm" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>CM</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.incident" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Incident</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.request" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Request</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.visit" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Visit</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service / Device</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.service_device" placeholder="Nama Service / Perangkat" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Mulai</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="time" x-model="reportFormData.ms_identitas.jam_mulai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Selesai</td>
                                            <td class="py-1 px-2">
                                                <input type="time" x-model="reportFormData.ms_identitas.jam_selesai" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]" x-text="sla.parameter"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="time" x-model="sla.waktu" class="w-full p-1 text-xs border-0 bg-transparent text-center font-medium cursor-pointer">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="sla.target_sla" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="sla.aktual" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <select x-model="sla.status" class="w-full p-1 text-xs border-0 bg-transparent font-bold text-center text-[#8F0A0D] cursor-pointer">
                                                        <option value="">-</option>
                                                        <option value="Met">Met</option>
                                                        <option value="Breach">Breach</option>
                                                    </select>
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" x-model="sla.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. KONDISI SERVICE / PERANGKAT</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsKondisiRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeMsKondisiRow()" x-show="reportFormData.ms_kondisi_perangkat.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Service / Device</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Parameter</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Before</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">After</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(k, idx) in reportFormData.ms_kondisi_perangkat" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="k.service_device" placeholder="Nama Service / Device" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="k.parameter" placeholder="Parameter" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.before" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.after" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" x-model="k.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN AKTIVITAS ENGINEER <span class="text-[#8F0A0D]">*</span></h3>
                                    <p class="text-[10.5px] text-gray-500">Rincian aktivitas operasi, penanganan tiket, alarm monitoring atau preventive maintenance.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsActRow()" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeMsActRow()" x-show="reportFormData.ms_aktivitas.length > 1" class="text-xs font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                                <table class="w-full border-collapse border border-[#1E293B] min-w-[750px]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                            <th class="py-1.5 px-1.5 w-24 text-center border-r border-[#1E293B]">Waktu</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Aktivitas <span class="text-red-500">*</span></th>
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Ticket / Alarm</th>
                                            <th class="py-1.5 px-2 w-1/6 border-r border-[#1E293B] text-left">Hasil</th>
                                            <th class="py-1.5 px-1.5 w-28 text-center border-r border-[#1E293B]">Status</th>
                                            <th class="py-1.5 px-2 w-1/8 border-r border-[#1E293B] text-left">Kendala</th>
                                            <th class="py-1.5 px-2 text-left">Follow-up</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(act, idx) in reportFormData.ms_aktivitas" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="time" x-model="act.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <textarea rows="2" x-model="act.aktivitas" placeholder="Aktivitas engineer..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] font-semibold resize-none focus:bg-white"></textarea>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.ticket_alarm" placeholder="TICK-001 / ALM" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.hasil" placeholder="Hasil tindakan" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <select x-model="act.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer">
                                                        <option value="Done">Done</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Pending">Pending</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.kendala" placeholder="Nihil" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" x-model="act.follow_up" placeholder="Tindak lanjut..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. INCIDENT / ROOT CAUSE / ESCALATION</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsIncidentRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Incident</span>
                                    </button>
                                    <button type="button" @click="removeMsIncidentRow()" x-show="reportFormData.ms_incident_escalation.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Incident</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Incident</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Impact</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Probable / Root Cause</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Corrective Action</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Escalation</th>
                                            <th class="py-1.5 px-2 w-24 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(inc, idx) in reportFormData.ms_incident_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.incident" placeholder="Deskripsi insiden..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.impact" placeholder="Dampak..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.root_cause" placeholder="Penyebab..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.corrective_action" placeholder="Tindakan perbaikan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.escalation" placeholder="Eskalasi ke..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center font-semibold"><input type="text" x-model="inc.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. PREVENTIVE MAINTENANCE / CHECKLIST</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsPmRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Checklist</span>
                                    </button>
                                    <button type="button" @click="removeMsPmRow()" x-show="reportFormData.ms_pm_checklist.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Checklist</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Item Pemeriksaan</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Kondisi</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Hasil</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Temuan</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan</th>
                                            <th class="py-1.5 px-2 w-20 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(pm, idx) in reportFormData.ms_pm_checklist" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.item_pemeriksaan" placeholder="Item pemeriksaan..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="pm.kondisi" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="pm.hasil" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.temuan" placeholder="Temuan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.tindakan" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center font-bold"><input type="text" x-model="pm.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center text-[#8F0A0D]"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. MATERIAL / SPARE PART</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsMaterialRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Item</span>
                                    </button>
                                    <button type="button" @click="removeMsMaterialRow()" x-show="reportFormData.ms_materials.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Item</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Item</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Type</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Qty</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Used / Replaced</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Old/New</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(mat, idx) in reportFormData.ms_materials" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.item" placeholder="Item material..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.type" placeholder="Type / Model" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.qty" placeholder="1 Pcs" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.used_replaced" placeholder="Replaced" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.old_new" placeholder="New" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1"><input type="text" x-model="mat.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. DOKUMENTASI &amp; EVIDENCE</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsEvidenceRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Evidence</span>
                                    </button>
                                    <button type="button" @click="removeMsEvidenceRow()" x-show="reportFormData.ms_evidence.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Evidence</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-left">Evidence</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Waktu</th>
                                            <th class="py-1.5 px-2 w-48 border-r border-[#1E293B] text-center">Foto / Screenshot / Log</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(evi, idx) in reportFormData.ms_evidence" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="evi.evidence" placeholder="Screenshot monitoring / foto" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="evi.waktu" class="w-full p-1 text-xs border-0 bg-transparent text-center font-medium cursor-pointer"></td>
                                                <td class="p-1.5 border-r border-[#1E293B] text-center">
                                                    <template x-if="evi.foto_url">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <img :src="evi.foto_url" class="h-9 w-auto rounded border object-contain">
                                                            <button type="button" @click="evi.foto_url = ''" class="text-red-500 hover:text-red-700 text-[11px] font-bold underline">Hapus</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!evi.foto_url">
                                                        <label class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-bold transition">
                                                            <span>Upload File</span>
                                                            <input type="file" accept="image/*" class="hidden" @change="handleMsEvidenceUpload($event, idx)">
                                                        </label>
                                                    </template>
                                                </td>
                                                <td class="p-1"><input type="text" x-model="evi.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic pt-0.5">Evidence dapat berupa foto onsite, screenshot monitoring, log, hasil check, atau bukti pengujian.</p>
                        </div>

                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">I. SERVICE CLOSURE</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service Status</td>
                                            <td class="w-3/10 py-1.5 px-2.5 border-r border-[#1E293B]">
                                                <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.service_status.resolved" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Resolved</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.service_status.monitoring" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Monitoring</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.service_status.escalated" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Escalated</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.service_status.closed" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Closed</span>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="w-1/6 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">SLA</td>
                                            <td class="w-1/3 py-1.5 px-2.5">
                                                <div class="flex items-center gap-5 text-xs font-semibold">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.sla.met" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Met</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_closure.sla.breach" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Breach</span>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer Confirmation</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_closure.customer_confirmation" placeholder="Konfirmasi pelanggan..." class="w-full p-1 text-xs border-0 bg-transparent font-medium">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                            <td class="p-1">
                                                <input type="text" x-model="reportFormData.ms_closure.outstanding" placeholder="Catatan outstanding issue..." class="w-full p-1 text-xs border-0 bg-transparent">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-1.5 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal / Jam</th>
                                            <th class="py-1.5 px-3 w-1/4 text-center">Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.engineer_pic" placeholder="Nama Engineer" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
                                            <td class="py-2 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader / Service Manager</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_verifikasi.lead_name" placeholder="Nama Team Leader / Service Manager" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
                                            <td class="py-2 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Authorized Representative</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_verifikasi.customer_name" placeholder="Nama Customer / Authorized PIC" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
                                            <td class="py-2 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic pt-0.5">Catatan: laporan Managed Service harus dapat ditelusuri ke contract/service, ticket, SLA, evidence, dan status closure.</p>
                        </div>

                    </div>


                    <div x-show="(reportFormData?.category || 'project') === 'help_desk'"
                         class="!mt-3.5 bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
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
                            <div class="text-right shrink-0">
                                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Dokumen Resmi
                                </span>
                            </div>
                        </div>

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
                                            <td class="w-3/10 py-1.5 px-2.5">
                                                <div class="flex items-center gap-4 text-xs font-semibold">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.hd_identitas.shift.pagi" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Pagi</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.hd_identitas.shift.siang" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
                                                        <span>Siang</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.hd_identitas.shift.malam" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-3.5 h-3.5">
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
                                                <input type="text" x-model="reportFormData.hd_identitas.area_site" placeholder="Area site..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Service</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.hd_identitas.customer_service" placeholder="Customer / Layanan..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jam Shift</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.hd_identitas.jam_shift" placeholder="08:00 - 16:00 WIB" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Jumlah Engineer</td>
                                            <td class="py-1 px-2">
                                                <input type="number" min="1" x-model="reportFormData.hd_identitas.jumlah_engineer" class="w-16 p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-bold"> orang
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. KONDISI AWAL SHIFT</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdKondisiRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeHdKondisiRow()" x-show="reportFormData.hd_kondisi_awal.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Item / Service</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kondisi Awal</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Alarm / Issue</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(k, idx) in reportFormData.hd_kondisi_awal" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.item_service" placeholder="Item / service..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.kondisi_awal" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.alarm_issue" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="k.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold text-[#8F0A0D]"></td>
                                                <td class="p-1"><input type="text" x-model="k.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. REKAP AKTIVITAS SHIFT <span class="text-[#8F0A0D]">*</span></h3>
                                    <p class="text-[10.5px] text-gray-500">Rincian aktivitas tiket, WO, troubleshooting perangkat, atau penanganan gangguan.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdActRow()" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeHdActRow()" x-show="reportFormData.hd_aktivitas.length > 1" class="text-xs font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                                <table class="w-full border-collapse border border-[#1E293B] min-w-[700px]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-1.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                            <th class="py-1.5 px-1.5 w-24 text-center border-r border-[#1E293B]">Waktu</th>
                                            <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-left">Aktivitas <span class="text-red-500">*</span></th>
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Ticket / WO</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Lokasi / Device</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Hasil</th>
                                            <th class="py-1.5 px-1.5 w-24 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(act, idx) in reportFormData.hd_aktivitas" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="time" x-model="act.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <textarea rows="2" x-model="act.aktivitas" placeholder="Aktivitas engineer..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] font-semibold resize-none focus:bg-white"></textarea>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.ticket_wo" placeholder="TICK/WO..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.lokasi_device" placeholder="Lokasi / Device..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="act.hasil" placeholder="Hasil..." class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold">
                                                </td>
                                                <td class="p-1 text-center">
                                                    <select x-model="act.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer">
                                                        <option value="Done">Done</option>
                                                        <option value="In Progress">In Progress</option>
                                                        <option value="Pending">Pending</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. REKAP TICKET / INCIDENT</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdTicketRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Ticket</span>
                                    </button>
                                    <button type="button" @click="removeHdTicketRow()" x-show="reportFormData.hd_ticket_incident.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Ticket</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Ticket</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Jenis</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Priority</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Start</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Restore</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Close / Status</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(tk, idx) in reportFormData.hd_ticket_incident" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tk.ticket" placeholder="TICK-xxx" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tk.jenis" placeholder="Incident / Request" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="tk.priority" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="tk.start" class="w-full p-1 text-xs border-0 bg-transparent text-center font-medium cursor-pointer"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="tk.restore" class="w-full p-1 text-xs border-0 bg-transparent text-center font-medium cursor-pointer"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="tk.close_status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold text-[#8F0A0D]"></td>
                                                <td class="p-1"><input type="text" x-model="tk.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. MONITORING &amp; SERVICE STATUS</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdMonitoringRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeHdMonitoringRow()" x-show="reportFormData.hd_monitoring_status.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Service / Device</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Alarm</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Performance / Parameter</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Action</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(m, idx) in reportFormData.hd_monitoring_status" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.service_device" placeholder="Service / Device..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="m.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold text-emerald-700"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="m.alarm" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.performance" placeholder="CPU 15%, RAM 40%" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.action" placeholder="Monitoring rutin" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1"><input type="text" x-model="m.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. PEKERJAAN ONSITE / FIELD</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdFieldRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Pekerjaan</span>
                                    </button>
                                    <button type="button" @click="removeHdFieldRow()" x-show="reportFormData.hd_pekerjaan_field.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Pekerjaan</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Lokasi</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Pekerjaan</th>
                                            <th class="py-1.5 px-2 w-1/6 border-r border-[#1E293B] text-left">Engineer</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Hasil</th>
                                            <th class="py-1.5 px-2 w-28 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(f, idx) in reportFormData.hd_pekerjaan_field" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.lokasi" placeholder="Lokasi site..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.pekerjaan" placeholder="Uraian pekerjaan onsite..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.engineer" placeholder="Nama engineer" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.hasil" placeholder="Hasil pekerjaan..." class="w-full p-1 text-xs border-0 bg-transparent font-semibold"></td>
                                                <td class="p-1 text-center font-bold"><input type="text" x-model="f.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center text-[#8F0A0D]"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. KENDALA &amp; ESCALATION</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdKendalaRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Kendala</span>
                                    </button>
                                    <button type="button" @click="removeHdKendalaRow()" x-show="reportFormData.hd_kendala_escalation.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Kendala</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kendala / Incident</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Dampak</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan</th>
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Escalated To</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 text-left">Next Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(kd, idx) in reportFormData.hd_kendala_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.kendala_incident" placeholder="Kendala..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.dampak" placeholder="Dampak..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.tindakan" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.escalated_to" placeholder="Eskalasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center font-bold"><input type="text" x-model="kd.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1"><input type="text" x-model="kd.next_action" placeholder="Next action..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. HANDOVER KE SHIFT BERIKUTNYA</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdHandoverRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Handover</span>
                                    </button>
                                    <button type="button" @click="removeHdHandoverRow()" x-show="reportFormData.hd_handover.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Handover</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Outstanding / Issue</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kondisi Terakhir</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan Berikutnya</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">PIC</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Due Time</th>
                                            <th class="py-1.5 px-2 text-left">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(ho, idx) in reportFormData.hd_handover" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.outstanding_issue" placeholder="Issue..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.kondisi_terakhir" placeholder="Kondisi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.tindakan_berikutnya" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.pic" placeholder="PIC" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="ho.due_time" placeholder="10:00 WIB" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1"><input type="text" x-model="ho.catatan" placeholder="Catatan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_rekap_shift.service_kritis" placeholder="Nihil / -" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Handover diperlukan</td>
                                            <td class="p-2">
                                                <div class="flex items-center gap-5 text-xs font-semibold">
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                                        <input type="radio" :value="true" x-model="reportFormData.hd_rekap_shift.handover_diperlukan" class="text-[#8F0A0D]">
                                                        <span>Ya</span>
                                                    </label>
                                                    <label class="flex items-center gap-1.5 cursor-pointer">
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

                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-1.5 px-3 w-1/3 text-center">Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / Shift PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_identitas.nama_engineer" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_identitas.team_leader" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer Shift Berikutnya / Handover</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_verifikasi.next_engineer_name" placeholder="Nama Engineer Shift Berikutnya..." class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center text-xs text-slate-500">Handover Verified</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic pt-0.5">Catatan: Daily/Shift Report bukan pengganti laporan Project atau Managed Service. Fungsinya sebagai kontrol operasional dan handover antar-shift.</p>
                        </div>

                    </div>

</div>
