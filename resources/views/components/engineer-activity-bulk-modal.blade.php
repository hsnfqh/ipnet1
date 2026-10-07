{{-- MODAL INPUT FORM LAPORAN AKTIVITAS ENGINEER – PROJECT --}}
@php
    $modalProjects = $projects ?? $myProjects ?? null;
    if ($modalProjects === null) {
        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        if ($isLead) {
            $modalProjects = \App\Models\Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->orderBy('name')
                ->get(['id', 'name', 'client', 'location', 'po_number', 'customer_pic_technical', 'description', 'progress']);
        } else {
            $modalProjects = \App\Models\Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->where(function($q) use ($authUser) {
                    $uid = $authUser->id;
                    $q->where('created_by', $uid)
                      ->orWhereHas('tasks', fn($tq) => $tq->where('engineer_id', $uid))
                      ->orWhereHas('schedules', fn($sq) => $sq->where('engineer_id', $uid));
                })
                ->orderBy('name')
                ->get(['id', 'name', 'client', 'location', 'po_number', 'customer_pic_technical', 'description', 'progress']);
        }
    }
    $projectsLookup = $modalProjects->keyBy('id');
@endphp

<div x-data="{
    isBulkModalOpen: false,
    bulkProjectId: '',
    activityTitle: '',
    projectsMap: {{ Js::from($projectsLookup) }},
    reportFormData: {
        identitas: {
            hari_tanggal: '{{ date('Y-m-d') }}',
            hari_tanggal_raw: '{{ date('Y-m-d') }}',
            no_laporan: 'IPNET-ACT-{{ date('Ym') }}-DRAFT',
            nama_project: '',
            no_so_spk: '',
            lokasi_site: '',
            work_order: '',
            nama_engineer: '{{ auth()->user()?->name ?? 'Engineer' }}',
            nama_leader: 'Nugraha Pratama',
            customer: '',
            jenis_pekerjaan: '',
            pic_customer: '',
            kategori_implementasi: false,
            kategori_managed_service: false,
            kategori_pekerjaan: '',
            jabatan: '{{ auth()->user()?->position ?: (auth()->user()?->getRoleNames()->first() ?: 'Field Engineer') }}',
            jam_mulai: '',
            jam_selesai: '',
        },
        manpower: [
            {
                nama: '{{ auth()->user()?->name ?? 'Engineer' }}',
                unit_kerja: '',
                jabatan: '{{ auth()->user()?->position ?: 'Field Engineer' }}',
                keterangan: ''
            }
        ],
        total_tenaga_kerja: 1,
        ruang_lingkup: {
            target_hari_ini: '',
            durasi_project: '',
            scope_pekerjaan: '',
            perangkat_sistem: '',
            kriteria_selesai: '',
        },
        rincian_aktivitas: [
            { no: 1, waktu: '', aktivitas: '', perangkat: '', hasil: '', status: '', kendala: '', tindak_lanjut: '' }
        ],
        materials: [
            { nama: '', spesifikasi: '', qty: '', satuan: '', kondisi: '', keterangan: '' }
        ],
        test_results: [
            { parameter: '', sebelum: '', sesudah: '', satuan: '', metode: '', keterangan: '' }
        ],
        incidents: [
            { waktu: '', kendala: '', dampak: '', tindakan: '', status: '' }
        ],
        hasil_akhir: {
            status_pekerjaan: '',
            progress_percent: '',
            kondisi_sistem: '',
            outstanding: '',
            rekomendasi: '',
            eskalasi_pic: '',
        },
        foto_dokumentasi: {
            before: { area: '', url: '', caption: '' },
            progress: { area: '', url: '', caption: '' },
            after: { area: '', url: '', caption: '' },
        },
        administrasi: {
            nomor_wo: '',
            nomor_ba: '',
            lampiran: '',
            folder: '',
        }
    },

    onProjectChange() {
        if (!this.bulkProjectId) return;
        const p = this.projectsMap[this.bulkProjectId];
        if (p) {
            this.reportFormData.identitas.nama_project = p.name || '';
            this.reportFormData.identitas.customer = p.client || '';
            this.reportFormData.identitas.lokasi_site = p.location || '';
            this.reportFormData.identitas.no_so_spk = p.po_number || '';
            this.reportFormData.identitas.pic_customer = p.customer_pic_technical || '';
            if (p.progress) this.reportFormData.hasil_akhir.progress_percent = p.progress;
            if ((p.name || '').toLowerCase().includes('maintenance') || (p.name || '').toLowerCase().includes('managed')) {
                this.reportFormData.identitas.kategori_managed_service = true;
                this.reportFormData.identitas.kategori_implementasi = false;
            } else {
                this.reportFormData.identitas.kategori_implementasi = true;
                this.reportFormData.identitas.kategori_managed_service = false;
            }
        }
    },

    addActRow() {
        const nextNo = this.reportFormData.rincian_aktivitas.length + 1;
        this.reportFormData.rincian_aktivitas.push({
            no: nextNo,
            waktu: '',
            aktivitas: '',
            perangkat: '',
            hasil: '',
            status: '',
            kendala: '',
            tindak_lanjut: ''
        });
    },

    removeActRow(idx) {
        if (this.reportFormData.rincian_aktivitas.length > 1) {
            if (typeof idx === 'number') {
                this.reportFormData.rincian_aktivitas.splice(idx, 1);
            } else {
                this.reportFormData.rincian_aktivitas.pop();
            }
            this.reportFormData.rincian_aktivitas.forEach((r, i) => r.no = i + 1);
        } else {
            this.reportFormData.rincian_aktivitas[0].aktivitas = '';
        }
    },

    addManpowerRow() {
        this.reportFormData.manpower.push({ nama: '', unit_kerja: '', jabatan: '', keterangan: '' });
        this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
    },
    removeManpowerRow(idx) {
        if (this.reportFormData.manpower.length > 1) {
            if (typeof idx === 'number') {
                this.reportFormData.manpower.splice(idx, 1);
            } else {
                this.reportFormData.manpower.pop();
            }
            this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
        }
    },

    addMaterialRow() {
        this.reportFormData.materials.push({ nama: '', spesifikasi: '', qty: '', satuan: '', kondisi: '', keterangan: '' });
    },
    removeMaterialRow(idx) {
        if (this.reportFormData.materials.length > 1) {
            if (typeof idx === 'number') {
                this.reportFormData.materials.splice(idx, 1);
            } else {
                this.reportFormData.materials.pop();
            }
        }
    },

    addTestResultRow() {
        this.reportFormData.test_results.push({ parameter: '', sebelum: '', sesudah: '', satuan: '', metode: '', keterangan: '' });
    },
    removeTestResultRow(idx) {
        if (this.reportFormData.test_results.length > 1) {
            if (typeof idx === 'number') {
                this.reportFormData.test_results.splice(idx, 1);
            } else {
                this.reportFormData.test_results.pop();
            }
        }
    },

    addIncidentRow() {
        this.reportFormData.incidents.push({ waktu: '', kendala: '', dampak: '', tindakan: '', status: '' });
    },
    removeIncidentRow(idx) {
        if (this.reportFormData.incidents.length > 1) {
            if (typeof idx === 'number') {
                this.reportFormData.incidents.splice(idx, 1);
            } else {
                this.reportFormData.incidents.pop();
            }
        }
    },

    compressImage(file, maxDimension = 1000, quality = 0.72) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    let width = img.width;
                    let height = img.height;
                    if (width > maxDimension || height > maxDimension) {
                        if (width > height) {
                            height = Math.round((height * maxDimension) / width);
                            width = maxDimension;
                        } else {
                            width = Math.round((width * maxDimension) / height);
                            height = maxDimension;
                        }
                    }
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/jpeg', quality));
                };
                img.onerror = () => resolve(e.target.result);
                img.src = e.target.result;
            };
            reader.onerror = () => resolve('');
            reader.readAsDataURL(file);
        });
    },

    async handlePhotoUpload(e, stage) {
        const file = e.target.files[0];
        if (!file) return;
        try {
            const compressed = await this.compressImage(file, 1000, 0.72);
            if (compressed) {
                this.reportFormData.foto_dokumentasi[stage].url = compressed;
            }
        } catch (err) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.reportFormData.foto_dokumentasi[stage].url = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    },

    removePhoto(stage) {
        this.reportFormData.foto_dokumentasi[stage].url = '';
    },

    submitBulkForm(e) {
        if (!this.bulkProjectId) {
            alert('Silakan pilih proyek tujuan terlebih dahulu.');
            e.preventDefault();
            return;
        }
        if (this.$refs.reportDataInput) {
            this.$refs.reportDataInput.value = JSON.stringify(this.reportFormData);
        }
    }
}"
@open-engineer-activity-modal.window="isBulkModalOpen = true"
@keydown.escape.window="isBulkModalOpen = false">

    {{-- Backdrop & Container Modal --}}
    <div x-show="isBulkModalOpen"
         x-cloak
         class="fixed inset-0 z-50 bg-[#0F172A]/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
         style="display: none;"
         @click.self="isBulkModalOpen = false">

        <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#CBD5E1] max-h-[94vh] flex flex-col overflow-hidden">
            
            {{-- Header Modal --}}
            <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                    <div>
                        <h3 class="text-[16px] font-bold text-[#0F172A]">Input Form Laporan Aktivitas Engineer</h3>
                        <p class="text-[11px] text-[#64748B]">Isi formulir laporan kerja resmi di bawah ini sesuai acuan template PT IP Network Solusindo.</p>
                    </div>
                </div>
                <button type="button" @click="isBulkModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Submit --}}
            <form action="{{ route('engineer.activity_log.store') }}" method="POST" @submit="submitBulkForm($event)" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf

                {{-- Bar Pilihan Proyek & Topik --}}
                <div class="p-4 sm:p-5 bg-[#F8FAFC] border-b border-[#CBD5E1] shrink-0">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                PILIH PROSPEK / PROYEK TUJUAN <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <select name="project_id" x-model="bulkProjectId" @change="onProjectChange()" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-xs">
                                <option value="">-- Pilih Proyek Terkait (Data otomatis terisi ke formulir) --</option>
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
                                JUDUL / TOPIK LAPORAN (OPSIONAL)
                            </label>
                            <input type="text" name="activity_title" x-model="activityTitle"
                                   placeholder="Contoh: Troubleshooting Jaringan..."
                                   class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-xs">
                        </div>
                    </div>
                </div>

                {{-- Hidden Inputs untuk Menyimpan ke EngineerActivityLog & ActivityDocumentSignature --}}
                <input type="hidden" name="report_data" x-ref="reportDataInput" :value="JSON.stringify(reportFormData)">
                <template x-for="(act, idx) in reportFormData.rincian_aktivitas" :key="idx">
                    <div>
                        <input type="hidden" :name="'activities[' + idx + '][subject]'" :value="act.aktivitas">
                        <input type="hidden" :name="'activities[' + idx + '][activity_date]'" :value="reportFormData.identitas.hari_tanggal_raw || '{{ date('Y-m-d') }}'">
                        <input type="hidden" :name="'activities[' + idx + '][time_str]'" :value="act.waktu">
                        <input type="hidden" :name="'activities[' + idx + '][client_pic]'" :value="reportFormData.identitas.pic_customer">
                        <input type="hidden" :name="'activities[' + idx + '][ipnet_pic]'" :value="reportFormData.identitas.nama_engineer">
                        <input type="hidden" :name="'activities[' + idx + '][notes]'" :value="act.tindak_lanjut !== '-' ? act.tindak_lanjut : (act.hasil || '')">
                        <input type="hidden" :name="'activities[' + idx + '][activity_type]'" :value="reportFormData.identitas.kategori_pekerjaan === 'maintenance' ? 'Maintenance' : 'Troubleshooting'">
                    </div>
                </template>

                {{-- Scrollable Form Dokumen Resmi (Paper Format) --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#F1F5F9]/70 space-y-5">
                    
                    <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
                        {{-- Kop Surat Dokumen --}}
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

                        {{-- ═══ A. IDENTITAS PEKERJAAN ═══ --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS PEKERJAAN</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Hari/Tanggal</td>
                                            <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                                <input type="date" x-model="reportFormData.identitas.hari_tanggal_raw" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-medium text-xs cursor-pointer">
                                            </td>
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Laporan</td>
                                            <td class="w-3/10 py-1 px-2">
                                                <input type="text" x-model="reportFormData.identitas.no_laporan" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#8F0A0D]">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Project</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.identitas.nama_project" placeholder="Pilih proyek di atas..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
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
                                            <td class="w-3/5 p-1"><textarea rows="1" x-model="reportFormData.ruang_lingkup.target_hari_ini" placeholder="Rincian target hari ini..." class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea></td>
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

                        {{-- ═══ D. RINCIAN AKTIVITAS ENGINEER (AKTIVITAS UTAMA) ═══ --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN AKTIVITAS ENGINEER <span class="text-[#8F0A0D]">*</span></h3>
                                    <p class="text-[10.5px] text-gray-500">Isi rincian aktivitas teknis di bawah ini. Setidaknya satu baris aktivitas wajib diisi.</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" @click="addActRow()" class="px-3 py-1 bg-red-50 hover:bg-red-100 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer">
                                        <span>+ Tambah Baris</span>
                                    </button>
                                    <button type="button" @click="removeActRow()" x-show="reportFormData.rincian_aktivitas.length > 1" class="text-xs font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
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
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Aktivitas / Tindakan <span class="text-red-500">*</span></th>
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
                                                <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="actIdx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="time" x-model="act.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <textarea rows="2" x-model="act.aktivitas" placeholder="Aktivitas / rincian tindakan..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] font-semibold resize-none focus:bg-white"></textarea>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.perangkat" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.hasil" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <select x-model="act.status" class="w-full p-1 text-center font-bold text-xs border-0 bg-transparent text-[#8F0A0D] cursor-pointer focus:ring-0">
                                                        <option value="">- Status -</option>
                                                        <option value="Selesai">Selesai</option>
                                                        <option value="Dalam Proses">Dalam Proses</option>
                                                        <option value="Tertunda">Tertunda</option>
                                                        <option value="Kendala">Kendala</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <input type="text" x-model="act.kendala" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" x-model="act.tindak_lanjut" class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
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
                                            <th class="py-1 px-2 w-20 text-center border-r border-[#1E293B]">Waktu</th>
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
                                                <td class="p-1 border-r border-[#1E293B] text-center">
                                                    <input type="time" x-model="inc.waktu" class="w-full p-1 text-center text-xs border-0 bg-transparent cursor-pointer">
                                                </td>
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
                                Lampirkan foto yang menunjukkan kondisi aktual. Minimal: Before, Progress (bila ada), dan After.
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
                                                <template x-if="reportFormData.foto_dokumentasi.before.url">
                                                    <div class="relative group inline-block">
                                                        <img :src="reportFormData.foto_dokumentasi.before.url" class="h-20 w-auto rounded object-contain border">
                                                        <button type="button" @click="removePhoto('before')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold">✕</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.before.url">
                                                    <div class="py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                        <span class="text-[10px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                        <label class="px-2 py-0.5 bg-white border border-gray-300 rounded text-[10px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block">
                                                            <span>Pilih Foto</span>
                                                            <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'before')">
                                                        </label>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="p-2">
                                                <textarea rows="2" x-model="reportFormData.foto_dokumentasi.before.caption" placeholder="Keterangan..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
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
                                                <template x-if="reportFormData.foto_dokumentasi.progress.url">
                                                    <div class="relative group inline-block">
                                                        <img :src="reportFormData.foto_dokumentasi.progress.url" class="h-20 w-auto rounded object-contain border">
                                                        <button type="button" @click="removePhoto('progress')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold">✕</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.progress.url">
                                                    <div class="py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                        <span class="text-[10px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                        <label class="px-2 py-0.5 bg-white border border-gray-300 rounded text-[10px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block">
                                                            <span>Pilih Foto</span>
                                                            <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'progress')">
                                                        </label>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="p-2">
                                                <textarea rows="2" x-model="reportFormData.foto_dokumentasi.progress.caption" placeholder="Keterangan..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
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
                                                <template x-if="reportFormData.foto_dokumentasi.after.url">
                                                    <div class="relative group inline-block">
                                                        <img :src="reportFormData.foto_dokumentasi.after.url" class="h-20 w-auto rounded object-contain border">
                                                        <button type="button" @click="removePhoto('after')" class="absolute top-0 right-0 bg-red-600 text-white rounded-full w-5 h-5 text-xs font-bold">✕</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.after.url">
                                                    <div class="py-2.5 border-2 border-dashed border-gray-300 rounded text-center bg-gray-50">
                                                        <span class="text-[10px] text-gray-500 font-bold block mb-1">[TEMPEL FOTO DI SINI]</span>
                                                        <label class="px-2 py-0.5 bg-white border border-gray-300 rounded text-[10px] font-bold text-gray-700 cursor-pointer hover:bg-gray-100 inline-block">
                                                            <span>Pilih Foto</span>
                                                            <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'after')">
                                                        </label>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="p-2">
                                                <textarea rows="2" x-model="reportFormData.foto_dokumentasi.after.caption" placeholder="Keterangan..." class="w-full p-1 text-xs border rounded bg-[#F8FAFC] resize-none"></textarea>
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
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.identitas.nama_engineer" placeholder="Nama Engineer" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-2.5 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[60px]"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Project Manager / Team Leader</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.identitas.nama_leader" placeholder="Nama PM / Team Leader" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-2.5 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[60px]"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Site Representative</td>
                                            <td class="py-1 px-2 border-r border-[#1E293B]">
                                                <input type="text" x-model="reportFormData.identitas.pic_customer" placeholder="Ketik nama PIC Klien / Customer..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                            </td>
                                            <td class="py-2.5 px-3 text-center border-r border-[#1E293B]">{{ date('d/m/Y') }}</td>
                                            <td class="py-6 px-3 text-center min-h-[60px]"></td>
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
                </div>

                {{-- Sticky Footer Simpan --}}
                <div class="px-6 py-4 bg-white border-t border-[#CBD5E1] flex items-center justify-between shrink-0 shadow-sm">
                    <button type="button" @click="isBulkModalOpen = false"
                            class="px-5 py-2.5 bg-white text-[#334155] border border-[#CBD5E1] hover:bg-gray-50 font-bold text-xs rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-6 py-2.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Laporan Aktivitas</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
