@extends('layouts.app')

@section('title', 'Tambah Project Baru - PT IP Network Solusindo')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .cf-label { display:block; font-size:10.5px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:.06em; margin-bottom:6px; }
    .cf-input { width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #E2E8F0; font-size:13.5px; color:#0F172A; outline:none; background:#FFFFFF; box-sizing:border-box; transition:border-color .15s,box-shadow .15s; font-family:inherit; }
    .cf-input:focus { border-color:#8F0A0D; box-shadow:0 0 0 3px rgba(143,10,13,.08); }
    .cf-input::placeholder { color:#94A3B8; font-size:13px; }
    .cf-textarea { width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #E2E8F0; font-size:13.5px; color:#0F172A; outline:none; background:#FFFFFF; box-sizing:border-box; resize:vertical; min-height:100px; transition:border-color .15s,box-shadow .15s; font-family:inherit; }
    .cf-textarea:focus { border-color:#8F0A0D; box-shadow:0 0 0 3px rgba(143,10,13,.08); }
    .cf-textarea::placeholder { color:#94A3B8; }
    .cf-select { width:100%; padding:10px 14px; border-radius:10px; border:1.5px solid #E2E8F0; font-size:13px; color:#0F172A; outline:none; background:#FFFFFF url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") no-repeat right 12px center; background-size:16px; box-sizing:border-box; cursor:pointer; transition:border-color .15s,box-shadow .15s; appearance:none; padding-right:36px; font-family:inherit; }
    .cf-select:focus { border-color:#8F0A0D; box-shadow:0 0 0 3px rgba(143,10,13,.08); }
    .main-card { background:#FFFFFF; border:1.5px solid #E8ECF2; border-radius:18px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.04); }
    .sec-divider { border:none; border-top:1.5px solid #F1F5F9; margin:0 -28px 24px; }
    .sec-title { font-size:13px; font-weight:800; color:#1E293B; display:flex; align-items:center; gap:8px; margin-bottom:20px; }
    .sec-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
    .rp-wrap { position:relative; }
    .rp-prefix { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:13px; font-weight:700; color:#64748B; pointer-events:none; }
    .rp-input { padding-left:42px !important; }
    .ms-item { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; border:1.5px solid #E2E8F0; background:#F8FAFC; margin-bottom:8px; }
    .ms-del-btn { width:24px; height:24px; display:flex; align-items:center; justify-content:center; border-radius:7px; color:#94A3B8; cursor:pointer; background:transparent; border:none; transition:all .15s; flex-shrink:0; }
    .ms-del-btn:hover { color:#EF4444; background:#FEF2F2; }
    .ms-modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(4px); z-index:9999; display:flex; align-items:center; justify-content:center; padding:16px; }
    .ms-modal-box { background:#FFFFFF; border-radius:20px; width:480px; max-width:100%; box-shadow:0 24px 64px rgba(15,23,42,.25); border:1.5px solid #E2E8F0; overflow:hidden; }
    .ms-modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1.5px solid #F1F5F9; background:linear-gradient(135deg,#F8FAFC 0%,#EFF9FF 100%); }
    .ms-modal-footer { display:flex; gap:10px; padding:14px 20px; border-top:1.5px solid #F1F5F9; justify-content:flex-end; background:#FAFAFA; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
    .afu { animation:fadeUp .4s cubic-bezier(.16,1,.3,1) both; }
    @keyframes scaleIn { from{opacity:0;transform:scale(.94)} to{opacity:1;transform:scale(1)} }
    .scale-in { animation:scaleIn .2s cubic-bezier(.16,1,.3,1) both; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F4F6F9] font-sans" x-data="createProjectPage()" x-cloak>
    @include('components.sidebar')
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])

        <div class="p-6 afu">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-[12.5px] text-[#64748B] mb-3">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-[#8F0A0D] transition font-semibold">Project</a>
                <svg class="w-3 h-3 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#1E293B] font-bold">Tambah</span>
            </div>

            {{-- Page Title --}}
            <h1 class="text-[22px] font-bold text-[#1E293B] tracking-tight mb-5">Tambah Project Baru</h1>

            {{-- SINGLE MAIN CARD --}}
            <div class="main-card">
                <form action="{{ route('sales.pipeline.store') }}" method="POST" id="createProjectForm">
                    @csrf
                    <input type="hidden" name="status" value="{{ $isCompleted ? 'Completed' : 'Opportunity' }}">
                    <input type="hidden" name="is_completed" value="{{ $isCompleted ? '1' : '0' }}">

                    <div class="p-7">

                        {{-- ─── SECTION 1: Project Information ─── --}}
                        <div class="sec-title">
                            <span class="sec-dot" style="background:linear-gradient(135deg,#8F0A0D,#D62E3C);"></span>
                            Project Information
                        </div>

                        <div class="mb-4">
                            <label class="cf-label">Project Name <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="name" required class="cf-input" placeholder="Contoh: Pengadaan Firewall &amp; Switch Datacenter PT Telkom" value="{{ old('name') }}">
                        </div>

                        <div class="mb-4">
                            <label class="cf-label">Project Detail</label>
                            <textarea name="sales_notes" class="cf-textarea" placeholder="Detail kebutuhan klien, spesifikasi teknis, atau catatan penting lainnya...">{{ old('sales_notes') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="cf-label">Client Name <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client" required list="clientListCreate" class="cf-input" placeholder="Contoh: PT Telkom Indonesia / Bank BRI" value="{{ old('client') }}">
                            <datalist id="clientListCreate">
                                @foreach($clients as $cl)<option value="{{ $cl->name }}">{{ $cl->department ? "({$cl->department})" : "" }}</option>@endforeach
                            </datalist>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="cf-label">Client Department</label>
                                <input type="text" name="client_department" class="cf-input" placeholder="Contoh: IT Division, Network Dept..." value="{{ old('client_department') }}">
                            </div>
                            <div>
                                <label class="cf-label">Sales / Client PIC</label>
                                <input type="text" name="sales_pic" class="cf-input" placeholder="Nama Sales / Account Manager" value="{{ old('sales_pic', auth()->user()->name) }}">
                            </div>
                        </div>

                        {{-- ─── DIVIDER ─── --}}
                        <hr class="sec-divider mt-6">

                        {{-- ─── SECTION 2: Estimasi & Pipeline ─── --}}
                        <div class="sec-title">
                            <span class="sec-dot" style="background:linear-gradient(135deg,#4F46E5,#7C3AED);"></span>
                            Estimasi &amp; Pipeline
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="cf-label">Project Estimation (Nilai Kontrak)</label>
                                <div class="rp-wrap"><span class="rp-prefix">Rp</span><input type="number" name="contract_value" min="0" class="cf-input rp-input" placeholder="0" value="{{ old('contract_value',0) }}"></div>
                            </div>
                            <div>
                                <label class="cf-label">Tahapan Sales</label>
                                <select name="sales_stage" class="cf-select" @change="updateProbability($event.target.value)" x-model="salesStage">
                                    @foreach($stages as $key => $stg)<option value="{{ $key }}" {{ old('sales_stage','Qualification') === $key ? 'selected' : ''}}>{{ $stg['label'] }}</option>@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="cf-label">Start Project Estimation</label>
                                <input type="date" name="start_date" class="cf-input" value="{{ old('start_date', date('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="cf-label">Estimasi Closing</label>
                                <input type="date" name="expected_closing_date" class="cf-input" value="{{ old('expected_closing_date', date('Y-m-d', strtotime('+1 month'))) }}">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="cf-label" style="margin-bottom:0;">Win Probability</label>
                                <span class="text-[13px] font-extrabold text-[#8F0A0D]" x-text="probability + '%'"></span>
                            </div>
                            <input type="range" name="win_probability" min="0" max="100" step="5" x-model.number="probability" class="w-full h-2 rounded-full appearance-none cursor-pointer" :style="`background:linear-gradient(to right,#8F0A0D 0%,#8F0A0D ${probability}%,#E2E8F0 ${probability}%,#E2E8F0 100%)`">
                            <div class="flex justify-between mt-1 text-[10.5px] text-[#94A3B8]"><span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span></div>
                        </div>

                        {{-- ─── DIVIDER ─── --}}
                        <hr class="sec-divider mt-6">

                        {{-- ─── SECTION 3: Milestone ─── --}}
                        <div class="flex items-center gap-2 mb-4">
                            <span class="sec-dot" style="background:linear-gradient(135deg,#0EA5E9,#0284C7);"></span>
                            <span class="text-[13px] font-extrabold text-[#1E293B]">Milestone</span>
                            <span class="ml-auto text-[11px] font-semibold text-[#94A3B8]">Complete (<span x-text="milestones.filter(m=>m.done).length"></span>/<span x-text="milestones.length"></span>)</span>
                        </div>

                        <template x-for="(ms, idx) in milestones" :key="idx">
                            <div class="ms-item">
                                <input type="checkbox" x-model="ms.done" class="w-4 h-4 rounded cursor-pointer flex-shrink-0" style="accent-color:#8F0A0D;">
                                <span class="flex-1 text-[13px] font-medium text-[#1E293B]" :class="ms.done ? 'line-through opacity-40' : ''" x-text="ms.title"></span>
                                <span class="text-[11px] text-[#94A3B8] whitespace-nowrap" x-show="ms.date" x-text="ms.date"></span>
                                <button type="button" class="ms-del-btn" @click="removeMilestone(idx)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                <input type="hidden" :name="`milestones[${idx}][title]`" :value="ms.title">
                                <input type="hidden" :name="`milestones[${idx}][date]`" :value="ms.date">
                            </div>
                        </template>

                        {{-- ─── BOTTOM ACTIONS (INSIDE CARD) ─── --}}
                        <div class="flex items-center justify-between pt-8 mt-6 border-t border-[#F1F5F9]">
                            <div>
                                <button type="button" @click="openMilestoneModal()" class="inline-flex items-center gap-1.5 text-[12.5px] font-bold text-[#8F0A0D] hover:text-[#6B0009] transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    + ADD MILESTONE
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('sales.pipeline.index') }}" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-[#475569] font-bold text-[13px] hover:bg-[#F8FAFC] transition cursor-pointer inline-flex items-center">
                                    Cancel
                                </a>
                                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-[13px] text-white cursor-pointer shadow-md hover:shadow-lg hover:brightness-105 active:scale-[0.98] transition-all" style="background:linear-gradient(135deg,#8F0A0D 0%,#D62E3C 100%);">
                                    {{ $isCompleted ? 'Simpan Project Selesai' : 'Create New Project' }}
                                </button>
                            </div>
                        </div>

                    </div>{{-- end p-7 --}}
                </form>
            </div>{{-- end main-card --}}
        </div>
    </div>

    {{-- MILESTONE MODAL --}}
    <div class="ms-modal-overlay" x-show="showMilestoneModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.self="closeMilestoneModal()">
        <div class="ms-modal-box scale-in" @click.stop>
            <div class="ms-modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#0EA5E9,#0284C7);">
                        <svg style="width:18px;height:18px;" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div><div class="text-[15px] font-bold text-[#1E293B]">Milestone</div><p class="text-[11.5px] text-[#64748B] mt-0.5">Tambah target / tahapan baru</p></div>
                </div>
                <button type="button" @click="closeMilestoneModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-[#64748B] hover:bg-[#E2E8F0] transition cursor-pointer"><svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 flex flex-col gap-4">
                <div>
                    <label class="cf-label">Milestone Title <span class="text-[#8F0A0D]">*</span></label>
                    <input type="text" class="cf-input" x-model="msForm.title" placeholder="Contoh: Presentasi Proposal, Tanda Tangan Kontrak..." @keyup.enter="addMilestone()">
                </div>
                <div>
                    <label class="cf-label">Remark <span class="text-[#94A3B8] font-normal normal-case">(opsional)</span></label>
                    <textarea class="cf-textarea" x-model="msForm.remark" rows="3" placeholder="Catatan tambahan untuk milestone ini..."></textarea>
                </div>
                <div>
                    <label class="cf-label">Estimation Date <span class="text-[#94A3B8] font-normal normal-case">(opsional)</span></label>
                    <div class="relative"><div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none"><svg class="w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><input type="date" class="cf-input" style="padding-left:42px;" x-model="msForm.date"></div>
                </div>
                <p x-show="msError" class="text-[12px] text-[#EF4444] font-semibold -mt-2" x-text="msError"></p>
            </div>
            <div class="ms-modal-footer">
                <button type="button" @click="closeMilestoneModal()" class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-[#475569] font-bold text-[13px] hover:bg-[#F8FAFC] transition cursor-pointer">Cancel</button>
                <button type="button" @click="addMilestone()" class="px-6 py-2.5 rounded-xl font-bold text-[13px] text-white cursor-pointer shadow-md" style="background:linear-gradient(135deg,#0EA5E9 0%,#0284C7 100%);">Add</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function createProjectPage() {
        return {
            probability: 10,
            salesStage: 'Qualification',
            milestones: [],
            showMilestoneModal: false,
            msForm: { title: '', remark: '', date: '' },
            msError: '',
            stageProbabilities: @json(collect($stages)->map(fn($s) => $s['default_prob'])),
            updateProbability(stage) {
                if (this.stageProbabilities[stage] !== undefined) this.probability = this.stageProbabilities[stage];
            },
            openMilestoneModal() { this.msForm = { title: '', remark: '', date: '' }; this.msError = ''; this.showMilestoneModal = true; },
            closeMilestoneModal() { this.showMilestoneModal = false; },
            addMilestone() {
                if (!this.msForm.title.trim()) { this.msError = 'Milestone title wajib diisi.'; return; }
                this.milestones.push({ title: this.msForm.title.trim(), remark: this.msForm.remark.trim(), date: this.msForm.date, done: false });
                this.showMilestoneModal = false;
            },
            removeMilestone(idx) { this.milestones.splice(idx, 1); }
        };
    }
</script>
@endpush
