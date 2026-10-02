{{-- ═══ BERKAS SALES CARD (CONFIDENTIAL - HANYA SALES & MANAGEMENT) ═══ --}}
@php
    $authUser = auth()->user();
    $canAccessSalesDocs = $authUser && $project->canAccessSalesDocs($authUser);

    $salesTemplates = [
        [
            'code' => 'SC',
            'name' => 'Sales Calculator',
            'desc' => 'Dokumen Perhitungan / Sales Calculator'
        ],
    ];

    $salesDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
        ->where(function($q) {
            $q->where('stage_name', 'Sales')
              ->orWhere('document_key', 'like', 'sales_berkas%');
        })
        ->whereNotNull('file_path')
        ->where('file_path', '!=', '')
        ->latest()
        ->get();

    $salesDocsByCode = ['SC' => []];
    $otherSalesDocs = [];

    foreach ($salesDocs as $doc) {
        $docKeyUpper = strtoupper($doc->document_key ?? '');
        $docTypeUpper = strtoupper($doc->document_type ?? '');
        $docTitle = strtolower($doc->document_title ?? ($doc->name ?? ''));
        $fileName = strtolower($doc->file_name ?? '');

        if (
            $docTypeUpper === 'SC' ||
            $docKeyUpper === 'SC' ||
            str_starts_with($docKeyUpper, 'SALES_BERKAS_SC') ||
            str_contains($docTitle, 'calculator') ||
            str_contains($fileName, 'calculator') ||
            $docKeyUpper === 'SALES_BERKAS' ||
            str_starts_with($docKeyUpper, 'SALES_BERKAS_')
        ) {
            $salesDocsByCode['SC'][] = $doc;
        } else {
            $otherSalesDocs[] = $doc;
        }
    }
@endphp

