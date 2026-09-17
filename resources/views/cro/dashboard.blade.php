@extends('layouts.app')

@section('title', 'Customer Management - CRO Control Tower')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="croDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6 max-w-[1600px] mx-auto">
            
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



            {{-- 4 Primary Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
                <x-metric-card label="Skor Kepuasan (CSAT)" value="{{ $avgCsat }} / 5.0" icon="Star" :accent="true" href="{{ route('cro.satisfaction.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </x-metric-card>

                <x-metric-card label="Akun Sehat / Total" value="{{ $healthyPct }}%" icon="ShieldCheck" href="{{ route('cro.retention.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </x-metric-card>

                <x-metric-card label="Open Concerns / Eskalasi" value="{{ $openConcernsCount }}" icon="AlertTriangle" href="{{ route('cro.concerns.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </x-metric-card>

                <x-metric-card label="Renewal Watch (90 Hari)" value="Rp {{ number_format($totalRenewalValue / 1000000, 0, ',', '.') }} Jt" icon="TrendingUp" href="{{ route('cro.retention.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </x-metric-card>
            </div>

            {{-- 2-Column Section: At-Risk Account Watchlist & Orchestration Tracker --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                
                {{-- Left: At-Risk & Warning Account Radar (6 Cols) --}}
                <div class="lg:col-span-6 bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <h3 class="font-bold text-gray-900 text-sm">At-Risk & Warning Account Radar</h3>
                        </div>
                        <a href="{{ route('cro.retention.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($atRiskWatchlist as $account)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 text-sm">{{ $account->client_name }}</span>
                                        
                                        @php $badge = $account->health_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>

                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold {{ $account->health_score < 60 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                            Score: {{ $account->health_score }}/100
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-600 line-clamp-2">
                                        {{ $account->risk_factors ?: ($account->retention_strategy ?: 'Tidak ada catatan faktor risiko.') }}
                                    </div>

                                    <div class="text-[11.5px] text-gray-400 flex items-center gap-2">
                                        <span>Jatuh Tempo: <strong class="text-gray-700 font-semibold">{{ $account->contract_end_date ? $account->contract_end_date->format('d M Y') : '-' }}</strong></span>
                                        <span>•</span>
                                        <span>Renewal: <strong class="text-gray-700 font-semibold">{{ $account->renewal_probability }}%</strong></span>
                                    </div>
                                </div>

                                <button @click="openHealthModal('{{ addslashes($account->client_name) }}', '{{ $account->health_status }}', {{ $account->health_score }}, '{{ $account->contract_end_date ? $account->contract_end_date->format('Y-m-d') : '' }}', {{ $account->renewal_probability }}, '{{ addslashes($account->risk_factors ?? '') }}', '{{ addslashes($account->retention_strategy ?? '') }}')"
                                        class="p-2 hover:bg-gray-100 rounded-lg text-gray-400 hover:text-gray-700 transition cursor-pointer flex-shrink-0" title="Edit Matriks">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Seluruh akun dalam status Sehat (Healthy). Tidak ada akun At-Risk terdeteksi.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Total Akun Dipantau: <strong>{{ $totalAccounts }} Klien</strong></span>
                        <span class="text-emerald-700 font-bold">{{ $healthyCount }} Healthy ({{ $healthyPct }}%)</span>
                    </div>
                </div>

                {{-- Right: Issue Orchestration & Escalation Radar (6 Cols) --}}
                <div class="lg:col-span-6 bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">Orkestrasi Disposisi Isu Aktif</h3>
                        </div>
                        <a href="{{ route('cro.concerns.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">
                            Daftar Tiket &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($concerns->whereNotIn('status', ['Closed'])->take(3) as $con)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-gray-800">{{ $con->ticket_number }}</span>

                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-gray-100 text-gray-700">
                                            {{ $con->assigned_dept }}
                                        </span>

                                        @php $sBadge = $con->severity_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $sBadge['bg'] }} {{ $sBadge['text'] }} {{ $sBadge['border'] }}">
                                            {{ $sBadge['label'] }}
                                        </span>

                                        @php $stBadge = $con->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $stBadge['bg'] }} {{ $stBadge['text'] }} {{ $stBadge['border'] }}">
                                            {{ $stBadge['label'] }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-gray-900 text-sm leading-snug truncate">
                                        {{ $con->title }}
                                    </h4>

                                    <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                                        <span class="font-semibold text-gray-700">{{ $con->client_name }}</span>
                                        <span>•</span>
                                        <span>SLA Target: <strong class="text-gray-700 font-semibold">{{ $con->sla_due_date ? $con->sla_due_date->format('d M Y') : '-' }}</strong></span>
                                        <span>•</span>
                                        <span>PIC: <strong class="text-[#C81E2C]">{{ $con->assignedUser ? $con->assignedUser->name : 'Belum Ditugaskan' }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Tidak ada concern/eskalasi yang sedang aktif.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Total Isu Aktif: <strong>{{ $openConcernsCount }} Tiket</strong></span>
                        <span class="text-amber-700 font-bold">{{ $pendingConfirmationCount }} Menunggu Konfirmasi Klien</span>
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Recent Engagements & Opportunities Bridge --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">
                
                {{-- Recent Engagements & Meeting Touchpoints --}}
                <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <h3 class="font-bold text-gray-900 text-sm">Log Relasi & Meeting Klien (QBR / Touchpoint)</h3>
                        <a href="{{ route('cro.engagements.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">Lihat Semua &rarr;</a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($engagements->take(3) as $eng)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 text-sm">{{ $eng->client_name }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-gray-100 text-gray-700">{{ $eng->engagement_type }}</span>
                                        
                                        @php $sentBadge = $eng->sentiment_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $sentBadge['bg'] }} {{ $sentBadge['text'] }} {{ $sentBadge['border'] }}">
                                            {{ $sentBadge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-700 font-medium truncate">{{ $eng->title }}</p>
                                    <p class="text-[11.5px] text-gray-400">{{ $eng->engagement_date ? $eng->engagement_date->format('d M Y') : '' }} &bull; PIC: {{ $eng->pic_name ?: 'Tim Klien' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs">Belum ada riwayat pertemuan/touchpoint.</div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 px-5">
                        <span>Aktivitas relasi rutin untuk menjaga kedekatan klien dan deteksi dini kebutuhan.</span>
                    </div>
                </div>

                {{-- Account Development & Opportunity Bridge to Sales --}}
                <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">Peluang Ekspansi Akun (Bridge to Sales)</h3>
                        </div>
                        <a href="{{ route('cro.opportunities.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">Lihat Semua &rarr;</a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($opportunities->take(3) as $opp)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 text-sm">{{ $opp->client_name }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-purple-50 text-purple-700 border border-purple-200">{{ $opp->opportunity_type }}</span>
                                        
                                        @php $opBadge = $opp->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $opBadge['bg'] }} {{ $opBadge['text'] }} {{ $opBadge['border'] }}">
                                            {{ $opBadge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-800 font-semibold truncate">{{ $opp->title }}</p>
                                    <p class="text-xs font-extrabold text-emerald-600">Rp {{ number_format($opp->estimated_value, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs">Belum ada peluang pengembangan akun tercatat.</div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Total Potensi Teridentifikasi:</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($totalOppValue, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- MODAL: Update Account Health --}}
    <div x-show="isHealthModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isHealthModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Perbarui Matriks Kesehatan Akun Klien</h3>
                <button @click="isHealthModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.retention.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan *</label>
                    <input type="text" name="client_name" x-model="healthForm.client_name" required class="wms-input" readonly>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status Kesehatan *</label>
                        <select name="health_status" x-model="healthForm.health_status" required class="wms-input font-bold">
                            <option value="Healthy">Healthy (Aman)</option>
                            <option value="Warning">Warning (Waspada)</option>
                            <option value="At-Risk">At-Risk (Bahaya Churn)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Health Score (0 - 100) *</label>
                        <input type="number" name="health_score" x-model="healthForm.health_score" min="0" max="100" required class="wms-input font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Berakhir Kontrak</label>
                        <input type="date" name="contract_end_date" x-model="healthForm.contract_end_date" class="wms-input">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Peluang Renewal (%) *</label>
                        <input type="number" name="renewal_probability" x-model="healthForm.renewal_probability" min="0" max="100" required class="wms-input">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Faktor Risiko (Risk Factors)</label>
                    <textarea name="risk_factors" x-model="healthForm.risk_factors" rows="2" class="wms-input" placeholder="Misal: komplain berulang, pergantian PIC klien, kompetitor masuk..."></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Strategi Retensi (Retention Plan)</label>
                    <textarea name="retention_strategy" x-model="healthForm.retention_strategy" rows="2" class="wms-input" placeholder="Rencana aksi pengamanan kontrak..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isHealthModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function croDashboard() {
    return {
        isHealthModalOpen: false,
        healthForm: {
            client_name: '',
            health_status: 'Healthy',
            health_score: 90,
            contract_end_date: '',
            renewal_probability: 80,
            risk_factors: '',
            retention_strategy: ''
        },
        openHealthModal(name, status, score, endDate, prob, risk, strat) {
            this.healthForm.client_name = name;
            this.healthForm.health_status = status;
            this.healthForm.health_score = score;
            this.healthForm.contract_end_date = endDate;
            this.healthForm.renewal_probability = prob;
            this.healthForm.risk_factors = risk;
            this.healthForm.retention_strategy = strat;
            this.isHealthModalOpen = true;
        }
    };
}
</script>
@endsection
