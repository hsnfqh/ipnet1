{{-- FORM LAPORAN AKTIVITAS ENGINEER – PROJECT (INTERAKTIF SESUAI TEMPLATE RESMI PDF) --}}
<div class="space-y-6">

    {{-- Alert Sukses Simpan --}}
    <div x-show="reportFormSavedSuccess" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Formulir Laporan Aktivitas berhasil disimpan! Data akan otomatis tercetak pada PDF.</span>
        </div>
        <button type="button" @click="reportFormSavedSuccess = false" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">✕</button>
    </div>

    {{-- Container Format Kertas Resmi (Tampilan Paper-Like Sesuai Acuan PDF) --}}
    <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-md space-y-5 text-[#0F172A] font-sans">

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

            {{-- Action Quick Buttons di Atas Form --}}
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <button type="button" @click="saveReportForm()" :disabled="reportFormSaving"
                        class="px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs rounded-lg transition shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                    <svg x-show="reportFormSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg x-show="!reportFormSaving" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="reportFormSaving ? 'Menyimpan...' : 'Simpan Form Laporan'"></span>
                </button>
                <div class="inline-flex rounded-lg shadow-xs">
                    <button type="button" @click="downloadReportPdf('download')"
                       class="px-3 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 font-bold text-xs rounded-l-lg transition inline-flex items-center gap-1.5 cursor-pointer"
                       title="Download file PDF 2 Halaman">
                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Download PDF (2 Hal)</span>
                    </button>
                    <button type="button" @click="downloadReportPdf('stream')"
                       class="px-2 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border-t border-b border-r border-red-200 hover:border-red-300 font-bold text-xs rounded-r-lg transition inline-flex items-center cursor-pointer"
                       title="Buka / Preview PDF di Tab Baru">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <template x-if="reportFormData">
            <div class="space-y-6">

                {{-- ═══ A. IDENTITAS PEKERJAAN ═══ --}}
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

                {{-- ═══ B. KOMPOSISI TENAGA KERJA ═══ --}}
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

                {{-- ═══ C. RUANG LINGKUP / TARGET PEKERJAAN ═══ --}}
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

                {{-- ═══ D. RINCIAN AKTIVITAS ENGINEER ═══ --}}
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

                {{-- ═══ E. MATERIAL, PERALATAN & SPARE PART ═══ --}}
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
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.qty" placeholder="1" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.satuan" placeholder="Pcs" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="mat.kondisi" placeholder="Baik" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="mat.keterangan" placeholder="Keterangan" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ═══ F. HASIL PENGUJIAN / PENGUKURAN ═══ --}}
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
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="test.sebelum" placeholder="-" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="test.sesudah" placeholder="-" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="test.satuan" placeholder="ms / Mbps" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                        <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="test.metode" placeholder="Ping / Speedtest" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        <td class="p-1"><input type="text" x-model="test.keterangan" placeholder="OK" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ═══ G. KENDALA / INCIDENT / DEVIASI ═══ --}}
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
                                        <td class="p-1 text-center"><input type="text" x-model="inc.status" placeholder="Closed" class="w-full p-1 text-center text-xs border-0 bg-transparent"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ═══ H. HASIL AKHIR PEKERJAAN ═══ --}}
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

                {{-- ═══ I. REKAP DOKUMENTASI FOTO ═══ --}}
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
                                {{-- 1. BEFORE --}}
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
                                                    <button type="button" @click="removePhoto('before')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">✕</button>
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

                                {{-- 2. PROGRESS --}}
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
                                                    <button type="button" @click="removePhoto('progress')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">✕</button>
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

                                {{-- 3. AFTER --}}
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
                                                    <button type="button" @click="removePhoto('after')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold" title="Hapus Foto">✕</button>
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

                {{-- ═══ J. VERIFIKASI & PENGESAHAN ═══ --}}
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
                                {{-- Engineer --}}
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Engineer</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B] align-middle">
                                        <input type="text" x-model="reportFormData.identitas.nama_engineer" placeholder="Nama Engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-2.5 px-3 text-center border-r border-[#1E293B] align-middle" x-text="sigInfo?.pic?.signed ? sigInfo?.pic?.signed_at : '{{ date('d/m/Y') }}'"></td>
                                    <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                </tr>
                                {{-- Project Manager / Team Leader --}}
                                <tr class="border-b border-[#1E293B]">
                                    <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Project Manager / Team Leader</td>
                                    <td class="py-1 px-2 border-r border-[#1E293B] align-middle">
                                        <input type="text" x-model="reportFormData.identitas.nama_leader" placeholder="Nama PM / Team Leader" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                    </td>
                                    <td class="py-2.5 px-3 text-center border-r border-[#1E293B] align-middle" x-text="sigInfo?.lead?.signed ? sigInfo?.lead?.signed_at : '{{ date('d/m/Y') }}'"></td>
                                    <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                </tr>
                                {{-- Customer / Site Representative --}}
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

                {{-- ═══ K. CATATAN ADMINISTRASI DOKUMEN ═══ --}}
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

</div>
