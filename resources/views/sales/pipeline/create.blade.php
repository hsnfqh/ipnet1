@extends('layouts.app')

@section('title', 'Tambah Project Baru - PT IP Network Solusindo')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .create-field-label {
        display: block;
        font-size: 10.5px;
        font-weight: 700;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }
    .create-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #E2E8F0;
        font-size: 13.5px;
        color: #0F172A;
        outline: none;
        background: #FFFFFF;
        box-sizing: border-box;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        font-family: inherit;
    }
    .create-input:focus {
        border-color: #8F0A0D;
        box-shadow: 0 0 0 3px rgba(143,10,13,0.08);
    }
    .create-input::placeholder { color: #94A3B8; font-size: 13px; }
    .create-select {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #E2E8F0;
        font-size: 13px;
        color: #0F172A;
        outline: none;
        background: #FFFFFF;
        box-sizing: border-box;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 36px;
    }
    .create-select:focus { border-color: #8F0A0D; box-shadow: 0 0 0 3px rgba(143,10,13,0.08); }
    .create-textarea {
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid #E2E8F0;
        font-size: 13.5px;
        color: #0F172A;
        outline: none;
        background: #FFFFFF;
        box-sizing: border-box;
        resize: vertical;
        min-height: 110px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        font-family: inherit;
    }
    .create-textarea:focus { border-color: #8F0A0D; box-shadow: 0 0 0 3px rgba(143,10,13,0.08); }
    .create-textarea::placeholder { color: #94A3B8; }
    .section-card {
        background: #FFFFFF;
        border: 1.5px solid #E8ECF2;
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 13px;
        font-weight: 800;
        color: #1E293B;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1.5px solid #F1F5F9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8F0A0D, #D62E3C);
        flex-shrink: 0;
    }
    .input-prefix-wrap { position: relative; }
    .input-prefix {
        position: absolute; left: 14px; top: 50%;
        transform: translateY(-50%);
        font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;
    }
    .input-with-prefix { padding-left: 42px !important; }
    .milestone-row {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 14px; border-radius: 10px;
        border: 1.5px solid #E2E8F0; background: #F8FAFC; margin-bottom: 8px;
    }
    .milestone-row input[type=text] {
        flex: 1; border: none; outline: none;
        background: transparent; font-size: 13px; color: #0F172A; font-family: inherit;
    }
    .milestone-row input[type=text]::placeholder { color: #94A3B8; }
    .page-footer {
        position: sticky; bottom: 0;
        background: #FFFFFF; border-top: 1.5px solid #E2E8F0;
        padding: 14px 28px;
        display: flex; justify-content: flex-end; gap: 12px;
        z-index: 10;
    }
    @keyframes fadeUp {
        from { opacity:0; transform:translateY(10px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .anim-fade-up { animation: fadeUp 0.35s cubic-bezier(0.16,1,0.3,1) both; }
    .anim-d1 { animation-delay: 0.06s; }
    .anim-d2 { animation-delay: 0.12s; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F4F6F9] font-sans" x-data="createProjectPage()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto flex flex-col">
        @include('components.topbar', ['title' => 'Project'])

        {{-- Breadcrumb --}}
        <div class="px-7 pt-5 pb-1 flex items-center gap-2 text-[12.5px] text-[#64748B]">
            <a href="{{ route('sales.pipeline.index') }}" class="hover:text-[#8F0A0D] transition font-semibold">Project</a>
            <svg class="w-3.5 h-3.5 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-[#1E293B] font-bold">Tambah</span>
        </div>

        {{-- Page Title --}}
        <div class="px-7 pt-3 pb-5">
            <h1 class="text-[24px] font-bold text-[#1E293B] tracking-tight">Tambah Project Baru</h1>
        </div>

        {{-- Form Body --}}
        <div class="flex-1 px-7 pb-0">
            <form action="{{ route('sales.pipeline.store') }}" method="POST" id="createProjectForm" class="max-w-[860px]">
                @csrf
                <input type="hidden" name="status" value="{{ $isCompleted ? 'Completed' : 'Opportunity' }}">
                <input type="hidden" name="is_completed" value="{{ $isCompleted ? '1' : '0' }}">

                {{-- SECTION 1: Project Information --}}
                <div class="section-card anim-fade-up">
                    <div class="section-title">
                        <span class="section-title-dot"></span>
                        Project Information
                    </div>

                    <div class="mb-4">
                        <label class="create-field-label">Project Name <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="name" required
                               class="create-input"
                               placeholder="Contoh: Pengadaan Firewall &amp; Switch Datacenter PT Telkom"
                               value="{{ old('name') }}">
                    </div>

                    <div class="mb-4">
                        <label class="create-field-label">Project Detail</label>
                        <textarea name="sales_notes" class="create-textarea"
                                  placeholder="Detail kebutuhan klien, spesifikasi teknis, atau catatan penting lainnya...">{{ old('sales_notes') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="create-field-label">Client Name <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="client" required
                               list="clientListCreate"
                               class="create-input"
                               placeholder="Contoh: PT Telkom Indonesia / Bank BRI"
                               value="{{ old('client') }}">
                        <datalist id="clientListCreate">
                            @foreach($clients as $cl)
                                <option value="{{ $cl->name }}">{{ $cl->department ? "({$cl->department})" : '' }}</option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="create-field-label">Client Department</label>
                            <select name="division_id" class="create-select">
                                <option value="">Pilih Divisi / Dept...</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="create-field-label">Sales / Client PIC</label>
                            <input type="text" name="sales_pic"
                                   class="create-input"
                                   placeholder="Nama Sales / Account Manager"
                                   value="{{ old('sales_pic', auth()->user()->name) }}">
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: Estimation & Pipeline --}}
                <div class="section-card anim-fade-up anim-d1">
                    <div class="section-title">
                        <span class="section-title-dot" style="background:linear-gradient(135deg,#4F46E5,#7C3AED);"></span>
                        Estimasi &amp; Pipeline
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="create-field-label">Project Estimation (Nilai Kontrak)</label>
                            <div class="input-prefix-wrap">
                                <span class="input-prefix">Rp</span>
                                <input type="number" name="contract_value" min="0"
                                       class="create-input input-with-prefix"
                                       placeholder="0"
                                       value="{{ old('contract_value', 0) }}">
                            </div>
                        </div>
                        <div>
                            <label class="create-field-label">Tahapan Sales</label>
                            <select name="sales_stage" class="create-select" @change="updateProbability($event.target.value)">
                                @foreach($stages as $key => $stg)
                                    <option value="{{ $key }}" {{ old('sales_stage','Qualification') === $key ? 'selected' : '' }}>{{ $stg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="create-field-label">Start Project Estimation</label>
                            <input type="date" name="start_date"
                                   class="create-input"
                                   value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>
                        <div>
                            <label class="create-field-label">Estimasi Closing</label>
                            <input type="date" name="expected_closing_date"
                                   class="create-input"
                                   value="{{ old('expected_closing_date', date('Y-m-d', strtotime('+1 month'))) }}">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="create-field-label" style="margin-bottom:0;">Win Probability</label>
                            <span class="text-[13px] font-extrabold text-[#8F0A0D]" x-text="probability + '%'"></span>
                        </div>
                        <input type="range" name="win_probability" min="0" max="100" step="5"
                               x-model.number="probability"
                               class="w-full h-2 rounded-full appearance-none cursor-pointer"
                               :style="`accent-color:#8F0A0D; background:linear-gradient(to right,#8F0A0D 0%,#8F0A0D ${probability}%,#E2E8F0 ${probability}%,#E2E8F0 100%)`">
                        <div class="flex justify-between mt-1 text-[10.5px] text-[#94A3B8]">
                            <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: Milestone --}}
                <div class="section-card anim-fade-up anim-d2">
                    <div class="section-title" style="margin-bottom:14px;">
                        <span class="section-title-dot" style="background:linear-gradient(135deg,#0EA5E9,#0284C7);"></span>
                        <span>Milestone</span>
                        <span class="ml-auto text-[11px] font-semibold text-[#94A3B8]">
                            Complete (<span x-text="milestones.filter(m=>m.done).length"></span>/<span x-text="milestones.length"></span>)
                        </span>
                    </div>

                    <template x-for="(ms, idx) in milestones" :key="idx">
                        <div class="milestone-row">
                            <input type="checkbox" x-model="ms.done"
                                   class="w-4 h-4 rounded cursor-pointer flex-shrink-0" style="accent-color:#8F0A0D;">
                            <input type="text" x-model="ms.label"
                                   :name="`milestones[${idx}]`"
                                   :placeholder="`Milestone ${idx + 1}...`"
                                   :class="ms.done ? 'line-through opacity-50' : ''">
                            <button type="button" @click="removeMilestone(idx)"
                                    class="w-6 h-6 flex items-center justify-center rounded-lg text-[#94A3B8] hover:text-[#EF4444] hover:bg-red-50 transition flex-shrink-0 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addMilestone()"
                            class="flex items-center gap-1.5 text-[12.5px] font-bold text-[#8F0A0D] hover:text-[#6B0009] transition cursor-pointer mt-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        + ADD MILESTONE
                    </button>
                </div>

            </form>
        </div>

        {{-- Sticky Footer --}}
        <div class="page-footer">
            <a href="{{ route('sales.pipeline.index') }}"
               class="px-5 py-2.5 rounded-xl border border-[#CBD5E1] text-[#475569] font-bold text-[13px] hover:bg-[#F8FAFC] transition cursor-pointer inline-flex items-center">
                Cancel
            </a>
            <button type="submit" form="createProjectForm"
                    class="px-6 py-2.5 rounded-xl font-bold text-[13px] text-white cursor-pointer shadow-md hover:opacity-90 active:scale-[0.98] transition-all"
                    style="background:linear-gradient(135deg,#8F0A0D 0%,#D62E3C 100%);">
                {{ $isCompleted ? 'Simpan Project Selesai' : 'Create New Project' }}
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function createProjectPage() {
        return {
            probability: 10,
            milestones: [],
            stageProbabilities: @json(collect($stages)->map(fn($s) => $s['default_prob'])),
            updateProbability(stage) {
                if (this.stageProbabilities[stage] !== undefined) {
                    this.probability = this.stageProbabilities[stage];
                }
            },
            addMilestone() {
                this.milestones.push({ label: '', done: false });
            },
            removeMilestone(idx) {
                this.milestones.splice(idx, 1);
            }
        };
    }
</script>
@endpush