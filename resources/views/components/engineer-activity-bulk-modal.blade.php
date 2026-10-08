{{-- MODAL INPUT FORM LAPORAN AKTIVITAS ENGINEER (PROJECT / MANAGED SERVICE / HELP DESK) --}}
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
    selectedCategory: 'project', // 'project' | 'managed_service' | 'help_desk'
    projectsMap: {{ Js::from($projectsLookup) }},
    reportFormData: {
        category: 'project',

        // ════════ 1. PROJECT DATA ════════
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
            kategori_implementasi: true,
            kategori_managed_service: false,
            kategori_pekerjaan: 'implementasi',
            jabatan: '{{ auth()->user()?->position ?: (auth()->user()?->getRoleNames()->first() ?: 'Field Engineer') }}',
            jam_mulai: '',
            jam_selesai: '',
        },
        manpower: [
            {
                nama: '{{ auth()->user()?->name ?? 'Engineer' }}',
                unit_kerja: 'Technical Support',
                jabatan: '{{ auth()->user()?->position ?: 'Field Engineer' }}',
                keterangan: 'PIC'
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
            { no: 1, waktu: '09:00', aktivitas: '', perangkat: '', hasil: '', status: 'Selesai', kendala: '', tindak_lanjut: '' }
        ],
        materials: [
            { nama: '', spesifikasi: '', qty: 1, satuan: 'Pcs', kondisi: 'Baik', keterangan: '' }
        ],
        test_results: [
            { parameter: '', sebelum: 0, sesudah: 0, satuan: 'ms', metode: '', keterangan: '' }
        ],
        incidents: [
            { waktu: '', kendala: '', dampak: '', tindakan: '', status: 'Closed' }
        ],
        hasil_akhir: {
            status_pekerjaan: 'selesai',
            progress_percent: 100,
            kondisi_sistem: 'Normal',
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
        },

        // ════════ 2. MANAGED SERVICE DATA ════════
        ms_identitas: {
            tanggal: '{{ date('Y-m-d') }}',
            no_report: 'IPNET-MS-{{ date('Ym') }}-DRAFT',
            customer: '',
            no_contract: '',
            site_lokasi: '',
            ticket_incident_no: '',
            engineer_pic: '{{ auth()->user()?->name ?? 'Engineer' }}',
            shift: 'Pagi / Regular',
            jenis_aktivitas: {
                monitoring: true,
                pm: false,
                cm: false,
                incident: false,
                request: false,
                visit: false,
            },
            service_device: '',
            jam_mulai: '08:00',
            jam_selesai: '17:00',
        },
        ms_sla: [
            { parameter: 'Ticket Received', waktu: '08:00', target_sla: '< 15 Menit', aktual: '5 Menit', status: 'Met', keterangan: 'Normal' },
            { parameter: 'Engineer Response', waktu: '08:05', target_sla: '< 30 Menit', aktual: '10 Menit', status: 'Met', keterangan: 'Respon cepat' },
            { parameter: 'Service Restore', waktu: '09:30', target_sla: '< 4 Jam', aktual: '1.5 Jam', status: 'Met', keterangan: 'Layanan pulih' },
            { parameter: 'Resolution / Close', waktu: '10:00', target_sla: '< 8 Jam', aktual: '2 Jam', status: 'Met', keterangan: 'Resolved' }
        ],
        ms_kondisi_perangkat: [
            { no: 1, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' },
            { no: 2, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' },
            { no: 3, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' }
        ],
        ms_aktivitas: [
            { no: 1, waktu: '08:30', aktivitas: '', ticket_alarm: '', hasil: '', status: 'Done', kendala: '', follow_up: '' },
            { no: 2, waktu: '09:30', aktivitas: '', ticket_alarm: '', hasil: '', status: 'Done', kendala: '', follow_up: '' }
        ],
        ms_incident_escalation: [
            { no: 1, incident: '', impact: '', root_cause: '', corrective_action: '', escalation: '', status: 'Closed' }
        ],
        ms_pm_checklist: [
            { no: 1, item_pemeriksaan: '', kondisi: 'Baik', hasil: 'Normal', temuan: '', tindakan: '', status: 'OK' }
        ],
        ms_materials: [
            { no: 1, item: '', type: '', qty: '', used_replaced: '', old_new: '', keterangan: '' }
        ],
        ms_evidence: [
            { no: 1, evidence: '', waktu: '', foto_url: '', keterangan: '' }
        ],
        ms_closure: {
            service_status: {
                resolved: true,
                monitoring: false,
                escalated: false,
                closed: false
            },
            sla: {
                met: true,
                breach: false
            },
            customer_confirmation: 'Layanan berjalan normal',
            outstanding: '-'
        },
        ms_verifikasi: {
            engineer_name: '{{ auth()->user()?->name ?? 'Engineer' }}',
            lead_name: 'Nugraha Pratama',
            customer_name: ''
        },

        // ════════ 3. HELP DESK DATA ════════
        hd_identitas: {
            tanggal: '{{ date('Y-m-d') }}',
            shift: {
                pagi: true,
                siang: false,
                malam: false
            },
            nama_engineer: '{{ auth()->user()?->name ?? 'Engineer' }}',
            team_leader: 'Nugraha Pratama',
            area_site: '',
            customer_service: '',
            jam_shift: '08:00 - 16:00 WIB',
            jumlah_engineer: 1
        },
        hd_kondisi_awal: [
            { no: 1, item_service: '', kondisi_awal: 'Normal', alarm_issue: 'Clear', status: 'OK', keterangan: '' }
        ],
        hd_aktivitas: [
            { no: 1, waktu: '08:30', aktivitas: '', ticket_wo: '', lokasi_device: '', hasil: '', status: 'Done' }
        ],
        hd_ticket_incident: [
            { no: 1, ticket: '', jenis: '', priority: 'Medium', start: '', restore: '', close_status: 'Closed', keterangan: '' }
        ],
        hd_monitoring_status: [
            { no: 1, service_device: '', status: 'Up', alarm: 'None', performance: '', action: '', keterangan: '' }
        ],
        hd_pekerjaan_field: [
            { no: 1, lokasi: '', pekerjaan: '', engineer: '{{ auth()->user()?->name ?? 'Engineer' }}', hasil: '', foto_evidence: '', status: 'Done' }
        ],
        hd_kendala_escalation: [
            { no: 1, kendala_incident: '', dampak: '', tindakan: '', escalated_to: '', status: 'Normal', next_action: '' }
        ],
        hd_handover: [
            { no: 1, outstanding_issue: '', kondisi_terakhir: '', tindakan_berikutnya: '', pic: '', due_time: '', catatan: '' }
        ],
        hd_rekap_shift: {
            total_ticket_diterima: 0,
            total_ticket_closed: 0,
            total_incident: 0,
            outstanding: 0,
            service_kritis: '0',
            handover_diperlukan: false
        },
        hd_verifikasi: {
            engineer_name: '{{ auth()->user()?->name ?? 'Engineer' }}',
            team_leader_name: 'Nugraha Pratama',
            next_engineer_name: ''
        }
    },

    setDocCategory(cat) {
        this.selectedCategory = cat;
        this.reportFormData.category = cat;
        // Sync project details across categories
        this.syncProjectDataToForms();
    },

    syncProjectDataToForms() {
        if (!this.bulkProjectId) return;
        const p = this.projectsMap[this.bulkProjectId];
        if (!p) return;

        // Project
        this.reportFormData.identitas.nama_project = p.name || '';
        this.reportFormData.identitas.customer     = p.client || '';
        this.reportFormData.identitas.lokasi_site  = p.location || '';
        this.reportFormData.identitas.no_so_spk    = p.po_number || '';
        this.reportFormData.identitas.pic_customer = p.customer_pic_technical || '';
        if (p.progress) this.reportFormData.hasil_akhir.progress_percent = p.progress;

        // Managed Service
        this.reportFormData.ms_identitas.service_device = p.name || '';
        this.reportFormData.ms_identitas.customer       = p.client || '';
        this.reportFormData.ms_identitas.site_lokasi    = p.location || '';
        this.reportFormData.ms_identitas.no_contract    = p.po_number || '';

        // Help Desk
        this.reportFormData.hd_identitas.customer_service = (p.name || '') + (p.client ? ' (' + p.client + ')' : '');
        this.reportFormData.hd_identitas.area_site        = p.location || '';
    },

    onProjectChange() {
        if (!this.bulkProjectId) return;
        const p = this.projectsMap[this.bulkProjectId];
        if (p) {
            const nameLower = (p.name || '').toLowerCase();
            if (nameLower.includes('managed') || nameLower.includes('maintenance')) {
                this.setDocCategory('managed_service');
            } else if (nameLower.includes('helpdesk') || nameLower.includes('help desk')) {
                this.setDocCategory('help_desk');
            }
            this.syncProjectDataToForms();
        }
    },

    // ── Row Helper: Project ──
    addActRow() {
        const nextNo = this.reportFormData.rincian_aktivitas.length + 1;
        this.reportFormData.rincian_aktivitas.push({ no: nextNo, waktu: '', aktivitas: '', perangkat: '', hasil: '', status: 'Selesai', kendala: '', tindak_lanjut: '' });
    },
    removeActRow(idx) {
        if (this.reportFormData.rincian_aktivitas.length > 1) {
            this.reportFormData.rincian_aktivitas.splice(idx, 1);
            this.reportFormData.rincian_aktivitas.forEach((r, i) => r.no = i + 1);
        }
    },
    addManpowerRow() {
        this.reportFormData.manpower.push({ nama: '', unit_kerja: '', jabatan: '', keterangan: '' });
        this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
    },
    removeManpowerRow(idx) {
        if (this.reportFormData.manpower.length > 1) {
            this.reportFormData.manpower.splice(idx, 1);
            this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
        }
    },
    addMaterialRow() {
        this.reportFormData.materials.push({ nama: '', spesifikasi: '', qty: 1, satuan: 'Pcs', kondisi: 'Baik', keterangan: '' });
    },
    removeMaterialRow(idx) {
        if (this.reportFormData.materials.length > 1) this.reportFormData.materials.splice(idx, 1);
    },
    addTestResultRow() {
        this.reportFormData.test_results.push({ parameter: '', sebelum: 0, sesudah: 0, satuan: 'ms', metode: '', keterangan: '' });
    },
    removeTestResultRow(idx) {
        if (this.reportFormData.test_results.length > 1) this.reportFormData.test_results.splice(idx, 1);
    },
    addIncidentRow() {
        this.reportFormData.incidents.push({ waktu: '', kendala: '', dampak: '', tindakan: '', status: 'Closed' });
    },
    removeIncidentRow(idx) {
        if (this.reportFormData.incidents.length > 1) this.reportFormData.incidents.splice(idx, 1);
    },

    // ── Row Helper: Managed Service ──
    addMsKondisiRow() {
        const n = this.reportFormData.ms_kondisi_perangkat.length + 1;
        this.reportFormData.ms_kondisi_perangkat.push({ no: n, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' });
    },
    removeMsKondisiRow(idx) {
        if (this.reportFormData.ms_kondisi_perangkat.length > 1) {
            this.reportFormData.ms_kondisi_perangkat.splice(idx, 1);
            this.reportFormData.ms_kondisi_perangkat.forEach((r, i) => r.no = i + 1);
        }
    },
    addMsActRow() {
        const n = this.reportFormData.ms_aktivitas.length + 1;
        this.reportFormData.ms_aktivitas.push({ no: n, waktu: '', aktivitas: '', ticket_alarm: '', hasil: '', status: 'Done', kendala: '', follow_up: '' });
    },
    removeMsActRow(idx) {
        if (this.reportFormData.ms_aktivitas.length > 1) {
            this.reportFormData.ms_aktivitas.splice(idx, 1);
            this.reportFormData.ms_aktivitas.forEach((r, i) => r.no = i + 1);
        }
    },
    addMsIncidentRow() {
        const n = this.reportFormData.ms_incident_escalation.length + 1;
        this.reportFormData.ms_incident_escalation.push({ no: n, incident: '', impact: '', root_cause: '', corrective_action: '', escalation: '', status: 'Closed' });
    },
    removeMsIncidentRow(idx) {
        if (this.reportFormData.ms_incident_escalation.length > 1) {
            this.reportFormData.ms_incident_escalation.splice(idx, 1);
            this.reportFormData.ms_incident_escalation.forEach((r, i) => r.no = i + 1);
        }
    },
    addMsPmRow() {
        const n = this.reportFormData.ms_pm_checklist.length + 1;
        this.reportFormData.ms_pm_checklist.push({ no: n, item_pemeriksaan: '', kondisi: 'Baik', hasil: '', temuan: '', tindakan: '', status: 'OK' });
    },
    removeMsPmRow(idx) {
        if (this.reportFormData.ms_pm_checklist.length > 1) {
            this.reportFormData.ms_pm_checklist.splice(idx, 1);
            this.reportFormData.ms_pm_checklist.forEach((r, i) => r.no = i + 1);
        }
    },
    addMsMaterialRow() {
        const n = this.reportFormData.ms_materials.length + 1;
        this.reportFormData.ms_materials.push({ no: n, item: '', type: '', qty: '', used_replaced: '', old_new: '', keterangan: '' });
    },
    removeMsMaterialRow(idx) {
        if (this.reportFormData.ms_materials.length > 1) {
            this.reportFormData.ms_materials.splice(idx, 1);
            this.reportFormData.ms_materials.forEach((r, i) => r.no = i + 1);
        }
    },
    addMsEvidenceRow() {
        const n = this.reportFormData.ms_evidence.length + 1;
        this.reportFormData.ms_evidence.push({ no: n, evidence: '', waktu: '', foto_url: '', keterangan: '' });
    },
    removeMsEvidenceRow(idx) {
        if (this.reportFormData.ms_evidence.length > 1) {
            this.reportFormData.ms_evidence.splice(idx, 1);
            this.reportFormData.ms_evidence.forEach((r, i) => r.no = i + 1);
        }
    },

    // ── Row Helper: Help Desk ──
    addHdKondisiRow() {
        const n = this.reportFormData.hd_kondisi_awal.length + 1;
        this.reportFormData.hd_kondisi_awal.push({ no: n, item_service: '', kondisi_awal: 'Normal', alarm_issue: 'Clear', status: 'OK', keterangan: '' });
    },
    removeHdKondisiRow(idx) {
        if (this.reportFormData.hd_kondisi_awal.length > 1) {
            this.reportFormData.hd_kondisi_awal.splice(idx, 1);
            this.reportFormData.hd_kondisi_awal.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdActRow() {
        const n = this.reportFormData.hd_aktivitas.length + 1;
        this.reportFormData.hd_aktivitas.push({ no: n, waktu: '', aktivitas: '', ticket_wo: '', lokasi_device: '', hasil: '', status: 'Done' });
    },
    removeHdActRow(idx) {
        if (this.reportFormData.hd_aktivitas.length > 1) {
            this.reportFormData.hd_aktivitas.splice(idx, 1);
            this.reportFormData.hd_aktivitas.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdTicketRow() {
        const n = this.reportFormData.hd_ticket_incident.length + 1;
        this.reportFormData.hd_ticket_incident.push({ no: n, ticket: '', jenis: '', priority: 'Medium', start: '', restore: '', close_status: 'Closed', keterangan: '' });
    },
    removeHdTicketRow(idx) {
        if (this.reportFormData.hd_ticket_incident.length > 1) {
            this.reportFormData.hd_ticket_incident.splice(idx, 1);
            this.reportFormData.hd_ticket_incident.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdMonitoringRow() {
        const n = this.reportFormData.hd_monitoring_status.length + 1;
        this.reportFormData.hd_monitoring_status.push({ no: n, service_device: '', status: 'Up', alarm: 'None', performance: '', action: '', keterangan: '' });
    },
    removeHdMonitoringRow(idx) {
        if (this.reportFormData.hd_monitoring_status.length > 1) {
            this.reportFormData.hd_monitoring_status.splice(idx, 1);
            this.reportFormData.hd_monitoring_status.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdFieldRow() {
        const n = this.reportFormData.hd_pekerjaan_field.length + 1;
        this.reportFormData.hd_pekerjaan_field.push({ no: n, lokasi: '', pekerjaan: '', engineer: '{{ auth()->user()?->name ?? 'Engineer' }}', hasil: '', foto_evidence: '', status: 'Done' });
    },
    removeHdFieldRow(idx) {
        if (this.reportFormData.hd_pekerjaan_field.length > 1) {
            this.reportFormData.hd_pekerjaan_field.splice(idx, 1);
            this.reportFormData.hd_pekerjaan_field.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdKendalaRow() {
        const n = this.reportFormData.hd_kendala_escalation.length + 1;
        this.reportFormData.hd_kendala_escalation.push({ no: n, kendala_incident: '', dampak: '', tindakan: '', escalated_to: '', status: 'Normal', next_action: '' });
    },
    removeHdKendalaRow(idx) {
        if (this.reportFormData.hd_kendala_escalation.length > 1) {
            this.reportFormData.hd_kendala_escalation.splice(idx, 1);
            this.reportFormData.hd_kendala_escalation.forEach((r, i) => r.no = i + 1);
        }
    },
    addHdHandoverRow() {
        const n = this.reportFormData.hd_handover.length + 1;
        this.reportFormData.hd_handover.push({ no: n, outstanding_issue: '', kondisi_terakhir: '', tindakan_berikutnya: '', pic: '', due_time: '', catatan: '' });
    },
    removeHdHandoverRow(idx) {
        if (this.reportFormData.hd_handover.length > 1) {
            this.reportFormData.hd_handover.splice(idx, 1);
            this.reportFormData.hd_handover.forEach((r, i) => r.no = i + 1);
        }
    },

    // ── Image Handling ──
    compressImage(file, maxDimension = 1000, quality = 0.72) {
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    let width = img.width, height = img.height;
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
            if (compressed) this.reportFormData.foto_dokumentasi[stage].url = compressed;
        } catch (err) {
            const reader = new FileReader();
            reader.onload = (ev) => { this.reportFormData.foto_dokumentasi[stage].url = ev.target.result; };
            reader.readAsDataURL(file);
        }
    },

    async handleMsEvidenceUpload(e, idx) {
        const file = e.target.files[0];
        if (!file) return;
        try {
            const compressed = await this.compressImage(file, 1000, 0.72);
            if (compressed) this.reportFormData.ms_evidence[idx].foto_url = compressed;
        } catch (err) {
            const reader = new FileReader();
            reader.onload = (ev) => { this.reportFormData.ms_evidence[idx].foto_url = ev.target.result; };
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
        try {
            this.reportFormData.category = this.selectedCategory;
            const rawPayload = typeof Alpine !== 'undefined' && Alpine.raw ? Alpine.raw(this.reportFormData) : this.reportFormData;
            const serialized = JSON.stringify(rawPayload);
            if (this.$refs.reportDataInput) {
                this.$refs.reportDataInput.value = serialized;
            }
        } catch (err) {
            console.error('Error stringifying reportFormData:', err);
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
                        <p class="text-[11px] text-[#64748B]">Isi formulir laporan kerja resmi di bawah ini sesuai acuan template standar PT IP Network Solusindo.</p>
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

                {{-- Bar Pilihan Proyek & Topik & Segmented Category Switcher --}}
                <div class="p-4 sm:p-5 bg-[#F8FAFC] border-b border-[#CBD5E1] shrink-0 space-y-3.5">
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
                                   placeholder="Contoh: Operasional & Troubleshooting..."
                                   class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-xs">
                        </div>
                    </div>

                    {{-- ══ KATEGORI DOKUMEN TABS / SWITCHER ══ --}}
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            KATEGORI DOKUMEN LAPORAN AKTIVITAS <span class="text-[#8F0A0D]">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            {{-- Tab 1: PROJECT --}}
                            <button type="button" @click="setDocCategory('project')"
                                    :class="selectedCategory === 'project' ? 'bg-[#8F0A0D] text-white border-[#8F0A0D] shadow-sm' : 'bg-white text-[#334155] border-[#CBD5E1] hover:border-[#8F0A0D]/40 hover:bg-[#F1F5F9]'"
                                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer font-bold">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     :class="selectedCategory === 'project' ? 'bg-white/20 text-white' : 'bg-red-50 text-[#8F0A0D]'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[12.5px] leading-tight font-black uppercase">1. Project</div>
                                    <div class="text-[10.5px] font-normal truncate opacity-85">Implementasi, Ruang Lingkup &amp; Test</div>
                                </div>
                            </button>

                            {{-- Tab 2: MANAGED SERVICE --}}
                            <button type="button" @click="setDocCategory('managed_service')"
                                    :class="selectedCategory === 'managed_service' ? 'bg-[#8F0A0D] text-white border-[#8F0A0D] shadow-sm' : 'bg-white text-[#334155] border-[#CBD5E1] hover:border-[#8F0A0D]/40 hover:bg-[#F1F5F9]'"
                                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer font-bold">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     :class="selectedCategory === 'managed_service' ? 'bg-white/20 text-white' : 'bg-red-50 text-[#8F0A0D]'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[12.5px] leading-tight font-black uppercase">2. Managed Service</div>
                                    <div class="text-[10.5px] font-normal truncate opacity-85">SLA Tracking, PM/CM &amp; Closure</div>
                                </div>
                            </button>

                            {{-- Tab 3: HELP DESK --}}
                            <button type="button" @click="setDocCategory('help_desk')"
                                    :class="selectedCategory === 'help_desk' ? 'bg-[#8F0A0D] text-white border-[#8F0A0D] shadow-sm' : 'bg-white text-[#334155] border-[#CBD5E1] hover:border-[#8F0A0D]/40 hover:bg-[#F1F5F9]'"
                                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl border text-left transition-all cursor-pointer font-bold">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                     :class="selectedCategory === 'help_desk' ? 'bg-white/20 text-white' : 'bg-red-50 text-[#8F0A0D]'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-[12.5px] leading-tight font-black uppercase">3. Help Desk</div>
                                    <div class="text-[10.5px] font-normal truncate opacity-85">Identitas Shift, Handover &amp; Rekap</div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Hidden Inputs untuk Menyimpan ke EngineerActivityLog & ActivityDocumentSignature --}}
                <input type="hidden" name="report_data" x-ref="reportDataInput" :value="JSON.stringify(reportFormData)">
                <input type="hidden" name="doc_category" :value="selectedCategory">

                {{-- Hidden Activities Mapping Berdasarkan Kategori yang Aktif --}}
                <template x-if="selectedCategory === 'project'">
                    <div>
                        <template x-for="(act, idx) in reportFormData.rincian_aktivitas" :key="'p_'+idx">
                            <div>
                                <input type="hidden" :name="'activities[' + idx + '][subject]'" :value="act.aktivitas">
                                <input type="hidden" :name="'activities[' + idx + '][activity_date]'" :value="reportFormData.identitas.hari_tanggal_raw || '{{ date('Y-m-d') }}'">
                                <input type="hidden" :name="'activities[' + idx + '][time_str]'" :value="act.waktu">
                                <input type="hidden" :name="'activities[' + idx + '][client_pic]'" :value="reportFormData.identitas.pic_customer">
                                <input type="hidden" :name="'activities[' + idx + '][ipnet_pic]'" :value="reportFormData.identitas.nama_engineer">
                                <input type="hidden" :name="'activities[' + idx + '][notes]'" :value="act.tindak_lanjut !== '-' ? act.tindak_lanjut : (act.hasil || '')">
                                <input type="hidden" :name="'activities[' + idx + '][activity_type]'" :value="reportFormData.identitas.kategori_managed_service ? 'Maintenance' : 'Troubleshooting'">
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedCategory === 'managed_service'">
                    <div>
                        <template x-for="(act, idx) in reportFormData.ms_aktivitas" :key="'ms_'+idx">
                            <div>
                                <input type="hidden" :name="'activities[' + idx + '][subject]'" :value="act.aktivitas">
                                <input type="hidden" :name="'activities[' + idx + '][activity_date]'" :value="reportFormData.ms_identitas.tanggal || '{{ date('Y-m-d') }}'">
                                <input type="hidden" :name="'activities[' + idx + '][time_str]'" :value="act.waktu">
                                <input type="hidden" :name="'activities[' + idx + '][client_pic]'" :value="reportFormData.ms_identitas.customer">
                                <input type="hidden" :name="'activities[' + idx + '][ipnet_pic]'" :value="reportFormData.ms_identitas.engineer_pic">
                                <input type="hidden" :name="'activities[' + idx + '][notes]'" :value="(act.ticket_alarm ? 'Ticket/Alarm: ' + act.ticket_alarm + ' | ' : '') + (act.follow_up || act.hasil || '')">
                                <input type="hidden" :name="'activities[' + idx + '][activity_type]'" value="Maintenance">
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="selectedCategory === 'help_desk'">
                    <div>
                        <template x-for="(act, idx) in reportFormData.hd_aktivitas" :key="'hd_'+idx">
                            <div>
                                <input type="hidden" :name="'activities[' + idx + '][subject]'" :value="act.aktivitas">
                                <input type="hidden" :name="'activities[' + idx + '][activity_date]'" :value="reportFormData.hd_identitas.tanggal || '{{ date('Y-m-d') }}'">
                                <input type="hidden" :name="'activities[' + idx + '][time_str]'" :value="act.waktu">
                                <input type="hidden" :name="'activities[' + idx + '][client_pic]'" :value="reportFormData.hd_identitas.customer_service">
                                <input type="hidden" :name="'activities[' + idx + '][ipnet_pic]'" :value="reportFormData.hd_identitas.nama_engineer">
                                <input type="hidden" :name="'activities[' + idx + '][notes]'" :value="(act.ticket_wo ? 'Ticket/WO: ' + act.ticket_wo + ' | ' : '') + (act.hasil || '')">
                                <input type="hidden" :name="'activities[' + idx + '][activity_type]'" value="Field Support">
                            </div>
                        </template>
                    </div>
                </template>

                {{-- Scrollable Form Dokumen Resmi (Paper Format) --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#F1F5F9]/70 space-y-5">
                    
                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    {{-- ══════════════ 1. TEMPLATE FORM: PROJECT ══════════════════════════ --}}
                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    <div x-show="selectedCategory === 'project'"
                         class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
                        {{-- Kop Surat Dokumen Project --}}
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

                        {{-- A. IDENTITAS PEKERJAAN --}}
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
                                            <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.scope_pekerjaan" placeholder="Ruang lingkup pekerjaan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Perangkat / Sistem</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.ruang_lingkup.perangkat_sistem" placeholder="Perangkat terkait..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
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
                                                <td class="p-1 text-center">
                                                    <button type="button" @click="removeActRow(idx)" class="text-red-500 hover:text-red-700 font-bold">✕</button>
                                                </td>
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
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.nama" placeholder="Nama item..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
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

                        {{-- F. HASIL UJI / TESTING --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. HASIL UJI / PENGUKURAN (TESTING &amp; COMMISSIONING)</h3>
                                <button type="button" @click="addTestResultRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Parameter Uji</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Parameter Uji</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Sebelum</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Sesudah</th>
                                            <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Satuan</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Metode Uji</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(tr, idx) in reportFormData.test_results" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.parameter" placeholder="Parameter..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.sebelum" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.sesudah" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.satuan" placeholder="ms / dB" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.metode" placeholder="Ping / OTDR" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="tr.keterangan" placeholder="Normal" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeTestResultRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- G. KENDALA / MASALAH LAPANGAN --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. KENDALA / MASALAH LAPANGAN</h3>
                                <button type="button" @click="addIncidentRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Kendala</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Waktu</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kendala / Masalah</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Dampak</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan Sementara</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(inc, idx) in reportFormData.incidents" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="inc.waktu" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.kendala" placeholder="Masalah..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.dampak" placeholder="Dampak..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.tindakan" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <select x-model="inc.status" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                        <option value="Closed">Closed</option>
                                                        <option value="Open">Open</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 text-center"><button type="button" @click="removeIncidentRow(idx)" class="text-red-500 font-bold">✕</button></td>
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
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Persentase Progress</td>
                                            <td class="p-1"><input type="number" min="0" max="100" x-model="reportFormData.hasil_akhir.progress_percent" class="w-24 p-1 text-xs border font-bold text-center"> %</td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kondisi Sistem</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.kondisi_sistem" placeholder="Normal & stabil" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding / Pekerjaan Lanjutan</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.outstanding" placeholder="Tidak ada / monitoring" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Rekomendasi Engineer</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.rekomendasi" placeholder="Rekomendasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Eskalasi ke PIC / Tim Terkait</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.hasil_akhir.eskalasi_pic" placeholder="Eskalasi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- I. DOKUMENTASI FOTO --}}
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
                                            {{-- Before --}}
                                            <td class="p-2.5 border-r border-[#1E293B] text-center align-top">
                                                <template x-if="reportFormData.foto_dokumentasi.before.url">
                                                    <div class="space-y-1.5">
                                                        <img :src="reportFormData.foto_dokumentasi.before.url" class="max-h-28 mx-auto rounded border object-contain">
                                                        <button type="button" @click="removePhoto('before')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.before.url">
                                                    <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'before')">
                                                    </label>
                                                </template>
                                                <input type="text" x-model="reportFormData.foto_dokumentasi.before.area" placeholder="Area / Rak..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                            </td>
                                            {{-- Progress --}}
                                            <td class="p-2.5 border-r border-[#1E293B] text-center align-top">
                                                <template x-if="reportFormData.foto_dokumentasi.progress.url">
                                                    <div class="space-y-1.5">
                                                        <img :src="reportFormData.foto_dokumentasi.progress.url" class="max-h-28 mx-auto rounded border object-contain">
                                                        <button type="button" @click="removePhoto('progress')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.progress.url">
                                                    <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'progress')">
                                                    </label>
                                                </template>
                                                <input type="text" x-model="reportFormData.foto_dokumentasi.progress.area" placeholder="Area / Rak..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                            </td>
                                            {{-- After --}}
                                            <td class="p-2.5 text-center align-top">
                                                <template x-if="reportFormData.foto_dokumentasi.after.url">
                                                    <div class="space-y-1.5">
                                                        <img :src="reportFormData.foto_dokumentasi.after.url" class="max-h-28 mx-auto rounded border object-contain">
                                                        <button type="button" @click="removePhoto('after')" class="text-[10px] text-red-600 font-bold hover:underline">Hapus Foto</button>
                                                    </div>
                                                </template>
                                                <template x-if="!reportFormData.foto_dokumentasi.after.url">
                                                    <label class="cursor-pointer inline-flex items-center gap-1 px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition">
                                                        <span>Pilih Foto</span>
                                                        <input type="file" accept="image/*" class="hidden" @change="handlePhotoUpload($event, 'after')">
                                                    </label>
                                                </template>
                                                <input type="text" x-model="reportFormData.foto_dokumentasi.after.area" placeholder="Area / Rak..." class="mt-2 w-full p-1 text-xs border rounded bg-[#F8FAFC]">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- J. VERIFIKASI PEKERJAAN (PROJECT) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI &amp; PENGESAHAN</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-1.5 px-3 w-1/3 text-center">Status / Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Field Engineer / PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.identitas.nama_engineer" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-emerald-700">Tersedia via TTD Digital</td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader Engineering</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.identitas.nama_leader" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-slate-600">Verifikasi Lead</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Client PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.identitas.pic_customer" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center text-slate-500">Tanda Tangan Lapangan</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>


                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    {{-- ════════════ 2. TEMPLATE FORM: MANAGED SERVICE ════════════════════ --}}
                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    <div x-show="selectedCategory === 'managed_service'"
                         class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
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
                            <div class="text-right shrink-0">
                                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Managed Service Form
                                </span>
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
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.monitoring" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Monitoring</span>
                                                    </label>
                                                    <label class="flex items-center gap-1 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.pm" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>PM</span>
                                                    </label>
                                                    <label class="flex items-center gap-1 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.cm" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>CM</span>
                                                    </label>
                                                    <label class="flex items-center gap-1 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.incident" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Incident</span>
                                                    </label>
                                                    <label class="flex items-center gap-1 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.request" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Request</span>
                                                    </label>
                                                    <label class="flex items-center gap-1 cursor-pointer">
                                                        <input type="checkbox" x-model="reportFormData.ms_identitas.jenis_aktivitas.visit" class="rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
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

                        {{-- D. RINCIAN AKTIVITAS ENGINEER --}}
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

                        {{-- E. INCIDENT / ROOT CAUSE / ESCALATION --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. INCIDENT / ROOT CAUSE / ESCALATION</h3>
                                <button type="button" @click="addMsIncidentRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Incident</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Incident</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Impact</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Probable / Root Cause</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Corrective Action</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Escalation</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(inc, idx) in reportFormData.ms_incident_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.incident" placeholder="Incident..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.impact" placeholder="Dampak..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.root_cause" placeholder="Penyebab..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.corrective_action" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="inc.escalation" placeholder="Tier 2/Vendor" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <select x-model="inc.status" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                        <option value="Closed">Closed</option>
                                                        <option value="Open">Open</option>
                                                        <option value="Monitoring">Monitoring</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 text-center"><button type="button" @click="removeMsIncidentRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- F. PREVENTIVE MAINTENANCE / CHECKLIST --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. PREVENTIVE MAINTENANCE / CHECKLIST</h3>
                                <button type="button" @click="addMsPmRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Item PM</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Item Pemeriksaan</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Kondisi</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Hasil</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Temuan</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(pm, idx) in reportFormData.ms_pm_checklist" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.item_pemeriksaan" placeholder="Item..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.kondisi" placeholder="Baik" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.hasil" placeholder="Normal" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.temuan" placeholder="Temuan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="pm.tindakan" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <select x-model="pm.status" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                        <option value="OK">OK</option>
                                                        <option value="Pending">Pending</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 text-center"><button type="button" @click="removeMsPmRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- G. MATERIAL / SPARE PART (MS) --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. MATERIAL / SPARE PART</h3>
                                <button type="button" @click="addMsMaterialRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Material</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Item</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Type</th>
                                            <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Qty</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Used / Replaced</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Old/New</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(mat, idx) in reportFormData.ms_materials" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.item" placeholder="Nama item..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.type" placeholder="Type..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.qty" placeholder="1 Pcs" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.used_replaced" placeholder="Used" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.old_new" placeholder="New" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="mat.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeMsMaterialRow(idx)" class="text-red-500 font-bold">✕</button></td>
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
                            <p class="text-[10.5px] text-[#475569] italic">Evidence dapat berupa foto onsite, screenshot monitoring, log, hasil check, atau bukti pengujian.</p>
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
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.ms_closure.customer_confirmation" placeholder="Konfirmasi dari customer..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Outstanding</td>
                                            <td class="p-1"><input type="text" x-model="reportFormData.ms_closure.outstanding" placeholder="Outstanding issue..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- J. VERIFIKASI (MS) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-1.5 px-3 w-1/3 text-center">Tanggal / Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.ms_identitas.engineer_pic" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-emerald-700">Digital Signature Ready</td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader / Service Manager</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.ms_verifikasi.lead_name" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-slate-600">Verifikasi Lead</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Authorized Representative</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.ms_identitas.customer" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center text-slate-500">Tanda Tangan &amp; Stempel</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic">Catatan: laporan Managed Service harus dapat ditelusuri ke contract/service, ticket, SLA, evidence, dan status closure.</p>
                        </div>

                    </div>


                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    {{-- ══════════════ 3. TEMPLATE FORM: HELP DESK ════════════════════════ --}}
                    {{-- ══════════════════════════════════════════════════════════════════ --}}
                    <div x-show="selectedCategory === 'help_desk'"
                         class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                        
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
                            <div class="text-right shrink-0">
                                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Help Desk Form
                                </span>
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
                                                <input type="text" x-model="reportFormData.hd_identitas.area_site" placeholder="NOC / Lokasi Site..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Customer / Service</td>
                                            <td class="py-1 px-2">
                                                <input type="text" x-model="reportFormData.hd_identitas.customer_service" placeholder="Customer / Layanan..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
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
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.item_service" placeholder="Item / Service..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.kondisi_awal" placeholder="Normal / Up" class="w-full p-1 text-xs border-0 bg-transparent"></td>
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
                                <button type="button" @click="addHdActRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Baris Aktivitas</button>
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
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="act.lokasi_device" placeholder="Lokasi/Device..." class="w-full p-0.5 text-xs border-0 bg-transparent"></td>
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

                        {{-- D. REKAP TICKET / INCIDENT --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. REKAP TICKET / INCIDENT</h3>
                                <button type="button" @click="addHdTicketRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Ticket</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Ticket</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Jenis</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Priority</th>
                                            <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Start</th>
                                            <th class="py-1.5 px-2 w-16 border-r border-[#1E293B] text-center">Restore</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Close / Status</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(t, idx) in reportFormData.hd_ticket_incident" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="t.ticket" placeholder="TICK-..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="t.jenis" placeholder="Koneksi lambat..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]">
                                                    <select x-model="t.priority" class="w-full p-1 text-xs border-0 bg-transparent text-center font-semibold">
                                                        <option value="Low">Low</option>
                                                        <option value="Medium">Medium</option>
                                                        <option value="High">High</option>
                                                        <option value="Critical">Critical</option>
                                                    </select>
                                                </td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="t.start" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="time" x-model="t.restore" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="t.close_status" placeholder="Closed" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="t.keterangan" placeholder="Keterangan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeHdTicketRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- E. MONITORING & SERVICE STATUS --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. MONITORING &amp; SERVICE STATUS</h3>
                                <button type="button" @click="addHdMonitoringRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Monitor</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Service / Device</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Alarm</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Performance / Parameter</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Action</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Keterangan</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(m, idx) in reportFormData.hd_monitoring_status" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.service_device" placeholder="Link Transit..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.status" placeholder="Up" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.alarm" placeholder="None" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.performance" placeholder="1 Gbps / 4ms" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.action" placeholder="Monitor" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="m.keterangan" placeholder="Normal" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeHdMonitoringRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- F. PEKERJAAN ONSITE / FIELD --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. PEKERJAAN ONSITE / FIELD</h3>
                                <button type="button" @click="addHdFieldRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Onsite</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Lokasi</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Pekerjaan</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Engineer</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Hasil</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">Foto / Evidence</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(f, idx) in reportFormData.hd_pekerjaan_field" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.lokasi" placeholder="Lokasi..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.pekerjaan" placeholder="Pekerjaan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.engineer" placeholder="Nama engineer..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.hasil" placeholder="Hasil..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.foto_evidence" placeholder="Ada / Lampiran" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="f.status" placeholder="Done" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeHdFieldRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- G. KENDALA & ESCALATION --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. KENDALA &amp; ESCALATION</h3>
                                <button type="button" @click="addHdKendalaRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Kendala</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kendala / Incident</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Dampak</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan</th>
                                            <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-left">Escalated To</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Status</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Next Action</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(k, idx) in reportFormData.hd_kendala_escalation" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.kendala_incident" placeholder="Kendala..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.dampak" placeholder="Dampak..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.tindakan" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.escalated_to" placeholder="Tier 2/SPV" class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.status" placeholder="Closed" class="w-full p-1 text-xs border-0 bg-transparent text-center font-bold"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="k.next_action" placeholder="Next action..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeHdKendalaRow(idx)" class="text-red-500 font-bold">✕</button></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- H. HANDOVER KE SHIFT BERIKUTNYA --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. HANDOVER KE SHIFT BERIKUTNYA</h3>
                                <button type="button" @click="addHdHandoverRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline cursor-pointer">+ Tambah Handover</button>
                            </div>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-2 w-8 border-r border-[#1E293B] text-center">No.</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Outstanding / Issue</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Kondisi Terakhir</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Tindakan Berikutnya</th>
                                            <th class="py-1.5 px-2 w-24 border-r border-[#1E293B] text-center">PIC</th>
                                            <th class="py-1.5 px-2 w-20 border-r border-[#1E293B] text-center">Due Time</th>
                                            <th class="py-1.5 px-2 border-r border-[#1E293B] text-left">Catatan</th>
                                            <th class="py-1.5 px-2 w-8 text-center">✕</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(h, idx) in reportFormData.hd_handover" :key="idx">
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="p-1 text-center font-bold border-r border-[#1E293B]" x-text="idx + 1"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.outstanding_issue" placeholder="Issue..." class="w-full p-1 text-xs border-0 bg-transparent font-medium"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.kondisi_terakhir" placeholder="Kondisi..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.tindakan_berikutnya" placeholder="Tindakan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.pic" placeholder="Shift Siang" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.due_time" placeholder="14:00" class="w-full p-1 text-xs border-0 bg-transparent text-center"></td>
                                                <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="h.catatan" placeholder="Catatan..." class="w-full p-1 text-xs border-0 bg-transparent"></td>
                                                <td class="p-1 text-center"><button type="button" @click="removeHdHandoverRow(idx)" class="text-red-500 font-bold">✕</button></td>
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

                        {{-- J. VERIFIKASI (HD) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">J. VERIFIKASI</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                        <tr>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-1.5 px-3 w-1/3 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-1.5 px-3 w-1/3 text-center">Status / Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer / Shift PIC</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_identitas.nama_engineer" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-emerald-700">Digital Signature Ready</td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Team Leader</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_identitas.team_leader" class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center font-bold text-slate-600">Verifikasi Lead</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Engineer Shift Berikutnya / Handover</td>
                                            <td class="p-1 border-r border-[#1E293B]"><input type="text" x-model="reportFormData.hd_verifikasi.next_engineer_name" placeholder="Nama Engineer Shift Berikutnya..." class="w-full p-1 text-xs font-bold bg-transparent border-0"></td>
                                            <td class="p-2 text-center text-slate-500">Handover Verified</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-[10.5px] text-[#475569] italic">Catatan: Daily/Shift Report bukan pengganti laporan Project atau Managed Service. Fungsinya sebagai kontrol operasional dan handover antar-shift.</p>
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
