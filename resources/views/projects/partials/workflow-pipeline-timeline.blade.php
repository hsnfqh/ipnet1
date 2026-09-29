{{-- ═══ CHEVRON SALES PIPELINE STAGE TIMELINE (SESUAI TEMPLATE INFOGRAFIS) ═══ --}}
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

    $pipelineSteps = [
        [
            'index'       => 1,
            'key'         => 'Qualification',
            'title'       => 'Qualification',
            'desc'        => 'Kualifikasi Awal',
            'prob'        => '10%',
            'active_fill' => '#F472B6', // Pink (Sesuai Gambar 1)
            'done_fill'   => '#FDF2F8',
            'text_active' => 'text-white',
            'text_done'   => 'text-pink-900',
            'border'      => '#9D174D',
            'align_tick'  => 'down', // Tick ke bawah (Ganjil)
        ],
        [
            'index'       => 2,
            'key'         => 'Discovery',
            'title'       => 'Discovery',
            'desc'        => 'Kebutuhan Klien',
            'prob'        => '25%',
            'active_fill' => '#A78BFA', // Ungu / Lavender (Sesuai Gambar 1)
            'done_fill'   => '#F5F3FF',
            'text_active' => 'text-white',
            'text_done'   => 'text-purple-900',
            'border'      => '#5B21B6',
            'align_tick'  => 'up', // Tick ke atas (Genap)
        ],
        [
            'index'       => 3,
            'key'         => 'Proposal / Quoting',
            'title'       => 'Proposal & Quoting',
            'desc'        => 'Penawaran Resmi',
            'prob'        => '50%',
            'active_fill' => '#FB7185', // Magenta / Pink Salmon (Sesuai Gambar 1)
            'done_fill'   => '#FFF1F2',
            'text_active' => 'text-white',
            'text_done'   => 'text-rose-900',
            'border'      => '#9F1239',
            'align_tick'  => 'down', // Tick ke bawah (Ganjil)
        ],
        [
            'index'       => 4,
            'key'         => 'Negotiation',
            'title'       => 'Negotiation',
            'desc'        => 'Pembahasan Kontrak',
            'prob'        => '75%',
            'active_fill' => '#FBBF24', // Warm Gold / Amber (Sesuai Gambar 1)
            'done_fill'   => '#FFFBEB',
            'text_active' => 'text-slate-900',
            'text_done'   => 'text-amber-900',
            'border'      => '#92400E',
            'align_tick'  => 'up', // Tick ke atas (Genap)
        ],
        [
            'index'       => 5,
            'key'         => $isClosedLost ? 'Closed Lost' : 'Closed Won',
            'title'       => $isClosedLost ? 'Closed Lost' : 'Closed Won',
            'desc'        => $isClosedLost ? 'Batal / Kalah Tender' : 'Menang / Deal',
            'prob'        => $isClosedLost ? '0%' : '100%',
            'active_fill' => $isClosedLost ? '#EF4444' : '#34D399', // Hijau Emerald atau Merah Lost
            'done_fill'   => $isClosedLost ? '#FEF2F2' : '#ECFDF5',
            'text_active' => 'text-white',
            'text_done'   => $isClosedLost ? 'text-red-900' : 'text-emerald-900',
            'border'      => $isClosedLost ? '#991B1B' : '#065F46',
            'align_tick'  => 'down', // Tick ke bawah (Ganjil)
        ],
    ];
@endphp

