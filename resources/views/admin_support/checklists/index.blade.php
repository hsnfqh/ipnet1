@extends('layouts.app')

@section('title', 'Project Gatekeeper Matrix - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Project Document Gatekeeper Matrix'])
        
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



            {{-- Filter Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('admin_support.checklists.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <select name="project_id" onchange="this.form.submit()" 
                            class="w-full sm:w-64 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    <select name="milestone" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Milestone</option>
                        <option value="Pre-Sales Handover" {{ request('milestone') == 'Pre-Sales Handover' ? 'selected' : '' }}>Pre-Sales Handover</option>
                        <option value="Project Kickoff" {{ request('milestone') == 'Project Kickoff' ? 'selected' : '' }}>Project Kickoff</option>
                        <option value="Procurement" {{ request('milestone') == 'Procurement' ? 'selected' : '' }}>Procurement Material</option>
                        <option value="Implementation" {{ request('milestone') == 'Implementation' ? 'selected' : '' }}>Implementation / UAT</option>
                        <option value="BAST Handover" {{ request('milestone') == 'BAST Handover' ? 'selected' : '' }}>BAST Handover</option>
                    </select>

                    @if(request('project_id') || request('milestone'))
                        <a href="{{ route('admin_support.checklists.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Checklists --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[22%]">Proyek & Milestone</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[28%]">Nama Dokumen Persyaratan</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Tingkat Wajib</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Penyerahan</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Verifikasi Admin</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($checklists as $chk)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $chk->project ? $chk->project->name : 'General Proyek' }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 font-medium">
                                            Tahap: <span class="text-gray-800 font-semibold">{{ $chk->milestone }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $chk->document_name }}</div>
                                        @if($chk->notes)
                                            <div class="text-[11px] text-gray-500 mt-0.5">{{ $chk->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @if($chk->is_mandatory)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Mandatory (Wajib)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-gray-100 text-gray-700">
                                                Optional
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @if($chk->is_submitted)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Telah Diserahkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Belum Diserahkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @if($chk->is_verified)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Verified Sah
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                Belum Lolos
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <form action="{{ route('admin_support.checklists.update', $chk->id) }}" method="POST" class="inline-flex items-center gap-2">
                                            @csrf
                                            @if(!$chk->is_verified)
                                                <input type="hidden" name="is_submitted" value="1">
                                                <input type="hidden" name="is_verified" value="1">
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                                    Setujui
                                                </button>
                                            @else
                                                <input type="hidden" name="is_submitted" value="1">
                                                <button type="submit" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                                                    Batalkan
                                                </button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada checklist gatekeeper tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($checklists->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $checklists->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
