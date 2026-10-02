{{-- ═══ CHEVRON SALES PIPELINE STAGE TIMELINE (IPNET ENTERPRISE DESIGN SYSTEM) ═══ --}}
@php
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
    $currentSalesStage = $project->sales_stage ?: ($currentStatus === 'In Progress' ? 'Closed Won' : 'Qualification');
    $currentProb = (int)($project->win_probability ?? ($currentSalesStage === 'Closed Won' || $currentStatus === 'In Progress' ? 100 : 10));

    $stageOrderMap = [
        'Qualification'      => 1,
        'Discovery'          => 2,
        'Proposal Request'   => 3,
        'Proposal / Quoting' => 3,
        'Quotation'          => 3,
        'Negotiation'        => 4,
        'Approval'           => 4,
        'Contract / PO / SPK'=> 4,
        'Closed Won'         => 5,
        'Closed Lost'        => 5,
    ];

    $currentStageIndex = $stageOrderMap[$currentSalesStage] ?? ($currentStatus === 'In Progress' ? 5 : 1);
    $isClosedLost = ($currentSalesStage === 'Closed Lost');
    $isClosedWon = ($currentSalesStage === 'Closed Won' || $currentStatus === 'In Progress');

    $pipelineSteps = [
        [
            'index'    => 1,
            'key'      => 'Qualification',
            'title'    => 'Qualification',
            'desc'     => 'Kualifikasi Awal',
            'prob'     => '10%',
        ],
        [
            'index'    => 2,
            'key'      => 'Discovery',
            'title'    => 'Discovery',
            'desc'     => 'Kebutuhan Klien',
            'prob'     => '25%',
        ],
        [
            'index'    => 3,
            'key'      => 'Proposal / Quoting',
            'title'    => 'Proposal & Quoting',
            'desc'     => 'Penawaran Resmi',
            'prob'     => '50%',
        ],
        [
            'index'    => 4,
            'key'      => 'Negotiation',
            'title'    => 'Negotiation',
            'desc'     => 'Pembahasan Kontrak',
            'prob'     => '75%',
        ],
        [
            'index'    => 5,
            'key'      => $isClosedLost ? 'Closed Lost' : 'Closed Won',
            'title'    => $isClosedLost ? 'Closed Lost' : 'Closed Won',
            'desc'     => $isClosedLost ? 'Batal / Kalah Tender' : 'Menang / Deal',
            'prob'     => $isClosedLost ? '0%' : '100%',
        ],
    ];
@endphp

