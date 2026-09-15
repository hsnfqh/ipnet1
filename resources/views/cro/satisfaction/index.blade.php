@extends('layouts.app')

@section('title', 'Customer Satisfaction (CSAT) - CRO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isModalOpen: false }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Customer Satisfaction & Feedback (CSAT)'])
        
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

            {{-- 3 Metric Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <x-metric-card label="Rata-rata Skor CSAT" value="{{ number_format($avgScore, 2) }} / 5.0" icon="Star" :accent="true" />
                <x-metric-card label="Total Responden Survey" value="{{ $totalResponses }} Klien" icon="Users" />
                <x-metric-card label="Tingkat Kepuasan ⭐⭐⭐⭐⭐" value="{{ $fiveStarPct }}%" icon="ThumbsUp" />
            </div>

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('cro.satisfaction.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
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
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Kategori Layanan</option>
                        <option value="Managed Service" {{ request('category') == 'Managed Service' ? 'selected' : '' }}>Managed Service</option>
                        <option value="Project Delivery" {{ request('category') == 'Project Delivery' ? 'selected' : '' }}>Project Delivery</option>
                        <option value="Technical Support" {{ request('category') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                        <option value="Commercial / Account Mgmt" {{ request('category') == 'Commercial / Account Mgmt' ? 'selected' : '' }}>Commercial / Account Mgmt</option>
                    </select>
                    @if(request('client') || request('category'))
                        <a href="{{ route('cro.satisfaction.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Hasil Survey CSAT</span>
                </button>
            </div>

            {{-- Table CSAT --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[13%] whitespace-nowrap">Tanggal</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[22%]">Klien & Responden</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%] whitespace-nowrap">Layanan</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Rating CSAT</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[31%]">Feedback & Saran Perbaikan</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[5%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($surveys as $s)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 font-medium text-[12.5px]">
                                        {{ $s->survey_date ? $s->survey_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $s->client_name }}</div>
                                        <div class="text-[11.5px] text-[#75727C] mt-0.5">
                                            {{ $s->respondent_name }} {{ $s->respondent_role ? "({$s->respondent_role})" : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                            {{ $s->service_category }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 font-bold text-[13px] {{ $s->score_color }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-3.5 h-3.5 {{ $i <= $s->csat_score ? 'text-amber-400 fill-amber-400' : 'text-gray-200 fill-gray-200' }}" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-[12px] font-extrabold">{{ $s->csat_score }}.0</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-[12.5px]">
                                        @if($s->feedback_notes)
                                            <p class="text-gray-800 font-medium italic">"{{ $s->feedback_notes }}"</p>
                                        @endif
                                        @if($s->areas_of_improvement)
                                            <p class="text-[11.5px] text-rose-600 mt-1">⚠️ Perbaikan: {{ $s->areas_of_improvement }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <form action="{{ route('cro.satisfaction.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus survey ini?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada hasil survey kepuasan pelanggan yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($surveys->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $surveys->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal Tambah Survey CSAT --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Hasil Survey Kepuasan (CSAT)</h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.satisfaction.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan *</label>
                    <input type="text" name="client_name" list="clientList" required class="wms-input" placeholder="Pilih atau ketik nama klien">
                    <datalist id="clientList">
                        @foreach($clients as $c)
                            <option value="{{ $c->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori Layanan *</label>
                        <select name="service_category" required class="wms-input">
                            <option value="Managed Service">Managed Service</option>
                            <option value="Project Delivery">Project Delivery</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Commercial / Account Mgmt">Commercial / Account Mgmt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Survey *</label>
                        <input type="date" name="survey_date" required class="wms-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Responden *</label>
                        <input type="text" name="respondent_name" required class="wms-input" placeholder="e.g. Ir. Hendra Gunawan">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Jabatan / Role</label>
                        <input type="text" name="respondent_role" class="wms-input" placeholder="e.g. VP IT Infrastructure">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Skor CSAT (1 - 5) *</label>
                        <select name="csat_score" required class="wms-input font-bold text-amber-600">
                            <option value="5" selected>⭐⭐⭐⭐⭐ 5 (Sangat Puas)</option>
                            <option value="4">⭐⭐⭐⭐ 4 (Puas)</option>
                            <option value="3">⭐⭐⭐ 3 (Cukup / Netral)</option>
                            <option value="2">⭐⭐ 2 (Kurang Puas)</option>
                            <option value="1">⭐ 1 (Sangat Kecewa)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Sentimen Umum *</label>
                        <select name="sentiment" required class="wms-input">
                            <option value="Positive">Positif</option>
                            <option value="Neutral">Netral</option>
                            <option value="Negative">Negatif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Feedback / Testimoni Klien</label>
                    <textarea name="feedback_notes" rows="2" class="wms-input" placeholder="Kutipan feedback atau komentar dari klien..."></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Evaluasi / Improvement Area</label>
                    <textarea name="areas_of_improvement" rows="2" class="wms-input" placeholder="Area yang disarankan untuk diperbaiki internal..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Survey</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
