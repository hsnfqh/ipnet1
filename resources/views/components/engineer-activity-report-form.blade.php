<div>

    <div x-show="reportFormSavedSuccess" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-3.5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Formulir Laporan Aktivitas berhasil disimpan! Data akan otomatis tercetak pada PDF.</span>
        </div>
        <button type="button" @click="reportFormSavedSuccess = false" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">&times;</button>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#CBD5E1] shadow-xs !mt-0">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">Kategori Laporan:</span>
            <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold tracking-wide uppercase shadow-2xs"
                  x-text="(reportFormData?.category || 'project') === 'managed_service' ? 'Managed Service' : ((reportFormData?.category || 'project') === 'help_desk' ? 'Help Desk' : 'Project')">
            </span>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="saveReportForm()" :disabled="reportFormSaving"
                    class="btn-ipnet-gradient px-4 py-2 font-bold text-xs rounded-lg transition shadow-md flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                <svg x-show="reportFormSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg x-show="!reportFormSaving" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span x-text="reportFormSaving ? 'Menyimpan...' : 'Simpan Form Laporan'"></span>
            </button>
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
                                                <div class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50/60 hover:bg-gray-50 transition">
                                                    <label class="px-3 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs transition">
                                                        <span>Upload Foto</span>
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
                                                <div class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50/60 hover:bg-gray-50 transition">
                                                    <label class="px-3 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs transition">
                                                        <span>Upload Foto</span>
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
                                                <div class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50/60 hover:bg-gray-50 transition">
                                                    <label class="px-3 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs transition">
                                                        <span>Upload Foto</span>
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

                        <!-- A. IDENTITAS SERVICE -->
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
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. SPK/Contract</td>
                                            <td class="w-3/10 py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.no_spk_contract" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.customer" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. SO</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.no_so" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Site / Lokasi</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.site_lokasi" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Ticket / Incident No.</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.ticket_incident_no" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.engineer_pic" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service / Device</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.service_device" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
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
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC Customer</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.ms_identitas.pic_customer" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
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

                        <!-- B. SLA TRACKING -->
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. B. SLA TRACKING</h3>
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
                                                    <input type="text" x-model="sla.target_sla" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="sla.aktual" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <select x-model="sla.status" class="w-full p-1 text-xs border-0 bg-transparent font-bold text-center text-[#8F0A0D] cursor-pointer">
                                                        <option value="">-</option>
                                                        <option value="Met">Met</option>
                                                        <option value="Breach">Breach</option>
                                                    </select>
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" x-model="sla.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- C. KONDISI SERVICE / PERANGKAT -->
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
                            <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                                <table class="w-full border-collapse border border-[#1E293B] min-w-[750px]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Perangkat</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">CPU Status</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Fan Status</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Power Supply Status</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Firmware Version</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(k, idx) in reportFormData.ms_kondisi_perangkat" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="k.perangkat" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.cpu_status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.fan_status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.power_supply_status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.firmware_version" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-mono">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="text" x-model="k.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold">
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

                        <!-- D. INCIDENT / ROOT CAUSE / ESCALATION -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. INCIDENT / ROOT CAUSE / ESCALATION</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsIncidentRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Incident</span>
                                    </button>
                                    <button type="button" @click="removeMsIncidentRow()" x-show="reportFormData.ms_incident_escalation.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Incident</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                                <table class="w-full border-collapse border border-[#1E293B] min-w-[700px]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Incident</th>
                                            <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Impact</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Probable / Root Cause</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Escalation</th>
                                            <th class="py-1.5 px-2 w-24 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(inc, idx) in reportFormData.ms_incident_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="inc.incident" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none focus:bg-white"></textarea></td>
                                                <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="inc.impact" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none focus:bg-white"></textarea></td>
                                                <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="inc.probable_root_cause" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none focus:bg-white"></textarea></td>
                                                <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="inc.escalation" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none focus:bg-white"></textarea></td>
                                                <td class="p-1 text-center font-semibold"><input type="text" x-model="inc.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- E. RINCIAN AKTIVITAS ENGINEER -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. RINCIAN AKTIVITAS ENGINEER</h3>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsActRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeMsActRow()" x-show="reportFormData.ms_aktivitas.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Baris</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 text-center border-r border-[#1E293B]">No.</th>
                                            <th class="py-1.5 px-2 w-28 text-center border-r border-[#1E293B]">Waktu</th>
                                            <th class="py-1.5 px-3 border-r border-[#1E293B] text-left">Detail Aktivitas</th>
                                            <th class="py-1.5 px-3 w-1/3 text-left">Hasil /Catatan</th>
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
                                                    <textarea rows="2" x-model="act.detail_aktivitas" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] font-semibold resize-none focus:bg-white"></textarea>
                                                </td>
                                                <td class="p-1">
                                                    <textarea rows="2" x-model="act.hasil_catatan" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none focus:bg-white"></textarea>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- F. MATERIAL / SPARE PART (RMA) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. MATERIAL / SPARE PART (RMA)</h3>
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
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Type</th>
                                            <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">QTY</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Serial Number Lama</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Serial Number Baru</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(mat, idx) in reportFormData.ms_materials" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.type" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="number" min="0" x-model.number="mat.qty" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.sn_lama" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-mono"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.sn_baru" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-mono font-semibold"></td>
                                                <td class="p-1"><input type="text" x-model="mat.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- G. DOKUMENTASI & EVIDENCE -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. DOKUMENTASI &amp; EVIDENCE</h3>
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
                                            <th class="py-1.5 px-2 w-56 border-r border-[#1E293B] text-center">Foto / Screenshot / Log</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(evi, idx) in reportFormData.ms_evidence" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="evi.evidence" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-2 border-r border-[#1E293B] text-center">
                                                    <template x-if="evi.foto_url">
                                                        <div class="relative group inline-block my-0.5">
                                                            <img :src="evi.foto_url" class="h-16 w-auto max-w-full rounded border object-contain">
                                                            <button type="button" @click="evi.foto_url = ''" class="absolute -top-1.5 -right-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 text-xs font-bold shadow flex items-center justify-center transition" title="Hapus Foto">&times;</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!evi.foto_url">
                                                        <div class="w-full py-2 px-1.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50/60 hover:bg-gray-50 transition">
                                                            <label class="px-3 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs transition">
                                                                <span>Upload Foto</span>
                                                                <input type="file" accept="image/*" class="hidden" @change="handleMsEvidenceUpload($event, idx)">
                                                            </label>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="p-1"><input type="text" x-model="evi.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic pt-0.5">** Evidence dapat berupa foto onsite, screenshot monitoring, log, hasil check, atau bukti pengujian.</p>
                        </div>

                        <!-- H. CATATAN -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. CATATAN</h3>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addMsCatatanRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Catatan</span>
                                    </button>
                                    <button type="button" @click="removeMsCatatanRow()" x-show="reportFormData.ms_catatan.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Catatan</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-3 text-left">Catatan Hasil Pekerjaan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(cat, idx) in reportFormData.ms_catatan" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1">
                                                    <textarea rows="2" x-model="cat.catatan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- I. SERVICE CLOSURE -->
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
                                                <input type="text" x-model="reportFormData.ms_closure.customer_confirmation" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                            <td class="p-1">
                                                <input type="text" x-model="reportFormData.ms_closure.outstanding" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- J. VERIFIKASI -->
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
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_identitas.engineer_pic" placeholder="" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader / Service Manager</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_verifikasi.lead_name" placeholder="" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
                                            <td class="py-6 px-3 text-center min-h-[56px]"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Authorized Representative</td>
                                            <td class="p-1 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.ms_verifikasi.customer_name" placeholder="" class="w-full p-1 text-xs font-bold bg-transparent border-0">
                                            </td>
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

                        <!-- A. IDENTITAS SHIFT -->
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS SHIFT</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-[18%] py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal</td>
                                            <td class="w-[42%] py-1 px-2 border-r border-[#1E293B]">
                                                <input type="date" x-model="reportFormData.hd_identitas.tanggal" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-medium text-xs cursor-pointer">
                                            </td>
                                            <td colspan="2" class="w-[40%] py-1.5 px-2 font-black bg-[#EBF3FB] text-center border-b border-[#1E293B] text-xs uppercase tracking-wider text-[#0F172A]">
                                                Shift
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Engineer</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.hd_identitas.nama_engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-1.5 px-3 border-r border-[#1E293B] font-semibold text-xs text-[#0F172A] w-[32%]">
                                                Pagi &nbsp;&nbsp;(06.00–14.00)
                                            </td>
                                            <td class="py-1.5 px-2 text-center w-[8%]">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.pagi" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-4 h-4 cursor-pointer">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Area / Site</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.hd_identitas.area_site" placeholder="" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                            <td class="py-1.5 px-3 border-r border-[#1E293B] font-semibold text-xs text-[#0F172A]">
                                                Siang &nbsp;(14.00–22.00)
                                            </td>
                                            <td class="py-1.5 px-2 text-center">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.siang" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-4 h-4 cursor-pointer">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.hd_identitas.team_leader" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                            </td>
                                            <td class="py-1.5 px-3 border-r border-[#1E293B] font-semibold text-xs text-[#0F172A]">
                                                Malam (22.00–06.00)
                                            </td>
                                            <td class="py-1.5 px-2 text-center">
                                                <input type="checkbox" x-model="reportFormData.hd_identitas.shift.malam" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D] w-4 h-4 cursor-pointer">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- B. KONDISI AWAL SHIFT -->
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
                                            <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Customer</th>
                                            <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Alarm / Issue</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-3 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(k, idx) in reportFormData.hd_kondisi_awal" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.customer" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.alarm_issue" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="k.status" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold text-[#8F0A0D]"></td>
                                                <td class="p-1"><input type="text" x-model="k.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- C. REKAP TICKET / INCIDENT -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. REKAP TICKET / INCIDENT</h3>
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
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Ticket</th>
                                            <th class="py-1.5 px-2 w-36 border-r border-[#1E293B] text-left">Customer</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Priority</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Time</th>
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(tk, idx) in reportFormData.hd_ticket_incident" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tk.ticket" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tk.customer" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="text" x-model="tk.priority" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center"><input type="time" x-model="tk.time" class="w-full p-1 text-xs border-0 bg-transparent text-center font-medium cursor-pointer"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <select x-model="tk.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer">
                                                        <option value="Open">Open</option>
                                                        <option value="Progress">Progress</option>
                                                        <option value="Close">Close</option>
                                                    </select>
                                                </td>
                                                <td class="p-1"><input type="text" x-model="tk.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- D. KENDALA & ESCALATION -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. KENDALA &amp; ESCALATION</h3>
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
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Ticket</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Dampak</th>
                                            <th class="py-1.5 px-2 w-1/3 border-r border-[#1E293B] text-left">Action</th>
                                            <th class="py-1.5 px-2 w-36 border-r border-[#1E293B] text-left">Escalated To</th>
                                            <th class="py-1.5 px-2 w-28 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(kd, idx) in reportFormData.hd_kendala_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.ticket" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.dampak" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><textarea rows="2" x-model="kd.action" placeholder="" class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="kd.escalated_to" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center font-bold">
                                                    <select x-model="kd.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer">
                                                        <option value="Open">Open</option>
                                                        <option value="Progress">Progress</option>
                                                        <option value="Close">Close</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- E. HANDOVER KE SHIFT BERIKUTNYA -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. HANDOVER KE SHIFT BERIKUTNYA</h3>
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
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Tiket Outstanding / Issue</th>
                                            <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Kondisi Terakhir</th>
                                            <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Tindakan Berikutnya</th>
                                            <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-center">Durasi Incident<br><span class="text-[10px] font-normal text-slate-500">Start / End</span></th>
                                            <th class="py-1.5 px-2 text-left">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(ho, idx) in reportFormData.hd_handover" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.ticket_issue" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.kondisi_terakhir" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="ho.tindakan_berikutnya" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <div class="flex items-center justify-center gap-1">
                                                        <input type="time" x-model="ho.durasi_start" class="w-16 p-1 text-xs border rounded bg-[#F8FAFC] text-center cursor-pointer">
                                                        <span class="text-slate-400">-</span>
                                                        <input type="time" x-model="ho.durasi_end" class="w-16 p-1 text-xs border rounded bg-[#F8FAFC] text-center cursor-pointer">
                                                    </div>
                                                </td>
                                                <td class="p-1"><input type="text" x-model="ho.catatan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- F. REKAP SHIFT -->
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. REKAP SHIFT</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total ticket diterima</td>
                                            <td class="w-1/4 p-1 border-r border-[#1E293B]"><input type="number" min="0" x-model.number="reportFormData.hd_rekap_shift.total_ticket_diterima" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total ticket closed</td>
                                            <td class="w-1/4 p-1"><input type="number" min="0" x-model.number="reportFormData.hd_rekap_shift.total_ticket_closed" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Total incident</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="number" min="0" x-model.number="reportFormData.hd_rekap_shift.total_incident" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                            <td class="p-1"><input type="number" min="0" x-model.number="reportFormData.hd_rekap_shift.outstanding" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Service kritis / alert</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_rekap_shift.service_kritis" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-bold"></td>
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

                        <!-- G. VERIFIKASI -->
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. VERIFIKASI</h3>
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
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_verifikasi.next_engineer_name" placeholder="" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center text-xs text-slate-500">Handover Verified</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic pt-0.5">Catatan: Daily/Shift Report bukan pengganti laporan Project atau Managed Service. Fungsinya sebagai kontrol operasional dan handover antar-shift.</p>
                        </div>

                        <!-- H. DOKUMENTASI & EVIDENCE (OPSIONAL) -->
                        <div class="space-y-1.5 pt-2 border-t border-dashed border-gray-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. DOKUMENTASI &amp; EVIDENCE (FOTO / SCREENSHOT / LOG)</h3>
                                    <p class="text-[10.5px] text-gray-500">Lampirkan tangkapan layar monitoring, foto perangkat fisik, log, atau bukti penanganan incident (opsional).</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addHdEvidenceRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Evidence</span>
                                    </button>
                                    <button type="button" @click="removeHdEvidenceRow()" x-show="reportFormData.hd_evidence && reportFormData.hd_evidence.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                        <span>- Hapus Evidence</span>
                                    </button>
                                </div>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-10 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Evidence / Keterangan Masalah</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-center">Foto / Screenshot / Log</th>
                                            <th class="py-1.5 px-3 text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(evi, idx) in reportFormData.hd_evidence" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="evi.evidence" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-2 border-r border-[#1E293B] text-center">
                                                    <template x-if="evi.foto_url">
                                                        <div class="relative group inline-block my-0.5">
                                                            <img :src="evi.foto_url" class="h-16 w-auto max-w-full rounded border object-contain">
                                                            <button type="button" @click="evi.foto_url = ''" class="absolute -top-1.5 -right-1.5 bg-red-600 hover:bg-red-700 text-white rounded-full w-5 h-5 text-xs font-bold shadow flex items-center justify-center transition" title="Hapus Foto">&times;</button>
                                                        </div>
                                                    </template>
                                                    <template x-if="!evi.foto_url">
                                                        <div class="w-full py-2 px-1.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50/60 hover:bg-gray-50 transition">
                                                            <label class="px-3 py-1 bg-white border border-gray-300 rounded text-[10.5px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block shadow-2xs transition">
                                                                <span>Upload Foto</span>
                                                                <input type="file" accept="image/*" class="hidden" @change="handleHdEvidenceUpload($event, idx)">
                                                            </label>
                                                        </div>
                                                    </template>
                                                </td>
                                                <td class="p-1"><input type="text" x-model="evi.keterangan" placeholder="" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>

</div>