<div class="ipnet-card p-5 sm:p-6 space-y-4">
    
    {{-- Header Section: Identitas IPNet & Info Stage --}}
    <div class="flex items-center justify-between flex-wrap gap-3 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5 flex-wrap">
            <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
            <h3 class="text-sm font-bold text-slate-900 tracking-tight">
                Sales Pipeline Stage &amp; Progress
            </h3>
            
            @if($isClosedWon)
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Deal / Won (100% Selesai)</span>
                </span>
            @elseif($isClosedLost)
                <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                    <span>Closed Lost (0%)</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D] animate-ping"></span>
                    <span>Tahap {{ $currentStageIndex }} dari 5: {{ $currentSalesStage }}</span>
                </span>
            @endif

            <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                Peluang: <strong class="text-slate-900">{{ $currentProb }}%</strong>
            </span>

            @if($project->expected_closing_date)
                <span class="text-slate-400 text-xs hidden sm:inline">•</span>
                <span class="text-xs text-slate-500 font-medium hidden sm:inline flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Target Closing: <strong class="text-slate-800">{{ \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') }}</strong>
                </span>
            @endif
        </div>

        @if(empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
            <button type="button" 
                    @click="isEditPipelineModalOpen = true; window.openModal('modal-edit-pipeline')"
                    onclick="window.openModal('modal-edit-pipeline')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold btn-ipnet-soft transition cursor-pointer shadow-none active:scale-95">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Ubah Stage &amp; Prospek</span>
            </button>
        @endif
    </div>

    {{-- Chevron Pipeline Stepper (Sesuai Desain IPNet yang Rapi, Bersambung, dan Elegan) --}}
    <div class="overflow-x-auto pt-2 pb-5 mb-2 scrollbar-thin">
        <div class="min-w-[820px] lg:min-w-0 flex items-stretch gap-1 sm:gap-1.5">
            
            @foreach($pipelineSteps as $step)
                @php
                    $isPast = ($step['index'] < $currentStageIndex);
                    $isActive = ($step['index'] === $currentStageIndex);
                    $isFuture = ($step['index'] > $currentStageIndex);
                    $isFirst = ($step['index'] === 1);
                    $isLast = ($step['index'] === 5);
                @endphp

                <div class="relative flex-1 min-h-[58px] sm:min-h-[62px] flex items-center transition-all duration-200 cursor-pointer select-none group"
                     @click="window.openEditPipelineWithStage('{{ $step['key'] }}')"
                     title="Klik untuk ubah ke tahap {{ $step['title'] }}">
                    
                    {{-- SVG Background Chevron Shape dengan Vektor Presisi --}}
                    <svg viewBox="0 0 200 60" class="absolute inset-0 w-full h-full filter transition-all duration-200 group-hover:brightness-95" preserveAspectRatio="none">
                        <defs>
                            {{-- Gradient Merah IPNet untuk Stage Aktif --}}
                            <linearGradient id="ipnetRedGrad_{{ $step['index'] }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#8F0A0D" />
                                <stop offset="100%" stop-color="#B81525" />
                            </linearGradient>

                            {{-- Gradient Hijau Emerald untuk Closed Won --}}
                            <linearGradient id="emeraldGrad_{{ $step['index'] }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#059669" />
                                <stop offset="100%" stop-color="#10B981" />
                            </linearGradient>

                            {{-- Gradient Merah untuk Closed Lost --}}
                            <linearGradient id="lostGrad_{{ $step['index'] }}" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#DC2626" />
                                <stop offset="100%" stop-color="#991B1B" />
                            </linearGradient>
                        </defs>

                        @php
                            // Tentukan path SVG berdasarkan posisi (Awal, Tengah, Akhir)
                            if ($isFirst) {
                                // Rounded kiri, arrow kanan
                                $pathD = "M 8 0 L 184 0 L 200 30 L 184 60 L 8 60 Q 0 60 0 52 L 0 8 Q 0 0 8 0 Z";
                            } elseif ($isLast) {
                                // Indent kiri, rounded kanan
                                $pathD = "M 0 0 L 192 0 Q 200 0 200 8 L 200 52 Q 200 60 192 60 L 0 60 L 16 30 Z";
                            } else {
                                // Indent kiri, arrow kanan (Interlocking Chevron)
                                $pathD = "M 0 0 L 184 0 L 200 30 L 184 60 L 0 60 L 16 30 Z";
                            }

                            // Tentukan fill & stroke sesuai status dan warna IPNet
                            if ($isActive) {
                                if ($isClosedWon) {
                                    $fill = "url(#emeraldGrad_{$step['index']})";
                                    $stroke = "#047857";
                                } elseif ($isClosedLost) {
                                    $fill = "url(#lostGrad_{$step['index']})";
                                    $stroke = "#7F1D1D";
                                } else {
                                    $fill = "url(#ipnetRedGrad_{$step['index']})";
                                    $stroke = "#6B080A";
                                }
                                $strokeWidth = "2";
                            } elseif ($isPast) {
                                $fill = "#ECFDF5"; // Emerald-50
                                $stroke = "#A7F3D0"; // Emerald-200
                                $strokeWidth = "1.5";
                            } else {
                                $fill = "#F8FAFC"; // Slate-50
                                $stroke = "#E2E8F0"; // Slate-200
                                $strokeWidth = "1.5";
                            }
                        @endphp

                        <path d="{{ $pathD }}" 
                              fill="{{ $fill }}" 
                              stroke="{{ $stroke }}" 
                              stroke-width="{{ $strokeWidth }}" 
                              stroke-linejoin="round" />
                    </svg>

                    {{-- Isi Teks & Ikon di Dalam Chevron --}}
                    <div class="relative z-10 w-full flex items-center justify-between {{ $isFirst ? 'pl-4 sm:pl-5' : 'pl-6 sm:pl-7' }} {{ $isLast ? 'pr-4 sm:pr-5' : 'pr-6 sm:pr-7' }} gap-2.5">
                        
                        <div class="flex items-center gap-2.5 min-w-0 flex-1">
                            {{-- Indikator Bulat (Checkmark untuk Selesai / Nomor / Ping untuk Aktif) --}}
                            @if($isPast)
                                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-2xs shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            @elseif($isActive)
                                <div class="w-6 h-6 rounded-full bg-white text-[#8F0A0D] flex items-center justify-center text-xs font-black shadow-xs shrink-0">
                                    @if($isClosedWon)
                                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @elseif($isClosedLost)
                                        <span class="text-rose-700 font-black text-xs">✕</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                    @endif
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-white border border-slate-300 text-slate-400 flex items-center justify-center text-[11px] font-bold shrink-0">
                                    {{ $step['index'] }}
                                </div>
                            @endif

                            {{-- Judul Tahapan & Subketerangan --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs sm:text-[13px] font-extrabold truncate tracking-tight {{ $isActive ? 'text-white' : ($isPast ? 'text-emerald-950' : 'text-slate-700') }}">
                                        {{ $step['title'] }}
                                    </span>
                                </div>
                                <div class="text-[10px] sm:text-[10.5px] truncate font-medium {{ $isActive ? 'text-white/80' : ($isPast ? 'text-emerald-700' : 'text-slate-400') }}">
                                    <span>{{ $step['desc'] }}</span>
                                    <span class="opacity-70">• {{ $step['prob'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status Tag Tipis --}}
                        <div class="hidden xl:block shrink-0">
                            @if($isActive)
                                <span class="px-2 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase bg-white/20 text-white tracking-wider border border-white/30">
                                    Aktif
                                </span>
                            @elseif($isPast)
                                <span class="text-[10px] font-bold text-emerald-600">
                                    Selesai
                                </span>
                            @endif
                        </div>

                    </div>

                </div>
            @endforeach

        </div>
    </div>

    {{-- Footer Info Bar --}}
    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 pt-3 border-t border-slate-100 flex-wrap gap-2">
        <div class="flex items-center gap-2">
            <span class="text-slate-400">Progres Penjualan:</span>
            <span class="font-bold text-slate-800">{{ $currentStageIndex }} dari 5 Tahap Selesai/Aktif</span>
            <span class="text-slate-300">•</span>
            <span class="text-slate-400">Peluang Deal:</span>
            <span class="font-bold text-[#8F0A0D]">{{ $currentProb }}% Win Rate</span>
        </div>
    </div>

</div>