@if($canAccessSalesDocs)
    <div x-data="{ 
            openSalesItems: {},
            toggleSalesItem(code) {
                this.openSalesItems[code] = !this.openSalesItems[code];
            }
         }" 
         class="ipnet-card p-6 space-y-4">
        
        {{-- Header Card Berkas Sales --}}
        <div class="flex items-center justify-between flex-wrap gap-3 pb-1 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                    Berkas Sales
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Dokumen khusus Head Sales &amp; Direktur.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if($salesDocs->count() > 0)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Total {{ $salesDocs->count() }} Berkas
                    </span>
                @endif
            </div>
        </div>

        {{-- Table Column Headers (Sama Persis Seperti Berkas Pendukung: KODE, NAMA & KETERANGAN DOKUMEN, STATUS BERKAS) --}}
        <div class="hidden sm:flex items-center justify-between gap-4 px-4 py-2 bg-slate-100/80 rounded-xl text-[10.5px] font-bold text-slate-500 uppercase tracking-wider border border-slate-200/80">
            <div class="flex items-center gap-4 sm:gap-5 min-w-0 pr-2 flex-1">
                <div class="w-16 sm:w-20 shrink-0 flex items-center justify-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                    <span>KODE</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span>NAMA &amp; KETERANGAN DOKUMEN</span>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="w-28 sm:w-32 text-center">STATUS BERKAS</span>
                <span class="w-7 text-center"></span>
            </div>
        </div>

        {{-- Accordion Item List: SC - Sales Calculator --}}
        <div class="space-y-2.5 pt-1">
            @foreach($salesTemplates as $tpl)
                @php
                    $tplDocs = $salesDocsByCode[$tpl['code']] ?? [];
                    $countForTpl = count($tplDocs);
                    $hasFile = $countForTpl > 0;
                @endphp

                <div x-data="{ 
                        isDragging: false, 
                        selectedFile: null, 
                        isUploading: false 
                      }" 
                     class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:border-slate-300 transition-all shadow-2xs">
                    
                    {{-- Row / Accordion Header --}}
                    <button type="button" 
                            @click="toggleSalesItem('{{ $tpl['code'] }}')" 
                            class="w-full px-4 py-2.5 flex items-center justify-between text-left transition hover:bg-slate-50/90 cursor-pointer select-none group">
                        <div class="flex items-center gap-4 sm:gap-5 min-w-0 pr-2 flex-1">
                            {{-- Badge Kode: Compact Pill --}}
                            <div class="w-16 sm:w-20 shrink-0">
                                <div class="inline-flex items-center justify-center w-full px-1.5 py-0.5 rounded-full bg-slate-100 border border-slate-200 group-hover:border-red-200 group-hover:bg-red-50/80 transition shadow-2xs">
                                    <span class="text-[11px] font-mono font-black text-slate-700 group-hover:text-[#8F0A0D] tracking-wide leading-tight">
                                        {{ $tpl['code'] }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Nama & Keterangan Dokumen --}}
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-[13px] text-slate-800 group-hover:text-[#8F0A0D] transition truncate">
                                    {{ $tpl['name'] }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-normal hidden sm:block truncate">
                                    {{ $tpl['desc'] }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            @if($countForTpl > 0)
                                <span class="w-28 sm:w-32 inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $countForTpl }} Berkas</span>
                                </span>
                            @else
                                <span class="w-28 sm:w-32 inline-flex items-center justify-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200 group-hover:border-slate-300 transition">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Kosong</span>
                                </span>
                            @endif

                            {{-- Dropdown Icon --}}
                            <div class="w-6 h-6 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-500 flex items-center justify-center transition shrink-0">
                                <svg class="w-3 h-3 transition-transform duration-200" 
                                     :class="{ 'rotate-180': openSalesItems['{{ $tpl['code'] }}'] }" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>

                    {{-- Dropdown Body (Ketika diklik dropdown untuk upload dan kelola berkas) --}}
                    <div x-show="openSalesItems['{{ $tpl['code'] }}']" 
                         x-cloak 
                         class="border-t border-slate-200 bg-slate-50/30 p-4 space-y-3">
                        
                        {{-- Daftar Berkas yang Sudah Diunggah --}}
                        @if($countForTpl > 0)
                            <div class="space-y-1.5">
                                <div class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                                    Berkas Tersedia ({{ $countForTpl }})
                                </div>
                                @foreach($tplDocs as $doc)
                                    <div class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-xs hover:border-slate-300 transition shadow-2xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-semibold text-[13px] text-slate-800 truncate block">{{ $doc->file_name ?? $doc->document_title }}</span>
                                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                    <span>{{ $doc->formatted_file_size }}</span>
                                                    @if($doc->created_at)
                                                        <span class="text-slate-300">•</span>
                                                        <span>{{ $doc->created_at->format('d M Y') }}</span>
                                                    @endif
                                                    @if($doc->notes)
                                                        <span class="text-slate-300">•</span>
                                                        <span class="italic text-slate-500 truncate max-w-[180px]">{{ $doc->notes }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shadow-2xs" 
                                               title="Unduh Berkas Sales">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Unduh</span>
                                            </a>
                                            <form action="{{ route('projects.documents.delete', [$project->id, $doc->id]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Hapus berkas sales ini?')" 
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" 
                                                        title="Hapus Berkas Sales">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Area Upload Berkas Dropzone --}}
                        <form action="{{ route('projects.documents.upload', $project->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data" 
                              @submit="isUploading = true" 
                              class="pt-1">
                            @csrf
                            <input type="hidden" name="stage_number" value="1">
                            <input type="hidden" name="stage_name" value="Sales">
                            <input type="hidden" name="document_category" value="sales">
                            <input type="hidden" name="document_key" value="sales_berkas_sc">
                            <input type="hidden" name="document_type" value="{{ $tpl['code'] }}">
                            <input type="hidden" name="document_title" value="{{ $tpl['name'] }}">

                            <div class="relative rounded-xl border-2 border-dashed transition-all duration-200 cursor-pointer overflow-hidden"
                                 :class="isDragging ? 'border-[#8F0A0D] bg-red-50/40' : (selectedFile ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-200 hover:border-slate-400 bg-white hover:bg-slate-50/60')"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="isDragging = false; if ($event.dataTransfer.files.length) { $refs.salesFileInput.files = $event.dataTransfer.files; selectedFile = $event.dataTransfer.files[0]; }">
                                
                                <input x-ref="salesFileInput" 
                                       type="file" 
                                       name="document_file" 
                                       required
                                       @change="selectedFile = $refs.salesFileInput.files[0]"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                <div class="py-6 px-4 flex flex-col items-center justify-center text-center space-y-2 pointer-events-none">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition"
                                         :class="selectedFile ? 'text-emerald-600 bg-emerald-100' : 'text-slate-800 bg-slate-100'">
                                        <template x-if="!selectedFile">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0l-4 4m4-4l4 4M5 20h14" />
                                            </svg>
                                        </template>
                                        <template x-if="selectedFile">
                                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </template>
                                    </div>

                                    <template x-if="!selectedFile">
                                        <div>
                                            <p class="text-xs font-semibold text-slate-800">
                                                Klik atau seret berkas ke sini untuk mengunggah <span class="font-bold text-[#8F0A0D]">{{ $tpl['name'] }}</span>
                                            </p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">Format: PDF, XLSX, XLS, DOCX, ZIP, PNG, JPG (Maks 50MB)</p>
                                        </div>
                                    </template>

                                    <template x-if="selectedFile">
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-bold text-slate-900 truncate max-w-sm" x-text="selectedFile.name"></p>
                                            <p class="text-[11px] text-emerald-600 font-medium" x-text="'Ukuran: ' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB • Siap diunggah'"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Opsi Catatan & Tombol Submit --}}
                            <div x-show="selectedFile" x-cloak class="mt-3 flex items-center justify-between flex-wrap gap-2 pt-2 border-t border-slate-100">
                                <div class="flex-1 min-w-[200px]">
                                    <input type="text" 
                                           name="notes" 
                                           placeholder="Catatan berkas (opsional)..." 
                                           class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:ring-1 focus:ring-red-500 focus:border-red-500">
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            @click="selectedFile = null; $refs.salesFileInput.value = ''" 
                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" 
                                            :disabled="isUploading"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-bold btn-ipnet-primary transition cursor-pointer shadow-xs disabled:opacity-50">
                                        <svg x-show="!isUploading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span x-text="isUploading ? 'Mengunggah...' : 'Unggah Berkas'"></span>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            @endforeach

            {{-- Jika Ada Berkas Sales Lain yang Pernah Diunggah Sebelumnya --}}
            @if(count($otherSalesDocs) > 0)
                <div x-data="{ openOther: false }" class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-2xs">
                    <button type="button" @click="openOther = !openOther" class="w-full px-5 py-3.5 flex items-center justify-between text-left transition hover:bg-slate-50/90 cursor-pointer select-none group">
                        <div class="flex items-center gap-4 sm:gap-6 min-w-0 pr-2 flex-1">
                            <div class="w-20 sm:w-24 shrink-0">
                                <div class="flex items-center justify-center px-2 py-1.5 rounded-lg bg-slate-100 border border-slate-200 group-hover:border-red-200 group-hover:bg-red-50/80 transition shadow-2xs">
                                    <span class="text-xs sm:text-sm font-mono font-black text-slate-900 group-hover:text-[#8F0A0D] tracking-wider text-center">
                                        DOC
                                    </span>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-[#8F0A0D] transition truncate">
                                    Dokumen Sales Lainnya
                                </div>
                                <div class="text-[10.5px] text-slate-400 font-medium hidden sm:block">
                                    Berkas sales pendukung tambahan
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="w-32 sm:w-36 inline-flex items-center justify-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <span>{{ count($otherSalesDocs) }} Berkas</span>
                            </span>
                            <div class="w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-600 flex items-center justify-center transition shrink-0">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openOther }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </button>
                    <div x-show="openOther" x-cloak class="border-t border-slate-200 bg-slate-50/30 p-4 space-y-1.5">
                        @foreach($otherSalesDocs as $doc)
                            <div class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-xs hover:border-slate-300 transition shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-semibold text-[13px] text-slate-800 truncate block">{{ $doc->file_name ?? $doc->document_title }}</span>
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                            <span>{{ $doc->formatted_file_size }}</span>
                                            @if($doc->created_at)
                                                <span class="text-slate-300">•</span>
                                                <span>{{ $doc->created_at->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh</span>
                                    </a>
                                    <form action="{{ route('projects.documents.delete', [$project->id, $doc->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>
@endif