<div class="ipnet-card p-5 sm:p-6 space-y-4">
    
    {{-- Header Bar --}}
    <div class="flex items-center justify-between flex-wrap gap-3 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5 flex-wrap">
            <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                Sales Pipeline Stage &amp; Timeline
            </h3>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                Tahap {{ $currentStageIndex }} dari 5: {{ $currentSalesStage }}
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                Win Probability: {{ $currentProb }}%
            </span>
        </div>

        @if(empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
            <button type="button" 
                    @click="isEditPipelineModalOpen = true; window.openModal('modal-edit-pipeline')"
                    onclick="window.openModal('modal-edit-pipeline')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#74080a] transition cursor-pointer shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Ubah Stage &amp; Prospek</span>
            </button>
        @endif
    </div>

    {{-- Chevron Progress Timeline Bar (Sesuai Gambar 1 & 3) --}}
    <div class="overflow-x-auto pb-3 pt-1 scrollbar-thin">
        <div class="min-w-[760px] lg:min-w-0 grid grid-cols-5 gap-2 sm:gap-3 items-center">
            
            @foreach($pipelineSteps as $step)
                @php
                    $isPast = ($step['index'] < $currentStageIndex);
                    $isActive = ($step['index'] === $currentStageIndex);
                    $isFuture = ($step['index'] > $currentStageIndex);

                    // Tentukan warna dan outline
                    if ($isActive) {
                        $fillColor = $step['active_fill'];
                        $strokeColor = '#1E293B';
                        $strokeWidth = '2.5';
                        $textColor = $step['text_active'];
                    } elseif ($isPast) {
                        $fillColor = $step['done_fill'];
                        $strokeColor = $step['border'];
                        $strokeWidth = '2';
                        $textColor = $step['text_done'];
                    } else {
                        $fillColor = '#F8FAFC';
                        $strokeColor = '#CBD5E1';
                        $strokeWidth = '1.8';
                        $textColor = 'text-slate-400';
                    }
                @endphp

                <div class="flex flex-col items-center select-none group cursor-pointer"
                     @click="window.openEditPipelineWithStage('{{ $step['key'] }}')"
                     title="Klik untuk ubah ke tahap {{ $step['title'] }}">

                    {{-- Top Label & Tick (Untuk Step Genap: Discovery & Negotiation) --}}
                    @if($step['align_tick'] === 'up')
                        <div class="mb-1 flex flex-col items-center text-center">
                            <span class="text-[10px] sm:text-[11px] font-bold tracking-tight {{ $isActive ? 'text-slate-900 font-extrabold' : ($isPast ? 'text-slate-700 font-semibold' : 'text-slate-400') }}">
                                {{ $step['desc'] }}
                            </span>
                            <span class="text-[9.5px] font-semibold text-slate-400 font-mono">
                                ({{ $step['prob'] }} Peluang)
                            </span>
                            {{-- Vertical Tick Line pointing to Chevron (Sesuai Gambar 1) --}}
                            <div class="w-[2px] h-2.5 mt-0.5 {{ $isActive ? 'bg-slate-900' : ($isPast ? 'bg-slate-400' : 'bg-slate-300') }}"></div>
                        </div>
                    @else
                        {{-- Spacer agar ketinggian sejajar --}}
                        <div class="h-9 hidden sm:block"></div>
                    @endif

                    {{-- Chevron Arrow Shape Box (SVG Berbentuk Panah Kanan dengan Lekukan Kiri persis Gambar 1) --}}
                    <div class="relative w-full h-11 sm:h-12 transition-transform duration-200 group-hover:scale-[1.02] filter {{ $isActive ? 'drop-shadow-md' : 'drop-shadow-2xs' }}">
                        <svg viewBox="0 0 160 48" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                            {{-- Path Chevron dengan Lekukan Kiri dan Ujung Panah Kanan --}}
                            <path d="M 0 0 L 140 0 L 160 24 L 140 48 L 0 48 L 18 24 Z" 
                                  fill="{{ $fillColor }}" 
                                  stroke="{{ $strokeColor }}" 
                                  stroke-width="{{ $strokeWidth }}" 
                                  stroke-linejoin="round" />
                        </svg>

                        {{-- Teks di Dalam Chevron --}}
                        <div class="absolute inset-0 flex items-center justify-center pl-3.5 pr-2.5 gap-1.5 text-center pointer-events-none">
                            @if($isPast)
                                <div class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">
                                    ✓
                                </div>
                            @elseif($isActive)
                                <div class="w-2 h-2 rounded-full bg-white animate-ping mr-0.5"></div>
                            @else
                                <span class="text-[10px] font-mono font-bold opacity-60">
                                    {{ $step['index'] }}.
                                </span>
                            @endif

                            <span class="text-[11px] sm:text-xs font-black truncate tracking-tight {{ $textColor }}">
                                {{ $step['title'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Bottom Label & Tick (Untuk Step Ganjil: Qualification, Proposal, Won) --}}
                    @if($step['align_tick'] === 'down')
                        <div class="mt-1 flex flex-col items-center text-center">
                            {{-- Vertical Tick Line pointing to Chevron (Sesuai Gambar 1) --}}
                            <div class="w-[2px] h-2.5 mb-0.5 {{ $isActive ? 'bg-slate-900' : ($isPast ? 'bg-slate-400' : 'bg-slate-300') }}"></div>
                            <span class="text-[10px] sm:text-[11px] font-bold tracking-tight {{ $isActive ? 'text-slate-900 font-extrabold' : ($isPast ? 'text-slate-700 font-semibold' : 'text-slate-400') }}">
                                {{ $step['desc'] }}
                            </span>
                            <span class="text-[9.5px] font-semibold text-slate-400 font-mono">
                                ({{ $step['prob'] }} Peluang)
                            </span>
                        </div>
                    @else
                        {{-- Spacer agar ketinggian sejajar --}}
                        <div class="h-9 hidden sm:block"></div>
                    @endif

                </div>
            @endforeach

        </div>
    </div>
</div>
