@extends('layouts.app')

@section('title', 'Retention & Account Health Monitoring - CRO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="croRetentionHandler()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Customer Retention & Account Health Monitoring'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('cro.retention.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="client" 
                               value="{{ request('client') }}"
                               placeholder="Cari Klien / Perusahaan..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Kesehatan</option>
                        <option value="Healthy" {{ request('status') == 'Healthy' ? 'selected' : '' }}>Healthy (Aman)</option>
                        <option value="Warning" {{ request('status') == 'Warning' ? 'selected' : '' }}>Warning (Waspada)</option>
                        <option value="At-Risk" {{ request('status') == 'At-Risk' ? 'selected' : '' }}>At-Risk (Bahaya Churn)</option>
                    </select>
                    @if(request('client') || request('status'))
                        <a href="{{ route('cro.retention.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="openCreateHealthModal()" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah / Nilai Akun Klien</span>
                </button>
            </div>

            {{-- Retention Table --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[22%]">Klien / Akun</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[11%] whitespace-nowrap">Status</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[11%] whitespace-nowrap">Health Score</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%] whitespace-nowrap">Nilai Kontrak Tahunan</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[13%] whitespace-nowrap">Jatuh Tempo</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[13%] whitespace-nowrap">Peluang Renewal</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%]">Risiko / Strategi</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[8%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($accounts as $acc)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $acc->client_name }}</div>
                                        <div class="text-[11.5px] text-[#75727C] mt-0.5">
                                            AM: {{ $acc->accountManager ? $acc->accountManager->name : '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $badge = $acc->health_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap font-extrabold text-[13.5px] {{ $acc->health_score < 60 ? 'text-rose-600' : ($acc->health_score < 80 ? 'text-amber-600' : 'text-emerald-600') }}">
                                        {{ $acc->health_score }} / 100
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-[#17151C]">
                                        Rp {{ number_format($acc->estimated_annual_value, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="font-medium text-gray-700 text-[12.5px]">{{ $acc->contract_end_date ? $acc->contract_end_date->format('d M Y') : '-' }}</span>
                                        @if($acc->contract_end_date && $acc->contract_end_date->diffInDays(now()) <= 60 && $acc->contract_end_date->isFuture())
                                            <span class="block text-[10.5px] text-rose-600 font-bold mt-0.5">⚠️ &lt; 60 Hari</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <div class="w-14 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#C81E2C] h-1.5 rounded-full" style="width: {{ $acc->renewal_probability }}%"></div>
                                            </div>
                                            <span class="font-bold text-[11.5px] text-gray-700">{{ $acc->renewal_probability }}%</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-[12px]">
                                        @if($acc->risk_factors)
                                            <div class="text-rose-700 font-medium line-clamp-1" title="{{ $acc->risk_factors }}">⚠️ {{ $acc->risk_factors }}</div>
                                        @endif
                                        @if($acc->retention_strategy)
                                            <div class="text-gray-500 line-clamp-1 mt-0.5" title="{{ $acc->retention_strategy }}">💡 {{ $acc->retention_strategy }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <button @click="openEditHealthModal('{{ addslashes($acc->client_name) }}', '{{ $acc->health_status }}', {{ $acc->health_score }}, '{{ $acc->contract_end_date ? $acc->contract_end_date->format('Y-m-d') : '' }}', {{ (float)$acc->estimated_annual_value }}, {{ $acc->renewal_probability }}, '{{ addslashes($acc->risk_factors ?? '') }}', '{{ addslashes($acc->retention_strategy ?? '') }}', '{{ $acc->account_manager_id }}')"
                                                class="px-2.5 py-1 bg-white border border-[#E7E5E3] hover:bg-gray-50 text-gray-700 font-bold rounded-lg shadow-2xs transition text-[11px] cursor-pointer">
                                            Edit Matriks
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada data kesehatan akun klien yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($accounts->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $accounts->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Create / Edit Account Health --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Perbarui Matriks Kesehatan Akun</h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.retention.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan *</label>
                    <input type="text" name="client_name" x-model="formData.client_name" list="clientList" required class="wms-input" placeholder="Pilih atau ketik nama klien">
                    <datalist id="clientList">
                        @foreach($clients as $c)
                            <option value="{{ $c->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status Kesehatan *</label>
                        <select name="health_status" x-model="formData.health_status" required class="wms-input font-bold">
                            <option value="Healthy">Healthy (Aman)</option>
                            <option value="Warning">Warning (Waspada)</option>
                            <option value="At-Risk">At-Risk (Bahaya Churn)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Health Score (0 - 100) *</label>
                        <input type="number" name="health_score" x-model="formData.health_score" min="0" max="100" required class="wms-input font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Berakhir Kontrak</label>
                        <input type="date" name="contract_end_date" x-model="formData.contract_end_date" class="wms-input">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Estimasi Nilai Kontrak Tahunan (Rp)</label>
                        <input type="number" step="1000" name="estimated_annual_value" x-model="formData.estimated_annual_value" class="wms-input" placeholder="e.g. 1500000000">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Peluang Renewal (%) *</label>
                        <input type="number" name="renewal_probability" x-model="formData.renewal_probability" min="0" max="100" required class="wms-input">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Account Manager (Sales/AM)</label>
                        <select name="account_manager_id" x-model="formData.account_manager_id" class="wms-input">
                            <option value="">-- Pilih AM --</option>
                            @foreach($salesUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Faktor Risiko (Risk Factors)</label>
                    <textarea name="risk_factors" x-model="formData.risk_factors" rows="2" class="wms-input" placeholder="e.g. Insiden teknis berulang, pergeseran budget klien, dsb..."></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Strategi Retensi (Retention Plan)</label>
                    <textarea name="retention_strategy" x-model="formData.retention_strategy" rows="2" class="wms-input" placeholder="e.g. Complimentary SLA review, executive meeting, dedicated engineer..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Matriks</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function croRetentionHandler() {
    return {
        isModalOpen: false,
        formData: {
            client_name: '',
            health_status: 'Healthy',
            health_score: 90,
            contract_end_date: '',
            estimated_annual_value: '',
            renewal_probability: 80,
            risk_factors: '',
            retention_strategy: '',
            account_manager_id: ''
        },
        openCreateHealthModal() {
            this.formData = {
                client_name: '',
                health_status: 'Healthy',
                health_score: 90,
                contract_end_date: '',
                estimated_annual_value: '',
                renewal_probability: 80,
                risk_factors: '',
                retention_strategy: '',
                account_manager_id: ''
            };
            this.isModalOpen = true;
        },
        openEditHealthModal(name, status, score, endDate, val, prob, risk, strat, amId) {
            this.formData = {
                client_name: name,
                health_status: status,
                health_score: score,
                contract_end_date: endDate,
                estimated_annual_value: val,
                renewal_probability: prob,
                risk_factors: risk,
                retention_strategy: strat,
                account_manager_id: amId
            };
            this.isModalOpen = true;
        }
    };
}
</script>
@endsection
